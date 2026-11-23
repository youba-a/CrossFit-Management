<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../check_auth.php';

if (!$authOwnerId) {
    http_response_code(401);
    echo json_encode(['error' => 'Non authentifié']);
    exit;
}

$action = $_GET['action'] ?? '';

if (in_array($action, ['save_activity', 'delete_organisation', 'delete_activity', 'update_organisation'], true)
    && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit(json_encode(['success' => false, 'message' => 'Methode non autorisee']));
}

switch ($action) {

    // 🧱 1. Liste des coachs
    case 'list_coachs':
        $query = "
            SELECT c.id_coach, u.firstName_user, u.lastName_user, c.actualLevel_coach, c.specialisation_coach, c.actif_coach
            FROM coach c
            JOIN user u ON c.id_user = u.id_user
            ORDER BY u.lastName_user
        ";
        $res = $db->query($query);
        echo json_encode($res->fetch_all(MYSQLI_ASSOC));
        break;


    // 🗓️ 2. Planning d’un coach
    case 'get_planning':
        $idCoach = intval($_GET['id_coach'] ?? 0);
        if (!$idCoach) {
            echo json_encode(['success' => false, 'message' => 'Coach non spécifié']);
            exit;
        }

        $stmt = $db->prepare("
            SELECT o.id_organisation, o.begin_organisation, o.end_organisation, 
                   t.name_typeActivity, b.name_box
            FROM organisation o
            JOIN activities a ON o.id_activities = a.id_activities
            JOIN typeactivities t ON a.id_typeActivities = t.id_typeActivities
            JOIN box b ON o.id_box = b.id_box
            WHERE o.id_coach = ?
            ORDER BY o.begin_organisation ASC
        ");
        $stmt->bind_param("i", $idCoach);
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        echo json_encode($rows);
        break;


    // 🧮 3. Créer / Modifier un type d’activité
    case 'save_activity':
        $data = json_decode(file_get_contents('php://input'), true);

        $id = intval($data['id'] ?? 0);
        $name = trim($data['name'] ?? '');
        $desc = trim($data['desc'] ?? '');
        $color = intval($data['color'] ?? 0);
        $allowGuest = !empty($data['allowGuest']) ? 1 : 0;

        $success = false;

        if ($id > 0) {
            // --- 🔄 Mise à jour
            $stmt = $db->prepare("
                UPDATE typeactivities
                SET name_typeActivity=?, description_typeActivity=?, color_typeActivity=?, allowGuest_typeActivity=? 
                WHERE id_typeActivities=?
            ");
            $stmt->bind_param("ssiii", $name, $desc, $color, $allowGuest, $id);
            $success = $stmt->execute();
            $stmt->close();

            if ($success) {
                // Vérifie si une entrée Activities existe déjà
                $check = $db->prepare("SELECT id_activities FROM activities WHERE id_typeActivities=?");
                $check->bind_param("i", $id);
                $check->execute();
                $check->store_result();

                // Si pas d’entrée -> on en crée une
                if ($check->num_rows === 0) {
                    $insertAct = $db->prepare("INSERT INTO activities (id_activities, id_typeActivities) VALUES (?, ?)");
                    $insertAct->bind_param("ii", $id, $id);
                    $insertAct->execute();
                    $insertAct->close();
                }
                $check->close();
            }

        } else {
            // --- 🆕 Création
            $stmt = $db->prepare("
                INSERT INTO typeactivities (name_typeActivity, description_typeActivity, color_typeActivity, allowGuest_typeActivity)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->bind_param("ssii", $name, $desc, $color, $allowGuest);
            $success = $stmt->execute();

            if ($success) {
                $newTypeId = $db->insert_id;

                // 🔗 Créer une entrée correspondante dans Activities
                $insertAct = $db->prepare("INSERT INTO activities (id_activities, id_typeActivities) VALUES (?, ?)");
                $insertAct->bind_param("ii", $newTypeId, $newTypeId);
                $insertAct->execute();
                $insertAct->close();
            }

            $stmt->close();
        }

        echo json_encode(['success' => $success]);
        break;


    // ❌ 4. Supprimer une organisation
    case 'delete_organisation':
        $id = intval($_GET['id'] ?? 0);
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'Organisation non spécifiée']);
            exit;
        }

        // Supprimer d'abord les utilisateurs liés
        $stmt = $db->prepare("DELETE FROM organisationuser WHERE id_organisation = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

        // Supprimer ensuite les discovery eventuels
        $stmt = $db->prepare("DELETE FROM discovery WHERE idOrganisation_discovery = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

        // Ensuite, supprimer l'organisation
        $stmt = $db->prepare("DELETE FROM organisation WHERE id_organisation = ?");
        $stmt->bind_param("i", $id);
        $success = $stmt->execute();
        $stmt->close();

        echo json_encode(['success' => $success]);
        break;

    // 🗑 Supprimer une activité
        case 'delete_activity':
            $id = intval($_GET['id'] ?? 0);
            $response = ['success' => false, 'message' => ''];

            if ($id > 0) {
                // 1️⃣ Vérifie s'il y a des organisations utilisant cette activité
                $check = $db->prepare("SELECT COUNT(*) FROM organisation WHERE id_activities = ?");
                $check->bind_param("i", $id);
                $check->execute();
                $check->bind_result($count);
                $check->fetch();
                $check->close();

                if ($count > 0) {
                    // Impossible de supprimer
                    $response['message'] = "Impossible de supprimer cette activité : elle est utilisée dans $count organisation(s).";
                } else {
                    // 2️⃣ Commence la transaction pour supprimer dans les deux tables
                    $db->begin_transaction();
                    try {
                        // Supprime dans Activities
                        $stmt1 = $db->prepare("DELETE FROM activities WHERE id_typeActivities = ?");
                        $stmt1->bind_param("i", $id);
                        $stmt1->execute();
                        $stmt1->close();

                        // Supprime dans TypeActivities
                        $stmt2 = $db->prepare("DELETE FROM typeactivities WHERE id_typeActivities = ?");
                        $stmt2->bind_param("i", $id);
                        $stmt2->execute();
                        $stmt2->close();

                        $db->commit();
                        $response['success'] = true;
                        $response['message'] = "Activité supprimée avec succès ✅";

                    } catch (Exception $e) {
                        $db->rollback();
                        $response['message'] = "Erreur lors de la suppression ❌";
                    }
                }
            } else {
                $response['message'] = "ID d’activité invalide ❌";
            }

            echo json_encode($response);
            break;


    // 📦 Récupérer toutes les box
    case 'get_boxes':
        $res = $db->query("SELECT id_box, name_box FROM box");
        echo json_encode($res->fetch_all(MYSQLI_ASSOC));
        break;

    // 🏋️‍♀️ Récupérer toutes les activités (avec TypeActivities)
    case 'get_activities':
        $res = $db->query("
            SELECT a.id_activities, t.name_typeActivity
            FROM activities a
            JOIN typeactivities t ON a.id_typeActivities = t.id_typeActivities
        ");
        echo json_encode($res->fetch_all(MYSQLI_ASSOC));
        break;

            // 🎨 Liste complète des TypeActivities
    case 'get_all_activities':
        $res = $db->query("
            SELECT id_typeActivities, name_typeActivity, description_typeActivity, color_typeActivity, allowGuest_typeActivity
            FROM typeactivities
            ORDER BY name_typeActivity
        ");
        echo json_encode($res->fetch_all(MYSQLI_ASSOC));
        break;
    
    default:
        echo json_encode(['error' => 'Action non reconnue']);
        break;

        // ✏️ 5. Modifier une organisation existante
    case 'update_organisation':
        $data = json_decode(file_get_contents('php://input'), true);

        $id = intval($data['id'] ?? 0);
        $begin = $data['begin'] ?? '';
        $end = $data['end'] ?? '';
        $idBox = intval($data['id_box'] ?? 0);
        $idActivity = intval($data['id_activity'] ?? 0);

        if (!$id || !$begin || !$end || !$idBox || !$idActivity) {
            echo json_encode(['success' => false, 'message' => 'Champs manquants']);
            exit;
        }

        $stmt = $db->prepare("
            UPDATE organisation
            SET id_box=?, id_activities=?, begin_organisation=?, end_organisation=? 
            WHERE id_organisation=?
        ");
        $stmt->bind_param("iissi", $idBox, $idActivity, $begin, $end, $id);
        $success = $stmt->execute();
        $stmt->close();

        echo json_encode(['success' => $success]);
        break;

}
