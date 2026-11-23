<?php
header("Content-Type: application/json; charset=utf-8");
include "../../check_auth.php";

// Vérification authentification
if (!$authOwnerId) {
    http_response_code(401);
    echo json_encode(["error" => "Accès interdit"]);
    exit;
}

// Récupération du JSON
$input = json_decode(file_get_contents("php://input"), true);

if (!$input) {
    echo json_encode(["error" => "Invalid JSON received"]);
    exit;
}

// Nettoyage des données
$id_box        = intval($input["id_box"] ?? 0);
$name          = trim($input["name_box"] ?? "");
$street        = trim($input["streetNameAddress_box"] ?? "");
$numero        = trim($input["numeroAddress_box"] ?? "");
$postbox       = trim($input["postBoxAddress_box"] ?? "");
$postalCode    = trim($input["postalCode_box"] ?? "");
$town          = trim($input["town_box"] ?? "");
$telephone     = trim($input["telephone_box"] ?? "");
$numberPeople  = trim($input["numberPeople_box"] ?? "");

// Vérification des champs obligatoires
if (!$id_box) {
    echo json_encode(["error" => "ID de box manquant"]);
    exit;
}

$missing = [];

if (!$name)         $missing[] = "name_box";
if (!$street)       $missing[] = "streetNameAddress_box";
if (!$numero)       $missing[] = "numeroAddress_box";
if (!$postalCode)   $missing[] = "postalCode_box";
if (!$town)         $missing[] = "town_box";
if (!$telephone)    $missing[] = "telephone_box";
if (!$numberPeople) $missing[] = "numberPeople_box";

// Message si champs manquants
if (!empty($missing)) {
    echo json_encode([
        "error" => "Certains champs obligatoires sont manquants",
        "missing_fields" => $missing
    ]);
    exit;
}

// Vérification que numberPeople est un chiffre valide
if (!ctype_digit($numberPeople)) {
    echo json_encode(["error" => "La capacité (numberPeople_box) doit être un nombre entier"]);
    exit;
}

try {

    $stmt = $db->prepare("
        UPDATE box SET
            name_box = ?, 
            streetNameAddress_box = ?,
            numeroAddress_box = ?,
            postBoxAddress_box = ?,
            postalCode_box = ?,
            town_box = ?,
            telephone_box = ?,
            numberPeople_box = ?
        WHERE id_box = ?
    ");

    $stmt->bind_param(
        "sssssssii",
        $name,
        $street,
        $numero,
        $postbox,
        $postalCode,
        $town,
        $telephone,
        $numberPeople,
        $id_box
    );

    $stmt->execute();

    if ($stmt->affected_rows === 0) {
        echo json_encode(["error" => "Aucune modification effectuée (ID inexistant?)"]);
        exit;
    }

    echo json_encode(["message" => "Box modifiée avec succès"]);

} catch (Exception $e) {

    error_log("Erreur updateBox : " . $e->getMessage());

    http_response_code(500);
    echo json_encode(["error" => "Erreur serveur"]);
}

$stmt->close();
$db->close();
