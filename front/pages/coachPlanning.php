<?php
if ($authCoachId === null) {
    exit;
}
?>
<section id="coachPlanningApp">
    <div class="container-fluid">
        <div class="row flex-nowrap">
            <?php
            require_once 'front/includes/dashboard_sidebar.php';
            ?>
            <div class="col py-3">
                <!-- MAIN PAGE BODY -->
                <h2>Mon planning (coach)</h2>
                <div id="calendar" class="row mb-5 mt-4"></div>

                <!-- FullCalendar Core + Plugins -->
                <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.19/index.global.min.js'></script>
                <script src='https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.19/locales/fr.global.min.js'></script>
              
                <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
                <script src="/js/coachPlanning.js"></script>
                <!-- MAIN PAGE BODY FIN -->
            </div>
        </div>
    </div>
</section>

