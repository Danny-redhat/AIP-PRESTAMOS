<?php
session_start();
require "config/conexion.php";

if ($_POST) {
    $dni = $_POST['dni'];
    $password = $_POST['contraseña'];

    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE dni=?");
    $stmt->execute([$dni]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['contraseña'])) {
        $_SESSION['id_usuario'] = $user['id_usuario'];
        $_SESSION['rol'] = $user['rol'];
        $_SESSION['nombre'] = $user['nombre_completo'];

        if ($user['rol'] === 'administrador') {
            header("Location: admin/dashboard.php");
        } else {
            header("Location: profesor/dashboard.php");
        }
        exit;
    } else {
        $error = "DNI o contraseña incorrectos.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login | Sistema de Préstamos</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="container" style="max-width:420px; text-align:center;">

    <!-- LOGO -->
    <img src="logo.png" alt="Logo Colegio" style="width:120px; margin-bottom:15px;">

    <h2>Ingreso al Sistema</h2>

    <?php if(isset($error)): ?>
        <p style="color:red;"><b><?= $error ?></b></p>
    <?php endif; ?>

    <form method="POST">
        <label>DNI</label>
        <input type="text" name="dni" required>

        <label>Contraseña</label>
        <input type="password" name="contraseña" required>

        <button type="submit">Ingresar</button>
    </form>

    <p style="margin-top:15px;">
        ¿No tienes cuenta?<br>
        <a href="auth/registro.php"><b>Registrarse aquí</b></a>
    </p>

</div>

</body>
</html>