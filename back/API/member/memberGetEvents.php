<?php
header('Content-Type: application/json; charset=utf-8');

include "../../check_auth.php"; // Vérifie la connexion de l'adhérent

// --- Vérification d'authentification ---
if (!$authUserId) {
    http_response_code(401);
    echo json_encode(['error' => 'Non authentifié']);
    exit;
}

// --- Vérification de la connexion à la base ---
if (!isset($db) || $db->connect_errno) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Impossible de se connecter à la base de données."
    ]);
    exit;
}

// --- Requête pour récupérer les événements de l’adhérent connecté ---
$sqlEvents = "
    SELECT 
        o.id_organisation,
        ta.name_typeActivity AS activite,
        ta.color_typeActivity AS color,
        o.begin_organisation AS debut,
        o.end_organisation AS fin,
        CONCAT(uCoach.firstName_user, ' ', uCoach.lastName_user) AS coach,
        b.name_box AS lieu,
        o.feedbackCoachOnOrganisation_organisation AS commentaire
    FROM organisationuser ou
    INNER JOIN organisation o
        ON o.id_organisation = ou.id_organisation
    INNER JOIN coach c
        ON o.id_coach = c.id_coach
    INNER JOIN user uCoach
        ON c.id_user = uCoach.id_user
    INNER JOIN box b
        ON o.id_box = b.id_box
    INNER JOIN activities a
        ON o.id_activities = a.id_activities
    INNER JOIN typeactivities ta
        ON a.id_typeActivities = ta.id_typeActivities
    WHERE ou.id_user = ?
    ORDER BY o.begin_organisation ASC
";

$events = [];

if ($stmt = $db->prepare($sqlEvents)) {
    $stmt->bind_param("i", $authUserId);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $events[] = [
            "id_organisation" => (int)$row["id_organisation"],
            "activite" => $row["activite"],
            "color" => $row["color"],
            "debut" => $row["debut"],
            "fin" => $row["fin"],
            "coach" => $row["coach"],
            "lieu" => $row["lieu"],
            "commentaire" => $row["commentaire"]
        ];
    }

    $stmt->close();
} else {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Erreur lors du chargement des événements futurs."
    ]);
    exit;
}

$db->close();

// --- Réponse finale ---
echo json_encode([
    "success" => true,
    "userId" => $authUserId,
    "evenementsFuturs" => $events
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
?>
