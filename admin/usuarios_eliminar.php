<?php session_start(); require "../config/conexion.php";
if($_SESSION['rol'] !== 'administrador'){
    header("Location: ../index.php");
    exit;
}
$id = $_GET['id'];
$pdo->prepare("DELETE FROM usuarios WHERE id_usuario=?")->execute([$id]);
header("Location: usuarios.php");
exit;