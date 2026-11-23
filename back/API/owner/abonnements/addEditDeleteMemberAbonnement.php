<?php
include "../../../check_auth.php";
header('Content-Type: application/json');

if (!$authOwnerId) {
    http_response_code(401);
    echo json_encode(['error' => 'Non authentifié']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$action = trim($input['action'] ?? '');

if (!in_array($action, ['add', 'edit', 'delete'])) {
    echo json_encode(['success' => false, 'message' => 'Action invalide']);
    exit;
}

try {
    if ($action === 'add') {
        $id_user = intval($input['id_user']);
        $id_box = intval($input['id_box']);
        $id_abonnement = intval($input['id_abonnement']);
        $datePayment = $input['datePayment_boxuser'];
        $begin = $input['subscriptionBegin_boxuser'];
        $end = $input['subscriptionEnd_boxuser'];

        $stmt = $db->prepare("
            INSERT INTO boxuser (idBox_boxuser, idUser_boxuser, idAbonnement_boxuser, datePayment_boxuser, subscriptionBegin_boxuser, subscriptionEnd_boxuser)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("iiisss", $id_box, $id_user, $id_abonnement, $datePayment, $begin, $end);
        $stmt->execute();

        echo json_encode(['success' => true, 'message' => 'Abonnement ajouté avec succès']);
    }

    if ($action === 'edit') {
        $id_boxuser = intval($input['id_boxuser']);

        $id_user = intval($input['id_user']);
        $id_box = intval($input['id_box']);
        $id_abonnement = intval($input['id_abonnement']);
        $datePayment = $input['datePayment_boxuser'];
        $begin = $input['subscriptionBegin_boxuser'];
        $end = $input['subscriptionEnd_boxuser'];

        $stmt = $db->prepare("
            UPDATE boxuser
            SET idAbonnement_boxuser = ?, datePayment_boxuser = ?, subscriptionBegin_boxuser = ?, subscriptionEnd_boxuser = ?
            WHERE id_boxuser = ?
        ");
        $stmt->bind_param("isssi", $id_abonnement, $datePayment, $begin, $end, $id_boxuser);
        $stmt->execute();

        echo json_encode(['success' => true, 'message' => 'Abonnement mis à jour']);
    }

    if ($action === 'delete') {
        $id_boxuser = intval($input['id_boxuser']);

        $stmt = $db->prepare("DELETE FROM boxuser WHERE id_boxuser = ?");
        $stmt->bind_param("i", $id_boxuser);
        $stmt->execute();

        echo json_encode(['success' => true, 'message' => 'Abonnement supprimé']);
    }

} catch (Exception $e) {
    error_log("Erreur abonnement membre : " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erreur serveur']);
}
?>
