<?php
session_start();
require "../config/conexion.php";

if ($_SESSION['rol'] !== 'profesor') {
    header("Location: ../index.php");
    exit;
}

$id_usuario = $_SESSION['id_usuario'];
$error  = '';
$exito  = '';

// Verificar si ya tiene solicitud pendiente
$stmtPend = $pdo->prepare(
    "SELECT COUNT(*) FROM solicitudes WHERE id_usuario=? AND estado='pendiente'"
);
$stmtPend->execute([$id_usuario]);
$tienePendiente = (int)$stmtPend->fetchColumn() > 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$tienePendiente) {
    $tipo     = $_POST['tipo_solicitud'];
    $mensaje  = trim($_POST['mensaje'] ?? '');
    $id_laptop = !empty($_POST['id_laptop']) ? $_POST['id_laptop'] : null;

    $stmt = $pdo->prepare(
        "INSERT INTO solicitudes (id_usuario, id_laptop, tipo_solicitud, mensaje)
         VALUES (?, ?, ?, ?)"
    );
    $stmt->execute([$id_usuario, $id_laptop, $tipo, $mensaje]);

    // Notificar al administrador
    $admin = $pdo->query("SELECT id_usuario FROM usuarios WHERE rol='administrador' LIMIT 1")->fetch();
    if ($admin) {
        $pdo->prepare("INSERT INTO notificaciones (usuario_id, mensaje) VALUES (?, ?)")
            ->execute([$admin['id_usuario'], "Nueva solicitud de " . $_SESSION['nombre_completo']]);
    }

    header("Location: solicitud_listar.php?exito=1");
    exit;
}

// Laptops disponibles según tipo
$laptops_tipear    = $pdo->query("SELECT * FROM laptops WHERE tipo='tipear'    AND estado='disponible'")->fetchAll();
$laptops_proyectar = $pdo->query("SELECT * FROM laptops WHERE tipo='proyectar' AND estado='disponible'")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Solicitar Laptop — AIP Préstamos</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include $_SERVER['DOCUMENT_ROOT'] . "/includes/navbar.php"; ?>

<div class="contenedor">
    <div class="page-header">
        <h2>Solicitar laptop</h2>
        <p>Completa el formulario para enviar tu solicitud al administrador</p>
    </div>

    <?php if ($tienePendiente): ?>
        <div class="alerta alerta-warning">
            Ya tienes una solicitud <strong>pendiente</strong>. Espera a que sea procesada antes de enviar otra.
            <a href="solicitud_listar.php" style="margin-left:8px; color:#92400E; font-weight:600;">Ver mis solicitudes →</a>
        </div>
    <?php else: ?>
    <div class="card" style="max-width:560px;">
        <form method="POST" action="">
            <div class="form-group">
                <label for="tipo_solicitud">Tipo de solicitud</label>
                <select name="tipo_solicitud" id="tipo_solicitud" onchange="mostrarLaptops()" required>
                    <option value="tipear">Tipear</option>
                    <option value="proyectar">Proyectar</option>
                    <option value="otros">Otros</option>
                </select>
            </div>

            <div class="form-group" id="div_tipear">
                <label>Laptop disponible (tipear)</label>
                <select name="id_laptop" id="sel_tipear">
                    <option value="">-- Sin preferencia --</option>
                    <?php foreach ($laptops_tipear as $l): ?>
                        <option value="<?= $l['id'] ?>"><?= htmlspecialchars($l['codigo']) ?> — <?= htmlspecialchars($l['marca']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group" id="div_proyectar" style="display:none;">
                <label>Laptop disponible (proyectar)</label>
                <select name="id_laptop" id="sel_proyectar">
                    <option value="">-- Sin preferencia --</option>
                    <?php foreach ($laptops_proyectar as $l): ?>
                        <option value="<?= $l['id'] ?>"><?= htmlspecialchars($l['codigo']) ?> — <?= htmlspecialchars($l['marca']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="mensaje">Mensaje u observaciones</label>
                <textarea name="mensaje" id="mensaje" placeholder="Ej: La necesito para la clase de 2do A a las 10am..."></textarea>
            </div>

            <div style="display:flex; gap:10px;">
                <button type="submit" class="btn btn-primary">Enviar solicitud</button>
                <a href="solicitud_listar.php" class="btn btn-outline-azul">Cancelar</a>
            </div>
        </form>
    </div>
    <?php endif; ?>
</div>

<script>
function mostrarLaptops() {
    const tipo = document.getElementById('tipo_solicitud').value;
    document.getElementById('div_tipear').style.display    = tipo === 'tipear'    ? 'block' : 'none';
    document.getElementById('div_proyectar').style.display = tipo === 'proyectar' ? 'block' : 'none';
}
</script>
<?php include __DIR__ . "/../includes/footer.php"; ?>
</body>
</html>