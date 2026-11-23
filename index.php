<?php 
session_start();

$page = $_GET['p'] ?? 'accueil';
$page = is_string($page) ? $page : '';

// Pages nécessitant authentification
$protected_pages = ['dashboard', 'ownerAddMember', 'coachPlanning', 'memberEditProfile', 'ownerManageUsers', 'ownerEditMember', 'coachOrganisationView', 'ownerOrganiserEvenement', 'coachOrganiserEvenement', 'ownerManagePlannings', 'ownerManageAbonnements', 'memberSubscriptionView', 'memberOrganisationView', 'memberReservationCalendar', 'memberPerformance', 'memberBooking', 'coachFeedbacks', 'ownerDisableRequests', 'ownerManageBox', 'memberSubscriptionPurchase'];

// Si page protégée, check auth **avant d’inclure le HTML**
if (in_array($page, $protected_pages)) {
    include "back/check_auth.php";
    if (!$authUserId) {
        header("Location: index.php?login=1");
        exit;
    }
    if ((str_starts_with($page, 'owner') && !$authOwnerId)
        || (str_starts_with($page, 'coach') && !$authCoachId)) {
        http_response_code(403);
        exit('Acces interdit');
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<?php
    // Include head
    require_once 'front/includes/head.php';
?>

<body>
    <?php
        // Include header
        require_once 'front/includes/header.php';

        // Include modal login admin
        require_once 'front/includes/modal_login.php';
        ?>
    
        <main>
            <?php
                $allowed_pages = ['accueil', 'dashboard', 'calendar', 'ownerAddMember', 'coachPlanning', 'memberEditProfile', 'ownerManageUsers', 'ownerEditMember', 'coachOrganisationView', 'ownerOrganiserEvenement', 'team', 'contact', 'freeTrial', 'tarif', 'coachOrganiserEvenement', 'ownerManagePlannings', 'ownerManageAbonnements', 'memberSubscriptionView', 'memberOrganisationView', 'memberReservationCalendar', 'memberPerformance', 'memberBooking', 'memberRegister', 'coachFeedbacks', 'memberResetPassword',
            'activity_wod', 'activity_strength', 'activity_weightlifting', 'activity_gym', 'activity_kids', 'activity_teens', 'memberForgotPassword', 'ownerDisableRequests', 'ownerManageBox', 'memberSubscriptionPurchase', 'validPayment'];

                if (in_array($page, $allowed_pages)) {
                    include __DIR__ . "/front/pages/$page.php";
                } else {
                    include __DIR__ . "/front/pages/404.php";
                }
            ?>
        </main>
    
    <?php
        // Include footer
        require_once 'front/includes/footer.php';
    ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init();
    </script>
</body>
</html>