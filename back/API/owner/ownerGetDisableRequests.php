<?php
header("Content-Type: application/json; charset=utf-8");

include "../../check_auth.php";

if (!$authOwnerId) {
    http_response_code(401);
    echo json_encode(["error" => "Non authentifié"]);
    exit;
}

$sql = "
    SELECT 
        id_user,
        firstName_user,
        lastName_user,
        emailAddress_user,
        needDisabled,
        dateCreation_user
    FROM user
    WHERE needDisabled = 1
    AND anonymisation_user = 0
    ORDER BY lastName_user ASC, firstName_user ASC
";

$res = $db->query($sql);
$users = [];

while ($row = $res->fetch_assoc()) {
    $users[] = $row;
}

echo json_encode([
    "success" => true,
    "users" => $users
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

$db->close();
?>