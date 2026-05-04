<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../mail/src/Exception.php';
require __DIR__ . '/../mail/src/PHPMailer.php';
require __DIR__ . '/../mail/src/SMTP.php';

function enviarCorreo($destino, $asunto, $mensaje) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'dannyandreqp@gmail.com';
        $mail->Password   = 'Ipay ohqr yycd hiyu';
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        $mail->setFrom('dannyandreqp@gmail.com', 'Sistema de Préstamos');
        $mail->addAddress($destino);

        $mail->isHTML(true);
        $mail->Subject = $asunto;
        $mail->Body    = $mensaje;
        $mensaje = "
<div style='font-family:Arial; border:2px solid #003366; padding:15px'>
    <h2 style='color:#003366'>Sistema de Préstamos</h2>
    <p>$mensaje</p>
    <hr>
    <small>Institución Educativa</small>
</div>
";

        $mail->send();
        return true;

    } catch (Exception $e) {
        return false;
    }
}