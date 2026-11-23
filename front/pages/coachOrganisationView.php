<?php
if ($authCoachId === null && $authOwnerId === null) {
    exit;
}
?>

<section id="coachOrganisationViewApp" v-cloak>
    <div class="container-fluid">
        <div class="row flex-nowrap">
            <?php require_once 'front/includes/dashboard_sidebar.php'; ?>
            <div class="col py-3">
                <!-- MAIN PAGE BODY -->
                <div v-if="organisation" class="container my-5">

                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-info text-white fw-bold">
                            Informations de l'événement
                        </div>
                        <div class="card-body" v-if="organisation">
                            <div class="row mb-3" style="background-color: rgba(0,123,255,0.1); padding: 15px; border-radius: 8px;">
                                <div class="col-md-6">
                                    <strong>Début :</strong> {{ formatDateTime(organisation.begin_organisation) }}
                                </div>
                                <div class="col-md-6">
                                    <strong>Fin :</strong> {{ formatDateTime(organisation.end_organisation) }}
                                </div>
                                <div class="col-md-6 mt-2">
                                    <strong>Activité :</strong> {{ organisation.activity.name_typeActivity || '—' }}
                                </div>
                                <div class="col-md-6 mt-2">
                                    <strong>Coach :</strong> {{ organisation.coach.firstName_user }} {{ organisation.coach.lastName_user }}
                                </div>
                                <div class="col-md-12 mt-2">
                                    <strong>Feedback coach :</strong> {{ organisation.feedbackCoachOnOrganisation || 'Aucun' }}
                                </div>
                            </div>
                        </div>
                        <div class="card-body" v-else>
                            <div class="alert alert-info">
                                Chargement des informations...
                            </div>
                        </div>
                    </div>

                    <!-- Membres -->
                    <div class="card shadow-sm">
                        <div class="card-header bg-info text-white fw-bold">
                            Membres participants ({{ organisation.total_members || 0 }})
                        </div>
                        <div class="card-body">
                            <table id="tableOrganisationMembers" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Nom</th>
                                        <th>Prénom</th>
                                        <th>Email</th>
                                        <th>Téléphone</th>
                                        <th>Niveau</th>
                                        <th>Blessures / Notes</th>
                                        <th>Feedback coach</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="member in organisation.members" :key="member.id_user">
                                        <td>{{ member.lastName_user }}</td>
                                        <td>{{ member.firstName_user }}</td>
                                        <td>{{ member.emailAddress_user }}</td>
                                        <td>{{ member.telephone_user || '—' }}</td>
                                        <td>{{ member.actualLevel_user || '—' }}</td>
                                        <td>{{ member.wound_user || '—' }}</td>
                                        <td>{{ member.feedbackCoachOnUser_organisationuser || '—' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>

                <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
                <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
                <script src="https://cdn.datatables.net/2.3.2/js/dataTables.min.js"></script>
                <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
                <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
                <script src="/js/coachOrganisationView.js"></script>
            </div>
        </div>
    </div>
</section>
