<?php
session_start();
require "../config/conexion.php";

if ($_SESSION["rol"] !== "administrador") {
    header("Location: ../index.php");
    exit;
}

if ($_POST) {
    $codigo = $_POST['codigo'];
    $marca = $_POST['marca'];
    $modelo = $_POST['modelo'];
    $tipo = $_POST['tipo'];
    $estado = $_POST['estado'];

    $stmt = $pdo->prepare("INSERT INTO laptops (codigo, marca, modelo, tipo, estado) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$codigo, $marca, $modelo, $tipo, $estado]);

    header("Location: laptops.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Laptop</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

?php include $_SERVER['DOCUMENT_ROOT']."/includes/navbar.php"; ?>

<div class="contenedor">
    <h2>Agregar Laptop</h2>
    <form method="POST">
        <label>Código:</label><br>
        <input type="text" name="codigo" required><br>

        <label>Marca:</label><br>
        <input type="text" name="marca" required><br>

        <label>Modelo:</label><br>
        <input type="text" name="modelo"><br>

        <label>Tipo:</label><br>
        <select name="tipo">
            <option value="tipear">Tipear</option>
            <option value="proyectar">Proyectar</option>
            <option value="otros">Otros</option>
        </select><br>

        <label>Estado:</label><br>
        <select name="estado">
            <option value="disponible">Disponible</option>
            <option value="prestada">Prestada</option>
            <option value="reparacion">En reparación</option>
            <option value="baja">Baja</option>
        </select><br><br>

        <button type="submit" class="btn-success">Agregar</button>
    </form>
</div>

</body>
</html>
