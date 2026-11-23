<?php
//--------------------------------------
// API : Dashboard adhérent
//--------------------------------------
include "../../check_auth.php";

header('Content-Type: application/json');

// Vérifie l’auth
if (!$authUserId) {
    http_response_code(401);
    echo json_encode(['error' => 'Non authentifié']);
    exit;
}

try {

    //--------------------------------------
    // 1) Infos abonnement & box
    //--------------------------------------
    $stmt = $db->prepare("
        SELECT 
            bu.subscriptionBegin_boxuser,
            bu.subscriptionEnd_boxuser,
            a.nom_abonnement,
            a.description_abonnement,
            a.prix_abonnement,
            b.name_box,
            b.town_box
        FROM boxuser bu
        INNER JOIN abonnements a ON bu.idAbonnement_boxuser = a.id_abonnement
        INNER JOIN box b ON bu.idBox_boxuser = b.id_box
        WHERE bu.idUser_boxuser = ?
        ORDER BY bu.subscriptionEnd_boxuser DESC
        LIMIT 1
    ");
    $stmt->bind_param("i", $authUserId);
    $stmt->execute();
    $subscription = $stmt->get_result()->fetch_assoc();


    //--------------------------------------
    // 2) Sessions futures (réservées)
    //--------------------------------------
    $stmt = $db->prepare("
        SELECT 
            o.id_organisation,
            o.begin_organisation,
            o.end_organisation,
            ta.name_typeActivity,
            ta.color_typeActivity,
            u.firstName_user AS coach_firstname,
            u.lastName_user AS coach_lastname
        FROM organisationuser ou
        INNER JOIN organisation o ON ou.id_organisation = o.id_organisation
        INNER JOIN activities ac ON o.id_activities = ac.id_activities
        INNER JOIN typeactivities ta ON ac.id_typeActivities = ta.id_typeActivities
        LEFT JOIN coach c ON o.id_coach = c.id_coach
        LEFT JOIN user u ON c.id_user = u.id_user
        WHERE ou.id_user = ?
        AND o.begin_organisation >= NOW()
        ORDER BY o.begin_organisation ASC
    ");
    $stmt->bind_param("i", $authUserId);
    $stmt->execute();
    $futureSessions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);


    //--------------------------------------
    // 3) Sessions passées (réservées) + feedbacks
    //--------------------------------------
    $stmt = $db->prepare("
        SELECT 
            o.id_organisation,
            o.begin_organisation,
            o.end_organisation,
            ta.name_typeActivity,
            ta.color_typeActivity,
            u.firstName_user AS coach_firstname,
            u.lastName_user AS coach_lastname,
            ou.feedbackUserOnCoach_organisationuser,
            ou.feedbackUserOnOrganisation_organisationuser,
            ou.feedbackCoachOnUser_organisationuser
        FROM organisationuser ou
        INNER JOIN organisation o ON ou.id_organisation = o.id_organisation
        INNER JOIN activities ac ON o.id_activities = ac.id_activities
        INNER JOIN typeactivities ta ON ac.id_typeActivities = ta.id_typeActivities
        LEFT JOIN coach c ON o.id_coach = c.id_coach
        LEFT JOIN user u ON c.id_user = u.id_user
        WHERE ou.id_user = ?
        AND o.begin_organisation < NOW()
        ORDER BY o.begin_organisation DESC
        LIMIT 50
    ");
    $stmt->bind_param("i", $authUserId);
    $stmt->execute();
    $pastSessions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);


    //--------------------------------------
    // 4) Prochains cours disponibles (non inscrits)
    //--------------------------------------
    $stmt = $db->prepare("
        SELECT 
            o.id_organisation,
            o.begin_organisation,
            o.end_organisation,
            ta.name_typeActivity,
            ta.color_typeActivity,
            u.firstName_user AS coach_firstname,
            u.lastName_user AS coach_lastname
        FROM organisation o
        INNER JOIN activities ac ON o.id_activities = ac.id_activities
        INNER JOIN typeactivities ta ON ac.id_typeActivities = ta.id_typeActivities
        LEFT JOIN coach c ON o.id_coach = c.id_coach
        LEFT JOIN user u ON c.id_user = u.id_user
        WHERE o.begin_organisation > NOW()
        AND NOT EXISTS (
            SELECT 1 FROM organisationuser ou
            WHERE ou.id_organisation = o.id_organisation
            AND ou.id_user = ?
        )
        ORDER BY o.begin_organisation ASC
        LIMIT 20
    ");
    $stmt->bind_param("i", $authUserId);
    $stmt->execute();
    $upcomingNotRegistered = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);



    //--------------------------------------
    // RETURN
    //--------------------------------------
    echo json_encode([
        'success' => true,
        'subscription' => $subscription,
        'futureSessions' => $futureSessions,
        'pastSessions' => $pastSessions,
        'upcomingNotRegistered' => $upcomingNotRegistered
    ]);

} catch (Exception $e) {
    error_log("Erreur memberGetDashboard : " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Erreur serveur']);
}
