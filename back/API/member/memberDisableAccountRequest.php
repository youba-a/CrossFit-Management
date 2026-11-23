<?php
// -------------------------------------------------------------
// Désactivation de compte utilisateur
// -------------------------------------------------------------

include "../../check_auth.php";

if (!$authUserId) {
    http_response_code(401);
    echo json_encode(['error' => 'Non authentifié']);
    exit;
}

try {
    $stmt = $db->prepare("SELECT id_owner FROM owner WHERE id_user = ?");
    $stmt->bind_param("i", $authUserId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Impossible de désactiver un compte Owner']);
        exit;
    }
    $stmt->close();

    $stmt = $db->prepare("UPDATE user SET needDisabled = 1 WHERE id_user = ?");
    $stmt->bind_param("i", $authUserId);
    $stmt->execute();
    
    if ($stmt->affected_rows > 0) {
        $stmtCoach = $db->prepare("UPDATE coach SET actif_coach = 0 WHERE id_user = ?");
        $stmtCoach->bind_param("i", $authUserId);
        $stmtCoach->execute();
        $stmtCoach->close();
        
        echo json_encode(['success' => true, 'message' => 'Votre demande de désactivation a été enregistrée.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Utilisateur non trouvé']);
    }
    
    $stmt->close();

} catch (Exception $e) {
    error_log("Erreur disable_account : " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erreur serveur lors de la désactivation']);
}
?>