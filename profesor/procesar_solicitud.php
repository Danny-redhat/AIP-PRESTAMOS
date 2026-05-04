<?php
session_start();
require "../config/conexion.php";

$id_usuario = $_SESSION["id_usuario"];
$tipo = $_POST["tipo"];
$laptop_id = $_POST["laptop_id"] ?: null;
$mensaje = $_POST["mensaje"];

$sql = "INSERT INTO solicitudes 
        (id_usuario, id_laptop, tipo_solicitud, mensaje)
        VALUES (?, ?, ?, ?)";

$stmt = $pdo->prepare($sql);
$stmt->execute([$id_usuario, $laptop_id, $tipo, $mensaje]);

header("Location: dashboard.php");