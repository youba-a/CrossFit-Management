<?php
header('Content-Type: application/json; charset=utf-8');
include "../../check_auth.php";

if (!$authUserId) {
    http_response_code(401);
    echo json_encode(["error" => "Non authentifié"]);
    exit;
}

$input = json_decode(file_get_contents("php://input"), true);
$idOrganisation = $_GET['id_organisation'] ?? ($input['id_organisation'] ?? null);

if (!$idOrganisation || !is_numeric($idOrganisation)) {
    http_response_code(400);
    echo json_encode(["error" => "ID d'organisation invalide"]);
    exit;
}

// Vérifie que l'adhérant est bien inscrit à cette organisation
$sqlCheck = "
    SELECT *
    FROM organisationuser
    WHERE id_organisation = ? AND id_user = ?;
";

$stmtCheck = $db->prepare($sqlCheck);
$stmtCheck->bind_param("ii", $idOrganisation, $authUserId);
$stmtCheck->execute();
$resCheck = $stmtCheck->get_result();

if ($resCheck->num_rows === 0) {
    http_response_code(403);
    echo json_encode(["error" => "Vous n'êtes pas inscrit à cette organisation!"]);
    exit;
}

$stmtCheck->close();


$sqlFeedback = "
    SELECT feedbackUserOnOrganisation_organisationuser
    FROM organisationuser
    WHERE id_organisation = ? AND id_user = ?
";

$stmtFb = $db->prepare($sqlFeedback);
$stmtFb->bind_param("ii", $idOrganisation, $authUserId);
$stmtFb->execute();
$resFb = $stmtFb->get_result();
$userFeedbackRow = $resFb->fetch_assoc();
$stmtFb->close();

$userFeedback = $userFeedbackRow["feedbackUserOnOrganisation_organisationuser"] ?? null;


$sqlFeedbackCoach = "
    SELECT feedbackUserOnCoach_organisationuser
    FROM organisationuser
    WHERE id_organisation = ? AND id_user = ?
";

$stmtFbCoach = $db->prepare($sqlFeedbackCoach);
$stmtFbCoach->bind_param("ii", $idOrganisation, $authUserId);
$stmtFbCoach->execute();
$resFbCoach = $stmtFbCoach->get_result();
$userCoachFeedbackRow = $resFbCoach->fetch_assoc();
$stmtFbCoach->close();

$userCoachFeedback = $userCoachFeedbackRow["feedbackUserOnCoach_organisationuser"] ?? null;



// Récupération des infos de l’organisation
$sqlOrg = "
    SELECT 
        o.id_organisation,
        o.begin_organisation,
        o.end_organisation,
        o.feedbackCoachOnOrganisation_organisation,
        c.id_coach,
        uc.firstName_user AS coach_firstName,
        uc.lastName_user AS coach_lastName,
        ta.name_typeActivity,
        ta.description_typeActivity,
        ta.color_typeActivity
    FROM organisation AS o
    INNER JOIN coach AS c ON o.id_coach = c.id_coach
    INNER JOIN user AS uc ON c.id_user = uc.id_user
    LEFT JOIN activities AS a ON o.id_activities = a.id_activities
    LEFT JOIN typeactivities AS ta ON a.id_typeActivities = ta.id_typeActivities
    WHERE o.id_organisation = ?
";

$stmtOrg = $db->prepare($sqlOrg);
$stmtOrg->bind_param("i", $idOrganisation);
$stmtOrg->execute();
$resOrg = $stmtOrg->get_result();
$organisation = $resOrg->fetch_assoc();
$stmtOrg->close();

if (!$organisation) {
    http_response_code(404);
    echo json_encode(["error" => "Erreur: Organisation inexistente"]);
    exit;
}

// Récupération des membres (limité à prénom + avatar)
$sqlMembers = "
    SELECT 
        u.id_user,
        u.firstName_user,
        u.avatar_user
    FROM organisationuser AS ou
    INNER JOIN user AS u ON ou.id_user = u.id_user
    WHERE ou.id_organisation = ?
    ORDER BY u.firstName_user ASC;
";

$stmtMembers = $db->prepare($sqlMembers);
$stmtMembers->bind_param("i", $idOrganisation);
$stmtMembers->execute();
$resMembers = $stmtMembers->get_result();

$members = [];

while ($row = $resMembers->fetch_assoc()) {
    $members[] = [
        "id_user" => $row["id_user"],
        "firstName_user" => $row["firstName_user"],
        "avatar_user" => $row["avatar_user"]
    ];
}

$stmtMembers->close();



$response = [
    "id_organisation" => $organisation["id_organisation"],
    "begin_organisation" => $organisation["begin_organisation"],
    "end_organisation" => $organisation["end_organisation"],

    "feedback_event" => $userFeedback,
    "feedback_coach" => $userCoachFeedback,

    "activity" => [
        "name_typeActivity" => $organisation["name_typeActivity"],
        "description_typeActivity" => $organisation["description_typeActivity"],
        "color_typeActivity" => $organisation["color_typeActivity"]
    ],
    "coach" => [
        "id_coach" => $organisation["id_coach"],
        "firstName_user" => $organisation["coach_firstName"],
        "lastName_user" => $organisation["coach_lastName"]
    ],
    "total_members" => count($members),
    "members" => $members,
];

echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

$db->close();
?>
