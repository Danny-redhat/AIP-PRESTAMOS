<?php
session_start();
require "../config/conexion.php";

if($_SESSION['rol'] !== 'administrador'){
    header("Location: ../index.php");
    exit;
}

$id = $_GET['id'];
$laptop = $pdo->prepare("SELECT * FROM laptops WHERE id=?");
$laptop->execute([$id]);
$laptop = $laptop->fetch();

if($_POST){
    $marca = $_POST['marca'];
    $modelo = $_POST['modelo'];
    $tipo = $_POST['tipo'];
    $estado = $_POST['estado'];

    $stmt = $pdo->prepare("UPDATE laptops SET marca=?, modelo=?, tipo=?, estado=? WHERE id=?");
    $stmt->execute([$marca,$modelo,$tipo,$estado,$id]);

    header("Location: laptops.php");
    exit;
}
?>

<h2>Editar Laptop</h2>
<form method="POST">
    <label>Marca:</label><br>
    <input type="text" name="marca" value="<?= $laptop['marca'] ?>"><br>
    <label>Modelo:</label><br>
    <input type="text" name="modelo" value="<?= $laptop['modelo'] ?>"><br>
    <label>Tipo:</label><br>
    <select name="tipo">
        <option value="tipear" <?= $laptop['tipo']=='tipear'?'selected':'' ?>>Tipear</option>
        <option value="proyectar" <?= $laptop['tipo']=='proyectar'?'selected':'' ?>>Proyectar</option>
        <option value="otros" <?= $laptop['tipo']=='otros'?'selected':'' ?>>Otros</option>
    </select><br>
    <label>Estado:</label><br>
    <select name="estado">
        <option value="disponible" <?= $laptop['estado']=='disponible'?'selected':'' ?>>Disponible</option>
        <option value="prestada" <?= $laptop['estado']=='prestada'?'selected':'' ?>>Prestada</option>
        <option value="reparacion" <?= $laptop['estado']=='reparacion'?'selected':'' ?>>En reparación</option>
        <option value="baja" <?= $laptop['estado']=='baja'?'selected':'' ?>>Baja</option>
    </select><br><br>
    <button type="submit">Guardar</button>
</form>
