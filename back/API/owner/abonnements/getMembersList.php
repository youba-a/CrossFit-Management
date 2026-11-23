<?php
include "../../../check_auth.php";
header('Content-Type: application/json');

if (!$authOwnerId) {
    http_response_code(401);
    echo json_encode(['error' => 'Non authentifié']);
    exit;
}

try {
    $query = "
        SELECT 
            u.id_user,
            u.lastName_user,
            u.firstName_user,
            a.nom_abonnement AS currentAbonnement,
            bu.subscriptionBegin_boxuser,
            bu.subscriptionEnd_boxuser
        FROM user u
        LEFT JOIN boxuser bu ON u.id_user = bu.idUser_boxuser
            AND bu.subscriptionEnd_boxuser >= CURDATE()
        LEFT JOIN abonnements a ON bu.idAbonnement_boxuser = a.id_abonnement
        WHERE u.anonymisation_user = 0
        ORDER BY u.lastName_user, u.firstName_user
    ";

    $stmt = $db->prepare($query);
    $stmt->execute();
    $result = $stmt->get_result();

    $members = $result->fetch_all(MYSQLI_ASSOC);

    echo json_encode(['success' => true, 'data' => $members]);

} catch (Exception $e) {
    error_log("Erreur getMembersList: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erreur serveur']);
}
?>
