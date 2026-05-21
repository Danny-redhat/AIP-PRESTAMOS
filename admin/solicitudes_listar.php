<?php
session_start();
require __DIR__ . "/../config/conexion.php";

if ($_SESSION['rol'] !== 'administrador') {
    header("Location: ../index.php");
    exit;
}

// Mensajes de confirmación desde procesar_solicitud.php
$msg = $_GET['msg'] ?? '';

$solicitudes = $pdo->query("
    SELECT s.*, u.nombre_completo, u.area, u.dni,
           l.codigo AS laptop_codigo, l.marca
    FROM solicitudes s
    JOIN usuarios u    ON s.id_usuario = u.id_usuario
    LEFT JOIN laptops l ON s.id_laptop  = l.id
    WHERE s.estado = 'pendiente'
    ORDER BY s.fecha_solicitud ASC
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Solicitudes Pendientes — AIP Préstamos</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include __DIR__ . "/../includes/navbar.php"; ?>

<div class="contenedor">
    <div class="page-header">
        <h2>Solicitudes pendientes</h2>
        <p>Acepta o rechaza las solicitudes de préstamo de los profesores</p>
    </div>

    <?php if ($msg === 'aceptada'): ?>
        <div class="alerta alerta-exito">✅ Solicitud aceptada. Se envió el código al correo del profesor.</div>
    <?php elseif ($msg === 'rechazada'): ?>
        <div class="alerta alerta-error">❌ Solicitud rechazada. Se notificó al profesor.</div>
    <?php endif; ?>

    <div class="card">
        <?php if (empty($solicitudes)): ?>
            <div style="text-align:center; padding:2rem; color:var(--gris-texto);">
                <p style="font-size:1.1rem;">✅ No hay solicitudes pendientes.</p>
            </div>
        <?php else: ?>
        <div class="tabla-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Profesor</th>
                        <th>Área / Curso</th>
                        <th>Laptop solicitada</th>
                        <th>Tipo</th>
                        <th>Mensaje</th>
                        <th>Fecha</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($solicitudes as $s): ?>
                    <tr>
                        <td>
                            <strong><?= htmlspecialchars($s['nombre_completo']) ?></strong><br>
                            <small style="color:var(--gris-texto);">DNI: <?= $s['dni'] ?></small>
                        </td>
                        <td>
                            <?php if ($s['area']): ?>
                                <span class="estado estado-pendiente"><?= htmlspecialchars($s['area']) ?></span>
                            <?php else: ?>—<?php endif; ?>
                        </td>
                        <td>
                            <?php if ($s['laptop_codigo']): ?>
                                <strong><?= htmlspecialchars($s['laptop_codigo']) ?></strong><br>
                                <small style="color:var(--gris-texto);"><?= htmlspecialchars($s['marca']) ?></small>
                            <?php else: ?>
                                <span style="color:var(--gris-texto); font-style:italic;">Sin preferencia</span>
                            <?php endif; ?>
                        </td>
                        <td><?= ucfirst($s['tipo_solicitud']) ?></td>
                        <td style="font-size:.88rem; max-width:200px;">
                            <?= htmlspecialchars($s['mensaje'] ?: '—') ?>
                        </td>
                        <td style="font-size:.82rem; color:var(--gris-texto); white-space:nowrap;">
                            <?= date('d/m/Y H:i', strtotime($s['fecha_solicitud'])) ?>
                        </td>
                        <td>
                            <div class="tabla-acciones">
                                <form method="POST" action="procesar_solicitud.php">
                                    <input type="hidden" name="id_solicitud" value="<?= $s['id_solicitud'] ?>">
                                    <button type="submit" name="accion" value="aceptar"
                                            class="btn btn-success btn-sm"
                                            onclick="return confirm('¿Aceptar solicitud de <?= htmlspecialchars($s['nombre_completo']) ?>?')">
                                        ✅ Aceptar
                                    </button>
                                </form>
                                <form method="POST" action="procesar_solicitud.php">
                                    <input type="hidden" name="id_solicitud" value="<?= $s['id_solicitud'] ?>">
                                    <button type="submit" name="accion" value="rechazar"
                                            class="btn btn-danger btn-sm"
                                            onclick="return confirm('¿Rechazar esta solicitud?')">
                                        ✗ Rechazar
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . "/../includes/footer.php"; ?>