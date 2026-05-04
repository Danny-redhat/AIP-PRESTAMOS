<?php
session_start();
require "../config/conexion.php";
require "../config/mail.php";

if ($_SESSION["rol"] !== "profesor") {
    header("Location: ../index.php");
    exit;
}

$laptop_id = $_POST["laptop_id"];
$tipo = $_POST["tipo"];
$descripcion = $_POST["descripcion"];
$usuario_id = $_SESSION["id_usuario"];

// Guardar incidencia
$stmt = $pdo->prepare("
    INSERT INTO incidencias (laptop_id, usuario_id, tipo, descripcion)
    VALUES (?, ?, ?, ?)
");
$stmt->execute([$laptop_id, $usuario_id, $tipo, $descripcion]);

// Crear notificación al administrador
$admin = $pdo->query("SELECT id_usuario, correo FROM usuarios WHERE rol='administrador'")->fetch();
$mensaje = "Nueva incidencia reportada por el profesor ID $usuario_id.";
$pdo->prepare("INSERT INTO notificaciones (usuario_id, mensaje) VALUES (?, ?)")->execute([$admin['id_usuario'], $mensaje]);

// Opcional: enviar correo al admin
enviarCorreo($admin['correo'], "Nueva Incidencia", "
<p>Se ha reportado una incidencia por el profesor ID $usuario_id.</p>
<p>Tipo: $tipo</p>
<p>Descripción: $descripcion</p>
");

header("Location: incidencias_listar.php");
exit;