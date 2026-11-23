<?php
header('Content-Type: application/json; charset=utf-8');

include "../../check_auth.php";

// Vérification de l'authentification
if (!$authCoachId) {
   http_response_code(401);
   echo json_encode(['error' => 'Non authentifié']);
   exit;
}

if (!isset($db) || $db->connect_errno) {
    http_response_code(500);
    echo json_encode(["error" => "Impossible de se connecter à la base de données."]);
    exit;
}

$idCoach = $authCoachId;

$requeteCoachID = $db->prepare("SELECT COUNT(*) AS nb FROM coach WHERE id_coach = ?");
$requeteCoachID->bind_param("i", $idCoach);
$requeteCoachID->execute();
$coachData = $requeteCoachID->get_result()->fetch_assoc();
$requeteCoachID->close();

if ($coachData['nb'] == 0) {
    http_response_code(403);
    echo json_encode(["error" => "User n'est pas un Coach. ID non valide"]);
    exit;
}

if ($idCoach <= 0) {
    http_response_code(400);
    echo json_encode(["error" => "ID négative, coach ID non valide"]);
    exit;
}



$sql = "
    SELECT 
        o.id_organisation,
        o.begin_organisation,
        o.end_organisation,
        ta.name_typeActivity,
        ta.description_typeActivity,
        ta.color_typeActivity,
        b.name_box,
        b.town_box,
        b.streetNameAddress_box AS street,
        b.numeroAddress_box AS numero,
        b.postalCode_box AS postal_code
    FROM organisation as o
    INNER JOIN coach as c ON o.id_coach = c.id_coach
    INNER JOIN activities as a ON o.id_activities = a.id_activities
    INNER JOIN typeactivities as ta ON a.id_typeActivities = ta.id_typeActivities
    INNER JOIN box as b ON o.id_box = b.id_box
    WHERE o.begin_organisation >= DATE_SUB(NOW(), INTERVAL 1 YEAR) AND c.id_coach = ?
    ORDER BY o.begin_organisation ASC;
";

if ($stmt = $db->prepare($sql)) {
    $stmt->bind_param("i", $idCoach);
    $stmt->execute();
    $result = $stmt->get_result();

    $organisations = [];
    while ($row = $result->fetch_assoc()) {
        $organisations[] = $row;
    }

    echo json_encode($organisations, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    $stmt->close();

} else {
    http_response_code(500);
    echo json_encode(["error" => "Impossible de se connecter à la base de données."]);
    exit;
}

$db->close();
?>
