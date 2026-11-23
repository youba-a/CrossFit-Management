<?php
header("Content-Type: application/json");
require_once "../../../config/db.php";

$input = json_decode(file_get_contents("php://input"), true);

$token = $input["token"] ?? null;
$password = $input["password"] ?? null;

if (!is_string($token) || !preg_match('/^[a-f0-9]{64}$/D', $token)
    || !is_string($password) || strlen($password) < 8 || strlen($password) > 72 || str_contains($password, "\0")) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Lien ou mot de passe invalide"]);
    exit;
}

$tokenHash = hash("sha256", $token);

$pwdHash = password_hash($password, PASSWORD_BCRYPT);

$upd = $db->prepare("
    UPDATE user
    SET password_user = ?, tokenInvitation_user = NULL 
    WHERE tokenInvitation_user = ?
      AND DATEDIFF(NOW(), dateInvitation_user) BETWEEN 0 AND 30
      AND COALESCE(anonymisation_user, 0) = 0
    LIMIT 1
");
$upd->bind_param("ss", $pwdHash, $tokenHash);
$upd->execute();

if ($upd->affected_rows !== 1) {
    http_response_code(404);
    exit(json_encode(["success" => false, "message" => "Lien d'inscription invalide ou expire"]));
}

echo json_encode([
    "success" => true,
    "redirect" => "/index.php?login=1"
]);
exit;
