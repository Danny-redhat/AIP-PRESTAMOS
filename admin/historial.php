<?php
session_start();
require "../config/conexion.php";

$historial = $pdo->query("
    SELECT h.*, u.nombre_completo 
    FROM historial h 
    LEFT JOIN usuarios u ON h.usuario_id=u.id_usuario
    ORDER BY h.fecha DESC
")->fetchAll();
?>

<link rel="stylesheet" href="../css/style.css">
<?php include $_SERVER['DOCUMENT_ROOT']."/includes/navbar.php"; ?>

<div class="contenedor">
<h2>Historial del Sistema</h2>

<table>
<tr style="background-color:#003366; color:#FFD700;">
    <th>Usuario</th>
    <th>Acción</th>
    <th>Descripción</th>
    <th>Fecha</th>
</tr>

<?php foreach($historial as $h): ?>
<tr>
    <td><?= $h['nombre_completo'] ?? 'Sistema' ?></td>
    <td><?= $h['accion'] ?></td>
    <td><?= $h['descripcion'] ?></td>
    <td><?= $h['fecha'] ?></td>
</tr>
<?php endforeach; ?>
</table>
