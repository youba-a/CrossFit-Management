<?php
header('Content-Type: application/json');

include "../../check_auth.php";

// Vérification de l'authentification
if (!$authOwnerId) {
   http_response_code(401);
   echo json_encode(['error' => 'Non authentifié']);
   exit;
}

require_once __DIR__ . '/mailer.php';

try {
    // Decode incoming JSON data
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
        echo json_encode(['success' => false, 'message' => 'Invalid JSON received']);
        exit;
    }

    // Extract and sanitize
    $prenom = trim($input['prenom'] ?? '');
    $nom = trim($input['nom'] ?? '');
    $dob = $input['dob'] ?? '';
    $genre = $input['genre'] ?? '';
    $email = trim($input['email'] ?? '');
    $street = trim($input['street'] ?? '');
    $numero = trim($input['numero'] ?? '');
    $boite = trim($input['boite'] ?? '');
    $postalcode = trim($input['postalcode'] ?? '');
    $town = trim($input['town'] ?? '');

    $birthDate = is_string($dob) ? DateTimeImmutable::createFromFormat('!Y-m-d', $dob) : false;
    if (!$prenom || !$nom || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 60
        || !$birthDate || $birthDate->format('Y-m-d') !== $dob || $birthDate > new DateTimeImmutable('today')) {
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        exit;
    }

    // Check if email already exists
    $stmt = $db->prepare("SELECT id_user FROM user WHERE emailAddress_user = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->fetch_assoc()) {
        echo json_encode(['success' => false, 'message' => 'Cet utilisateur existe déjà.']);
        exit;
    }
    $stmt->close();

    // Create token
    $token = bin2hex(random_bytes(32));
    $tokenHash = hash('sha256', $token);

    // Insert new user - CORRECTED VERSION
    if ($boite === '') {
    $boite = null;
}

    $insert = $db->prepare("
        INSERT INTO user (
            lastName_user, firstName_user, birthdate_user, gender_user,
            streetNameAddress_user, numeroAddress_user, postBoxAddress_user,
            postalCode_user, town_user, emailAddress_user,
            tokenInvitation_user, dateInvitation_user, dateCreation_user,
            wound_user, actualLevel_user, notesCoach_user
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW(), '', '', '')
    ");

    $insert->bind_param(
        "sssssssssss",  // 11 strings (not 12)
        $nom, $prenom, $dob, $genre,
        $street, $numero, $boite,
        $postalcode, $town, $email,
        $tokenHash
    );


    $insertSuccess = $insert->execute();

    if (!$insertSuccess) {
        error_log("MySQL Error: " . $db->error);
        throw new Exception("Failed to insert user: " . $db->error);
    }
    
    $insert->close();

    // Send invitation email
    $link = APP_BASE_URL . '/index.php?p=memberRegister&token=' . $token;
    $sent = sendInvitationEmail($email, "$prenom $nom", $link);

    echo json_encode([
        'success' => $sent,
        'message' => $sent
            ? 'Invitation envoyée avec succès.'
            : 'Utilisateur ajouté, mais erreur lors de l\'envoi de l\'e-mail.'
    ]);

} catch (Throwable $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erreur serveur'
    ]);
}
