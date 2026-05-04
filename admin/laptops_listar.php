<?php
session_start();
require "../config/conexion.php";

$laptops = $pdo->query("SELECT * FROM laptops ORDER BY id ASC")->fetchAll();
?>

<link rel="stylesheet" href="../css/style.css">

<div class="container">
<h2>Laptops Disponibles</h2>

<div class="laptop-grid">
<?php foreach ($laptops as $l): ?>
    <div class="laptop estado-<?= $l['estado'] ?>">
        <?= $l['codigo'] ?><br>
        <?= ucfirst($l['tipo']) ?><br>
        <?= ucfirst($l['estado']) ?>
    </div>
<?php endforeach; ?>
</div>
</div>