<?php
// -------------------------------------------------------------
// API : demande de réinitialisation de mot de passe
// -------------------------------------------------------------

date_default_timezone_set('Europe/Brussels');

header('Content-Type: application/json; charset=utf-8');
require "../../../config/db.php";
require_once __DIR__ . '/../../smtp.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Envoi du mail de réinitialisation
 */
function sendResetPasswordEmail($userEmail, $token)
{
    $userEmail = filter_var(trim($userEmail), FILTER_SANITIZE_EMAIL);

    if (!filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
        return ["success" => false, "error" => "Adresse email invalide."];
    }

    $resetLink = APP_BASE_URL . '/index.php?p=memberResetPassword&token=' . $token;

    $body = "
        <h2>Réinitialisation de votre mot de passe</h2>
        <p>Une demande de réinitialisation a été effectuée pour votre compte.</p>
        <p>Si vous êtes à l'origine de cette demande, cliquez sur le lien ci-dessous pour définir un nouveau mot de passe :</p>
        <p><a href='{$resetLink}'>{$resetLink}</a></p>
        <p>Si vous n'avez pas demandé cette réinitialisation, ignorez simplement ce mail.</p>
    ";

    $mail = new PHPMailer(true);
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    try {
        configureSmtp($mail);

        $mail->addAddress($userEmail);

        $mail->isHTML(true);
        $mail->Subject = "[CrossFit Mind Steel] Réinitialisation de votre mot de passe";
        $mail->Body    = $body;

        $mail->send();
        return ["success" => true];

    } catch (Exception $e) {
        error_log("Erreur d'envoi email: " . $mail->ErrorInfo);
        return ["success" => false, "error" => "Impossible d'envoyer le mail de réinitialisation."];
    }
}

// -------------------------------------------------------------
// Lecture et validation du JSON reçu
// -------------------------------------------------------------
$input = json_decode(file_get_contents('php://input'), true);

$email = is_string($input["email"] ?? null) ? trim($input["email"]) : '';

if (strlen($email) > 60 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["success" => false, "message" => "Email manquant"]);
    exit;
}

// -------------------------------------------------------------
// Vérification de l'existence de l'utilisateur
// -------------------------------------------------------------
$stmt = $db->prepare("SELECT id_user FROM user WHERE emailAddress_user = ? AND password_user IS NOT NULL AND COALESCE(anonymisation_user, 0) = 0 LIMIT 1");
$stmt->bind_param("s", $email);
$stmt->execute();
$res = $stmt->get_result();
$user = $res->fetch_assoc();
$stmt->close();

$msg = "Si un compte existe, un lien de réinitialisation a été envoyé.";

// -------------------------------------------------------------
// Si l'utilisateur existe, générer le token et envoyer l'email
// -------------------------------------------------------------
if ($user) {
    $token = bin2hex(random_bytes(32));
    $tokenHash = hash('sha256', $token);
    $expiration = date("Y-m-d H:i:s", strtotime("+1 hour"));

    $stmt = $db->prepare("UPDATE user SET resetPasswordToken=?, resetPasswordExpiration=? WHERE id_user=?");
    $stmt->bind_param("ssi", $tokenHash, $expiration, $user["id_user"]);
    $stmt->execute();
    $stmt->close();

    // Envoi de l'email
    $emailResponse = sendResetPasswordEmail($email, $token);
    if (!$emailResponse["success"]) {
        // Ne pas divulguer si l'email existe, log seulement
        error_log('CrossFit: envoi du lien de reinitialisation impossible.');
    }
}

// Toujours renvoyer le même message pour éviter de révéler l'existence de l'utilisateur
echo json_encode([
    "success" => true,
    "message" => $msg
]);
