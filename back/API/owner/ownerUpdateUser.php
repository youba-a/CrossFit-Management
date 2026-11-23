<?php
// -------------------------------------------------------------
// API : mettre à jour les informations d’un utilisateur
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
$id_user = intval($input['id_user']);
$lastName_user = trim($input['lastName_user'] ?? '');
$firstName_user = trim($input['firstName_user'] ?? '');
$birthdate_user = $input['birthdate_user'] ?? '';
$gender_user = $input['gender_user'] ?? '';
$poids_user = trim($input['poids_user'] ?? '');
$taille_user = trim($input['taille_user'] ?? '');
$streetNameAddress_user = trim($input['streetNameAddress_user'] ?? '');
$numeroAddress_user = trim($input['numeroAddress_user'] ?? '');
$postBoxAddress_user = trim($input['postBoxAddress_user'] ?? '');
$postalCode_user = trim($input['postalCode_user'] ?? '');
$town_user = trim($input['town_user'] ?? '');
$telephone_user = trim($input['telephone_user'] ?? '');
$emailAddress_user = trim($input['emailAddress_user'] ?? '');
$isCoach = trim($input['isCoach'] ?? '');
$isOwner = trim($input['isOwner'] ?? '');
$actualLevel_coach = trim($input['actualLevel_coach'] ?? '');
// (l'api ne prend pas en input "actif_coach" car c'est gérer en interne)
$salary_coach = trim($input['salary_coach'] ?? '');
$specialisation_coach = trim($input['specialisation_coach'] ?? '');
$email_owner = trim($input['email_owner'] ?? '');
$telephone_owner = trim($input['telephone_owner'] ?? '');
$salary_owner = trim($input['salary_owner'] ?? '');


try {
    // 🔹 Met à jour les infos de base de l'utilisateur
    $stmt = $db->prepare("
        UPDATE user
        SET 
            lastName_user = ?, 
            firstName_user = ?, 
            birthdate_user = ?, 
            gender_user = ?, 
            poids_user = ?, 
            taille_user = ?, 
            streetNameAddress_user = ?, 
            numeroAddress_user = ?, 
            postBoxAddress_user = ?, 
            postalCode_user = ?, 
            town_user = ?, 
            telephone_user = ?, 
            emailAddress_user = ?
        WHERE id_user = ?
    ");
    $stmt->bind_param(
        "ssssddsssssssi",
        $lastName_user,
        $firstName_user,
        $birthdate_user,
        $gender_user,
        $poids_user,
        $taille_user,
        $streetNameAddress_user,
        $numeroAddress_user,
        $postBoxAddress_user,
        $postalCode_user,
        $town_user,
        $telephone_user,
        $emailAddress_user,
        $id_user
    );
    $stmt->execute();
    $stmt->close();

    // 🔹 Vérifie si l'utilisateur est déjà Coach / Owner
    $stmt = $db->prepare("
        SELECT 
            u.id_user,
            c.id_coach,
            c.actif_coach,
            o.id_owner
        FROM user u
        LEFT JOIN coach c ON c.id_user = u.id_user
        LEFT JOIN owner o ON o.id_user = u.id_user
        WHERE u.id_user = ?
    ");
    $stmt->bind_param("i", $id_user);
    $stmt->execute();
    $result = $stmt->get_result();
    $userRow = $result->fetch_assoc();
    $stmt->close();

    $isAlreadyCoach = !empty($userRow['id_coach']);
    $isAlreadyOwner = !empty($userRow['id_owner']);

    // ----------------------------------------------------
    // 🔹 Gestion du statut Coach
    // ----------------------------------------------------
    if ($isCoach) {
        if ($isAlreadyCoach) {
            // L'utilisateur est déjà coach → on update
            $stmt = $db->prepare("
                UPDATE coach
                SET 
                    actualLevel_coach = ?,
                    salary_coach = ?,
                    specialisation_coach = ?,
                    actif_coach = 1
                WHERE id_user = ?
            ");
            $stmt->bind_param("sdsi", $actualLevel_coach, $salary_coach, $specialisation_coach, $id_user);
            $stmt->execute();
            $stmt->close();
        } else {
            // Il n'est pas coach → on crée une entrée
            $stmt = $db->prepare("
                INSERT INTO coach (id_user, actualLevel_coach, actif_coach, salary_coach, specialisation_coach)
                VALUES (?, ?, 1, ?, ?)
            ");
            $stmt->bind_param("isds", $id_user, $actualLevel_coach, $salary_coach, $specialisation_coach);
            $stmt->execute();
            $stmt->close();
        }
    } else {
        // Si décoché et qu'il était coach → on désactive
        if ($isAlreadyCoach) {
            $stmt = $db->prepare("UPDATE coach SET actif_coach = 0 WHERE id_user = ?");
            $stmt->bind_param("i", $id_user);
            $stmt->execute();
            $stmt->close();
        }
    }

    // ----------------------------------------------------
    // 🔹 Gestion du statut Owner
    // ----------------------------------------------------
    if ($isOwner) {
        if (!$isAlreadyOwner) {
            // Créer une entrée Owner
            $stmt = $db->prepare("
                INSERT INTO owner (id_user, email_owner, telephone_owner, salary_owner)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->bind_param("issd", $id_user, $email_owner, $telephone_owner, $salary_owner);
            $stmt->execute();
            $stmt->close();
        } else {
            // Déjà owner → on met à jour les infos
            $stmt = $db->prepare("
                UPDATE owner
                SET email_owner = ?, telephone_owner = ?, salary_owner = ?
                WHERE id_user = ?
            ");
            $stmt->bind_param("ssdi", $email_owner, $telephone_owner, $salary_owner, $id_user);
            $stmt->execute();
            $stmt->close();
        }
    } else {
        // Si décoché et qu'il est owner → on supprime
        if ($isAlreadyOwner && $id_user != $authOwnerId) {
            $stmt = $db->prepare("DELETE FROM owner WHERE id_user = ?");
            $stmt->bind_param("i", $id_user);
            $stmt->execute();
            $stmt->close();
        } elseif ($id_user == $authOwnerId) {
            // Empêche un owner de se retirer lui-même
            echo json_encode([
                'success' => false,
                'message' => 'Un owner ne peut pas retirer son propre rôle.'
            ]);
            exit;
        }
    }

    echo json_encode(['success' => true, 'message' => 'Utilisateur mis à jour avec succès']);
} catch (Exception $e) {
    error_log("Erreur updateUser : " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erreur serveur']);
}

?>
