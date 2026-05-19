<?php
session_start();
require "../config/conexion.php";
require "../config/mail.php";

if ($_SESSION['rol'] !== 'administrador') {
    header("Location: ../index.php");
    exit;
}

$mensaje = '';
$tipo    = '';
$prestamo_activo = null;

// ── BUSCAR SOLICITUD POR CÓDIGO ──────────────────────────
if (isset($_POST['buscar'])) {
    $codigo = strtoupper(trim($_POST['codigo']));
    $stmt = $pdo->prepare("
        SELECT s.*, u.nombre_completo, u.correo, u.id_usuario,
               l.id AS laptop_id, l.codigo AS laptop_codigo,
               l.marca, l.modelo
        FROM solicitudes s
        JOIN usuarios u ON s.id_usuario = u.id_usuario
        LEFT JOIN laptops l ON s.id_laptop = l.id
        WHERE s.codigo_confirmacion = ?
          AND s.estado = 'aceptada'
        LIMIT 1
    ");
    $stmt->execute([$codigo]);
    $prestamo_activo = $stmt->fetch();

    if (!$prestamo_activo) {
        $mensaje = "Código inválido o ya fue procesado.";
        $tipo    = "error";
    }
}

// ── CONFIRMAR ENTREGA ────────────────────────────────────
if (isset($_POST['confirmar_entrega'])) {
    $id_solicitud = (int)$_POST['id_solicitud'];
    $laptop_id    = (int)$_POST['laptop_id'];
    $id_usuario   = (int)$_POST['id_usuario'];
    $codigo       = $_POST['codigo_confirmacion'];
    $nombre       = $_POST['nombre_profesor'];
    $correo       = $_POST['correo_profesor'];

    // Registrar en tabla prestamos
    $pdo->prepare("
        INSERT INTO prestamos
            (solicitud_id, laptop_id, codigo_confirmacion, estado)
        VALUES (?, ?, ?, 'prestado')
    ")->execute([$id_solicitud, $laptop_id, $codigo]);

    // Marcar solicitud como entregada (usamos 'aceptada' ya está, solo actualizamos laptop estado)
    $pdo->prepare("UPDATE laptops SET estado='prestada' WHERE id=?")
        ->execute([$laptop_id]);

    // Notificación al profesor
    $pdo->prepare("INSERT INTO notificaciones (usuario_id, mensaje) VALUES (?, ?)")
        ->execute([$id_usuario, "Tu laptop fue entregada correctamente. Recuerda devolverla al terminar."]);

    // Historial
    $pdo->prepare("INSERT INTO historial (accion, usuario_id, descripcion) VALUES ('entrega_laptop', ?, ?)")
        ->execute([$_SESSION['id_usuario'], "Laptop entregada al profesor {$nombre}. Código: {$codigo}"]);

    $mensaje = "✅ Laptop entregada correctamente al profesor {$nombre}.";
    $tipo    = "exito";
}

// ── CONFIRMAR DEVOLUCIÓN ─────────────────────────────────
if (isset($_POST['confirmar_devolucion'])) {
    $codigo = strtoupper(trim($_POST['codigo_dev']));

    $stmt = $pdo->prepare("
        SELECT p.*, u.nombre_completo, u.correo, u.id_usuario,
               l.codigo AS laptop_codigo
        FROM prestamos p
        JOIN solicitudes s  ON p.solicitud_id = s.id_solicitud
        JOIN usuarios u     ON s.id_usuario   = u.id_usuario
        JOIN laptops l      ON p.laptop_id    = l.id
        WHERE p.codigo_confirmacion = ?
          AND p.estado = 'prestado'
        LIMIT 1
    ");
    $stmt->execute([$codigo]);
    $dev = $stmt->fetch();

    if ($dev) {
        // Cerrar préstamo
        $pdo->prepare("
            UPDATE prestamos
            SET estado='devuelto', fecha_devolucion=NOW(),
                responsable_devolucion=?
            WHERE id_prestamo=?
        ")->execute([$dev['nombre_completo'], $dev['id_prestamo']]);

        // Liberar laptop
        $pdo->prepare("UPDATE laptops SET estado='disponible' WHERE id=?")
            ->execute([$dev['laptop_id']]);

        // Notificación al profesor
        $pdo->prepare("INSERT INTO notificaciones (usuario_id, mensaje) VALUES (?, ?)")
            ->execute([$dev['id_usuario'], "Devolución registrada correctamente. ¡Gracias!"]);

        // Historial
        $pdo->prepare("INSERT INTO historial (accion, usuario_id, descripcion) VALUES ('devolucion_laptop', ?, ?)")
            ->execute([$_SESSION['id_usuario'], "Laptop {$dev['laptop_codigo']} devuelta por {$dev['nombre_completo']}."]);

        $mensaje = "✅ Devolución registrada. Laptop {$dev['laptop_codigo']} disponible nuevamente.";
        $tipo    = "exito";
    } else {
        $mensaje = "Código inválido o la laptop ya fue devuelta.";
        $tipo    = "error";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Entrega y Devolución — AIP Préstamos</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include $_SERVER['DOCUMENT_ROOT'] . "/includes/navbar.php"; ?>

<div class="contenedor">
    <div class="page-header">
        <h2>Entrega y devolución de laptops</h2>
        <p>Usa el código de confirmación para registrar la entrega o devolución del equipo</p>
    </div>

    <?php if ($mensaje): ?>
        <div class="alerta alerta-<?= $tipo === 'exito' ? 'exito' : 'error' ?>">
            <?= htmlspecialchars($mensaje) ?>
        </div>
    <?php endif; ?>

    <div style="display:grid; grid-template-columns:1fr 1fr; gap:1.5rem; margin-top:1rem;">

        <!-- PANEL ENTREGA -->
        <div class="card">
            <div class="card-header">
                <span class="card-title">📦 Registrar entrega</span>
            </div>

            <form method="POST">
                <div class="form-group">
                    <label>Código de confirmación del profesor</label>
                    <input type="text" name="codigo"
                           value="<?= htmlspecialchars($_POST['codigo'] ?? '') ?>"
                           placeholder="Ej: AB3X7K"
                           maxlength="6"
                           style="text-transform:uppercase; letter-spacing:4px; font-size:1.2rem; font-weight:700; text-align:center;"
                           required>
                </div>
                <button type="submit" name="buscar" class="btn btn-primary btn-block">
                    Verificar código
                </button>
            </form>

            <?php if ($prestamo_activo): ?>
                <div style="margin-top:1.2rem;">
                    <div class="codigo-box">
                        <p>Solicitud verificada</p>
                        <div class="codigo-valor"><?= $prestamo_activo['codigo_confirmacion'] ?></div>
                    </div>
                    <table style="margin-top:.8rem;">
                        <tr><td style="color:var(--gris-texto); font-size:.85rem;">Profesor</td>
                            <td><strong><?= htmlspecialchars($prestamo_activo['nombre_completo']) ?></strong></td></tr>
                        <tr><td style="color:var(--gris-texto); font-size:.85rem;">Laptop</td>
                            <td><?= htmlspecialchars($prestamo_activo['laptop_codigo'] ?? 'Por asignar') ?></td></tr>
                        <tr><td style="color:var(--gris-texto); font-size:.85rem;">Tipo</td>
                            <td><?= ucfirst($prestamo_activo['tipo_solicitud']) ?></td></tr>
                        <tr><td style="color:var(--gris-texto); font-size:.85rem;">Fecha</td>
                            <td><?= $prestamo_activo['fecha_solicitud'] ?></td></tr>
                    </table>

                    <form method="POST" style="margin-top:1rem;">
                        <input type="hidden" name="id_solicitud"     value="<?= $prestamo_activo['id_solicitud'] ?>">
                        <input type="hidden" name="laptop_id"        value="<?= $prestamo_activo['laptop_id'] ?>">
                        <input type="hidden" name="id_usuario"       value="<?= $prestamo_activo['id_usuario'] ?>">
                        <input type="hidden" name="codigo_confirmacion" value="<?= $prestamo_activo['codigo_confirmacion'] ?>">
                        <input type="hidden" name="nombre_profesor"  value="<?= htmlspecialchars($prestamo_activo['nombre_completo']) ?>">
                        <input type="hidden" name="correo_profesor"  value="<?= htmlspecialchars($prestamo_activo['correo']) ?>">
                        <button type="submit" name="confirmar_entrega" class="btn btn-success btn-block">
                            ✅ Confirmar entrega del equipo
                        </button>
                    </form>
                </div>
            <?php endif; ?>
        </div>

        <!-- PANEL DEVOLUCIÓN -->
        <div class="card">
            <div class="card-header">
                <span class="card-title">🔄 Registrar devolución</span>
            </div>

            <form method="POST">
                <div class="form-group">
                    <label>Código de confirmación del préstamo</label>
                    <input type="text" name="codigo_dev"
                           placeholder="Ej: AB3X7K"
                           maxlength="6"
                           style="text-transform:uppercase; letter-spacing:4px; font-size:1.2rem; font-weight:700; text-align:center;"
                           required>
                </div>
                <p style="font-size:0.83rem; color:var(--gris-texto); margin-bottom:1rem;">
                    El profesor presenta el mismo código que recibió al momento del préstamo.
                </p>
                <button type="submit" name="confirmar_devolucion" class="btn btn-warning btn-block">
                    🔄 Registrar devolución
                </button>
            </form>
        </div>

    </div>
</div>

<?php include __DIR__ . "/../includes/footer.php"; ?>
</body>
</html>