<?php
// -------------------------------------------------------------
// API : récupérer la liste de tous les utilisateurs
// -------------------------------------------------------------
// Ce script renvoie la liste complète des utilisateurs avec leur rôle
// (Owner / Coach / Adhérant / Utilisateur)
// -------------------------------------------------------------

include "../../check_auth.php";

header('Content-Type: application/json');

// -------------------------------------------------------------
// Vérification de l'authentification
// -------------------------------------------------------------
if (!$authOwnerId) {
    http_response_code(401);
    echo json_encode(['error' => 'Non authentifié']);
    exit;
}

// -------------------------------------------------------------
// 1️⃣ Requête SQL principale
// -------------------------------------------------------------
try {
    $stmt = $db->prepare("
        SELECT 
            u.id_user,
            u.firstName_user,
            u.lastName_user,
            u.emailAddress_user,
            u.telephone_user,
            u.town_user,
            CASE 
                WHEN o.id_owner IS NOT NULL AND c.id_coach IS NOT NULL AND c.actif_coach = 1 THEN 'Owner / Coach'
                WHEN o.id_owner IS NOT NULL THEN 'Owner'
                WHEN c.id_coach IS NOT NULL AND c.actif_coach = 1 THEN 'Coach'
                ELSE 'Membre'
            END AS role
        FROM user u
        LEFT JOIN owner o ON u.id_user = o.id_user
        LEFT JOIN coach c ON u.id_user = c.id_user
        ORDER BY u.lastName_user ASC, u.firstName_user ASC
    ");

    $stmt->execute();
    $result = $stmt->get_result();

    // -------------------------------------------------------------
    // 2️⃣ Vérification du résultat
    // -------------------------------------------------------------
    if ($result->num_rows === 0) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Aucun utilisateur trouvé'
        ]);
        exit;
    }

    // -------------------------------------------------------------
    // 3️⃣ Construction du tableau de résultats
    // -------------------------------------------------------------
    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = [
            'id_user' => $row['id_user'],
            'firstName_user' => $row['firstName_user'],
            'lastName_user' => $row['lastName_user'],
            'emailAddress_user' => $row['emailAddress_user'],
            'telephone_user' => $row['telephone_user'],
            'town_user' => $row['town_user'],
            'role' => $row['role'] // ✅ ajout explicite du rôle
        ];
    }

    // -------------------------------------------------------------
    // 4️⃣ Réponse JSON
    // -------------------------------------------------------------
    echo json_encode([
        'success' => true,
        'count' => count($users),
        'data' => $users
    ]);

} catch (Exception $e) {
    // Log interne
    error_log("Erreur getAllUsers : " . $e->getMessage());

    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Une erreur serveur est survenue. Veuillez réessayer plus tard.'
    ]);
    exit;
}
?>
