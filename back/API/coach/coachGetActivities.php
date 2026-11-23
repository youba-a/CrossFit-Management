<?php
header('Content-Type: application/json; charset=utf-8');

include "../../check_auth.php";

if (!$authCoachId) {
   http_response_code(401);
   echo json_encode(['error' => 'Non authentifié']);
   exit;
}

if (!isset($db) || $db->connect_errno) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Impossible de se connecter à la base de données."
    ]);
    exit;
}

// --- Get activities ---
$sqlActivities = "
    SELECT 
        a.id_activities, 
        ta.id_typeActivities, 
        ta.name_typeActivity, 
        ta.description_typeActivity, 
        ta.color_typeActivity 
    FROM activities AS a
    INNER JOIN typeactivities AS ta
        ON a.id_typeActivities = ta.id_typeActivities
";

$activityTypes = [];

if ($stmt = $db->prepare($sqlActivities)) {
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $activityTypes[] = $row;
    }
    $stmt->close();
} else {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Erreur lors du chargement des activités."
    ]);
    exit;
}

// --- Get boxes ---
$sqlBoxes = "
    SELECT 
        b.id_box, 
        CONCAT(b.name_box, ', ', b.streetNameAddress_box) AS box_label
    FROM box b
";

$boxes = [];

if ($stmt = $db->prepare($sqlBoxes)) {
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $boxes[] = $row;
    }
    $stmt->close();
} else {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Erreur lors du chargement des box."
    ]);
    exit;
}

$db->close();

// --- Final response ---
echo json_encode([
    "success" => true,
    "activityTypes" => $activityTypes,
    "boxes" => $boxes
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
?>