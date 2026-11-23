<?php
// -------------------------------------------------------------
// API : Anonymiser un membre (RGPD compliant)
// -------------------------------------------------------------
include "../../check_auth.php";
//header('Content-Type: application/json');

// Vérifie l'authentification
if (!$authOwnerId) {
    http_response_code(401);
    echo json_encode(['error' => 'Non authentifié']);
    exit;
}

// Lecture des données envoyées
$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    echo json_encode(['success' => false, 'message' => 'Invalid JSON received']);
    exit;
}

$id_user = intval($input['id_user'] ?? 0);
if ($id_user <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID utilisateur invalide']);
    exit;
}

try {
    // Vérifier si le membre est un OWNER
    $stmt = $db->prepare("SELECT id_owner FROM owner WHERE id_user = ?");
    $stmt->bind_param("i", $id_user);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Impossible d’anonymiser un Owner.']);
        exit;
    }
    $stmt->close();

    // Supprimer les feedbacks liés à ce membre dans OrganisationUser
    $stmt = $db->prepare("
        UPDATE organisationuser
        SET feedbackUserOnCoach_organisationuser = NULL,
            feedbackUserOnOrganisation_organisationuser = NULL,
            feedbackCoachOnUser_organisationuser = NULL
        WHERE id_user = ?
    ");
    $stmt->bind_param("i", $id_user);
    $stmt->execute();
    $stmt->close();

    // Anonymiser les données de coach (s’il existe)
    $stmt = $db->prepare("
        UPDATE coach
        SET actualLevel_coach = 'Anonyme',
            specialisation_coach = '',
            salary_coach = 0,
            actif_coach = 0
        WHERE id_user = ?
    ");
    $stmt->bind_param("i", $id_user);
    $stmt->execute();
    $stmt->close();

    // Suppression de l'ancien avatar du membre (si existant)
    $stmt = $db->prepare("SELECT avatar_user FROM user WHERE id_user = ?");
    $stmt->bind_param("i", $id_user);
    $stmt->execute();
    $result = $stmt->get_result();
    $oldAvatarFilename = $result->fetch_assoc()['avatar_user'] ?? null;
    $stmt->close();

    if (!empty($oldAvatarFilename)) {
        // On sécurise le chemin
        $avatarDir = rtrim($_SERVER['DOCUMENT_ROOT'] ?? __DIR__, '/') . '/img/avatar/';
        $oldPath = $avatarDir . basename($oldAvatarFilename);

        if (is_file($oldPath)) {
            // Supprime le fichier silencieusement
            if (!@unlink($oldPath)) {
                error_log("Impossible de supprimer l'ancien avatar : $oldPath");
            }
        }
    }


    // Anonymiser les données personnelles du membre
    $stmt = $db->prepare("
        UPDATE user
        SET 
            lastName_user = CONCAT('Anonyme_', id_user),
            firstName_user = 'Utilisateur',
            birthdate_user = '1970-01-01',
            gender_user = 'Non spécifié',
            poids_user = NULL,
            taille_user = NULL,
            streetNameAddress_user = 'N/A',
            numeroAddress_user = '0',
            postBoxAddress_user = NULL,
            postalCode_user = '0000',
            town_user = 'N/A',
            telephone_user = NULL,
            firstContactTelephone_user = NULL,
            secondContactTelephone_user = NULL,
            emailAddress_user = CONCAT('anonyme_', id_user, '@example.com'),
            password_user = NULL,
            tokenInvitation_user = NULL,
            wound_user = '',
            actualLevel_user = '',
            notesCoach_user = '',
            avatar_user = NULL,
            anonymisation_user  = 1
        WHERE id_user = ?
    ");
    $stmt->bind_param("i", $id_user);
    $stmt->execute();
    $stmt->close();

    echo json_encode(['success' => true, 'message' => 'Le membre a été anonymisé avec succès.']);

} catch (Exception $e) {
    error_log("Erreur anonymize_member : " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erreur serveur.']);
}
?>
