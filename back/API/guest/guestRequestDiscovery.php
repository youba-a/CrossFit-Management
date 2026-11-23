<?php
header('Content-Type: application/json; charset=utf-8');
include '../../../config/db.php';


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["error" => "Méthode non autorisée"]);
    exit;
} 

if (!isset($db) || $db->connect_errno) {
    http_response_code(500);
    echo json_encode(["error" => "Erreur de connexion à la base de données"]);
    exit;
}


$data = json_decode(file_get_contents("php://input"), true);

$nom = trim($data['nom_discovery'] ?? '');
$prenom = trim($data['prenom_discovery'] ?? '');
$email = trim($data['email_discovery'] ?? '');
$telephone = trim($data['telephone_discovery'] ?? '');
$note = trim($data['note_discovery'] ?? '');
$idOrganisation = intval($data['idOrganisation_discovery'] ?? 0);

$ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
$userAgent = substr($_SERVER['HTTP_USER_AGENT'] ?? 'Inconnu', 0, 50);




// Champs obligatoires
if (empty($nom) || empty($prenom) || empty($email) || empty($telephone) || $idOrganisation <= 0) {
    http_response_code(400);
    echo json_encode(["error" => "Veuillez remplir tous les champs requis."]);
    exit;
}

// Email valide ?
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(["error" => "Adresse e-mail invalide"]);
    exit;
}

// Téléphone valide ?
if (!preg_match('/^[0-9+\s\-]{6,20}$/', $telephone)) {
    http_response_code(400);
    echo json_encode(["error" => "Numéro de téléphone invalide"]);
    exit;
}

// Vérifier si l'organisation existe ET autorise les invités
$queryCheckOrg = $db->prepare("
    SELECT o.id_organisation
    FROM organisation o
    INNER JOIN activities a ON o.id_activities = a.id_activities
    INNER JOIN typeactivities t ON a.id_typeActivities = t.id_typeActivities
    WHERE o.id_organisation = ? AND t.allowGuest_typeActivity = 1
");
$queryCheckOrg->bind_param("i", $idOrganisation);
$queryCheckOrg->execute();
$resOrg = $queryCheckOrg->get_result();

if ($resOrg->num_rows === 0) {
    http_response_code(403);
    echo json_encode(["error" => "Cette séance ne permet pas les essais invités."]);
    $queryCheckOrg->close();
    exit;
}
$queryCheckOrg->close();

// Vérifier si le même email OU la même IP a déjà fait une demande récente
$check = $db->prepare("
    SELECT id_discovery 
    FROM discovery
    WHERE email_discovery = ? OR adresseIP_discovery = ? OR telephone_discovery = ?
");

$check->bind_param("sss", $email, $ip, $telephone);
$check->execute();
$res = $check->get_result();

if ($res->num_rows > 0) {
    http_response_code(429);
    echo json_encode(["error" => "Vous avez déjà effectué une demande récemment."]);
    $check->close();
    exit;
}
$check->close();


$stmt = $db->prepare("
    INSERT INTO discovery (
        idOrganisation_discovery,
        nom_discovery,
        prenom_discovery,
        email_discovery,
        telephone_discovery,
        note_discovery,
        adresseIP_discovery,
        webBrowser_discovery
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
");

if (!$stmt) {
    http_response_code(500);
    echo json_encode(["error" => "Erreur de préparation SQL"]);
    exit;
}

$stmt->bind_param(
    "isssssss",
    $idOrganisation,
    $nom,
    $prenom,
    $email,
    $telephone,
    $note,
    $ip,
    $userAgent
);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Votre demande d’essai gratuit a bien été enregistrée !"]);
} else {
    http_response_code(500);
    echo json_encode(["error" => "Erreur lors de l’enregistrement de la demande."]);
}

$stmt->close();
$db->close();
?>
