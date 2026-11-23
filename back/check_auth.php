<?php


$token = $_COOKIE['auth_token'] ?? null;
$headers = array_change_key_case(getallheaders() ?: [], CASE_LOWER);

if (!$token && isset($headers['authorization'])) {
    if (preg_match('/Bearer\s(\S+)/', $headers['authorization'], $matches)) {
        $token = $matches[1];
    }
}

$authUserId = $authCoachId = $authOwnerId = null;

if (is_string($token) && preg_match('/^[a-f0-9]{64}$/D', $token)) {
    require __DIR__ . '/../config/db.php';
    $tokenHash = hash('sha256', $token);
    $stmt = $db->prepare("SELECT idUser_session, 
    `owner`.id_owner AS owner_id, 
    coach.id_coach AS coach_id
    FROM `session` JOIN user ON user.id_user = session.idUser_session AND COALESCE(user.anonymisation_user, 0) = 0
    LEFT JOIN `owner` ON `session`.idUser_session = `owner`.id_user 
    LEFT JOIN coach ON coach.id_user = session.idUser_session AND coach.actif_coach = 1
    WHERE tokenHash_session=? AND expiresAt_session>NOW()");
    $stmt->bind_param("s", $tokenHash);
    $stmt->execute();
    $res = $stmt->get_result();
    $user = $res->fetch_assoc();

    if ($user)
    {
        $authUserId = $user['idUser_session'] ?? null;
        $authCoachId = $user['coach_id'] ?? null;
        $authOwnerId = $user['owner_id'] ?? null;
    }
        
        
}

// $authUserId est null si non connecté
// $authCoachId est null si ce n'est pas un coach
// $authOwnerId est null si ce n'est pas un owner
?>