<?php
session_start();
require "../config/conexion.php";
require "../config/mail.php";

if ($_SESSION["rol"] !== "administrador") {
    header("Location: ../index.php");
    exit;
}

$solicitudes = $pdo->query("
    SELECT s.*, u.nombre_completo, l.codigo 
    FROM solicitudes s
    JOIN usuarios u ON s.id_usuario=u.id_usuario
    LEFT JOIN laptops l ON s.id_laptop=l.id
    WHERE s.estado='pendiente'
")->fetchAll();

// Procesar aceptar/rechazar
if($_POST){
    $id = $_POST['id_solicitud'];
    $accion = $_POST['accion']; // aceptar o rechazar

    if($accion=='aceptar'){
        // Generar código
        $codigo = substr(md5(uniqid()),0,8);
        $pdo->prepare("UPDATE solicitudes SET estado='aceptada', codigo_confirmacion=? WHERE id_solicitud=?")->execute([$codigo, $id]);
    } else {
        $pdo->prepare("UPDATE solicitudes SET estado='rechazada' WHERE id_solicitud=?")->execute([$id]);
    }

    header("Location: solicitudes_listar.php");
    exit;
}
?>
<link rel="stylesheet" href="../css/style.css">
<?php include $_SERVER['DOCUMENT_ROOT']."/includes/navbar.php"; ?>

<h2>Solicitudes Pendientes</h2>
<table border="1" cellpadding="5">
<tr>
    <th>Profesor</th>
    <th>Laptop</th>
    <th>Tipo</th>
    <th>Mensaje</th>
    <th>Acción</th>
</tr>
<?php foreach($solicitudes as $s): ?>
<tr>
    <td><?= $s['nombre_completo'] ?></td>
    <td><?= $s['codigo'] ?? '-' ?></td>
    <td><?= $s['tipo_solicitud'] ?></td>
    <td><?= $s['mensaje'] ?></td>
    <td>
        <form method="POST">
            <input type="hidden" name="id_solicitud" value="<?= $s['id_solicitud'] ?>">
            <button name="accion" value="aceptar">Aceptar</button>
            <button name="accion" value="rechazar">Rechazar</button>
        </form>
    </td>
</tr>
<?php endforeach; ?>
</table>