<?php
session_start();
require "../config/conexion.php";

if (isset($_SESSION['rol'])) {
    header("Location: " . ($_SESSION['rol'] === 'administrador' ? '../admin/dashboard.php' : '../profesor/dashboard.php'));
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dni  = trim($_POST['dni'] ?? '');
    $pass = trim($_POST['password'] ?? '');

    if (empty($dni) || empty($pass)) {
        $error = "Completa todos los campos.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE dni = ? LIMIT 1");
        $stmt->execute([$dni]);
        $usuario = $stmt->fetch();

        if ($usuario && password_verify($pass, $usuario['contraseña'])) {
            $_SESSION['id_usuario']      = $usuario['id_usuario'];
            $_SESSION['rol']             = $usuario['rol'];
            $_SESSION['nombre_completo'] = $usuario['nombre_completo']; // ← unificado

            $pdo->prepare("INSERT INTO historial (accion, usuario_id, descripcion) VALUES ('login', ?, ?)")
                ->execute([$usuario['id_usuario'], "Ingreso al sistema: " . $usuario['nombre_completo']]);

            header("Location: " . ($usuario['rol'] === 'administrador' ? '../admin/dashboard.php' : '../profesor/dashboard.php'));
            exit;
        } else {
            $error = "DNI o contraseña incorrectos.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión — AIP Préstamos</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="login-body">
<div class="login-wrapper">
    <div class="login-card">
        <div class="login-logo">
            <img src="../logo.png" alt="Logo Modesto Basadre">
            <h1>AIP Préstamos</h1>
            <p>I.E. Modesto Basadre — Tacna</p>
        </div>

        <?php if ($error): ?>
            <div class="alerta alerta-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="dni">DNI</label>
                <input type="text" id="dni" name="dni" placeholder="Ingresa tu DNI"
                       value="<?= htmlspecialchars($_POST['dni'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Ingresar</button>
        </form>

        <p style="text-align:center; margin-top:1rem; font-size:0.85rem; color:#6B7280;">
            ¿No tienes cuenta? <a href="registro.php" style="color:#1A4A8A; font-weight:600;">Regístrate aquí</a>
        </p>
    </div>
</div>
</body>
</html>