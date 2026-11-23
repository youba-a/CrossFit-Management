<?php
header('Content-Type: application/json; charset=utf-8');
include "../../check_auth.php"; 

// --- Authentification ---
if (!$authUserId) {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "Non authentifié"]);
    exit;
}

// --- Connexion DB ---
if (!isset($db) || $db->connect_errno) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Erreur de connexion à la base de données"]);
    exit;
}

// --- Récupération des abonnements de l'utilisateur ---
$sql = "
    SELECT 
        a.id_abonnement,
        a.nom_abonnement,
        a.description_abonnement,
        a.prix_abonnement,
        bu.datePayment_boxuser,
        bu.subscriptionBegin_boxuser,
        bu.subscriptionEnd_boxuser,
        b.name_box,
        b.streetNameAddress_box,
        b.numeroAddress_box,
        b.postBoxAddress_box,
        b.telephone_box
    FROM boxuser AS bu
    INNER JOIN abonnements AS a ON bu.idAbonnement_boxuser = a.id_abonnement
    INNER JOIN box AS b ON bu.idBox_boxuser = b.id_box
    WHERE bu.idUser_boxuser = ?
    ORDER BY bu.subscriptionEnd_boxuser DESC
";

$stmt = $db->prepare($sql);
$stmt->bind_param("i", $authUserId);
$stmt->execute();
$result = $stmt->get_result();

// --- Vérifie s’il y a des abonnements ---
if ($result->num_rows === 0) {
    echo json_encode([
        "success" => true,
        "data" => [],
        "message" => "Aucun abonnement trouvé"
    ]);
    exit;
}

// --- Prépare la réponse ---
$today = new DateTime();
$subscriptions = [];

while ($subscription = $result->fetch_assoc()) {
    // Détermination du statut (actif ou expiré)
    $statut = 'expiré';
    try {
        if (!empty($subscription['subscriptionEnd_boxuser'])) {
            $subscriptionEndDate = new DateTime($subscription['subscriptionEnd_boxuser']);
            if ($subscriptionEndDate >= $today) {
                $statut = 'actif';
            }
        }
    } catch (Exception $e) {
        // date invalide -> statut par défaut
    }
    $subscription['statut'] = $statut;
    
    // Ajoute à la liste
    $subscriptions[] = $subscription;
}

$stmt->close();
$db->close();

// --- Retour JSON ---
echo json_encode([
    "success" => true,
    "data" => $subscriptions
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
