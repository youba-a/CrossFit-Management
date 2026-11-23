<?php
session_start();
include "../../../config/db.php";

header('Content-Type: application/json');

// Récupération du login/password envoyés en JSON
$data = json_decode(file_get_contents("php://input"), true);
$login = $data['login'] ?? '';
$password = $data['password'] ?? '';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !is_string($login) || !is_string($password)
    || strlen($login) > 60 || strlen($password) > 4096) {
    http_response_code(400);
    exit(json_encode(['success' => false, 'message' => 'Requete invalide']));
}

// Vérification utilisateur
$stmt = $db->prepare("SELECT id_user, password_user FROM user WHERE emailAddress_user = ? AND COALESCE(anonymisation_user, 0) = 0");
$stmt->bind_param("s", $login);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user || !is_string($user['password_user']) || !password_verify($password, $user['password_user'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Identifiants incorrects']);
    exit;
}

// Création d'un token aléatoire
$token = bin2hex(random_bytes(32));
$tokenHash = hash('sha256', $token);

// Récupération info client (facultatif mais utile)
$ip = $_SERVER['REMOTE_ADDR'] ?? null;
$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;

// Préparation de l'INSERT
$stmt = $db->prepare("
    INSERT INTO session (
        tokenHash_session,
        idUser_session,
        ipAddress_session,
        userAgent_session,
        createdAt_session,
        expiresAt_session
    ) VALUES (?, ?, ?, ?, NOW(), DATE_ADD(NOW(), INTERVAL 1 HOUR))
");

$stmt->bind_param("siss", $tokenHash, $user['id_user'], $ip, $userAgent);

// Exécution
if (!$stmt->execute()) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Impossible de créer la session']);
    exit;
}

// Cookie HttpOnly pour navigateur web
setcookie(
    'auth_token',
    $token,
    [
        'expires' => time() + 3600,
        'path' => '/',
        'secure' => parse_url(APP_BASE_URL, PHP_URL_SCHEME) === 'https',
        'httponly' => true,
        'samesite' => 'Strict'
    ]
);

// Retour JSON pour clients externes
echo json_encode([
    'success' => true,
    'token' => $token
]);
