<?php
header('Content-Type: application/json; charset=utf-8');

include "../../check_auth.php";

// Vérification authentification
if (!$authCoachId) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Non authentifié'
    ]);
    exit;
}

// Vérification connexion DB
if (!isset($db) || $db->connect_errno) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur interne. Impossible de se connecter à la base de données.'
    ]);
    exit;
}

$idCoach = $authCoachId;

$query = "
    SELECT 
        o.id_organisation,
        o.begin_organisation AS date,
        ta.name_typeActivity AS event_type,
        c.firstName_user AS coach_firstname,
        c.lastName_user AS coach_lastname,
        u.firstName_user AS user_firstname,
        u.lastName_user AS user_lastname,
        ou.feedbackUserOnCoach_organisationuser AS feedback
    FROM 
        organisation o
    JOIN 
        organisationuser ou ON o.id_organisation = ou.id_organisation
    JOIN 
        user u ON ou.id_user = u.id_user
    JOIN 
        coach co ON o.id_coach = co.id_coach
    JOIN 
        user c ON co.id_user = c.id_user
    JOIN 
        activities a ON o.id_activities = a.id_activities
    JOIN 
        typeactivities ta ON a.id_typeActivities = ta.id_typeActivities
    WHERE 
        o.id_coach = ?
        AND ou.feedbackUserOnCoach_organisationuser IS NOT NULL
        AND TRIM(ou.feedbackUserOnCoach_organisationuser) <> '';
";

if ($stmt = $db->prepare($query)) {

    $stmt->bind_param("i", $idCoach);
    $stmt->execute();
    $result = $stmt->get_result();

    $feedbacks = [];

    while ($row = $result->fetch_assoc()) {
        $feedbacks[] = [
            'id_organisation' => $row['id_organisation'],
            'date' => $row['date'],
            'event_type' => $row['event_type'],
            'coach_firstname' => $row['coach_firstname'],
            'coach_lastname' => $row['coach_lastname'],
            'user_firstname' => $row['user_firstname'],
            'user_lastname' => $row['user_lastname'],
            'feedback' => $row['feedback']
        ];
    }

    if (!empty($feedbacks)) {
        echo json_encode([
            'success' => true,
            'data' => $feedbacks
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Aucun commentaire trouvé.'
        ]);
    }

    $stmt->close();

} else {

    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur interne lors du traitement de la requête.'
    ]);
}

$db->close();
?>
