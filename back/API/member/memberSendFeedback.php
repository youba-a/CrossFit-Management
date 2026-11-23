<?php
header('Content-Type: application/json; charset=utf-8');
include "../../check_auth.php"; // 

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

$sqlCheck = "
    SELECT 1 
    FROM organisationuser
    WHERE id_organisation = ? AND id_user = ?
";

$stmtCheck = $db->prepare($sqlCheck);
$stmtCheck->bind_param("ii", $idOrganisation, $authUserId);
$stmtCheck->execute();
$result = $stmtCheck->get_result();

if ($result->num_rows === 0) {
    http_response_code(403);
    echo json_encode(["error" => "Vous n'êtes pas inscrit à cette organisation."]);
    exit;
}
$stmtCheck->close();

// Mettre à jour le feedback
$sqlUpdate = "
    UPDATE organisationuser
    SET feedbackUserOnOrganisation_organisationuser = ?
    WHERE id_organisation = ? AND id_user = ?
";
$stmtUpdate = $db->prepare($sqlUpdate);
$stmtUpdate->bind_param("sii", $feedback, $idOrganisation, $authUserId);

if ($stmtUpdate->execute()) {
    echo json_encode([
        "success" => true,
        "message" => "Merci pour votre feedback !"
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        "error" => "Erreur lors de l'enregistrement du feedback."
    ]);
}

$stmtUpdate->close();
$db->close();
?>
