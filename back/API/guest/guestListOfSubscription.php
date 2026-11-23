<?php
header('Content-Type: application/json; charset=utf-8');


include "../../../config/db.php"; 

if (!isset($db) || $db->connect_errno) {
    http_response_code(500);
    echo json_encode(["error" => "Impossible de se connecter à la base de données."]);
    exit;
}



$sql = "
    SELECT 
    id_abonnement, nom_abonnement, description_abonnement, prix_abonnement
    FROM abonnements
    WHERE inactive_abonnement = 0;
";



if ($stmt = $db->prepare($sql)) {
    $stmt->execute();
    $result = $stmt->get_result();

    $organisations = [];
    while ($row = $result->fetch_assoc()) 
    {
        $organisations[] = $row;
    }

    echo json_encode($organisations, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

    $stmt->close();
} else {
    http_response_code(500);
    echo json_encode(["error" => "Impossible de se connecter à la base de données."]);
    exit;
}

$db->close();
?>
