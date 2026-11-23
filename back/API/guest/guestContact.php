<?php
// -------------------------------------------------------------
// API : envoyer un mail via le formulaire de contact
// -------------------------------------------------------------

header('Content-Type: application/json');

// Inclusion éventuelle d'une vérification d'authentification
// include "../../check_auth.php"; // <-- à activer si nécessaire

require_once __DIR__ . '/../../smtp.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendMailToAdmin($guestFirstName, $guestLastName, $guestEmail, $subject, $message)
{
    
    $guestFirstName = trim(htmlspecialchars($guestFirstName));
    $guestLastName  = trim(htmlspecialchars($guestLastName));
    $guestEmail     = filter_var(trim($guestEmail), FILTER_SANITIZE_EMAIL);
    $subject        = trim(htmlspecialchars($subject));
    $message        = trim(htmlspecialchars($message));

    
    if (empty($guestFirstName) || empty($guestLastName) || empty($guestEmail) || empty($subject) || empty($message)) 
    {
        return ["success" => false, "error" => "Tous les champs sont requis."];
    }

    if (!filter_var($guestEmail, FILTER_VALIDATE_EMAIL)) 
    {
        return ["success" => false, "error" => "L'adresse email est invalide."];
    }

    
    $adminEmail = APP_CONFIG['contact_recipient'];
    $adminName  = 'CrossFit Mind Steel Admin';

    $body = "
        <h2>Nouveau message depuis le site</h2>
        <p><strong>Nom :</strong> {$guestLastName}</p>
        <p><strong>Prénom :</strong> {$guestFirstName}</p>
        <p><strong>Email :</strong> {$guestEmail}</p>
        <p><strong>Sujet :</strong> {$subject}</p>
        <p><strong>Message :</strong><br>{$message}</p>
    ";

    $mail = new PHPMailer(true);
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';
    
    try {
        configureSmtp($mail);

        $mail->addReplyTo($guestEmail, "{$guestFirstName} {$guestLastName}");
        $mail->addAddress($adminEmail, $adminName);

        $mail->isHTML(true);
        $mail->Subject = "[Contact site] " . $subject;
        $mail->Body    = $body;

        $mail->send();

        return ["success" => true, "message" => "Votre message a bien été envoyé, merci de nous avoir contactés!"];

    } catch (Exception $e) 
    {
        error_log("Erreur d'envoi email: " . $mail->ErrorInfo);
        return ["success" => false, "error" => "Une erreur est apparue lors de l'envoi de votre message."];
    }

   
}

// -------------------------------------------------------------
// Lecture et validation du JSON reçu
// -------------------------------------------------------------
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid JSON received']);
    exit;
}

// Extraction et nettoyage des données
$guestFirstName = trim($input['guestFirstName'] ?? '');
$guestLastName  = trim($input['guestLastName'] ?? '');
$guestEmail     = trim($input['guestEmail'] ?? '');
$subject        = trim($input['subject'] ?? '');
$message        = trim($input['message'] ?? '');

// -------------------------------------------------------------
// Appel de la fonction d’envoi
// -------------------------------------------------------------
try {
    $response = sendMailToAdmin($guestFirstName, $guestLastName, $guestEmail, $subject, $message);

    if ($response['success']) {
        echo json_encode([
            'success' => true,
            'message' => $response['message']
        ]);
    } else {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => $response['error']
        ]);
    }

} catch (Exception $e) {
    error_log("Erreur API sendMail : " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erreur interne du serveur.'
    ]);
}