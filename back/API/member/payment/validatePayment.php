<?php

header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../../../check_auth.php';
require_once __DIR__ . '/StripeProvider.php';

if (!$authUserId) {
    http_response_code(401);
    exit(json_encode(['success' => false, 'message' => 'Non authentifie']));
}

$input = json_decode(file_get_contents('php://input'), true);
$paymentId = $input['paymentId'] ?? null;
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || ($input['provider'] ?? null) !== 'Stripe'
    || !is_string($paymentId) || strlen($paymentId) > 255
    || !preg_match('/^cs_(test|live)_[a-zA-Z0-9]+$/D', $paymentId)) {
    http_response_code(400);
    exit(json_encode(['success' => false, 'message' => 'Paiement invalide']));
}

try {
    $session = (new StripeProvider($authUserId))->getSession($paymentId);
    if ($session->payment_status !== 'paid' || $session->mode !== 'payment'
        || $session->currency !== 'eur' || $session->client_reference_id !== (string) $authUserId) {
        http_response_code(403);
        exit(json_encode(['success' => false, 'message' => 'Paiement non confirme pour ce compte']));
    }

    $idAbonnement = (int) ($session->metadata->id_abonnement ?? 0);
    $idBox = (int) ($session->metadata->id_box ?? 0);
    $dateBegin = (string) ($session->metadata->subscription_begin ?? '');
    $begin = DateTimeImmutable::createFromFormat('!Y-m-d', $dateBegin);
    if (!$idAbonnement || !$idBox || !$begin || $begin->format('Y-m-d') !== $dateBegin) {
        http_response_code(400);
        exit(json_encode(['success' => false, 'message' => 'Informations de paiement invalides']));
    }
    $dateEnd = $begin->modify('+1 year')->modify('-1 day')->format('Y-m-d');

    $db->begin_transaction();

    // Serialize confirmations for the paying account, including on existing schemas.
    $lock = $db->prepare('SELECT id_user FROM user WHERE id_user = ? FOR UPDATE');
    $lock->bind_param('i', $authUserId);
    $lock->execute();
    $lock->close();

    $check = $db->prepare('SELECT idUser_boxuser FROM boxuser WHERE idPayment_boxuser = ?');
    $check->bind_param('s', $paymentId);
    $check->execute();
    $existing = $check->get_result()->fetch_assoc();
    $check->close();

    if ($existing && (int) $existing['idUser_boxuser'] !== (int) $authUserId) {
        $db->rollback();
        http_response_code(403);
        exit(json_encode(['success' => false, 'message' => 'Paiement deja associe']));
    }

    if (!$existing) {
        $insert = $db->prepare('INSERT INTO boxuser
            (idUser_boxuser, idBox_boxuser, idAbonnement_boxuser, datePayment_boxuser,
             subscriptionBegin_boxuser, subscriptionEnd_boxuser, idPayment_boxuser)
            VALUES (?, ?, ?, CURRENT_DATE, ?, ?, ?)');
        $insert->bind_param('iiisss', $authUserId, $idBox, $idAbonnement, $dateBegin, $dateEnd, $paymentId);
        $insert->execute();
        $insert->close();
    }

    $db->commit();
    echo json_encode(['success' => true, 'message' => 'Paiement valide et abonnement active']);
} catch (Throwable $e) {
    $db->rollback();
    error_log('CrossFit: erreur de validation Stripe.');
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Validation du paiement indisponible']);
}
