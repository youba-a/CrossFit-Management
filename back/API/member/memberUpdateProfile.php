<?php
header('Content-Type: application/json; charset=utf-8');
include "../../check_auth.php"; // doit définir $authUserId, $authCoachId, $authOwnerId et $db (mysqli)

if (!$authUserId) {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "Non authentifié"]);
    exit;
}

if (!isset($db) || $db->connect_errno) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Erreur de connexion à la base de données"]);
    exit;
}

// --- Récupération des données ---
$input = [];
$contentType = $_SERVER['CONTENT_TYPE'] ?? '';
if (stripos($contentType, 'application/json') !== false) {
    $input = json_decode(file_get_contents("php://input"), true) ?? [];
} else {
    $input = $_POST;
}

// --- Normalisation et extraction ---
$poids = isset($input['poids_user']) && $input['poids_user'] !== '' ? floatval($input['poids_user']) : null;
$taille = isset($input['taille_user']) && $input['taille_user'] !== '' ? intval($input['taille_user']) : null;
$street = trim($input['streetNameAddress_user'] ?? '');
$numero = trim($input['numeroAddress_user'] ?? '');
$postbox = trim($input['postBoxAddress_user'] ?? '');
$postal = trim($input['postalCode_user'] ?? '');
$town = trim($input['town_user'] ?? '');
$telephone1 = trim($input['telephone_user'] ?? '');
$telephone2 = trim($input['firstContactTelephone_user'] ?? '');
$telephone3 = trim($input['secondContactTelephone_user'] ?? '');
$wound = trim($input['wound_user'] ?? '');
$password = trim($input['password_user'] ?? '');
$email = trim($input['emailAddress_user'] ?? '');
$specialisation = trim($input['specialisation_coach'] ?? '');
$ownerEmail = trim($input['email_owner'] ?? '');
$ownerTel = trim($input['telephone_owner'] ?? '');
$ownerSalary = isset($input['salary_owner']) && $input['salary_owner'] !== '' ? floatval($input['salary_owner']) : null;

// --- Validations basiques ---
if ($poids !== null && ($poids <= 0 || $poids > 500)) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Poids invalide"]);
    exit;
}
if ($taille !== null && ($taille <= 0 || $taille > 250)) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Taille invalide"]);
    exit;
}

$telephoneRegex = '/^[0-9+]{6,20}$/';
foreach (['telephone1' => $telephone1, 'telephone2' => $telephone2, 'telephone3' => $telephone3] as $key => $val) {
    if (!empty($val) && !preg_match($telephoneRegex, $val)) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Numéro de téléphone invalide ($key)"]);
        exit;
    }
}

if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Adresse e-mail invalide"]);
    exit;
}

if (!empty($ownerEmail) && !filter_var($ownerEmail, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Email propriétaire invalide"]);
    exit;
}

if (!empty($ownerTel) && !preg_match($telephoneRegex, $ownerTel)) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Téléphone propriétaire invalide"]);
    exit;
}

// --- Vérifier utilisateur ---
$check = $db->prepare("SELECT id_user, avatar_user FROM user WHERE id_user = ?");
$check->bind_param("i", $authUserId);
$check->execute();
$res = $check->get_result();
if ($res->num_rows === 0) {
    http_response_code(403);
    echo json_encode(["success" => false, "message" => "Utilisateur introuvable"]);
    exit;
}
$currentUser = $res->fetch_assoc();
$oldAvatarFilename = $currentUser['avatar_user'] ?? null;
$check->close();

// --- Vérifier unicité e-mail ---
if (!empty($email)) {
    $q = $db->prepare("SELECT id_user FROM user WHERE emailAddress_user = ? AND id_user != ?");
    $q->bind_param("si", $email, $authUserId);
    $q->execute();
    $r = $q->get_result();
    if ($r->num_rows > 0) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Cette adresse e-mail est déjà utilisée par un autre compte"]);
        exit;
    }
    $q->close();
}

// --- Upload avatar ---
$avatarField = 'avatar_file';
$avatarToSaveFilename = null;
if (isset($_FILES[$avatarField]) && $_FILES[$avatarField]['error'] !== UPLOAD_ERR_NO_FILE) {
    $file = $_FILES[$avatarField];
    if ($file['error'] !== UPLOAD_ERR_OK) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Erreur lors de l'upload de l'avatar (code {$file['error']})"]);
        exit;
    }

    $maxSize = 2 * 1024 * 1024;
    if ($file['size'] > $maxSize) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Avatar trop volumineux (max 2MB)"]);
        exit;
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    $allowed = [
        'image/jpeg' => '.jpg',
        'image/pjpeg' => '.jpg',
        'image/png' => '.png',
        'image/gif' => '.gif',
        'image/webp' => '.webp'
    ];
    if (!isset($allowed[$mime])) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Type d'image non autorisé"]);
        exit;
    }

    try {
        $random = bin2hex(random_bytes(16));
    } catch (Exception $e) {
        $random = time() . '_' . bin2hex(random_bytes(8));
    }
    $ext = $allowed[$mime];
    $avatarToSaveFilename = $random . $ext;

    $uploadDir = rtrim($_SERVER['DOCUMENT_ROOT'] ?? __DIR__, '/') . '/img/avatar';
    if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true)) {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Impossible de créer le dossier des avatars"]);
        exit;
    }

    $destPath = $uploadDir . '/' . $avatarToSaveFilename;
    if (!move_uploaded_file($file['tmp_name'], $destPath)) {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Impossible d'enregistrer l'avatar"]);
        exit;
    }
    @chmod($destPath, 0644);
}

