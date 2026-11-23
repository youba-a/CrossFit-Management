<?php
// back/API/subscription/getSubscriptionInfo.php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../../../check_auth.php'; // doit définir $authUserId et $db

if (!$authUserId) {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "Non authentifié"]);
    exit;
}
if (!isset($db) || $db->connect_errno) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Erreur DB"]);
    exit;
}

$id_abonnement = isset($_GET['id_abonnement']) ? intval($_GET['id_abonnement']) : 0;
if ($id_abonnement <= 0) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Identifiant d'abonnement invalide"]);
    exit;
}

// Récupérer l'abonnement
$stmt = $db->prepare("SELECT id_abonnement, nom_abonnement, description_abonnement, prix_abonnement, inactive_abonnement FROM abonnements WHERE id_abonnement = ? LIMIT 1");
$stmt->bind_param("i", $id_abonnement);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows === 0) {
    http_response_code(404);
    echo json_encode(["success" => false, "message" => "Abonnement introuvable"]);
    exit;
}
$ab = $res->fetch_assoc();
$stmt->close();

// Récupérer la date de fin de la dernière souscription de l'utilisateur (si existante)
// On prend la plus grande subscriptionEnd_boxuser (la plus récente)
$q = $db->prepare("SELECT subscriptionEnd_boxuser FROM boxuser WHERE idUser_boxuser = ? ORDER BY subscriptionEnd_boxuser DESC LIMIT 1");
$q->bind_param("i", $authUserId);
$q->execute();
$r = $q->get_result();
$lastEnd = null;
if ($r->num_rows > 0) {
    $row = $r->fetch_assoc();
    // format yyyy-mm-dd
    $lastEnd = $row['subscriptionEnd_boxuser'];
}
$q->close();

echo json_encode([
    "success" => true,
    "abonnement" => $ab,
    "lastSubscriptionEnd" => $lastEnd // peut être null
]);
exit;
