<?php
session_start();
require "../config/conexion.php";

if ($_SESSION['rol'] !== 'profesor') {
    header("Location: ../index.php");
    exit;
}

$id_usuario = $_SESSION['id_usuario'];
$incidencias = $pdo->prepare("
    SELECT i.*, l.codigo FROM incidencias i
    JOIN laptops l ON i.laptop_id=l.id
    WHERE i.usuario_id=? ORDER BY i.fecha DESC
");
$incidencias->execute([$id_usuario]);
$incidencias = $incidencias->fetchAll();
?>
<link rel="stylesheet" href="../css/style.css">
<?php include $_SERVER['DOCUMENT_ROOT']."/includes/navbar.php"; ?>
<div class="container">
<h2>⚠️ Mis Incidencias</h2>

<table>
<tr>
    <th>Fecha</th>
    <th>Laptop</th>
    <th>Tipo</th>
    <th>Descripción</th>
    <th>Estado</th>
</tr>

<?php foreach($incidencias as $i): ?>
<tr>
    <td><?= $i['fecha'] ?></td>
    <td><?= $i['codigo'] ?></td>
    <td><?= ucfirst($i['tipo']) ?></td>
    <td><?= $i['descripcion'] ?></td>
    <td><?= ucfirst($i['estado']) ?></td>
</tr>
<?php endforeach; ?>
</table>
</div>