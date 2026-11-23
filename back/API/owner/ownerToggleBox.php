<?php
header("Content-Type: application/json");
include "../../check_auth.php";

if (!$authOwnerId) {
   echo json_encode(["error" => "Accès interdit"]);
   exit;
}

$input = json_decode(file_get_contents("php://input"), true);
$id = filter_var($input["id_box"] ?? null, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]]);
if (!$id) {
    http_response_code(400);
    exit(json_encode(["error" => "Salle invalide"]));
}

$stmt = $db->prepare("UPDATE box SET actif_box = 1 - actif_box WHERE id_box = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

echo json_encode(["message" => "Statut modifié"]);