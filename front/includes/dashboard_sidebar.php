<div id="sidebar" class="col-auto col-md-3 col-xl-2 px-md-2 px-0">
    <div class="d-flex flex-column align-items-center align-items-md-start px-3 pt-2 text-white min-vh-100">

        <ul class="nav nav-pills flex-column mb-md-auto mb-0 align-items-center align-items-md-start" id="menu">

            <!-- Menu utilisateur classique -->
            <li>
                <a href="index.php?p=dashboard" class="nav-link px-0 align-middle">
                    <i class="fs-4 bi-house"></i> 
                    <span class="ms-1 d-none d-md-inline">Dashboard</span> 
                </a>
            </li>

            <li>
                <a href="index.php?p=memberPerformance" class="nav-link px-0 align-middle">
                    <i class="fs-4 bi-speedometer2"></i> 
                    <span class="ms-1 d-none d-md-inline">Mes performances</span>
                </a>
            </li>

            <li>
                <a href="index.php?p=memberBooking" class="nav-link px-0 align-middle">
                    <i class="fs-4 bi-calendar-plus"></i> 
                    <span class="ms-1 d-none d-md-inline">Réserver un cours</span>
                </a>
            </li>

            <li>
                <a href="index.php?p=memberReservationCalendar" class="nav-link px-0 align-middle">
                    <i class="fs-4 bi-table"></i> 
                    <span class="ms-1 d-none d-md-inline">Mes Réservations</span>
                </a>
            </li>

            <li>
                <a href="index.php?p=memberSubscriptionView" class="nav-link px-0 align-middle">
                    <i class="fs-4 bi-credit-card"></i> 
                    <span class="ms-1 d-none d-md-inline">Abonnement</span>
                </a>
            </li>

            <li>
                <a href="index.php?p=memberEditProfile" class="nav-link px-0 align-middle">
                    <i class="fs-4 bi-person-lines-fill"></i> 
                    <span class="ms-1 d-none d-md-inline">Mon profil</span>
                </a>
            </li>

            <?php if ($authCoachId) { ?>
                <!-- Section Coach -->
                <li class="mt-4 mb-2 w-100 text-center">
                    <span class="badge bg-info text-dark px-3 py-1 rounded-pill fw-bold">COACH</span>
                </li>

                <div class="w-100 p-2 rounded-3" style="background-color: rgba(0, 123, 255, 0.1);">
                    <li class="mb-1">
                        <a href="index.php?p=coachPlanning" class="nav-link px-2 align-middle text-dark fw-bold rounded coach-link">
                            <i class="fs-4 bi-calendar-week"></i> 
                            <span class="ms-1 d-none d-md-inline">Mon planning</span>
                        </a>
                    </li>

                    <li class="mb-1">
                        <a href="index.php?p=coachOrganiserEvenement" class="nav-link px-2 align-middle text-dark fw-bold rounded admin-link">
                            <i class="fs-4 bi-calendar-event"></i> 
                            <span class="ms-1 d-none d-md-inline">Organiser un événement</span>
                        </a>
                    </li>

                    <li class="mb-1">
                        <a href="index.php?p=coachFeedbacks" class="nav-link px-2 align-middle text-dark fw-bold rounded coach-link">
                            <i class="fs-4 bi-chat-dots"></i> 
                            <span class="ms-1 d-none d-md-inline">Feedbacks reçus</span>
                        </a>
                    </li>
                </div>
            <?php } ?>


            <?php if ($authOwnerId) { ?>
                <!-- Section Admin -->
                <li class="mt-4 mb-2 w-100 text-center">
                    <span class="badge bg-warning text-dark px-3 py-1 rounded-pill fw-bold">ADMIN</span>
                </li>

                <div class="w-100 p-2 rounded-3" style="background-color: #ffc107;">
                    <li class="mb-1">
                        <a href="index.php?p=ownerAddMember" class="nav-link px-2 align-middle text-dark fw-bold rounded admin-link">
                            <i class="fs-4 bi-person-plus"></i> 
                            <span class="ms-1 d-none d-md-inline">Ajouter un membre</span>
                        </a>
                    </li>

                    <li class="mb-1">
                        <a href="index.php?p=ownerManageUsers" class="nav-link px-2 align-middle text-dark fw-bold rounded admin-link">
                            <i class="fs-4 bi-gear"></i> 
                            <span class="ms-1 d-none d-md-inline">Gestion des utilisateurs</span>
                        </a>
                    </li>

                    <li class="mb-1">
                        <a href="index.php?p=ownerOrganiserEvenement" class="nav-link px-2 align-middle text-dark fw-bold rounded admin-link">
                            <i class="fs-4 bi-calendar-event"></i> 
                            <span class="ms-1 d-none d-md-inline">Organiser un événement</span>
                        </a>
                    </li>

                    <li class="mb-1">
                        <a href="index.php?p=ownerManagePlannings" class="nav-link px-2 align-middle text-dark fw-bold rounded admin-link">
                            <i class="fs-4 bi-calendar3"></i> 
                            <span class="ms-1 d-none d-md-inline">Gérer les plannings et les activités</span>
                        </a>
                    </li>

                    <li class="mb-1">
                        <a href="index.php?p=ownerManageAbonnements" class="nav-link px-2 align-middle text-dark fw-bold rounded admin-link">
                            <i class="fs-4 bi-card-checklist"></i> 
                            <span class="ms-1 d-none d-md-inline">Gestion des abonnements</span>
                        </a>
                    </li>

                    <li class="mb-1">
                        <a href="index.php?p=ownerDisableRequests" class="nav-link px-2 align-middle text-dark fw-bold rounded admin-link">
                            <i class="fs-4 bi-person-x"></i> 
                            <span class="ms-1 d-none d-md-inline">Membres à désactiver</span>
                        </a>
                    </li>

                    <li class="mb-1">
                        <a href="index.php?p=ownerManageBox" class="nav-link px-2 align-middle text-dark fw-bold rounded admin-link">
                            <i class="fs-4 bi-building"></i>
                            <span class="ms-1 d-none d-md-inline">Gestion des box</span>
                        </a>
                    </li>
                </div>
            <?php } ?>

        </ul>

    </div>
</div>
