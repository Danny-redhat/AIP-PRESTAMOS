<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require __DIR__ . '/../mail/src/Exception.php';
require __DIR__ . '/../mail/src/PHPMailer.php';
require __DIR__ . '/../mail/src/SMTP.php';
function enviarCorreo($destino, $asunto, $mensaje, $nombreDestino = '') {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->SMTPDebug  = 0;                              // ← 0 en producción, 2 solo para depurar
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'dannyandreqp@gmail.com';
        $mail->Password   = 'qjkl xvvm ngmk wdgc';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->CharSet    = 'UTF-8';                        // ← evita caracteres raros en tildes/ñ
        $mail->setFrom('dannyandreqp@gmail.com', 'Sistema de Préstamos');
        $mail->addAddress($destino, $nombreDestino);        // ← ahora incluye el nombre del destinatario
        $mail->isHTML(true);
        $anio = date('Y');
        $mail->Subject = $asunto;
        $mail->Body    = "
        <div style='font-family:Arial,sans-serif; max-width:560px; margin:0 auto;
                    border:2px solid #0D2B55; border-radius:10px; overflow:hidden;'>
            <!-- HEADER -->
            <div style='background:#0D2B55; padding:20px 24px; text-align:center;'>
                <h2 style='color:#F5C518; margin:0; font-size:1.2rem; letter-spacing:1px;'>
                    🎓 Sistema de Préstamos
                </h2>
                <p style='color:rgba(255,255,255,0.7); margin:4px 0 0; font-size:0.85rem;'>
                    I.E. Modesto Basadre — Tacna
                </p>
            </div>
            <!-- BODY -->
            <div style='padding:24px; background:#ffffff; color:#1F2937;'>
                $mensaje
            </div>
            <!-- FOOTER -->
            <div style='background:#F4F6FA; padding:12px 24px; text-align:center;
                        border-top:1px solid #DDE3ED;'>
                <small style='color:#6B7280; font-size:0.8rem;'>
                    Este correo fue generado automáticamente · $anio
                </small>
            </div>
        </div>
        ";
        // Versión texto plano como respaldo                // ← buena práctica anti-spam
        $mail->AltBody = strip_tags(str_replace(['<br>', '<br/>'], "\n", $mensaje));
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Error PHPMailer: " . $mail->ErrorInfo); // ← guarda el error en log, no lo muestra al usuario
        return false;
    }
}
?>