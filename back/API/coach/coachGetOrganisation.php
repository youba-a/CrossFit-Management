<?php
header('Content-Type: application/json; charset=utf-8');

include "../../check_auth.php"; // Définit $db (mysqli), $authCoachId, $authOwnerId

// Vérification d'authentification
if (!$authCoachId && !$authOwnerId) {
    http_response_code(401);
    echo json_encode(["error" => "Non authentifié"]);
    exit;
}

// Récupération de l'id de l'organisation
$input = json_decode(file_get_contents("php://input"), true);
$idOrganisation = $_GET['id_organisation'] ?? ($input['id_organisation'] ?? null);

if (!$idOrganisation || !is_numeric($idOrganisation)) {
    http_response_code(400);
    echo json_encode(["error" => "ID d'organisation manquant ou invalide"]);
    exit;
}

// Vérifie que l’organisation existe et appartient au coach si ce n’est pas un admin
if ($authCoachId && !$authOwnerId) {
    $sqlCheck = "SELECT id_organisation FROM organisation WHERE id_organisation = ? AND id_coach = ?";
    $stmtCheck = $db->prepare($sqlCheck);
    $stmtCheck->bind_param("ii", $idOrganisation, $authCoachId);
} else {
    // Si admin, il peut accéder à n'importe quelle organisation
    $sqlCheck = "SELECT id_organisation FROM organisation WHERE id_organisation = ?";
    $stmtCheck = $db->prepare($sqlCheck);
    $stmtCheck->bind_param("i", $idOrganisation);
}

$stmtCheck->execute();
$resCheck = $stmtCheck->get_result();

if ($resCheck->num_rows === 0) {
    http_response_code(403);
    echo json_encode(["error" => "Organisation non trouvée ou non autorisée"]);
    exit;
}
$stmtCheck->close();

// Récupération des infos de l’organisation
$sqlOrg = "
    SELECT 
        o.id_organisation,
        o.begin_organisation,
        o.end_organisation,
        o.feedbackCoachOnOrganisation_organisation,
        c.id_coach,
        u.firstName_user AS coach_firstName,
        u.lastName_user AS coach_lastName,
        u.emailAddress_user AS coach_email,
        a.id_activities,
        ta.name_typeActivity,
        ta.description_typeActivity,
        ta.color_typeActivity
    FROM organisation AS o
    INNER JOIN coach AS c ON o.id_coach = c.id_coach
    INNER JOIN user AS u ON c.id_user = u.id_user
    LEFT JOIN activities AS a ON o.id_activities = a.id_activities
    LEFT JOIN typeactivities AS ta ON a.id_typeActivities = ta.id_typeActivities
    WHERE o.id_organisation = ?
    LIMIT 1;
";
$stmtOrg = $db->prepare($sqlOrg);
$stmtOrg->bind_param("i", $idOrganisation);
$stmtOrg->execute();
$resOrg = $stmtOrg->get_result();
$organisation = $resOrg->fetch_assoc();
$stmtOrg->close();

if (!$organisation) {
    http_response_code(404);
    echo json_encode(["error" => "Organisation introuvable"]);
    exit;
}

// Récupération des membres associés à l'organisation
$sqlMembers = "
    SELECT 
        u.id_user,
        u.firstName_user,
        u.lastName_user,
        u.emailAddress_user,
        u.telephone_user,
        u.actualLevel_user,
        u.wound_user,
        ou.performance_organisationuser,
        ou.feedbackCoachOnUser_organisationuser,
        ou.feedbackUserOnOrganisation_organisationuser
    FROM organisationuser AS ou
    INNER JOIN user AS u ON ou.id_user = u.id_user
    WHERE ou.id_organisation = ?
    ORDER BY u.lastName_user ASC, u.firstName_user ASC;
";
$stmtMembers = $db->prepare($sqlMembers);
$stmtMembers->bind_param("i", $idOrganisation);
$stmtMembers->execute();
$resMembers = $stmtMembers->get_result();

$members = [];
while ($row = $resMembers->fetch_assoc()) {
    $members[] = $row;
}
$stmtMembers->close();

// Structure du JSON de réponse
$response = [
    "id_organisation" => $organisation["id_organisation"],
    "begin_organisation" => $organisation["begin_organisation"],
    "end_organisation" => $organisation["end_organisation"],
    "feedbackCoachOnOrganisation" => $organisation["feedbackCoachOnOrganisation_organisation"],
    "coach" => [
        "id_coach" => $organisation["id_coach"],
        "firstName_user" => $organisation["coach_firstName"],
        "lastName_user" => $organisation["coach_lastName"],
        "emailAddress_user" => $organisation["coach_email"]
    ],
    "activity" => [
        "id_activities" => $organisation["id_activities"],
        "name_typeActivity" => $organisation["name_typeActivity"],
        "description_typeActivity" => $organisation["description_typeActivity"],
        "color_typeActivity" => $organisation["color_typeActivity"]
    ],
    "total_members" => count($members),
    "members" => $members
];

echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

$db->close();
?>
