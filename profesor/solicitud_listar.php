<?php
session_start();
require "../config/conexion.php";

if ($_SESSION['rol'] !== 'profesor') {
    header("Location: ../index.php");
    exit;
}

$id_usuario = $_SESSION['id_usuario'];

$solicitudes = $pdo->prepare("SELECT s.*, l.codigo FROM solicitudes s LEFT JOIN laptops l ON s.id_laptop=l.id WHERE s.id_usuario=? ORDER BY fecha_solicitud DESC");
$solicitudes->execute([$id_usuario]);
$solicitudes = $solicitudes->fetchAll();
?>

<link rel="stylesheet" href="../css/style.css">
<?php include $_SERVER['DOCUMENT_ROOT']."/includes/navbar.php"; ?>
<div class="container">
<h2>📄 Mis Solicitudes</h2>

<table>
<tr>
    <th>Fecha</th>
    <th>Tipo</th>
    <th>Laptop</th>
    <th>Mensaje</th>
    <th>Estado</th>
</tr>

<?php foreach($solicitudes as $s): ?>
<tr>
    <td><?= $s['fecha_solicitud'] ?></td>
    <td><?= ucfirst($s['tipo_solicitud']) ?></td>
    <td><?= $s['codigo'] ?? '-' ?></td>
    <td><?= $s['mensaje'] ?></td>
    <td><?= ucfirst($s['estado']) ?></td>
</tr>
<?php endforeach; ?>
</table>
</div>