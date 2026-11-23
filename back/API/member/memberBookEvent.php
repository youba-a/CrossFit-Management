<?php
// back/API/member/bookEvent.php
// Réserve un cours pour l'utilisateur authentifié.
// Lecture JSON depuis php://input
// Ajuste les chemins require_once selon ton arborescence.

header('Content-Type: application/json; charset=utf-8');

include "../../check_auth.php"; 


if (!$authUserId) 
{
    http_response_code(401);
    echo json_encode(["error" => "Non authentifié"]);
    exit;
}

try {
    $data = json_decode(file_get_contents("php://input"), true);
    $idOrganisation = filter_var($data['id_organisation'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

    if ($idOrganisation === false) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Paramètre manquant ou invalide : id_organisation.', 'data' => null]);
        exit;
    }

    // 1) Vérifier que l'organisation existe + récupérer id_box, horaires
    $db->begin_transaction();
    $sqlOrg = "SELECT id_organisation, id_box, begin_organisation, end_organisation FROM organisation WHERE id_organisation = ? FOR UPDATE";
    if (!$stmt = $db->prepare($sqlOrg)) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Erreur serveur.', 'data' => null]);
        exit;
    }
    $stmt->bind_param('i', $idOrganisation);
    $stmt->execute();
    $res = $stmt->get_result();
    $org = $res->fetch_assoc();
    $stmt->close();

    if (!$org) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Cours non trouvé.', 'data' => null]);
        exit;
    }

    // 2) Vérifier si l'utilisateur a déjà réservé ce cours
    $sqlCheck = "SELECT 1 FROM organisationuser WHERE id_organisation = ? AND id_user = ?";
    $stmt = $db->prepare($sqlCheck);
    if (!$stmt) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Erreur serveur.', 'data' => null]);
        exit;
    }
    $stmt->bind_param('ii', $idOrganisation, $authUserId);
    $stmt->execute();
    $exists = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($exists) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Vous avez déjà réservé ce cours.', 'data' => null]);
        exit;
    }

    // 3) Vérifier capacité (box.numberPeople_box) et nombre de réservations actuelles
    $stmt = $db->prepare("
        SELECT b.numberPeople_box,
               (SELECT COUNT(*) FROM organisationuser ou WHERE ou.id_organisation = ?) as bookedCount
        FROM box b
        JOIN organisation o ON o.id_box = b.id_box
        WHERE o.id_organisation = ?
        LIMIT 1
    ");
    if (!$stmt) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Erreur serveur.', 'data' => null]);
        exit;
    }
    $stmt->bind_param('ii', $idOrganisation, $idOrganisation);
    $stmt->execute();
    $res = $stmt->get_result();
    $capRow = $res->fetch_assoc();
    $stmt->close();

    $capacity = isset($capRow['numberPeople_box']) && is_numeric($capRow['numberPeople_box']) ? (int)$capRow['numberPeople_box'] : 0;
    $bookedCount = isset($capRow['bookedCount']) ? (int)$capRow['bookedCount'] : 0;

    if ($capacity > 0 && $bookedCount >= $capacity) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Le cours est complet.', 'data' => null]);
        exit;
    }

    // vérif de la date
    $now = date('Y-m-d H:i:s'); // date et heure actuelles
    $endOrganisation = $org['end_organisation'];

    if (strtotime($endOrganisation) < strtotime($now)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Impossible de réserver : le cours est déjà terminé.',
            'data' => null
        ]);
        exit;
    }

    // 4) Insérer la réservation dans OrganisationUser
    $today = date('Y-m-d');
    $sqlInsert = "INSERT INTO organisationuser (id_organisation, id_user, performance_organisationuser, date_organisationuser, feedbackUserOnCoach_organisationuser, feedbackUserOnOrganisation_organisationuser, feedbackCoachOnUser_organisationuser)
                  VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $db->prepare($sqlInsert);
    if (!$stmt) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Erreur serveur.', 'data' => null]);
        exit;
    }
    // Valeurs par défaut pour les champs de feedback/performance (vide)
    $performance = '';
    $feedbackUserOnCoach = '';
    $feedbackUserOnOrg = '';
    $feedbackCoachOnUser = '';

    $stmt->bind_param('iisssss', $idOrganisation, $authUserId, $performance, $today, $feedbackUserOnCoach, $feedbackUserOnOrg, $feedbackCoachOnUser);

    if (!$stmt->execute()) {
        // Duplicate key ou autre insert error : ne pas afficher SQL
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Impossible d\'enregistrer la réservation.', 'data' => null]);
        $stmt->close();
        exit;
    }
    $stmt->close();

    $db->commit();
    http_response_code(201);
    echo json_encode(['success' => true, 'message' => 'Réservation créée.', 'data' => ['id_organisation' => (int)$idOrganisation, 'id_user' => (int)$authUserId]]);
    exit;
} catch (Throwable $e) {
    $db->rollback();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erreur serveur inattendue.', 'data' => null]);
    exit;
}
