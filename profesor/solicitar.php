<?php
session_start();
require "../config/conexion.php";

if ($_SESSION["rol"] !== "profesor") {
    header("Location: ../index.php");
}

$laptops = $pdo->query("
    SELECT * FROM laptops 
    WHERE estado = 'disponible'
")->fetchAll();
?>

<link rel="stylesheet" href="../css/style.css">
<?php include $_SERVER['DOCUMENT_ROOT']."/includes/navbar.php"; ?>
<div class="container">
<h2>💻 Solicitud de Laptop</h2>

<form method="POST" action="procesar_solicitud.php">

    <label>Tipo de solicitud</label>
    <select name="tipo" required>
        <option value="tipear">Para Tipear</option>
        <option value="proyectar">Para Proyectar</option>
        <option value="otros">Otros</option>
    </select>

    <label>Laptop disponible</label>
    <select name="laptop_id">
        <option value="">-- Seleccionar --</option>
        <?php foreach ($laptops as $l): ?>
            <option value="<?= $l['id'] ?>">
                <?= $l['codigo'] ?> - <?= $l['marca'] ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label>Mensaje</label>
    <textarea name="mensaje"></textarea>

    <button type="submit">Enviar Solicitud</button>
</form>
</div>