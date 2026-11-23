<?php
include "../../../check_auth.php";
header('Content-Type: application/json');

if (!$authOwnerId) {
    http_response_code(401);
    echo json_encode(['error' => 'Non authentifié']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$id_user = intval($input['id_user'] ?? 0);

if ($id_user <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID utilisateur invalide']);
    exit;
}

try {
    $stmt = $db->prepare("
        SELECT 
            bu.id_boxuser,
            bu.idBox_boxuser,
            a.id_abonnement,
            a.nom_abonnement,
            a.prix_abonnement,
            bu.subscriptionBegin_boxuser,
            bu.subscriptionEnd_boxuser,
            bu.datePayment_boxuser
        FROM boxuser bu
        INNER JOIN abonnements a ON bu.idAbonnement_boxuser = a.id_abonnement
        WHERE bu.idUser_boxuser = ?
        ORDER BY bu.subscriptionBegin_boxuser DESC
    ");
    $stmt->bind_param("i", $id_user);
    $stmt->execute();
    $result = $stmt->get_result();

    $abonnements = $result->fetch_all(MYSQLI_ASSOC);

    echo json_encode(['success' => true, 'data' => $abonnements]);

} catch (Exception $e) {
    error_log("Erreur getMemberAbonnements: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erreur serveur']);
}
?>
