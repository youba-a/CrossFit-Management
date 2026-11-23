<?php
require_once __DIR__ . '/../../smtp.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendInvitationEmail($to, $name, $link)
{
    $mail = new PHPMailer(true);
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    try {
        configureSmtp($mail);

        $mail->addAddress($to, $name);

        $mail->isHTML(true);
        $mail->Subject = 'Invitation à rejoindre CrossFit Mind Steel';
        $name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
        $link = htmlspecialchars($link, ENT_QUOTES, 'UTF-8');
        $mail->Body = "
        <html>
            <head>
                <meta charset=\"UTF-8\">
            </head>
            <p>Bonjour $name,</p>
            <p>Vous avez été invité à rejoindre notre plateforme CrossFit Mind Steel.</p>
            <p>Veuillez compléter votre inscription en cliquant sur le lien suivant :</p>
            <p><a href='$link'>$link</a></p>
            <p>Cordialement,<br>L'équipe CrossFit Mind Steel</p>
        </html>
        ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Erreur d'envoi email: " . $mail->ErrorInfo);
        return false;
    }
}
