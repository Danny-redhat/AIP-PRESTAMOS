<?php
session_start();
require "../config/conexion.php";
if ($_SESSION["rol"] !== "profesor") {
    header("Location: ../index.php");
    exit;
}// Obtener laptops disponibles del profesor
$id_usuario = $_SESSION["id_usuario"];
$laptops = $pdo->prepare("
    SELECT p.id_laptop, l.codigo 
    FROM prestamos p
    JOIN laptops l ON p.laptop_id = l.id
    WHERE p.solicitud_id IN (
        SELECT id_solicitud FROM solicitudes WHERE id_usuario = ?
    )
    AND p.estado = 'prestado'
");
$laptops->execute([$id_usuario]);
?>
<link rel="stylesheet" href="../css/style.css">
<?php include $_SERVER['DOCUMENT_ROOT']."/includes/navbar.php"; ?>
<div class="container">
<h2>🛠️ Reportar Incidencia</h2>
<form method="POST" action="incidencias_guardar.php">
    <label>Laptop</label>
    <select name="laptop_id" required>
        <?php foreach ($laptops as $l): ?>
            <option value="<?= $l['id_laptop'] ?>"><?= $l['codigo'] ?></option>
        <?php endforeach; ?>
    </select>
    <label>Tipo</label>
    <select name="tipo">
        <option value="hardware">Hardware</option>
        <option value="software">Software</option>
        <option value="otros">Otros</option>
    </select>
    <label>Descripción</label>
    <textarea name="descripcion" required></textarea>
    <button type="submit">Reportar</button>
</form>
</div>