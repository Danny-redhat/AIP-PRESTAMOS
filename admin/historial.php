<?php
session_start();
require __DIR__ . "/../config/conexion.php";

if ($_SESSION['rol'] !== 'administrador') {
    header("Location: ../index.php");
    exit;
}

// ── FILTROS ──────────────────────────────────────────────
$filtro_area  = trim($_GET['area']  ?? '');
$filtro_desde = trim($_GET['desde'] ?? '');
$filtro_hasta = trim($_GET['hasta'] ?? '');

$where  = ["p.id_prestamo IS NOT NULL"]; // solo movimientos reales
$params = [];

if ($filtro_area) {
    $where[]  = "u.area = ?";
    $params[] = $filtro_area;
}
if ($filtro_desde) {
    $where[]  = "p.fecha_prestamo >= ?";
    $params[] = $filtro_desde . " 00:00:00";
}
if ($filtro_hasta) {
    $where[]  = "p.fecha_prestamo <= ?";
    $params[] = $filtro_hasta . " 23:59:59";
}

$whereSQL = implode(" AND ", $where);

// ── CONSULTA PRINCIPAL ────────────────────────────────────
// Une prestamos → solicitudes → usuarios → laptops
$stmt = $pdo->prepare("
    SELECT
        p.id_prestamo,
        p.fecha_prestamo,
        p.fecha_devolucion,
        p.estado        AS estado_prestamo,
        p.codigo_confirmacion,
        l.codigo        AS laptop_codigo,
        l.marca,
        l.tipo          AS laptop_tipo,
        u.nombre_completo,
        u.dni,
        u.area,
        s.tipo_solicitud,
        s.fecha_solicitud,
        s.mensaje
    FROM prestamos p
    JOIN solicitudes s ON p.solicitud_id = s.id_solicitud
    JOIN usuarios u    ON s.id_usuario   = u.id_usuario
    JOIN laptops l     ON p.laptop_id    = l.id
    WHERE $whereSQL
    ORDER BY p.fecha_prestamo DESC
    LIMIT 500
");
$stmt->execute($params);
$movimientos = $stmt->fetchAll();

// ── ÁREAS PARA FILTRO ─────────────────────────────────────
$areas = $pdo->query(
    "SELECT DISTINCT area FROM usuarios
     WHERE area IS NOT NULL AND area != ''
     ORDER BY area"
)->fetchAll(PDO::FETCH_COLUMN);

// ── EXPORTAR CSV ──────────────────────────────────────────
if (isset($_GET['exportar'])) {
    $nombre_archivo = "movimientos_laptops";
    if ($filtro_desde && $filtro_hasta) {
        $nombre_archivo .= "_{$filtro_desde}_al_{$filtro_hasta}";
    }
    if ($filtro_area) {
        $nombre_archivo .= "_" . preg_replace('/[^a-zA-Z0-9]/', '_', $filtro_area);
    }
    $nombre_archivo .= ".csv";

    header('Content-Type: text/csv; charset=utf-8');
    header("Content-Disposition: attachment; filename=\"$nombre_archivo\"");
    header('Pragma: no-cache');

    $out = fopen('php://output', 'w');
    fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM para Excel

    fputcsv($out, [
        'N°', 'Fecha Préstamo', 'Fecha Devolución', 'Estado',
        'Laptop', 'Marca', 'Tipo Laptop',
        'Profesor', 'DNI', 'Área / Curso',
        'Tipo Solicitud', 'Código'
    ], ';');

    foreach ($movimientos as $i => $m) {
        fputcsv($out, [
            $i + 1,
            $m['fecha_prestamo']   ? date('d/m/Y H:i', strtotime($m['fecha_prestamo']))   : '-',
            $m['fecha_devolucion'] ? date('d/m/Y H:i', strtotime($m['fecha_devolucion'])) : 'Pendiente',
            $m['estado_prestamo'] === 'prestado' ? 'En préstamo' : 'Devuelto',
            $m['laptop_codigo'],
            $m['marca'],
            ucfirst($m['laptop_tipo']),
            $m['nombre_completo'],
            $m['dni'],
            $m['area'] ?? '-',
            ucfirst($m['tipo_solicitud']),
            $m['codigo_confirmacion'],
        ], ';');
    }
    fclose($out);
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Historial de Movimientos — AIP Préstamos</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .filtros-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
            gap: 12px;
            margin-bottom: .8rem;
        }
        .filtros-grid .form-group { margin-bottom: 0; }
        .estado-pill {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 20px;
            font-size: .76rem;
            font-weight: 700;
        }
        .pill-prestado  { background: #DBEAFE; color: #1E3A8A; }
        .pill-devuelto  { background: #D1FAE5; color: #065F46; }
    </style>
</head>
<body>
<?php include __DIR__ . "/../includes/navbar.php"; ?>

<div class="contenedor">
    <!-- CABECERA -->
    <div class="page-header" style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:12px;">
        <div>
            <h2>Historial de movimientos de laptops</h2>
            <p>Qué profesor usó cada laptop, cuándo la recogió y cuándo la devolvió</p>
        </div>
        <a href="?<?= http_build_query(array_merge($_GET, ['exportar' => '1'])) ?>"
           class="btn btn-success">
            ⬇ Descargar CSV
        </a>
    </div>

    <!-- FILTROS -->
    <div class="card">
        <div class="card-header">
            <span class="card-title">Filtrar movimientos</span>
        </div>
        <form method="GET" action="">
            <div class="filtros-grid">
                <div class="form-group">
                    <label>Área / Curso</label>
                    <select name="area">
                        <option value="">— Todos los cursos —</option>
                        <?php foreach ($areas as $a): ?>
                            <option value="<?= htmlspecialchars($a) ?>"
                                <?= $filtro_area === $a ? 'selected' : '' ?>>
                                <?= htmlspecialchars($a) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Desde (fecha préstamo)</label>
                    <input type="date" name="desde" value="<?= htmlspecialchars($filtro_desde) ?>">
                </div>
                <div class="form-group">
                    <label>Hasta (fecha préstamo)</label>
                    <input type="date" name="hasta" value="<?= htmlspecialchars($filtro_hasta) ?>">
                </div>
            </div>
            <div style="display:flex; gap:8px; flex-wrap:wrap; align-items:center;">
                <button type="submit" class="btn btn-primary btn-sm">🔍 Filtrar</button>
                <a href="historial.php" class="btn btn-outline-azul btn-sm">✕ Limpiar</a>
                <span style="font-size:.85rem; color:var(--gris-texto); margin-left:8px;">
                    <?= count($movimientos) ?> movimiento(s) encontrado(s)
                    <?php if ($filtro_area): ?>
                        — Área: <strong><?= htmlspecialchars($filtro_area) ?></strong>
                    <?php endif; ?>
                </span>
            </div>
        </form>
    </div>

    <!-- TABLA -->
    <div class="card">
        <div class="tabla-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>N°</th>
                        <th>Laptop</th>
                        <th>Tipo</th>
                        <th>Profesor</th>
                        <th>Área / Curso</th>
                        <th>Fecha préstamo</th>
                        <th>Fecha devolución</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($movimientos)): ?>
                    <tr>
                        <td colspan="8" style="text-align:center; color:var(--gris-texto); padding:2rem;">
                            No hay movimientos registrados con esos filtros.
                        </td>
                    </tr>
                <?php endif; ?>
                <?php foreach ($movimientos as $i => $m): ?>
                    <tr>
                        <td style="color:var(--gris-texto); font-size:.82rem;"><?= $i + 1 ?></td>
                        <td>
                            <strong><?= htmlspecialchars($m['laptop_codigo']) ?></strong><br>
                            <small style="color:var(--gris-texto);"><?= htmlspecialchars($m['marca']) ?></small>
                        </td>
                        <td><?= ucfirst($m['laptop_tipo']) ?></td>
                        <td>
                            <strong><?= htmlspecialchars($m['nombre_completo']) ?></strong><br>
                            <small style="color:var(--gris-texto);">DNI: <?= $m['dni'] ?></small>
                        </td>
                        <td>
                            <?php if ($m['area']): ?>
                                <span class="estado estado-pendiente" style="font-size:.78rem;">
                                    <?= htmlspecialchars($m['area']) ?>
                                </span>
                            <?php else: ?>
                                <span style="color:var(--gris-texto);">—</span>
                            <?php endif; ?>
                        </td>
                        <td style="font-size:.85rem; white-space:nowrap;">
                            <?= date('d/m/Y', strtotime($m['fecha_prestamo'])) ?><br>
                            <small style="color:var(--gris-texto);">
                                <?= date('H:i', strtotime($m['fecha_prestamo'])) ?>
                            </small>
                        </td>
                        <td style="font-size:.85rem; white-space:nowrap;">
                            <?php if ($m['fecha_devolucion']): ?>
                                <?= date('d/m/Y', strtotime($m['fecha_devolucion'])) ?><br>
                                <small style="color:var(--gris-texto);">
                                    <?= date('H:i', strtotime($m['fecha_devolucion'])) ?>
                                </small>
                            <?php else: ?>
                                <span style="color:var(--gris-texto); font-style:italic; font-size:.82rem;">
                                    Pendiente
                                </span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="estado-pill <?= $m['estado_prestamo'] === 'prestado' ? 'pill-prestado' : 'pill-devuelto' ?>">
                                <?= $m['estado_prestamo'] === 'prestado' ? 'En préstamo' : 'Devuelto' ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . "/../includes/footer.php"; ?>