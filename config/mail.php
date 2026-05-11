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

        // contraseña de aplicación
        $mail->Password   = 'Ipay ohqr yycd hiyu';

        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom(
            'dannyandreqp@gmail.com',
            'Sistema de Prestamos'
        );

        $mail->addAddress($destino);

        $mail->isHTML(true);

        // HTML DEL CORREO
        $contenido = "
        <div style='font-family:Arial;padding:20px;border:2px solid #003366'>
            
            <h2 style='color:#003366'>
                Sistema de Prestamos
            </h2>

            $mensaje

            <hr>

            <small>
                Institucion Educativa
            </small>

        </div>
        ";

        $mail->Subject = $asunto;
        $mail->Body    = $contenido;

        $mail->send();

        return true;

    } catch (Exception $e) {

        echo $mail->ErrorInfo;

        return false;
    }
}
?>