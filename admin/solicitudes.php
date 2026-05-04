<?php
session_start();
require "../config/conexion.php";

if ($_SESSION["rol"] !== "administrador") {
    header("Location: ../index.php");
}

$solicitudes = $pdo->query("
    SELECT s.*, u.nombre_completo, u.correo, l.codigo AS laptop_codigo
    FROM solicitudes s
    JOIN usuarios u ON s.id_usuario = u.id_usuario
    LEFT JOIN laptops l ON s.id_laptop = l.id
    WHERE s.estado = 'pendiente'
    ORDER BY s.fecha_solicitud DESC
")->fetchAll();
?>
<link rel="stylesheet" href="../css/style.css">

<h2>Solicitudes Pendientes</h2>

<?php foreach ($solicitudes as $s): ?>
<div style="border:1px solid #ccc; padding:10px; margin:10px;">
    <p><b>Profesor:</b> <?= $s['nombre_completo'] ?></p>
    <p><b>Tipo:</b> <?= $s['tipo_solicitud'] ?></p>
    <p><b>Laptop:</b> <?= $s['laptop_codigo'] ?? 'No seleccionada' ?></p>
    <p><b>Mensaje:</b> <?= $s['mensaje'] ?></p>

    <form method="POST" action="procesar_solicitud.php">
        <input type="hidden" name="id_solicitud" value="<?= $s['id_solicitud'] ?>">
        <button name="accion" value="aceptar">Aceptar</button>
        <button name="accion" value="rechazar">Rechazar</button>
    </form>
</div>
<?php endforeach; ?>
