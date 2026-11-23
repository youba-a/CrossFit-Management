<?php

if (!isset($authUserId)) {
    exit;
}
?>
<section id="memberBookingApp" v-cloak>
    <div class="container-fluid">
        <div class="row flex-nowrap">
            <?php
            // Sidebar comme dans ton autre page
            require_once 'front/includes/dashboard_sidebar.php';
            ?>
            <div class="col py-3">
                <!-- MAIN PAGE BODY -->
                <!-- MODAL DE CONFIRMATION RESERVATION -->
                <div class="modal fade" id="bookingConfirmModal" tabindex="-1" aria-labelledby="bookingConfirmModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-primary">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title fw-bold" id="bookingConfirmModalLabel">
                        Confirmation de réservation
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                    </div>
                    
                    <div class="modal-body">
                        <div v-if="message_error" class="alert alert-danger" role="alert">
                            {{message_error}}
                        </div>
                        <p id="bookingModalText" class="mb-2"></p>
                        <p class="fw-bold">Confirmer la réservation ?</p>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Annuler
                        </button>
                        <button type="button" class="btn btn-primary" id="confirmBookingBtn">
                        Confirmer
                        </button>
                    </div>
                    </div>
                </div>
                </div>

                <!-- TOAST DE SUCCÈS -->
                <div class="toast-container position-fixed bottom-0 end-0 p-3">
                <div id="bookingSuccessToast" class="toast text-bg-success" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="d-flex">
                    <div class="toast-body">
                        {{message_success}}
                    </div>
                    <button type="button" class="btn-close btn-close-black me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                </div>
                </div>

                <h2>Réserver un cours</h2>

                <!-- MESSAGE AUCUN COURS -->
                <div v-if="eventsList.length === 0 && loading === false" class="alert alert-info text-center my-3" role="alert">
                    <h5 class="alert-heading">Aucun cours disponible pour le moment</h5>
                    <p>Revenez plus tard, de nouveaux créneaux seront bientôt ajoutés.</p>
                </div>

                
                <p>Cliquez sur un créneau pour réserver.</p>

                <ul class="nav nav-tabs mt-4" id="viewTabs">
                    <li class="nav-item">
                        <a class="nav-link active" data-view="calendar" href="#">Calendrier</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-view="table" href="#">Liste</a>
                    </li>
                </ul>

                <div class="mt-3">
                    <div id="calendarView">
                        <div id="calendar" class="row mb-5 mt-4"></div>
                    </div>

                    <div id="tableView" style="display:none;">
                        <table id="eventsTable" class="table table-striped table-bordered w-100">
                            <thead>
                                <tr>
                                    <th>Date début</th>
                                    <th>Activité</th>
                                    <th>Lieu</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>

                <!-- FullCalendar Core + Plugins -->
                <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.19/index.global.min.js'></script>
                <script src='https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.19/locales/fr.global.min.js'></script>
              
                <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
                <script src="https://cdn.datatables.net/2.3.2/js/dataTables.min.js"></script>
                
                <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
                <script src="/js/memberBooking.js"></script>
                <!-- MAIN PAGE BODY FIN -->
            </div>
        </div>
    </div>
</section>
