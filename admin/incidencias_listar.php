<?php
session_start();
require "../config/conexion.php";

if ($_SESSION["rol"] !== "administrador") {
    header("Location: ../index.php");
    exit;
}

// Obtener todas las incidencias
$incidencias = $pdo->query("
    SELECT i.*, u.nombre_completo, l.codigo 
    FROM incidencias i
    LEFT JOIN usuarios u ON i.usuario_id=u.id_usuario
    LEFT JOIN laptops l ON i.laptop_id=l.id
    ORDER BY i.fecha DESC
")->fetchAll();
?>
<link rel="stylesheet" href="../css/style.css">
<?php include $_SERVER['DOCUMENT_ROOT']."/includes/navbar.php"; ?>


<div class="contenedor">
<h2 style="color:#003366;">Incidencias Reportadas</h2>

<table>
<tr>
    <th>Profesor</th>
    <th>Laptop</th>
    <th>Tipo</th>
    <th>Descripción</th>
    <th>Estado</th>
    <th>Fecha</th>
</tr>

<?php foreach($incidencias as $i): ?>
<tr>
    <td><?= $i['nombre_completo'] ?? 'N/A' ?></td>
    <td><?= $i['codigo'] ?? 'N/A' ?></td>
    <td><?= ucfirst($i['tipo']) ?></td>
    <td><?= $i['descripcion'] ?></td>
    <td><?= ucfirst($i['estado']) ?></td>
    <td><?= $i['fecha'] ?></td>
</tr>
<?php endforeach; ?>
</table>