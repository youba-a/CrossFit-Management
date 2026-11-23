<?php
// -------------------------------------------------------------
// API : récupérer la liste (id + nom) des box
// -------------------------------------------------------------
include "../../../check_auth.php";

header('Content-Type: application/json');

// Vérifie l'authentification
if (!$authOwnerId) {
    http_response_code(401);
    echo json_encode(['error' => 'Non authentifié']);
    exit;
}

try {
    // Préparation de la requête
    $stmt = $db->prepare("
        SELECT 
            id_box,
            name_box
        FROM box
        ORDER BY name_box ASC
    ");

    $stmt->execute();
    $result = $stmt->get_result();

    $boxes = [];
    while ($row = $result->fetch_assoc()) {
        $boxes[] = [
            'id_box' => (int)$row['id_box'],
            'name_box' => $row['name_box']
        ];
    }

    echo json_encode(['success' => true, 'data' => $boxes]);

} catch (Exception $e) {
    error_log("Erreur getAllBox : " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erreur serveur']);
}
?>
