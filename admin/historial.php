<?php
session_start();
require "../config/conexion.php";

if ($_SESSION['rol'] !== 'administrador') {
    header("Location: ../index.php");
    exit;
}

$historial = $pdo->query("
    SELECT h.*, u.nombre_completo 
    FROM historial h 
    LEFT JOIN usuarios u ON h.usuario_id = u.id_usuario
    ORDER BY h.fecha DESC
    LIMIT 200
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Historial — AIP Préstamos</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include $_SERVER['DOCUMENT_ROOT'] . "/includes/navbar.php"; ?>

<div class="contenedor">
    <div class="page-header">
        <h2>Historial del sistema</h2>
        <p>Registro de todas las acciones realizadas</p>
    </div>

    <div class="card">
        <div class="tabla-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Usuario</th>
                        <th>Acción</th>
                        <th>Descripción</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($historial as $h): ?>
                    <tr>
                        <td><?= htmlspecialchars($h['nombre_completo'] ?? 'Sistema') ?></td>
                        <td><span class="estado estado-entregado"><?= htmlspecialchars($h['accion']) ?></span></td>
                        <td><?= htmlspecialchars($h['descripcion']) ?></td>
                        <td><?= $h['fecha'] ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($historial)): ?>
                    <tr><td colspan="4" style="text-align:center; color:#6B7280;">Sin registros aún.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include $_SERVER['DOCUMENT_ROOT'] . "/includes/footer.php"; ?>
</body>
</html>