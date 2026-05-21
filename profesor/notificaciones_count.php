<?php
session_start();
require "../config/conexion.php";

header('Content-Type: application/json');

if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(["nuevas" => 0]);
    exit;
}

$stmt = $pdo->prepare(
    "SELECT COUNT(*) FROM notificaciones
     WHERE usuario_id = ? AND leido = 0"
);
$stmt->execute([$_SESSION['id_usuario']]);
echo json_encode(["nuevas" => (int)$stmt->fetchColumn()]);