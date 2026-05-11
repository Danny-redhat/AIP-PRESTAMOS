<?php
session_start();
require "../config/conexion.php";
require "../config/mail.php";

if (!isset($_SESSION["rol"]) || $_SESSION["rol"] !== "administrador") {
    exit;
}

$id = $_POST["id_solicitud"];
$accion = $_POST["accion"];

// Obtener datos
$stmt = $pdo->prepare("
    SELECT s.*, u.correo, u.nombre_completo, l.id AS laptop_id, l.codigo
    FROM solicitudes s
    JOIN usuarios u ON s.id_usuario = u.id_usuario
    LEFT JOIN laptops l ON s.id_laptop = l.id
    WHERE s.id_solicitud = ?
");

$stmt->execute([$id]);
$sol = $stmt->fetch();


// =========================
// RECHAZAR
// =========================
if ($accion === "rechazar") {

    $pdo->prepare("
        UPDATE solicitudes 
        SET estado='rechazada' 
        WHERE id_solicitud=?
    ")->execute([$id]);

    enviarCorreo(
        $sol["correo"],
        "Solicitud rechazada",
        "Tu solicitud de préstamo fue rechazada."
    );

    // Notificación
    $pdo->prepare("
        INSERT INTO notificaciones (usuario_id, mensaje)
        VALUES (?, ?)
    ")->execute([
        $sol["id_usuario"],
        "Tu solicitud fue RECHAZADA."
    ]);

    header("Location: solicitudes.php");
    exit;
}


// =========================
// ACEPTAR
// =========================

$codigo = strtoupper(substr(md5(uniqid()), 0, 8));

$pdo->prepare("
    UPDATE solicitudes
    SET estado='aceptada', codigo_confirmacion=?
    WHERE id_solicitud=?
")->execute([$codigo, $id]);


// 📧 Correo
$mensaje = "
<h3>Solicitud Aceptada</h3>

<p>Profesor: {$sol['nombre_completo']}</p>

<p>Laptop: {$sol['codigo']}</p>

<p><b>Código de retiro:</b> $codigo</p>

<p>Presente este código para retirar el equipo.</p>
";

enviarCorreo(
    $sol["correo"],
    "Solicitud Aceptada",
    $mensaje
);


// 🔔 Notificación
$pdo->prepare("
    INSERT INTO notificaciones (usuario_id, mensaje)
    VALUES (?, ?)
")->execute([
    $sol["id_usuario"],
    "Tu solicitud fue ACEPTADA. Código: $codigo"
]);


header("Location: solicitudes.php");
exit;
?>