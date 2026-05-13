<?php
session_start();
require "../config/conexion.php";
if($_SESSION['rol'] !== 'administrador'){
    header("Location: ../index.php");
    exit;
}
$id = $_GET['id'] ?? 0;
$user = $pdo->prepare("SELECT * FROM usuarios WHERE id_usuario=?");
$user->execute([$id]);
$user = $user->fetch();
if(!$user){
    header("Location: usuarios.php");
    exit;
}
if($_POST){
    $nombre = $_POST['nombre_completo'];
    $correo = $_POST['correo'];
    $telefono = $_POST['telefono'];
    $area = $_POST['area'];
    $stmt = $pdo->prepare("
        UPDATE usuarios 
        SET nombre_completo=?, correo=?, telefono=?, area=? 
        WHERE id_usuario=?
    ");
    $stmt->execute([$nombre, $correo, $telefono, $area, $id]);
    header("Location: usuarios.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Profesor</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include $_SERVER['DOCUMENT_ROOT']."/includes/navbar.php"; ?>
<div class="contenedor">
    <h2>Editar Profesor</h2>
    <form method="POST">
        <label>Nombre completo:</label><br>
        <input type="text" name="nombre_completo" value="<?= htmlspecialchars($user['nombre_completo']) ?>" required><br>
        <label>Correo:</label><br>
        <input type="email" name="correo" value="<?= htmlspecialchars($user['correo']) ?>" required><br>
        <label>Teléfono:</label><br>
        <input type="text" name="telefono" value="<?= htmlspecialchars($user['telefono']) ?>"><br>
        <label>Área:</label><br>
        <input type="text" name="area" value="<?= htmlspecialchars($user['area']) ?>"><br><br>
        <button type="submit" class="btn-success">💾 Guardar cambios</button>
        <a href="usuarios.php"><button type="button">Cancelar</button></a>
    </form>
</div>
</body>
</html>