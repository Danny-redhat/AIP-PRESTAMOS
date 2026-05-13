<?php
session_start();
require "../config/conexion.php";
require "../config/mail.php";

if ($_SESSION['rol'] !== 'administrador') {
    header("Location: ../index.php");
    exit;
}

$id     = $_POST['id_solicitud'] ?? 0;
$accion = $_POST['accion'] ?? '';

// Obtener datos completos de la solicitud
$stmt = $pdo->prepare("
    SELECT s.*, u.correo, u.nombre_completo, u.id_usuario,
           l.id AS laptop_id, l.codigo AS laptop_codigo
    FROM solicitudes s
    JOIN usuarios u ON s.id_usuario = u.id_usuario
    LEFT JOIN laptops l ON s.id_laptop = l.id
    WHERE s.id_solicitud = ?
");
$stmt->execute([$id]);
$sol = $stmt->fetch();

if (!$sol) {
    header("Location: solicitudes_listar.php");
    exit;
}

if ($accion === 'rechazar') {
    $pdo->prepare("UPDATE solicitudes SET estado='rechazada' WHERE id_solicitud=?")
        ->execute([$id]);

    // Notificación interna
    $pdo->prepare("INSERT INTO notificaciones (usuario_id, mensaje) VALUES (?, ?)")
        ->execute([$sol['id_usuario'], "Tu solicitud fue RECHAZADA por el administrador."]);

    // Correo al profesor
    enviarCorreo(
        $sol['correo'],
        "Solicitud de laptop rechazada",
        "<p>Estimado/a <strong>{$sol['nombre_completo']}</strong>,</p>
         <p>Tu solicitud de préstamo de laptop ha sido <strong style='color:#DC3545'>rechazada</strong>.</p>
         <p>Si tienes dudas, acércate a la AIP.</p>",
        $sol['nombre_completo']
    );

    // Historial
    $pdo->prepare("INSERT INTO historial (accion, usuario_id, descripcion) VALUES ('rechazar_solicitud', ?, ?)")
        ->execute([$_SESSION['id_usuario'], "Solicitud #{$id} rechazada — Profesor: {$sol['nombre_completo']}"]);

    header("Location: solicitudes_listar.php?msg=rechazada");
    exit;
}

if ($accion === 'aceptar') {
    // Generar código legible (6 caracteres alfanuméricos en mayúsculas)
    $codigo = strtoupper(substr(str_shuffle('ABCDEFGHJKLMNPQRSTUVWXYZ23456789'), 0, 6));

    $pdo->prepare("UPDATE solicitudes SET estado='aceptada', codigo_confirmacion=? WHERE id_solicitud=?")
        ->execute([$codigo, $id]);

    // Si hay laptop asignada, marcarla como prestada
    if ($sol['laptop_id']) {
        $pdo->prepare("UPDATE laptops SET estado='prestada' WHERE id=?")
            ->execute([$sol['laptop_id']]);
    }

    // Notificación interna
    $pdo->prepare("INSERT INTO notificaciones (usuario_id, mensaje) VALUES (?, ?)")
        ->execute([
            $sol['id_usuario'],
            "Tu solicitud fue ACEPTADA. Código de retiro: {$codigo}. Preséntate a la AIP."
        ]);

    // Correo al profesor con el código
    $laptop_txt = $sol['laptop_codigo'] ? "Laptop asignada: <strong>{$sol['laptop_codigo']}</strong>" : "Laptop por asignar en la AIP";
    enviarCorreo(
        $sol['correo'],
        "✅ Solicitud aceptada — Código de retiro: {$codigo}",
        "<p>Estimado/a <strong>{$sol['nombre_completo']}</strong>,</p>
         <p>Tu solicitud de préstamo ha sido <strong style='color:#28A745'>aceptada</strong>.</p>
         <br>
         <div style='background:#EEF2FF; border:2px dashed #1A4A8A; border-radius:10px; padding:20px; text-align:center;'>
             <p style='margin:0; font-size:13px; color:#6B7280;'>Tu código de retiro es:</p>
             <p style='font-size:2.4rem; font-weight:900; color:#003366; letter-spacing:8px; font-family:monospace; margin:8px 0;'>{$codigo}</p>
             <p style='margin:0; font-size:12px; color:#6B7280;'>Preséntate a la AIP y muestra este código para retirar el equipo.</p>
         </div>
         <br>
         <p>{$laptop_txt}</p>
         <p><small>Una vez entregado el equipo, guarda este código. Lo necesitarás también para la devolución.</small></p>",
        $sol['nombre_completo']
    );

    // Historial
    $pdo->prepare("INSERT INTO historial (accion, usuario_id, descripcion) VALUES ('aceptar_solicitud', ?, ?)")
        ->execute([$_SESSION['id_usuario'], "Solicitud #{$id} aceptada — Código: {$codigo} — Profesor: {$sol['nombre_completo']}"]);

    header("Location: solicitudes_listar.php?msg=aceptada");
    exit;
}

header("Location: solicitudes_listar.php");
exit;
?>