<?php session_start(); require "../config/conexion.php";
$id = $_SESSION["id_usuario"];
$pdo->prepare("
    UPDATE notificaciones SET leido=1 WHERE usuario_id=?
")->execute([$id]);
$notificaciones = $pdo->prepare("
    SELECT * FROM notificaciones
    WHERE usuario_id=? ORDER BY fecha DESC
");
$notificaciones->execute([$id]);
?>
<link rel="stylesheet" href="../css/style.css">
<?php include $_SERVER['DOCUMENT_ROOT']."/includes/navbar.php"; ?>
<div class="container">
<h2>🔔 Notificaciones</h2>
<?php foreach ($notificaciones as $n): ?>
<div class="card">
    <div class="card-header">Mensaje</div>
    <?= $n["mensaje"] ?><br>
    <small><?= $n["fecha"] ?></small>
</div>
<?php endforeach; ?>
</div>