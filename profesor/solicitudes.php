<?php
session_start();
require "../config/conexion.php";

if ($_SESSION['rol'] !== 'profesor') {
    header("Location: ../index.php");
    exit;
}

$id_usuario = $_SESSION['id_usuario'];

// Obtener laptops disponibles
$laptops = $pdo->query("SELECT * FROM laptops WHERE estado='disponible'")->fetchAll();

if($_POST){
    $tipo = $_POST['tipo_solicitud'];
    $mensaje = $_POST['mensaje'] ?? '';

    $id_laptop = $_POST['id_laptop'] ?? null;

    $stmt = $pdo->prepare("INSERT INTO solicitudes (id_usuario, id_laptop, tipo_solicitud, mensaje) VALUES (?,?,?,?)");
    $stmt->execute([$id_usuario, $id_laptop, $tipo, $mensaje]);

    header("Location: solicitudes_listar.php");
    exit;
}
?>
<?php include $_SERVER['DOCUMENT_ROOT']."/includes/navbar.php"; ?>
<h2>Solicitar Laptop</h2>
<form method="POST">
    <label>Tipo de solicitud:</label><br>
    <select name="tipo_solicitud" required>
        <option value="tipear">Tipear</option>
        <option value="proyectar">Proyectar</option>
        <option value="otros">Otros</option>
    </select><br>

    <label>Laptop disponible (opcional):</label><br>
    <select name="id_laptop">
        <option value="">-- Elegir --</option>
        <?php foreach($laptops as $l): ?>
            <option value="<?= $l['id'] ?>"><?= $l['codigo'] ?> (<?= $l['tipo'] ?>)</option>
        <?php endforeach; ?>
    </select><br>

    <label>Mensaje / Observaciones:</label><br>
    <textarea name="mensaje"></textarea><br><br>

    <button type="submit">Solicitar</button>
</form>