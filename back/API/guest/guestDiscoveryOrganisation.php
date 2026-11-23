<?php
header('Content-Type: application/json; charset=utf-8');//pour que ça soit plus beau à lire sur le navigateur, sinon c'est moche


include "../../../config/db.php"; 

if (!isset($db) || $db->connect_errno) {
    // Message générique pour le client
    http_response_code(500);
    echo json_encode(["error" => "Impossible de se connecter à la base de données."]);
    exit;
}

//je retourne plein d'infos sur l'organisation, même les adresses, si besoin

$sql = "
    SELECT 
        o.id_organisation,
        o.begin_organisation,
        o.end_organisation,
        CONCAT(u.firstName_user, ' ', u.lastName_user) AS coach_name,
        ta.name_typeActivity AS type_activity,
        ta.description_typeActivity AS description_activity,
        ta.color_typeActivity AS color_activity,
        ta.allowGuest_typeActivity AS allowGuest_activity,
        b.name_box AS box_name,
        b.town_box AS town,
        b.streetNameAddress_box AS street,
        b.numeroAddress_box AS numero,
        b.postalCode_box AS postal_code
    FROM organisation o
    INNER JOIN coach c ON o.id_coach = c.id_coach
    INNER JOIN user u ON c.id_user = u.id_user
    INNER JOIN activities a ON o.id_activities = a.id_activities
    INNER JOIN typeactivities ta ON a.id_typeActivities = ta.id_typeActivities
    INNER JOIN box b ON o.id_box = b.id_box
    WHERE o.begin_organisation >= DATE_SUB(NOW(), INTERVAL 1 WEEK)
    ORDER BY o.begin_organisation ASC
";



if ($stmt = $db->prepare($sql)) {
    $stmt->execute();
    $result = $stmt->get_result();

    $organisations = [];
    while ($row = $result->fetch_assoc()) {
        $organisations[] = $row;
    }

    echo json_encode($organisations, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

    $stmt->close();
} else {
    // Message générique pour le client
    http_response_code(500);
    echo json_encode(["error" => "Impossible de se connecter à la base de données."]);
    exit;
}

$db->close();
?>
