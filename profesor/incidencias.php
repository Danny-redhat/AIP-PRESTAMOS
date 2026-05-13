<?php session_start(); require "../config/conexion.php";
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'profesor') {
    header("Location: ../index.php");
    exit;
}
$id_usuario = $_SESSION['id_usuario'];
/* ===============================
   LAPTOPS PRESTADAS AL PROFESOR
================================ */
$stmt = $pdo->prepare("
    SELECT DISTINCT p.laptop_id, l.codigo 
    FROM prestamos p 
    INNER JOIN laptops l ON p.laptop_id = l.id
    INNER JOIN solicitudes s ON p.solicitud_id = s.id_solicitud
    WHERE p.estado = 'prestado'
    AND s.id_usuario = ?
");
$stmt->execute([$id_usuario]);
$laptops = $stmt->fetchAll();
/* ===============================
   GUARDAR INCIDENCIA
================================ */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['laptop_id']) || empty($_POST['descripcion'])) {
        $error = "Todos los campos son obligatorios.";
    } else {
        $laptop_id  = $_POST['laptop_id'];
        $descripcion = trim($_POST['descripcion']);
        $tipo = $_POST['tipo'];
        $stmt = $pdo->prepare("
            INSERT INTO incidencias (laptop_id, usuario_id, descripcion, tipo)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$laptop_id, $id_usuario, $descripcion, $tipo]);
        header("Location: incidencias_listar.php");
        exit;
    }
}
?>
<link rel="stylesheet" href="../css/style.css">
<?php include $_SERVER['DOCUMENT_ROOT']."/includes/navbar.php"; ?>
<div class="contenedor">
    <h2>Reportar Incidencia</h2>
    <?php if (empty($laptops)): ?>
        <p style="color:red;">
            No tienes laptops prestadas actualmente.
        </p>
    <?php else: ?>
        <?php if (isset($error)): ?>
            <p style="color:red;"><?= $error ?></p>
        <?php endif; ?>
        <form method="POST">
            <label>Laptop:</label><br>
            <select name="laptop_id" required>
                <?php foreach ($laptops as $l): ?>
                    <option value="<?= $l['laptop_id'] ?>">
                        <?= $l['codigo'] ?>
                    </option>
                <?php endforeach; ?>
            </select><br><br>
            <label>Tipo:</label><br>
            <select name="tipo">
                <option value="hardware">Hardware</option>
                <option value="software">Software</option>
                <option value="otros" selected>Otros</option>
            </select><br><br>
            <label>Descripción:</label><br>
            <textarea name="descripcion" required></textarea><br><br>
            <button type="submit">Reportar</button>
        </form>
    <?php endif; ?>
</div>