<?php
// -------------------------------------------------------------
// API : Déconnexion de l'utilisateur
// -------------------------------------------------------------
session_start();
include "../../../config/db.php";
header('Content-Type: application/json');

// Vérifie si un cookie auth_token existe
if (empty($_COOKIE['auth_token'])) {
    echo json_encode(['success' => true, 'message' => 'Déjà déconnecté']);
    exit;
}

$token = $_COOKIE['auth_token'];
$tokenHash = hash('sha256', $token);

try {
    // On met la date d'expiration à maintenant
    $stmt = $db->prepare("
        UPDATE session
        SET expiresAt_session = NOW()
        WHERE tokenHash_session = ?
    ");
    $stmt->bind_param("s", $tokenHash);
    $stmt->execute();
} catch (Exception $e) {
    error_log("Erreur logout.php : " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erreur serveur']);
    exit;
}

// Supprime le cookie côté client
setcookie(
    'auth_token',
    '',
    [
        'expires' => time() - 3600,
        'path' => '/',
        'secure' => parse_url(APP_BASE_URL, PHP_URL_SCHEME) === 'https',
        'httponly' => true,
        'samesite' => 'Strict'
    ]
);

// Réponse JSON
echo json_encode(['success' => true, 'message' => 'Session expirée et utilisateur déconnecté']);
