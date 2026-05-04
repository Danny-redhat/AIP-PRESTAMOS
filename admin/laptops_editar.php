<?php
session_start();
require "../config/conexion.php";

if ($_SESSION["rol"] !== "administrador") {
    header("Location: ../index.php");
    exit;
}

$id = $_GET['id'];
$laptop = $pdo->prepare("SELECT * FROM laptops WHERE id=?");
$laptop->execute([$id]);
$l = $laptop->fetch();

if ($_POST) {
    $codigo = $_POST['codigo'];
    $marca = $_POST['marca'];
    $modelo = $_POST['modelo'];
    $tipo = $_POST['tipo'];
    $estado = $_POST['estado'];

    $stmt = $pdo->prepare("UPDATE laptops SET codigo=?, marca=?, modelo=?, tipo=?, estado=? WHERE id=?");
    $stmt->execute([$codigo, $marca, $modelo, $tipo, $estado, $id]);

    header("Location: laptops.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Laptop</title>
    <link rel="stylesheet" href="../css/style.css">
    <?php include $_SERVER['DOCUMENT_ROOT']."/includes/navbar.php"; ?>
</head>
<body>
<div class="contenedor">
    <h2>Editar Laptop</h2>
    <form method="POST">
        <label>Código:</label><br>
        <input type="text" name="codigo" value="<?= htmlspecialchars($l['codigo']) ?>" required><br>

        <label>Marca:</label><br>
        <input type="text" name="marca" value="<?= htmlspecialchars($l['marca']) ?>" required><br>

        <label>Modelo:</label><br>
        <input type="text" name="modelo" value="<?= htmlspecialchars($l['modelo']) ?>"><br>

        <label>Tipo:</label><br>
        <select name="tipo">
            <option value="tipear" <?= $l['tipo']=='tipear'?'selected':'' ?>>Tipear</option>
            <option value="proyectar" <?= $l['tipo']=='proyectar'?'selected':'' ?>>Proyectar</option>
            <option value="otros" <?= $l['tipo']=='otros'?'selected':'' ?>>Otros</option>
        </select><br>

        <label>Estado:</label><br>
        <select name="estado">
            <option value="disponible" <?= $l['estado']=='disponible'?'selected':'' ?>>Disponible</option>
            <option value="prestada" <?= $l['estado']=='prestada'?'selected':'' ?>>Prestada</option>
            <option value="reparacion" <?= $l['estado']=='reparacion'?'selected':'' ?>>En reparación</option>
            <option value="baja" <?= $l['estado']=='baja'?'selected':'' ?>>Baja</option>
        </select><br><br>

        <button type="submit" class="btn-success">Actualizar</button>
    </form>
</div>

</body>
</html>
