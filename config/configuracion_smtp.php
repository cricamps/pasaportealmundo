<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';
require_once '../config/db.php';

function enviarFacturaPorCorreo($usuarioEmail, $nombreUsuario, $pdfContent, $numeroFactura)
{
    global $conn;

    $sql = "SELECT * FROM configuracion_smtp LIMIT 1";
    $result = mysqli_query($conn, $sql);

    if (!$result || mysqli_num_rows($result) == 0) {
        return "Error: No se encontró configuración SMTP.";
    }

    $smtp = mysqli_fetch_assoc($result);

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = $smtp['host'];
        $mail->Username = $smtp['usuario'];
        $mail->Password = $smtp['password'];
        $mail->SMTPSecure = $smtp['seguridad'];
        $mail->Port = $smtp['puerto'];


        $mail->setFrom($smtp['remitente_correo'], $smtp['remitente_nombre']);
        $mail->addAddress($usuarioEmail, $nombreUsuario);
        $mail->addStringAttachment($pdfContent, 'Factura_' . $numeroFactura . '_' . preg_replace('/[^A-Za-z0-9]/', '', $nombreUsuario) . '.pdf');

        $mail->isHTML(true);
        $mail->Subject = "Factura de tu reserva - $numeroFactura";
        $mail->Body = "<p>Estimado/a <strong>$nombreUsuario</strong>,<br>Adjunto encontrarás tu factura correspondiente a la reserva realizada.</p><p>Gracias por confiar en nosotros.</p>";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return "Error al enviar correo: {$mail->ErrorInfo}";
    }
}