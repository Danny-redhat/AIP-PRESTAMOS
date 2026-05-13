<?php require "../config/conexion.php";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $dni      = $_POST["dni"];
    $nombre   = $_POST["nombre"];
    $area     = $_POST["area"];
    $telefono = $_POST["telefono"];
    $correo   = $_POST["correo"];
    $pass1    = $_POST["password"];
    $pass2    = $_POST["confirmar"];
    if ($pass1 !== $pass2) {
        die("Las contraseñas no coinciden");
    }
    $password = password_hash($pass1, PASSWORD_BCRYPT);
    $sql = "INSERT INTO usuarios 
            (dni, nombre_completo, area, telefono, correo, contraseña, rol)
            VALUES (?, ?, ?, ?, ?, ?, 'profesor')";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $dni, $nombre, $area, $telefono, $correo, $password
    ]);
    header("Location: ../index.php");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro Profesor</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="container">
    <h2>📘 Registro de Profesor</h2>
    <?php if(!empty($error)): ?>
        <p style="color:red; text-align:center;"><b><?= $error ?></b></p>
    <?php endif; ?>
    <form method="POST">
        <label>DNI</label>
        <input type="text" name="dni" required>
        <label>Nombre Completo</label>
        <input type="text" name="nombre" required>
        <label>Área</label>
        <input type="text" name="area">
        <label>Teléfono</label>
        <input type="text" name="telefono">
        <label>Correo</label>
        <input type="email" name="correo" required>
        <label>Contraseña</label>
        <input type="password" name="password" required>
        <label>Confirmar Contraseña</label>
        <input type="password" name="confirmar" required>
        <button type="submit">Registrarse</button>
    </form>
    <p style="text-align:center; margin-top:15px;">
        <a href="../index.php">⬅ Volver al login</a>
    </p>
</div>
</body>
</html>