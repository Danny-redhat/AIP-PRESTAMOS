<?php if (!isset($_SESSION)) session_start(); ?>

<nav class="navbar">
    <div class="navbar-left">
        <img src="../logo.png" class="logo">
        <span class="titulo">Panel Administrador</span>
    </div>

    <ul class="navbar-links">
        <li><a href="dashboard.php">Inicio</a></li>
        <li><a href="usuarios.php">Profesores</a></li>
        <li><a href="laptops.php">Laptops</a></li>
        <li><a href="solicitudes_listar.php">Solicitudes</a></li>
        <li><a href="incidencias_listar.php">Incidencias</a></li>
        <li><a href="../auth/logout.php" class="logout">Salir</a></li>
    </ul>
</nav>
