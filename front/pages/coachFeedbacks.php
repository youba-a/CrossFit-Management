<?php
if ($authCoachId === null) {
    exit;
}
?>

<section id="coachFeedbacksApp" v-cloak>
    <div class="container-fluid">
        <div class="row flex-nowrap">

            <?php
            require_once 'front/includes/dashboard_sidebar.php';
            ?>

            <div class="col py-3">

                <h2>Feedbacks reçus</h2>

                <!-- Message si aucun feedback -->
                <div v-if="feedbacks.success === false || !feedbacks.data || feedbacks.data.length === 0" 
                    class="alert alert-info mt-3">
                    Aucun feedback pour le moment. Revenez plus tard 😊
                </div>

                <!-- Tableau -->
                <table v-else id="tableCoachFeedbacks" class="table-striped table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Type d'événement</th>
                            <th>Utilisateur</th>
                            <th>Commentaire</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="feedback in feedbacks.data" :key="feedback.id_organisation">
                            <td>{{ formatDateFR(feedback.date) }}</td>
                            <td>{{ feedback.event_type }}</td>
                            <td>{{ feedback.user_firstname }} {{ feedback.user_lastname }}</td>
                            <td>{{ feedback.feedback }}</td>
                        </tr>
                    </tbody>
                </table>

                <!-- Scripts -->
                <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
                <script src="https://cdn.datatables.net/2.3.2/js/dataTables.min.js"></script>

                <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
                <script src="/js/coachFeedbacks.js"></script>

            </div>
        </div>
    </div>
</section>
