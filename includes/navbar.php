<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['rol'])) {
    // Detectar si estamos en subcarpeta o raíz
    $base = (strpos($_SERVER['PHP_SELF'], '/admin/') || strpos($_SERVER['PHP_SELF'], '/profesor/')) ? '../' : '';
    header("Location: {$base}auth/login.php");
    exit;
}

$rol    = $_SESSION['rol'];
$nombre = $_SESSION['nombre_completo'] ?? $_SESSION['nombre'] ?? 'Usuario';

// Detectar nivel de carpeta para rutas relativas
$enSubcarpeta = strpos($_SERVER['PHP_SELF'], '/admin/') !== false
             || strpos($_SERVER['PHP_SELF'], '/profesor/') !== false;
$base = $enSubcarpeta ? '../' : '';

require_once __DIR__ . '/../config/conexion.php';

// Badge solicitudes pendientes (solo admin)
$badgeSolicitudes = 0;
if ($rol === 'administrador') {
    $badgeSolicitudes = (int)$pdo->query(
        "SELECT COUNT(*) FROM solicitudes WHERE estado='pendiente'"
    )->fetchColumn();
}

// Badge notificaciones no leídas
$stmtN = $pdo->prepare("SELECT COUNT(*) FROM notificaciones WHERE usuario_id=? AND leido=0");
$stmtN->execute([$_SESSION['id_usuario']]);
$badgeNotif = (int)$stmtN->fetchColumn();
?>
<nav class="navbar">
    <div class="navbar-brand">
        <img src="<?= $base ?>logo.png" alt="Logo" class="navbar-logo">
        <span>AIP Préstamos</span>
    </div>

    <div class="navbar-menu">
        <?php if ($rol === 'administrador'): ?>
            <a href="<?= $base ?>admin/dashboard.php"          class="nav-link">Inicio</a>
            <a href="<?= $base ?>admin/solicitudes_listar.php"  class="nav-link" style="position:relative;">
                Solicitudes
                <?php if ($badgeSolicitudes > 0): ?>
                    <span class="badge"><?= $badgeSolicitudes ?></span>
                <?php endif; ?>
            </a>
            <a href="<?= $base ?>admin/confirmar_entrega.php"   class="nav-link">Entregas</a>
            <a href="<?= $base ?>admin/laptops.php"             class="nav-link">Laptops</a>
            <a href="<?= $base ?>admin/usuarios.php"            class="nav-link">Profesores</a>
            <a href="<?= $base ?>admin/incidencias_listar.php"  class="nav-link">Incidencias</a>
            <a href="<?= $base ?>admin/historial.php"           class="nav-link">Historial</a>
        <?php else: ?>
            <a href="<?= $base ?>profesor/dashboard.php"           class="nav-link">Inicio</a>
            <a href="<?= $base ?>profesor/solicitudes.php"          class="nav-link">Solicitar Laptop</a>
            <a href="<?= $base ?>profesor/solicitud_listar.php"     class="nav-link">Mis Solicitudes</a>
            <a href="<?= $base ?>profesor/incidencias.php"          class="nav-link">Reportar Incidencia</a>
            <a href="<?= $base ?>profesor/incidencias_listar.php"   class="nav-link">Mis Incidencias</a>
            <a href="<?= $base ?>profesor/notificaciones.php" class="nav-link" style="position:relative;">
                Notificaciones
                <?php if ($badgeNotif > 0): ?>
                    <span class="badge"><?= $badgeNotif ?></span>
                <?php endif; ?>
            </a>
        <?php endif; ?>
    </div>

    <div class="navbar-user">
        <span class="user-name">
            👤 <?= htmlspecialchars($nombre) ?>
            <small style="color:var(--amarillo); font-size:.75rem;">(<?= $rol ?>)</small>
        </span>
        <a href="<?= $base ?>auth/logout.php" class="btn btn-outline btn-sm">Salir</a>
    </div>
</nav>
echo '<main class="main-wrapper">';