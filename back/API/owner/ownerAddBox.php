<?php
header("Content-Type: application/json; charset=utf-8");
include "../../check_auth.php";

if (!$authOwnerId) {
    http_response_code(401);
    echo json_encode(["error" => "Non authentifié"]);
    exit;
}

$input = json_decode(file_get_contents("php://input"), true);

// Récupération + nettoyage des données
$name                  = trim($input["name_box"] ?? "");
$street                = trim($input["streetNameAddress_box"] ?? "");
$numero                = trim($input["numeroAddress_box"] ?? "");
$postbox               = trim($input["postBoxAddress_box"] ?? "");
$postalCode            = trim($input["postalCode_box"] ?? "");
$town                  = trim($input["town_box"] ?? "");
$telephone             = trim($input["telephone_box"] ?? "");
$numberPeople          = trim($input["numberPeople_box"] ?? "");

// Vérification des champs obligatoires
if (!$name || !$street || !$numero || !$postalCode || !$town || !$telephone || !$numberPeople) {
    echo json_encode(["error" => "Certains champs obligatoires sont manquants"]);
    exit;
}

// Vérification que numberPeople est un chiffre valide
if (!ctype_digit($numberPeople)) {
    echo json_encode(["error" => "La capacité (numberPeople_box) doit être un nombre entier"]);
    exit;
}

// Requête SQL
$stmt = $db->prepare("
    INSERT INTO box (
        name_box,
        streetNameAddress_box,
        numeroAddress_box,
        postBoxAddress_box,
        postalCode_box,
        town_box,
        telephone_box,
        numberPeople_box,
        actif_box
    )
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)
");

$stmt->bind_param(
    "ssssssss",
    $name,
    $street,
    $numero,
    $postbox,
    $postalCode,
    $town,
    $telephone,
    $numberPeople
);

$stmt->execute();

echo json_encode([
    "success" => true,
    "id_box" => $stmt->insert_id
]);

$stmt->close();
$db->close();
