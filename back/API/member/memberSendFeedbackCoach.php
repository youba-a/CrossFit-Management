<?php
header('Content-Type: application/json; charset=utf-8');
include "../../check_auth.php";

if (!$authUserId) {
    http_response_code(401);
    echo json_encode(["error" => "Non authentifié"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

$idOrganisation = intval($data['id_organisation'] ?? 0);
$feedback = trim($data['feedback'] ?? '');

if (!$idOrganisation || empty($feedback)) {
    http_response_code(400);
    echo json_encode(["error" => "Organisation ou feedback manquant"]);
    exit;
}

// Vérifier que l’utilisateur est bien inscrit
$sqlCheck = "
    SELECT 1 
    FROM organisationuser
    WHERE id_organisation = ? AND id_user = ?
";

$stmt = $db->prepare($sqlCheck);
$stmt->bind_param("ii", $idOrganisation, $authUserId);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    http_response_code(403);
    echo json_encode(["error" => "Vous n'êtes pas inscrit à cette organisation."]);
    exit;
}
$stmt->close();

$sqlUpdate = "
    UPDATE organisationuser
    SET feedbackUserOnCoach_organisationuser = ?
    WHERE id_organisation = ? AND id_user = ?
";

$stmt2 = $db->prepare($sqlUpdate);
$stmt2->bind_param("sii", $feedback, $idOrganisation, $authUserId);

if ($stmt2->execute()) {
    echo json_encode(["success" => true, "message" => "Feedback sur le coach enregistré !"]);
} else {
    http_response_code(500);
    echo json_encode(["error" => "Erreur lors de l’enregistrement du feedback."]);
}

$stmt2->close();
$db->close();
?>
