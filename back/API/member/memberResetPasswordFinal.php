<?php
// -------------------------------------------------------
// choisir nouveau mdp apres reset
// -------------------------------------------------------

header('Content-Type: application/json; charset=utf-8');
include "../../../config/db.php";

if (!isset($db) || $db->connect_errno) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Erreur de connexion a la base de donnees"]);
    exit;
}

$input = [];
$contentType = $_SERVER['CONTENT_TYPE'] ?? '';
if (stripos($contentType, 'application/json') !== false) {
    $input = json_decode(file_get_contents("php://input"), true) ?? [];
} else {
    $input = $_POST;
}

$token = $input['token'] ?? null;
$password = $input['password_user'] ?? ($input['newPassword'] ?? null);

if (!is_string($token) || !preg_match('/^[a-f0-9]{64}$/D', $token)
    || !is_string($password) || strlen($password) < 8 || strlen($password) > 72 || str_contains($password, "\0")) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Token ou mot de passe manquant"]);
    $db->close();
    exit;
}

$tokenHash = hash('sha256', $token);
$db->begin_transaction();
try {

    $check = $db->prepare("
        SELECT id_user
        FROM user
        WHERE resetPasswordToken = ?
          AND resetPasswordExpiration IS NOT NULL
          AND resetPasswordExpiration > NOW() AND COALESCE(anonymisation_user, 0) = 0
        LIMIT 1
        FOR UPDATE
    ");
    if (!$check) {
        throw new Exception("Prepare SELECT failed: " . $db->error);
    }

    $check->bind_param("s", $tokenHash);
    $check->execute();
    $result = $check->get_result();
    $user = $result->fetch_assoc();
    $check->close();

    if (!$user) {
        http_response_code(404);
        echo json_encode(["success" => false, "message" => "Token invalide ou expire"]);
        $db->rollback();
        $db->close();
        exit;
    }

    $userId = (int)$user['id_user'];

    $passwordHash = password_hash($password, PASSWORD_BCRYPT);

    $stmt = $db->prepare("
        UPDATE user
        SET password_user = ?,
            resetPasswordToken = NULL,
            resetPasswordExpiration = NULL
        WHERE id_user = ?
          AND resetPasswordToken = ?
        LIMIT 1
    ");
    if (!$stmt) {
        throw new Exception("Prepare UPDATE failed: " . $db->error);
    }

    $stmt->bind_param("sis", $passwordHash, $userId, $tokenHash);
    $stmt->execute();

    if ($stmt->affected_rows === 0) {
        http_response_code(404);
        echo json_encode(["success" => false, "message" => "Token invalide ou expire"]);
        $stmt->close();
        $db->rollback();
        $db->close();
        exit;
    }
    $stmt->close();

    $exp = $db->prepare("
        UPDATE session
        SET expiresAt_session = NOW()
        WHERE idUser_session = ?
    ");
    if (!$exp) {
        throw new Exception("Prepare UPDATE session failed: " . $db->error);
    }
    $exp->bind_param("i", $userId);
    $exp->execute();
    $exp->close();

    $db->commit();
    echo json_encode([
        "success" => true,
        "message" => "Votre mot de passe a été réinitialisé avec succès. Vous pouvez maintenant vous reconnecter."
    ]);
    $db->close();
    exit;

} catch (Exception $e) {
    $db->rollback();

    error_log("ResetPassword error: " . $e->getMessage());

    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Une erreur serveur est survenue. Veuillez reessayer plus tard."
    ]);
    $db->close();
    exit;
}
?>