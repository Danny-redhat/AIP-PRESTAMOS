<?php
session_start();
require "../config/conexion.php";

$dni = $_POST["dni"];
$password = $_POST["password"];

$sql = "SELECT * FROM usuarios WHERE dni = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$dni]);
$usuario = $stmt->fetch();

if ($usuario && password_verify($password, $usuario["contraseña"])) {

    $_SESSION["id_usuario"] = $usuario["id_usuario"];
    $_SESSION["rol"] = $usuario["rol"];
    $_SESSION["nombre"] = $usuario["nombre_completo"];

    if ($usuario["rol"] === "administrador") {
        header("Location: ../admin/dashboard.php");
    } else {
        header("Location: ../profesor/dashboard.php");
    }

} else {
    echo "DNI o contraseña incorrectos";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<div class="container">
    <h2>🔐 Sistema de Préstamos</h2>

    <?php if(!empty($error)): ?>
        <p style="color:red; text-align:center;"><b><?= $error ?></b></p>
    <?php endif; ?>

    <form method="POST" action="login.php">
        <label>DNI</label>
        <input type="text" name="dni" required>

        <label>Contraseña</label>
        <input type="password" name="password" required>

        <button type="submit">Ingresar</button>
    </form>

    <p style="text-align:center; margin-top:15px;">
        ¿No tienes cuenta?
        <a href="registro.php"><b>Registrarse</b></a>
    </p>
</div>

</body>
</html>