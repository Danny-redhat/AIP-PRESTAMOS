<?php
session_start();
require "../config/conexion.php";
if ($_SESSION["rol"] !== "administrador") {
    exit;
}
$id = $_POST["id_incidencia"];
$estado = $_POST["estado"];
$pdo->prepare("UPDATE incidencias SET estado=? WHERE id_incidencia=?")
    ->execute([$estado, $id]);
$stmt = $pdo->prepare("SELECT usuario_id FROM incidencias WHERE id_incidencia=?");
$stmt->execute([$id]);
$usuario_id = $stmt->fetchColumn();
$mensaje = "Tu incidencia fue actualizada a: $estado";
$pdo->prepare("INSERT INTO notificaciones (usuario_id, mensaje) VALUES (?, ?)")->execute([$usuario_id, $mensaje]);
header("Location: incidencias_listar.php");
?>