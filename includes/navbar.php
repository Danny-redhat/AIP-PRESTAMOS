<?php
if (!isset($_SESSION)) session_start();

// Protección: si no hay sesión activa, redirigir
if (!isset($_SESSION['rol'])) {
    header("Location: /auth/login.php");
    exit;
}

$rol    = $_SESSION['rol'];
$nombre = $_SESSION['nombre_completo']; // ← unificado

require_once $_SERVER['DOCUMENT_ROOT'] . '/config/conexion.php';

// Badge solicitudes pendientes (solo admin)
$badgeSolicitudes = 0;
if ($rol === 'administrador') {
    $badgeSolicitudes = (int)$pdo->query(
        "SELECT COUNT(*) FROM solicitudes WHERE estado='pendiente'"
    )->fetchColumn();
}

// Badge notificaciones no leídas
$badgeNotif = (int)$pdo->prepare(
    "SELECT COUNT(*) FROM notificaciones WHERE usuario_id=? AND leido=0"
)->execute([$_SESSION['id_usuario']]) 
    ? $pdo->prepare("SELECT COUNT(*) FROM notificaciones WHERE usuario_id=? AND leido=0")->execute([$_SESSION['id_usuario']]) 
    : 0;

// Forma más limpia:
$stmtN = $pdo->prepare("SELECT COUNT(*) FROM notificaciones WHERE usuario_id=? AND leido=0");
$stmtN->execute([$_SESSION['id_usuario']]);
$badgeNotif = (int)$stmtN->fetchColumn();
?>
<nav class="navbar">
    <div class="navbar-brand">
        <img src="/logo.png" alt="Logo" class="navbar-logo">
        <span>AIP Préstamos</span>
    </div>

    <div class="navbar-menu">
        <?php if ($rol === 'administrador'): ?>
            <a href="/admin/dashboard.php"          class="nav-link">Inicio</a>
            <a href="/admin/solicitudes_listar.php"  class="nav-link" style="position:relative;">
                Solicitudes
                <?php if ($badgeSolicitudes > 0): ?>
                    <span class="badge"><?= $badgeSolicitudes ?></span>
                <?php endif; ?>
            </a>
            <a href="/admin/laptops.php"             class="nav-link">Laptops</a>
            <a href="/admin/usuarios.php"            class="nav-link">Profesores</a>
            <a href="/admin/incidencias_listar.php"  class="nav-link">Incidencias</a>
            <a href="/admin/historial.php"           class="nav-link">Historial</a>
            <a href="/admin/confirmar_entrega.php" class="nav-link">Entregas</a>
        <?php else: ?>
            <a href="/profesor/dashboard.php"           class="nav-link">Inicio</a>
            <a href="/profesor/solicitudes.php"          class="nav-link">Solicitar Laptop</a>
            <a href="/profesor/solicitud_listar.php"     class="nav-link">Mis Solicitudes</a>
            <a href="/profesor/incidencias.php"          class="nav-link">Reportar Incidencia</a>
            <a href="/profesor/incidencias_listar.php"   class="nav-link">Mis Incidencias</a>
            <a href="/profesor/notificaciones.php"       class="nav-link" style="position:relative;">
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
        <a href="/auth/logout.php" class="btn btn-outline btn-sm">Salir</a>
    </div>
</nav>