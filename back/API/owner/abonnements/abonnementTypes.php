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

try {
    switch ($action) {
        case 'list':
            $result = $db->query("SELECT * FROM abonnements ORDER BY nom_abonnement");
            $abos = $result->fetch_all(MYSQLI_ASSOC);
            echo json_encode(['success' => true, 'data' => $abos]);
            break;

        case 'add':
            $stmt = $db->prepare("INSERT INTO abonnements (nom_abonnement, description_abonnement, prix_abonnement) VALUES (?, ?, ?)");
            $stmt->bind_param("ssd", $input['nom'], $input['description'], $input['prix']);
            $stmt->execute();
            echo json_encode(['success' => true, 'message' => 'Type ajouté']);
            break;

        case 'edit':
            $stmt = $db->prepare("UPDATE abonnements SET nom_abonnement=?, description_abonnement=?, prix_abonnement=? WHERE id_abonnement=?");
            $stmt->bind_param("ssdi", $input['nom'], $input['description'], $input['prix'], $input['id']);
            $stmt->execute();
            echo json_encode(['success' => true, 'message' => 'Type mis à jour']);
            break;

        case 'delete':
            // Vérifie que l'abonnement existe
            $stmt = $db->prepare("SELECT id_abonnement FROM abonnements WHERE id_abonnement = ?");
            $stmt->bind_param("i", $input['id']);
            $stmt->execute();
            $res = $stmt->get_result()->fetch_assoc();

            if (!$res) {
                echo json_encode(['success' => false, 'message' => 'Abonnement introuvable']);
                break;
            }

            // Mise à jour : inactif = 1
            $stmt = $db->prepare("UPDATE abonnements SET inactive_abonnement = 1 WHERE id_abonnement = ?");
            $stmt->bind_param("i", $input['id']);
            $stmt->execute();

            echo json_encode(['success' => true, 'message' => 'Abonnement désactivé']);
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Action invalide']);
    }

} catch (Exception $e) {
    error_log("Erreur abonnementTypes: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erreur serveur']);
}
?>
