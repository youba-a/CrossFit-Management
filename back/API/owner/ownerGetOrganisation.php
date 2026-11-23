<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../check_auth.php';

if (!$authOwnerId) {
    http_response_code(401);
    echo json_encode(['error' => 'Non authentifié']);
    exit;
}

$id = intval($_GET['id'] ?? 0);
if (!$id) {
    echo json_encode(['success' => false, 'message' => 'ID manquant']);
    exit;
}

$stmt = $db->prepare("
    SELECT o.id_organisation, o.begin_organisation, o.end_organisation, 
           o.id_box, o.id_activities, 
           t.name_typeActivity, b.name_box
    FROM organisation o
    JOIN activities a ON o.id_activities = a.id_activities
    JOIN typeactivities t ON a.id_typeActivities = t.id_typeActivities
    JOIN box b ON o.id_box = b.id_box
    WHERE o.id_organisation = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
$stmt->close();

echo json_encode(['success' => true, 'organisation' => $row]);
