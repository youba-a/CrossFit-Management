<?php
header('Content-Type: application/json; charset=utf-8');
include "../../../config/db.php";

if (!isset($db) || $db->connect_errno) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Erreur de connexion a la base de donnees"]);
    exit;
}

$token = $_GET['token'] ?? null;

if (!is_string($token) || !preg_match('/^[a-f0-9]{64}$/D', $token)) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Token manquant"]);
    exit;
}

$stmt = $db->prepare("
    SELECT id_user
    FROM user
    WHERE resetPasswordToken = ?
      AND resetPasswordExpiration IS NOT NULL
      AND resetPasswordExpiration > NOW() AND COALESCE(anonymisation_user, 0) = 0
    LIMIT 1
");

$tokenHash = hash('sha256', $token);
$stmt->bind_param("s", $tokenHash);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$stmt->close();
$db->close();

if ($user) {
    echo json_encode(["success" => true, "message" => "Token valide"]);
} else {
    http_response_code(404);
    echo json_encode(["success" => false, "message" => "Token invalide ou expiré"]);
}
