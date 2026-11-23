<?php
// back/API/member/memberBookingView.php
// Retourne la liste des organisations (événements) pour le calendrier,
// avec infos sur la place restante.
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
    // Requête : join organisation, Activities->TypeActivities, Box
    $sql = "
        SELECT 
            o.id_organisation,
            o.begin_organisation,
            o.end_organisation,
            ta.id_typeActivities,
            ta.name_typeActivity,
            ta.description_typeActivity,
            ta.color_typeActivity,
            b.id_box,
            b.name_box,
            b.numeroAddress_box,
            b.streetNameAddress_box,
            b.postBoxAddress_box,
            b.postalCode_box,
            b.town_box,
            b.numberPeople_box
        FROM organisation o
        JOIN activities a ON a.id_activities = o.id_activities
        JOIN typeactivities ta ON ta.id_typeActivities = a.id_typeActivities
        JOIN box b ON b.id_box = o.id_box
        WHERE NOT EXISTS (
            SELECT 1 
            FROM organisationuser ou
            WHERE ou.id_organisation = o.id_organisation
            AND ou.id_user = ?
        )
        AND o.end_organisation >= NOW()
        ORDER BY o.begin_organisation ASC
    ";

    if (!$stmt = $db->prepare($sql)) {
        // Ne pas renvoyer l'erreur SQL
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Erreur serveur.', 'data' => null]);
        exit;
    }
    $stmt->bind_param('i', $authUserId);
    $stmt->execute();
    $res = $stmt->get_result();
    $rows = $res->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    if (empty($rows)) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Aucun cours trouvé.', 'data' => []]);
        exit;
    }

    // Pour chaque organisation, calculer nombre de réservations et places restantes
    $out = [];
    $countStmt = $db->prepare("SELECT COUNT(*) as c FROM organisationuser WHERE id_organisation = ?");
    if (!$countStmt) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Erreur serveur.', 'data' => null]);
        exit;
    }

    foreach ($rows as $r) {
        $idOrg = $r['id_organisation'];
        $countStmt->bind_param('i', $idOrg);
        $countStmt->execute();
        $g = $countStmt->get_result()->fetch_assoc();
        $booked = (int)$g['c'];
        $capacity = is_numeric($r['numberPeople_box']) ? (int)$r['numberPeople_box'] : 0;
        $spotsRemaining = max(0, $capacity - $booked);

        $out[] = [
            'id_organisation' => (int)$r['id_organisation'],
            'begin_organisation' => $r['begin_organisation'],
            'end_organisation' => $r['end_organisation'],
            'id_typeActivities' => (int)$r['id_typeActivities'],
            'name_typeActivity' => $r['name_typeActivity'],
            'description_typeActivity' => $r['description_typeActivity'],
            'color_typeActivity' => (int)$r['color_typeActivity'],
            'id_box' => (int)$r['id_box'],
            'name_box' => $r['name_box'],
            'numeroAddress_box' => $r['numeroAddress_box'],
            'streetNameAddress_box' => $r['streetNameAddress_box'],
            'postBoxAddress_box' => $r['postBoxAddress_box'],
            'postalCode_box' => $r['postalCode_box'],
            'town_box' => $r['town_box'],
            'capacity' => $capacity,
            'booked' => $booked,
            'spotsRemaining' => $spotsRemaining
        ];
    }
    $countStmt->close();

    echo json_encode(['success' => true, 'message' => 'Liste des cours.', 'data' => $out]);

} catch (Throwable $e) {
    // Ne pas exposer $e->getMessage() en prod
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erreur serveur inattendue.', 'data' => null]);
    exit;
}
