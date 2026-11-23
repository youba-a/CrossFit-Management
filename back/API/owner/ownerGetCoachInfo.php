<?php
header('Content-Type: application/json');
include "../../check_auth.php";

if (!$authOwnerId) {
    http_response_code(401);
    echo json_encode(['error' => 'Non authentifié']);
    exit;
}

$idCoach = intval($_GET['id_coach'] ?? 0);
if (!$idCoach) {
    echo json_encode(['success' => false, 'message' => 'Aucun coach spécifié']);
    exit;
}

$query = "
    SELECT u.firstName_user, u.lastName_user, c.actualLevel_coach, c.specialisation_coach
    FROM coach c
    JOIN user u ON c.id_user = u.id_user
    WHERE c.id_coach = ? AND c.actif_coach = 1
";

$stmt = $db->prepare($query);
$stmt->bind_param("i", $idCoach);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();
$stmt->close();

if ($result) {
    echo json_encode(['success' => true, 'coach' => $result]);
} else {
    echo json_encode(['success' => false, 'message' => 'Coach introuvable']);
}
