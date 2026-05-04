<?php
session_start();
if (!isset($_SESSION["rol"]) || $_SESSION["rol"] !== "administrador") {
    header("Location: ../index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Administrador</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include $_SERVER['DOCUMENT_ROOT']."/includes/navbar.php"; ?>


<div class="contenedor">
    <h2>Bienvenido Administrador</h2>

    <ul class="menu-admin">
        <li><a href="usuarios.php">👨‍🏫 Profesores</a></li>
        <li><a href="laptops.php">💻 Laptops</a></li>
        <li><a href="solicitudes_listar.php">📄 Solicitudes</a></li>
        <li><a href="incidencias_listar.php">⚠️ Incidencias</a></li>
    </ul>
</div>

</body>
</html>
