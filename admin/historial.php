<?php
session_start();
require "../config/conexion.php";

if ($_SESSION['rol'] !== 'administrador') {
    header("Location: ../index.php");
    exit;
}

// ── FILTROS ─────────────────────────────────────────────
$filtro_area   = $_GET['area']   ?? '';
$filtro_desde  = $_GET['desde']  ?? '';
$filtro_hasta  = $_GET['hasta']  ?? '';
$filtro_accion = $_GET['accion'] ?? '';

$where  = ["1=1"];
$params = [];

if ($filtro_area) {
    $where[]  = "u.area = ?";
    $params[] = $filtro_area;
}
if ($filtro_desde) {
    $where[]  = "h.fecha >= ?";
    $params[] = $filtro_desde . " 00:00:00";
}
if ($filtro_hasta) {
    $where[]  = "h.fecha <= ?";
    $params[] = $filtro_hasta . " 23:59:59";
}
if ($filtro_accion) {
    $where[]  = "h.accion = ?";
    $params[] = $filtro_accion;
}

$whereSQL = implode(" AND ", $where);

// ── DATOS ────────────────────────────────────────────────
$stmt = $pdo->prepare("
    SELECT h.*, u.nombre_completo, u.area, u.dni,
           l.codigo AS laptop_codigo
    FROM historial h
    LEFT JOIN usuarios u ON h.usuario_id = u.id_usuario
    LEFT JOIN (
        SELECT id_solicitud, id_laptop FROM solicitudes
    ) s ON h.descripcion LIKE CONCAT('%#', s.id_solicitud, '%')
    LEFT JOIN laptops l ON s.id_laptop = l.id
    WHERE $whereSQL
    ORDER BY h.fecha DESC
    LIMIT 500
");
$stmt->execute($params);
$historial = $stmt->fetchAll();

// ── ÁREAS DISPONIBLES PARA FILTRO ───────────────────────
$areas = $pdo->query(
    "SELECT DISTINCT area FROM usuarios WHERE area IS NOT NULL AND area != '' ORDER BY area"
)->fetchAll(PDO::FETCH_COLUMN);

// ── EXPORTAR CSV ─────────────────────────────────────────
if (isset($_GET['exportar'])) {
    $nombre_archivo = "historial_AIP";
    if ($filtro_desde && $filtro_hasta) {
        $nombre_archivo .= "_{$filtro_desde}_al_{$filtro_hasta}";
    }
    if ($filtro_area) {
        $nombre_archivo .= "_area_" . preg_replace('/[^a-zA-Z0-9]/', '_', $filtro_area);
    }
    $nombre_archivo .= ".csv";

    header('Content-Type: text/csv; charset=utf-8');
    header("Content-Disposition: attachment; filename=\"$nombre_archivo\"");
    header('Pragma: no-cache');

    $out = fopen('php://output', 'w');
    // BOM para que Excel abra tildes correctamente
    fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));

    fputcsv($out, ['Fecha', 'Hora', 'Usuario', 'DNI', 'Area', 'Accion', 'Descripcion'], ';');
    foreach ($historial as $h) {
        $fecha = explode(' ', $h['fecha']);
        fputcsv($out, [
            $fecha[0] ?? '',
            $fecha[1] ?? '',
            $h['nombre_completo'] ?? 'Sistema',
            $h['dni'] ?? '-',
            $h['area'] ?? '-',
            $h['accion'],
            $h['descripcion'],
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
    <title>Historial — AIP Préstamos</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .filtros-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 12px;
            margin-bottom: 1rem;
        }
        .filtros-grid .form-group { margin-bottom: 0; }
        .filtros-acciones {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            align-items: flex-end;
            margin-top: 4px;
        }
        .tag-accion {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 20px;
            font-size: 0.76rem;
            font-weight: 700;
            background: #EEF2FF;
            color: #3730A3;
        }
        .tag-accion.aceptar_solicitud { background: #D1FAE5; color: #065F46; }
        .tag-accion.rechazar_solicitud { background: #FEE2E2; color: #991B1B; }
        .tag-accion.entrega_laptop  { background: #DBEAFE; color: #1E3A8A; }
        .tag-accion.devolucion_laptop { background: #FEF3C7; color: #92400E; }
    </style>
</head>
<body>
<?php include __DIR__ . "/../includes/navbar.php"; ?>

<div class="contenedor">
    <div class="page-header" style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:12px;">
        <div>
            <h2>Historial de Prestamos</h2>
            <p>Registro de préstamos, entregas y devoluciones de laptops</p>
        </div>
        <a href="?<?= http_build_query(array_merge($_GET, ['exportar'=>'1'])) ?>"
           class="btn btn-success">
            ⬇ Descargar CSV
        </a>
    </div>

    <!-- FILTROS -->
    <div class="card">
        <div class="card-header">
            <span class="card-title">Filtros de búsqueda</span>
        </div>
        <form method="GET" action="">
            <div class="filtros-grid">
                <div class="form-group">
                    <label>Área / Curso</label>
                    <select name="area">
                        <option value="">— Todos —</option>
                        <?php foreach ($areas as $a): ?>
                            <option value="<?= htmlspecialchars($a) ?>"
                                <?= $filtro_area === $a ? 'selected' : '' ?>>
                                <?= htmlspecialchars($a) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Acción</label>
                    <select name="accion">
                        <option value="">— Todas —</option>
                        <option value="aceptar_solicitud"  <?= $filtro_accion==='aceptar_solicitud'?'selected':'' ?>>Solicitud aceptada</option>
                        <option value="rechazar_solicitud" <?= $filtro_accion==='rechazar_solicitud'?'selected':'' ?>>Solicitud rechazada</option>
                        <option value="entrega_laptop"     <?= $filtro_accion==='entrega_laptop'?'selected':'' ?>>Entrega de laptop</option>
                        <option value="devolucion_laptop"  <?= $filtro_accion==='devolucion_laptop'?'selected':'' ?>>Devolución de laptop</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Desde</label>
                    <input type="date" name="desde" value="<?= htmlspecialchars($filtro_desde) ?>">
                </div>
                <div class="form-group">
                    <label>Hasta</label>
                    <input type="date" name="hasta" value="<?= htmlspecialchars($filtro_hasta) ?>">
                </div>
            </div>
            <div class="filtros-acciones">
                <button type="submit" class="btn btn-primary btn-sm">🔍 Filtrar</button>
                <a href="historial.php" class="btn btn-outline-azul btn-sm">✕ Limpiar filtros</a>
                <span style="font-size:0.85rem; color:var(--gris-texto); margin-left:8px;">
                    <?= count($historial) ?> registro(s) encontrado(s)
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
                        <th>Fecha y hora</th>
                        <th>Profesor / Usuario</th>
                        <th>Área</th>
                        <th>Acción</th>
                        <th>Laptop</th>
                        <th>Descripción</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($historial)): ?>
                    <tr>
                        <td colspan="5" style="text-align:center; color:var(--gris-texto); padding:2rem;">
                            No hay registros con los filtros seleccionados.
                        </td>
                    </tr>
                <?php endif; ?>
                <?php foreach ($historial as $h): ?>
                    <tr>
                        <td style="white-space:nowrap; font-size:0.85rem; color:var(--gris-texto);">
                            <?= date('d/m/Y', strtotime($h['fecha'])) ?><br>
                            <small><?= date('H:i', strtotime($h['fecha'])) ?></small>
                        </td>
                        <td>
                            <strong><?= htmlspecialchars($h['nombre_completo'] ?? 'Sistema') ?></strong>
                            <?php if ($h['dni']): ?>
                                <br><small style="color:var(--gris-texto);">DNI: <?= $h['dni'] ?></small>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($h['area'] ?? '—') ?></td>
                        <td>
                            <span class="tag-accion <?= $h['accion'] ?>">
                                <?= htmlspecialchars($h['accion']) ?>
                            </span>
                        </td>
                        <td>
                            <?= htmlspecialchars($h['laptop_codigo'] ?? '—') ?>
                        </td>
                        <td style="font-size:0.88rem;">
                            <?= htmlspecialchars($h['descripcion']) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . "/../includes/footer.php"; ?>