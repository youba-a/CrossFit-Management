<?php
header("Content-Type: application/json");
include "../../check_auth.php";

if (!$authOwnerId) {
    http_response_code(403);
    echo json_encode(["error" => "Accès interdit"]);
    exit;
}

$sql = "SELECT * FROM box ORDER BY id_box";
$res = $db->query($sql);

$boxes = [];
while ($row = $res->fetch_assoc()) {
    $boxes[] = $row;
}

echo json_encode($boxes);