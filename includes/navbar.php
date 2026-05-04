<?php
if (!isset($_SESSION)) {
    session_start();
}
?>

<nav class="navbar">
    <div class="nav-left">
        <img src="/logo.png" class="logo">
        <span class="titulo">Sistema de Préstamos</span>
    </div>

    <ul class="nav-links">
        <?php if ($_SESSION['rol'] === 'administrador'): ?>
            <li><a href="/admin/dashboard.php">Inicio</a></li>
            <li><a href="/admin/usuarios.php">Profesores</a></li>
            <li><a href="/admin/laptops.php">Laptops</a></li>
            <li><a href="/admin/solicitudes_listar.php">Solicitudes</a></li>
            <li><a href="/admin/incidencias_listar.php">Incidencias</a></li>
            <li><a href="/admin/historial.php">Historial</a></li>
        <?php else: ?>
            <li><a href="/profesor/dashboard.php">Inicio</a></li>
            <li><a href="/profesor/solicitar.php">Solicitar</a></li>
            <li><a href="/profesor/solicitud_listar.php">Mis Solicitudes</a></li>
            <li><a href="/profesor/incidencias_listar.php">Incidencias</a></li>
            <li><a href="/profesor/notificaciones.php">Notificaciones</a></li>
        <?php endif; ?>
    </ul>

    <div class="nav-right">
        👤 <?= $_SESSION['nombre'] ?>
        <a href="/auth/logout.php" class="logout">Salir</a>
    </div>
</nav>