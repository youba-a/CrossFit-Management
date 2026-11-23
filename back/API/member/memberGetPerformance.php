<?php
header('Content-Type: application/json');

include "../../check_auth.php";

if ($authUserId === null) {
    echo json_encode(['error' => 'not_authenticated']);
    exit;
}

$id_user = intval($authUserId);

$stmt = $db->prepare("
    SELECT 
        o.begin_organisation AS date_session,
        ou.performance_organisationuser,
        ou.feedbackCoachOnUser_organisationuser,
        t.name_typeActivity AS activity_type,
        t.color_typeActivity AS activity_color,
        c.id_coach,
        u.firstName_user AS coach_firstname,
        u.lastName_user AS coach_lastname
    FROM organisationuser ou
    JOIN organisation o ON o.id_organisation = ou.id_organisation
    JOIN activities a ON a.id_activities = o.id_activities
    JOIN typeactivities t ON t.id_typeActivities = a.id_typeActivities
    JOIN coach c ON c.id_coach = o.id_coach
    JOIN user u ON u.id_user = c.id_user
    WHERE ou.id_user = ?
    ORDER BY o.begin_organisation ASC
");

$stmt->bind_param('i', $id_user);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    if (!empty($row['date_session'])) {
        $row['date_session'] = date('c', strtotime($row['date_session'])); // ISO format
    }
    $data[] = $row;
}

echo json_encode($data);
