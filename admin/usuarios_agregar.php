<?php
session_start();
require "../config/conexion.php";

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: ../index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dni      = trim($_POST['dni']);
    $nombre   = trim($_POST['nombre_completo']);
    $correo   = trim($_POST['correo']);
    $telefono = trim($_POST['telefono']);
    $area     = trim($_POST['area']);
    $password = password_hash($_POST['contraseña'], PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("
        INSERT INTO usuarios 
        (dni, nombre_completo, correo, telefono, area, contraseña, rol) 
        VALUES (?, ?, ?, ?, ?, ?, 'profesor')
    ");
    $stmt->execute([$dni, $nombre, $correo, $telefono, $area, $password]);

    header("Location: usuarios.php");
    exit;
}
?>

<link rel="stylesheet" href="../css/style.css">
<?php include $_SERVER['DOCUMENT_ROOT']."/includes/navbar.php"; ?>

<div class="contenedor">
    <h2>Agregar Profesor</h2>

    <form method="POST">
        <label>DNI:</label><br>
        <input type="text" name="dni" required><br>

        <label>Nombre completo:</label><br>
        <input type="text" name="nombre_completo" required><br>

        <label>Correo:</label><br>
        <input type="email" name="correo" required><br>

        <label>Teléfono:</label><br>
        <input type="text" name="telefono"><br>

        <label>Área:</label><br>
        <input type="text" name="area"><br>

        <label>Contraseña:</label><br>
        <input type="password" name="contraseña" required><br><br>

        <button type="submit">Agregar</button>
    </form>
</div>

