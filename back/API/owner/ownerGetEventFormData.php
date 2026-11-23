<?php
header('Content-Type: application/json');

include "../../check_auth.php";

if (!$authOwnerId) {
    http_response_code(401);
    echo json_encode(['error' => 'Non authentifié']);
    exit;
}

try {
    // --- Get active coaches ---
    $queryCoaches = "
        SELECT c.id_coach, u.firstName_user, u.lastName_user
        FROM coach c
        JOIN user u ON c.id_user = u.id_user
        WHERE c.actif_coach = 1
    ";
    $coaches = $db->query($queryCoaches)->fetch_all(MYSQLI_ASSOC);

    // --- Get activity types ---
    $queryActivities = "
        SELECT id_typeActivities, name_typeActivity
        FROM typeactivities
        ORDER BY name_typeActivity
    ";
    $activityTypes = $db->query($queryActivities)->fetch_all(MYSQLI_ASSOC);

    // --- Get boxes owned by this owner ---
    $queryBoxes = "
        SELECT b.id_box, CONCAT(b.name_box, ', ', b.streetNameAddress_box) AS box_label
        FROM box b
    ";
    $stmt = $db->prepare($queryBoxes);
    $stmt->execute();
    $boxes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    echo json_encode([
        'success' => true,
        'coaches' => $coaches,
        'activityTypes' => $activityTypes,
        'boxes' => $boxes,
        'ownerId' => $authOwnerId
    ]);

} catch (Throwable $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur serveur']);
}
