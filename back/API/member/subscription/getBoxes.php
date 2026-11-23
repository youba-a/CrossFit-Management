<?php
// back/API/member/subscription/getUserBoxes.php
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

try {
    // Récupérer toutes les box (par défaut on renvoie seulement les box actives — voir commentaire si tu veux toutes)
    // Si tu veux **toutes** les box (y compris inactives), enlève la clause WHERE actif_box = 1
    $sql = "
        SELECT
            id_box,
            name_box,
            streetNameAddress_box,
            numeroAddress_box,
            postBoxAddress_box,
            postalCode_box,
            town_box,
            telephone_box,
            numberPeople_box,
            actif_box
        FROM box
        WHERE actif_box = 1
        ORDER BY name_box ASC
    ";

    $res = $db->query($sql);
    if ($res === false) {
        throw new Exception('Erreur exécution SQL');
    }

    $boxes = [];
    while ($row = $res->fetch_assoc()) {
        $boxes[] = [
            "id_box" => (int)$row['id_box'],
            "name_box" => $row['name_box'],
            "streetNameAddress_box" => $row['streetNameAddress_box'],
            "numeroAddress_box" => $row['numeroAddress_box'],
            "postBoxAddress_box" => $row['postBoxAddress_box'],
            "postalCode_box" => $row['postalCode_box'],
            "town_box" => $row['town_box'],
            "telephone_box" => $row['telephone_box'],
            "numberPeople_box" => $row['numberPeople_box'],
            "actif_box" => (int)$row['actif_box'],
        ];
    }

    echo json_encode([
        "success" => true,
        "boxes" => $boxes
    ]);
    exit;

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => 'Erreur serveur'
    ]);
    exit;
}
