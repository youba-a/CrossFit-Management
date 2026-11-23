<?php

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/phpmailer/src/PHPMailer.php';
require_once __DIR__ . '/phpmailer/src/SMTP.php';
require_once __DIR__ . '/phpmailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function configureSmtp(PHPMailer $mail): void
{
    if (!APP_CONFIG['smtp_host'] || !APP_CONFIG['smtp_from']) {
        throw new Exception('SMTP non configure.');
    }

    $mail->isSMTP();
    $mail->Host = APP_CONFIG['smtp_host'];
    $mail->Port = APP_CONFIG['smtp_port'];
    $mail->SMTPAuth = APP_CONFIG['smtp_user'] !== '';
    $mail->Username = APP_CONFIG['smtp_user'];
    $mail->Password = APP_CONFIG['smtp_password'];
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->setFrom(APP_CONFIG['smtp_from'], 'CrossFit Mind Steel');
}