// --- Transaction mise à jour ---
$db->begin_transaction();
try {
    $fields = [];
    $types = '';
    $values = [];

    $fields[] = "u.poids_user = ?";
    $types .= 'd';
    $values[] = $poids;

    $fields[] = "u.taille_user = ?";
    $types .= 'i';
    $values[] = $taille;

    $fields[] = "u.streetNameAddress_user = ?";
    $types .= 's';
    $values[] = $street;

    $fields[] = "u.numeroAddress_user = ?";
    $types .= 's';
    $values[] = $numero;

    $fields[] = "u.postBoxAddress_user = ?";
    $types .= 's';
    $values[] = $postbox;

    $fields[] = "u.postalCode_user = ?";
    $types .= 's';
    $values[] = $postal;

    $fields[] = "u.town_user = ?";
    $types .= 's';
    $values[] = $town;

    $fields[] = "u.telephone_user = ?";
    $types .= 's';
    $values[] = $telephone1;

    $fields[] = "u.firstContactTelephone_user = ?";
    $types .= 's';
    $values[] = $telephone2;

    $fields[] = "u.secondContactTelephone_user = ?";
    $types .= 's';
    $values[] = $telephone3;

    $fields[] = "u.wound_user = ?";
    $types .= 's';
    $values[] = $wound;

    if (!empty($email)) {
        $fields[] = "u.emailAddress_user = ?";
        $types .= 's';
        $values[] = $email;
    }

    if (!empty($password)) {
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        $fields[] = "u.password_user = ?";
        $types .= 's';
        $values[] = $passwordHash;
    }

    if ($avatarToSaveFilename !== null) {
        $fields[] = "u.avatar_user = ?";
        $types .= 's';
        $values[] = $avatarToSaveFilename;
    }

    $sql = "UPDATE user AS u SET " . implode(",\n", $fields) . " WHERE u.id_user = ? LIMIT 1";
    $types .= 'i';
    $values[] = $authUserId;

    $stmt = $db->prepare($sql);
    if (!$stmt) {
        throw new Exception("Erreur de préparation de la requête (user): " . $db->error);
    }

    $bind_names[] = $types;
    for ($i = 0; $i < count($values); $i++) {
        $bind_names[] = &$values[$i];
    }
    call_user_func_array([$stmt, 'bind_param'], $bind_names);
    if (!$stmt->execute()) {
        throw new Exception("Erreur exécution update user: " . $stmt->error);
    }
    $stmt->close();

    if ($avatarToSaveFilename !== null && !empty($oldAvatarFilename)) {
        $oldPath = rtrim($_SERVER['DOCUMENT_ROOT'] ?? __DIR__, '/') . '/img/avatar/' . basename($oldAvatarFilename);
        if (is_file($oldPath)) {
            @unlink($oldPath);
        }
    }

    if ($authCoachId && !empty($specialisation)) {
        $sqlCoach = "UPDATE coach AS c SET c.specialisation_coach = ? WHERE c.id_user = ? LIMIT 1";
        $stmtCoach = $db->prepare($sqlCoach);
        if (!$stmtCoach) throw new Exception("Erreur préparation coach: " . $db->error);
        $stmtCoach->bind_param("si", $specialisation, $authUserId);
        if (!$stmtCoach->execute()) throw new Exception("Erreur update coach: " . $stmtCoach->error);
        $stmtCoach->close();
    }

    if ($authOwnerId && (!empty($ownerEmail) || !empty($ownerTel) || $ownerSalary !== null)) {
        $ownerFields = [];
        $ownerTypes = '';
        $ownerValues = [];

        if (!empty($ownerEmail)) {
            $ownerFields[] = "o.email_owner = ?";
            $ownerTypes .= 's';
            $ownerValues[] = $ownerEmail;
        }
        if (!empty($ownerTel)) {
            $ownerFields[] = "o.telephone_owner = ?";
            $ownerTypes .= 's';
            $ownerValues[] = $ownerTel;
        }
        if ($ownerSalary !== null && $ownerSalary >= 0) {
            $ownerFields[] = "o.salary_owner = ?";
            $ownerTypes .= 'd';
            $ownerValues[] = $ownerSalary;
        }

        $sqlOwner = "UPDATE owner AS o SET " . implode(", ", $ownerFields) . " WHERE o.id_user = ? LIMIT 1";
        $ownerTypes .= 'i';
        $ownerValues[] = $authUserId;

        $stmtOwner = $db->prepare($sqlOwner);
        if (!$stmtOwner) throw new Exception("Erreur préparation owner: " . $db->error);

        $bind_owner = [$ownerTypes];
        foreach ($ownerValues as $k => $v) $bind_owner[] = &$ownerValues[$k];
        call_user_func_array([$stmtOwner, 'bind_param'], $bind_owner);
        if (!$stmtOwner->execute()) throw new Exception("Erreur update owner: " . $stmtOwner->error);
        $stmtOwner->close();
    }

    $db->commit();
    echo json_encode(["success" => true, "message" => "Profil mis à jour avec succès"]);
    $db->close();
    exit;

} catch (Exception $e) {
    $db->rollback();
    if (!empty($avatarToSaveFilename)) {
        $maybe = rtrim($_SERVER['DOCUMENT_ROOT'] ?? __DIR__, '/') . '/img/avatar/' . basename($avatarToSaveFilename);
        if (is_file($maybe)) @unlink($maybe);
    }

    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => 'Erreur serveur'
    ]);
    $db->close();
    exit;
}
