<?php
// back/API/subscription/initiatePayment.php
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

// lecture JSON
$input = json_decode(file_get_contents("php://input"), true) ?? [];

$id_abonnement = isset($input['id_abonnement']) ? intval($input['id_abonnement']) : 0;
$subscriptionBeginRaw = is_string($input['subscriptionBegin'] ?? null) ? trim($input['subscriptionBegin']) : '';
$id_box = isset($input['id_box']) ? intval($input['id_box']) : 0;

// ---------------------------
//  NOUVEAU : validation id_box
// ---------------------------

if ($id_box <= 0) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Box invalide"]);
    exit;
}

// Vérifier si la box existe et est active
$stmtBox = $db->prepare("SELECT id_box, name_box FROM box WHERE id_box = ? AND actif_box = 1 LIMIT 1");
$stmtBox->bind_param("i", $id_box);
$stmtBox->execute();
$resBox = $stmtBox->get_result();

if ($resBox->num_rows === 0) {
    http_response_code(404);
    echo json_encode(["success" => false, "message" => "Box introuvable ou inactive"]);
    exit;
}

$box = $resBox->fetch_assoc();
$stmtBox->close();

// ---------------------------------------

if ($id_abonnement <= 0) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Identifiant d'abonnement invalide"]);
    exit;
}
if (empty($subscriptionBeginRaw)) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Date de début requise"]);
    exit;
}

// Valider date format YYYY-MM-DD
$dt = DateTime::createFromFormat('!Y-m-d', $subscriptionBeginRaw);
$dt_errors = DateTime::getLastErrors() ?: ['warning_count' => 0, 'error_count' => 0];

if (!$dt || ($dt_errors['warning_count'] + $dt_errors['error_count']) > 0) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Format de date invalide"]);
    exit;
}
$subscriptionBegin = $dt->format('Y-m-d');

// comparer à today et à la dernière subscription end si existante
$today = new DateTimeImmutable('today');
$minDate = $today; // par défaut

// récupérer la dernière subscription end
$stmt = $db->prepare("SELECT subscriptionEnd_boxuser FROM boxuser WHERE idUser_boxuser = ? ORDER BY subscriptionEnd_boxuser DESC LIMIT 1");
$stmt->bind_param("i", $authUserId);
$stmt->execute();
$res = $stmt->get_result();
if ($res && $res->num_rows > 0) {
    $row = $res->fetch_assoc();
    if (!empty($row['subscriptionEnd_boxuser'])) {
        $lastEnd = DateTime::createFromFormat('!Y-m-d', $row['subscriptionEnd_boxuser']);
        if ($lastEnd !== false) {
            $lastEndPlus = (clone $lastEnd)->modify('+1 day');
            if ($lastEndPlus > $minDate) $minDate = $lastEndPlus;
        }
    }
}
$stmt->close();

$chosen = DateTime::createFromFormat('!Y-m-d', $subscriptionBegin);
if ($chosen < $minDate) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "La date choisie doit être au minimum " . $minDate->format('Y-m-d')]);
    exit;
}

// récupérer l'abonnement pour prix + nom
$stmt2 = $db->prepare("SELECT id_abonnement, nom_abonnement, prix_abonnement FROM abonnements WHERE id_abonnement = ? AND inactive_abonnement = 0 LIMIT 1");
$stmt2->bind_param("i", $id_abonnement);
$stmt2->execute();
$r2 = $stmt2->get_result();
if ($r2->num_rows === 0) {
    http_response_code(404);
    echo json_encode(["success" => false, "message" => "Abonnement introuvable ou inactif"]);
    exit;
}
$ab = $r2->fetch_assoc();
$stmt2->close();

// calcul date de fin (1 an - 1 jour)
$beginDate = DateTime::createFromFormat('!Y-m-d', $subscriptionBegin);
$endDate = (clone $beginDate)->modify('+1 year')->modify('-1 day');

// Préparer description/payload
$amount = (float)$ab['prix_abonnement'];
$description = sprintf(
    "Achat abonnement %s (id %d) — Début %s — Fin %s — userId %d — Box %d (%s)",
    $ab['nom_abonnement'],
    $ab['id_abonnement'],
    $beginDate->format('Y-m-d'),
    $endDate->format('Y-m-d'),
    $authUserId,
    $box['id_box'],
    $box['name_box']
);

try {
    require_once __DIR__ . '/../payment/StripeProvider.php';
    require_once __DIR__ . '/../payment/PaymentService.php';

    $provider = new StripeProvider($authUserId);
    $payment = new PaymentService($provider);

    // ---------------------------------------
    // AJOUT DE L'ARGUMENT ID_BOX
    // ---------------------------------------
    $result = $payment->pay($amount, $description, $id_abonnement, $subscriptionBegin, $id_box);

    if (!is_array($result) || empty($result['redirectUrl'])) {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Erreur lors de l'initialisation du paiement (pas d'URL de redirection fournie)"]);
        exit;
    }

    echo json_encode([
        "success" => true,
        "redirectUrl" => $result['redirectUrl']
    ]);
    exit;

} catch (Exception $e) {
    error_log("Payment init error: " );
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Erreur lors de l'initialisation du paiement : " ]);
    exit;
}
