<?php
if ($authUserId === null) {
    exit;
}
?>
<section id="memberReservationCalendarApp">
    <div class="container-fluid">
        <div class="row flex-nowrap">
            <?php
            require_once 'front/includes/dashboard_sidebar.php';
            ?>
            <div class="col py-3">
                <!-- MAIN PAGE BODY -->
                <h2>Mes Réservations</h2>

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
                                    <th>Coach</th>
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
                <script src="/js/memberReservationCalendar.js"></script>
                <!-- MAIN PAGE BODY FIN -->
            </div>
        </div>
    </div>
</section>

