<?php
// -------------------------------------------------------------
// API : Récupérer id/prénom/avatar de l'utilisateur connecté
// -------------------------------------------------------------
include "../../check_auth.php";

header('Content-Type: application/json');

// Vérifie si un utilisateur est connecté
if (!$authUserId) {
    http_response_code(401);
    echo json_encode(['error' => 'Non authentifié']);
    exit;
}

try {
    // Prépare la requête pour récupérer les infos de l'utilisateur connecté
    $stmt = $db->prepare("
        SELECT 
            id_user,
            firstName_user,
            avatar_user
        FROM user
        WHERE id_user = ?
        LIMIT 1
    ");

    $stmt->bind_param("i", $authUserId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        // Si aucun utilisateur trouvé (cas rare)
        http_response_code(404);
        echo json_encode(['error' => 'No User']);
        exit;
    }

    $user = $result->fetch_assoc();

    echo json_encode(['success' => true, 'data' => $user]);

} catch (Exception $e) {
    error_log("Erreur getUserInfo.php : " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Erreur serveur']);
}
?>
