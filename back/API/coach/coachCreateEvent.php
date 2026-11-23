<?php
header('Content-Type: application/json');
include "../../check_auth.php";

if (!$authCoachId) {
   http_response_code(401);
   echo json_encode(['error' => 'Non authentifié']);
   exit;
}

try {
   $input = json_decode(file_get_contents('php://input'), true);

    if (!$input) 
    {
        throw new Exception("Aucune donnée reçue.");
    }

    
    $idCoach = $authCoachId;
    $idTypeActivity = intval($input['id_typeActivities'] ?? 0);
    $idBox = intval($input['id_box'] ?? 0);
    $begin = trim($input['begin'] ?? '');
    $end = trim($input['end'] ?? '');
    $isRecurring = !empty($input['recurring']);
    $weeks = intval($input['weeks'] ?? 1);
    
    if (!$idCoach || !$idTypeActivity || !$idBox || !$begin || !$end) 
    {
        throw new Exception("Certains champs requis sont manquants.");
    }

    
    $beginTimestamp = strtotime($begin);
    $endTimestamp = strtotime($end);

    if (!$beginTimestamp || !$endTimestamp || $endTimestamp <= $beginTimestamp) 
    {
        throw new Exception("Dates invalides. La date de fin doit être postérieure au début.");
    }

    
    if ($weeks < 1) $weeks = 1;
    if ($weeks > 52) $weeks = 52;

    // Check that coach exists and is active
    $stmt = $db->prepare("SELECT id_coach FROM coach WHERE id_coach = ? AND actif_coach = 1");
    $stmt->bind_param("i", $idCoach);
    $stmt->execute();

    if ($stmt->get_result()->num_rows === 0) 
    {
        throw new Exception("Coach invalide ou inactif.");
    }

    $stmt->close();

    // Insert all events (with recurrence)
    $stmt = $db->prepare("
        INSERT INTO organisation (
            id_coach, id_owner, id_activities, id_box, begin_organisation, end_organisation
        ) VALUES (?, ?, ?, ?, ?, ?)
    ");

    $totalInserted = 0;
    for ($i = 0; $i < $weeks; $i++) 
    {
        $beginDate = date('Y-m-d H:i:s', strtotime("+$i week", $beginTimestamp));
        $endDate = date('Y-m-d H:i:s', strtotime("+$i week", $endTimestamp));

        $stmt->bind_param("iiiiss", $idCoach, $authOwnerId, $idTypeActivity, $idBox, $beginDate, $endDate);
        if ($stmt->execute()) $totalInserted++;
    }
    $stmt->close();

    echo json_encode([
        'success' => true,
        'message' => $isRecurring
            ? "Événement créé et répété sur $weeks semaine(s) ($totalInserted insertion(s))."
            : "Événement créé avec succès."
    ]);

} catch (Throwable $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Erreur serveur']);
}
