<?php
session_start();
require "../config/conexion.php";

if($_SESSION['rol'] !== 'administrador'){
    header("Location: ../index.php");
    exit;
}

if($_POST){
    $codigo = $_POST['codigo'];
    $marca = $_POST['marca'];
    $modelo = $_POST['modelo'];
    $tipo = $_POST['tipo'];
    $estado = $_POST['estado'];

    $stmt = $pdo->prepare("INSERT INTO laptops (codigo, marca, modelo, tipo, estado) VALUES (?,?,?,?,?)");
    $stmt->execute([$codigo,$marca,$modelo,$tipo,$estado]);

    header("Location: laptops.php");
    exit;
}
?>

<h2>Agregar Laptop</h2>
<form method="POST">
    <label>Código:</label><br>
    <input type="text" name="codigo" required><br>
    <label>Marca:</label><br>
    <input type="text" name="marca"><br>
    <label>Modelo:</label><br>
    <input type="text" name="modelo"><br>
    <label>Tipo:</label><br>
    <select name="tipo" required>
        <option value="tipear">Tipear</option>
        <option value="proyectar">Proyectar</option>
        <option value="otros">Otros</option>
    </select><br>
    <label>Estado:</label><br>
    <select name="estado" required>
        <option value="disponible">Disponible</option>
        <option value="prestada">Prestada</option>
        <option value="reparacion">En reparación</option>
        <option value="baja">Baja</option>
    </select><br><br>
    <button type="submit">Agregar</button>
</form>
