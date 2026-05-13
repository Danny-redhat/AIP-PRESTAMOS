<?php session_start(); require "../config/conexion.php";
$id_usuario = $_SESSION["id_usuario"];
$stmt = $pdo->prepare("
    SELECT COUNT(*) FROM notificaciones
    WHERE usuario_id=? AND leido=0 AND mensaje LIKE 'Tu incidencia%' 
");
$stmt->execute([$id_usuario]);
echo json_encode(["nuevas" => $stmt->fetchColumn()]);