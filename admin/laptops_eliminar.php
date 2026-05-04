<?php
session_start();
require "../config/conexion.php";

if($_SESSION['rol'] !== 'administrador'){
    header("Location: ../index.php");
    exit;
}

$id = $_GET['id'];
$pdo->prepare("DELETE FROM laptops WHERE id=?")->execute([$id]);
header("Location: laptops.php");
exit;
