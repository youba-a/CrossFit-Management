<?php
header('Content-Type: application/json; charset=utf-8');
include "../../check_auth.php";



if (!$authUserId) {
    http_response_code(401);
    echo json_encode(["error" => "Non authentifié"]);
    exit;
}


if (!isset($db) || $db->connect_errno) {
    http_response_code(500);
    echo json_encode(["error" => "Erreur de connexion à la base de données"]);
    exit;
}

$sql = "
    SELECT
        u.id_user,
        u.lastName_user,
        u.firstName_user,
        u.birthdate_user,
        u.gender_user,
        u.poids_user,
        u.taille_user,
        u.streetNameAddress_user,
        u.numeroAddress_user,
        u.postBoxAddress_user,
        u.postalCode_user,
        u.town_user,
        u.telephone_user,
        u.firstContactTelephone_user,
        u.secondContactTelephone_user,
        u.emailAddress_user,
        u.dateInvitation_user,
        u.dateCreation_user,
        u.wound_user,
        u.actualLevel_user,
        u.needDisabled,
        u.notesCoach_user,
        u.avatar_user,
        c.specialisation_coach,
        o.email_owner,
        o.telephone_owner,
        o.salary_owner,
        CASE 
            WHEN o.id_owner IS NOT NULL THEN true
            ELSE false
        END AS isOwner,

        CASE 
            WHEN c.id_coach IS NOT NULL AND c.actif_coach = 1 THEN true
            ELSE false
        END AS isCoach
    FROM user as u
    LEFT JOIN coach as c ON c.id_user = u.id_user
    LEFT JOIN owner as o ON o.id_user = u.id_user
    WHERE u.id_user = ?;
";

$stmt = $db->prepare($sql);
$stmt->bind_param("i", $authUserId);
$stmt->execute();
$resultat = $stmt->get_result();

if ($resultat->num_rows === 0) {
    http_response_code(403);
    echo json_encode(["error" => "Cet utilisateur n'est pas un  membre"]);
    exit;
}

$user = $resultat->fetch_assoc();
$stmt->close();
$db->close();

echo json_encode($user, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
?>
