<?php
session_start();
require "../config/conexion.php";

$id_usuario = $_SESSION['id_usuario']; // ID del profesor

// Obtener laptops disponibles
$laptops_tipear = $pdo->query("SELECT * FROM laptops WHERE tipo='tipear' AND estado='disponible'")->fetchAll();
$laptops_proyectar = $pdo->query("SELECT * FROM laptops WHERE tipo='proyectar' AND estado='disponible'")->fetchAll();

// Procesar envío de solicitud
if ($_POST) {
    $tipo_solicitud = $_POST['tipo_solicitud'];
    $id_laptop = $_POST['id_laptop'] ?? NULL;
    $mensaje = $_POST['mensaje'] ?? '';

    $stmt = $pdo->prepare("INSERT INTO solicitudes (id_usuario, id_laptop, tipo_solicitud, mensaje) VALUES (?, ?, ?, ?)");
    $stmt->execute([$id_usuario, $id_laptop, $tipo_solicitud, $mensaje]);

    header("Location: solicitud_listar.php");
    exit;
}
?>
<link rel="stylesheet" href="../css/style.css">
<?php include $_SERVER['DOCUMENT_ROOT']."/includes/navbar.php"; ?>
<h2>Solicitar Laptop</h2>
<form method="POST">
    <label>Tipo de Laptop:</label>
    <select name="tipo_solicitud" id="tipo_solicitud" onchange="mostrarLaptops()">
        <option value="tipear">Tipear</option>
        <option value="proyectar">Proyectar</option>
        <option value="otros">Otros</option>
    </select><br><br>

    <div id="laptops_tipear_div">
        <label>Seleccione Laptop disponible:</label>
        <select name="id_laptop">
            <?php foreach ($laptops_tipear as $l): ?>
                <option value="<?= $l['id'] ?>"><?= $l['codigo'] ?> (<?= $l['tipo'] ?>)</option>
            <?php endforeach; ?>
        </select>
    </div>

    <div id="laptops_proyectar_div" style="display:none;">
        <label>Seleccione Laptop disponible:</label>
        <select name="id_laptop">
            <?php foreach ($laptops_proyectar as $l): ?>
                <option value="<?= $l['id'] ?>"><?= $l['codigo'] ?> (<?= $l['tipo'] ?>)</option>
            <?php endforeach; ?>
        </select>
    </div>

    <div id="otros_div" style="display:none;">
        <label>Especifique:</label>
        <input type="text" name="mensaje">
    </div>

    <button type="submit">Enviar Solicitud</button>
</form>

<script>
function mostrarLaptops() {
    let tipo = document.getElementById('tipo_solicitud').value;
    document.getElementById('laptops_tipear_div').style.display = (tipo=='tipear') ? 'block':'none';
    document.getElementById('laptops_proyectar_div').style.display = (tipo=='proyectar') ? 'block':'none';
    document.getElementById('otros_div').style.display = (tipo=='otros') ? 'block':'none';
}
</script>
