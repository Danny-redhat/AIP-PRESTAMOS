<?php session_start();
if (!isset($_SESSION["rol"]) || $_SESSION["rol"] !== "profesor") {
    header("Location: ../index.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Panel Profesor</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include $_SERVER['DOCUMENT_ROOT'] . "/includes/navbar.php"; ?>
    <div class="container">
        <h2>👨‍🏫 Bienvenido, <?= $_SESSION['nombre-completo'] ??$_SESSION['nombre'] ?? 'Profesor' ?></h2>
        <div class="dashboard-grid">
            <a href="solicitar.php"><button>💻 Solicitar Laptop</button></a>
            <a href="solicitud_listar.php"><button>📄 Mis Solicitudes</button></a>
            <a href="incidencias.php"><button>⚠️ Reportar Incidencia</button></a>
            <a href="notificaciones.php"><button class="btn-amarillo">🔔 Notificaciones</button></a>
        </div>
    </div>
</body>
</html>
<script src="/assets/notificacion.mp3"></script>