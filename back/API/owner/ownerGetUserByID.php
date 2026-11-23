<?php
// -------------------------------------------------------------
// API : récupérer les informations d’un utilisateur par son ID
// -------------------------------------------------------------
include "../../check_auth.php";

header('Content-Type: application/json');

// Vérifie l'authentification
if (!$authOwnerId) {
    http_response_code(401);
    echo json_encode(['error' => 'Non authentifié']);
    exit;
}

// Decode incoming JSON data
$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    echo json_encode(['success' => false, 'message' => 'Invalid JSON received']);
    exit;
}

// Extract and sanitize
$id_user = trim($input['id_user'] ?? '');

$id_user = intval($id_user);

try {
    $stmt = $db->prepare("
        SELECT 
            u.id_user,
            u.lastName_user,
            u.firstName_user,
            u.birthdate_user,
            u.gender_user,
            u.poids_user,
            u.taille_user,
            u.streetNameAddress_user,
            u.numeroAddress_user,
            u.postBoxAddress_user,
            u.postalCode_user,
            u.town_user,
            u.telephone_user,
            u.emailAddress_user,
            CASE 
                WHEN o.id_owner IS NOT NULL THEN true
                ELSE false
            END AS isOwner,

            CASE 
                WHEN c.id_coach IS NOT NULL AND c.actif_coach = 1 THEN true
                ELSE false
            END AS isCoach,

            c.actualLevel_coach,
            c.actif_coach,
            c.salary_coach,
            c.specialisation_coach,
            o.email_owner,
            o.telephone_owner,
            o.salary_owner
        FROM user u
        LEFT JOIN owner o ON u.id_user = o.id_user
        LEFT JOIN coach c ON u.id_user = c.id_user
        WHERE u.id_user = ?
    ");
    $stmt->bind_param("i", $id_user);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        http_response_code(404);
        echo json_encode(['error' => 'Utilisateur non trouvé']);
        exit;
    }

    $user = $result->fetch_assoc();
    echo json_encode(['success' => true, 'data' => $user]);

} catch (Exception $e) {
    error_log("Erreur getUserById : " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erreur serveur']);
}
?>
