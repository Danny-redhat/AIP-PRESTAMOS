<?php
session_start();

if (isset($_SESSION['id_usuario'])) {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/conexion.php';
    $pdo->prepare("INSERT INTO historial (accion, usuario_id, descripcion) VALUES ('logout', ?, ?)")
        ->execute([$_SESSION['id_usuario'], "Cierre de sesión: " . ($_SESSION['nombre_completo'] ?? '')]);
}

session_unset();
session_destroy();
header("Location: /auth/login.php");
exit;