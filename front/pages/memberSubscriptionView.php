<?php
if ($authUserId === null) {
    exit;
}
?>

<section id="memberSubscriptionViewApp" v-cloak>
    <div class="container-fluid">
        <div class="row flex-nowrap">
            <?php require_once 'front/includes/dashboard_sidebar.php'; ?>
            <div class="col py-3 responsive">
                <!-- MAIN PAGE BODY -->
                <div class="container my-5">

                    <!-- Message d'erreur -->
                    <div v-if="message_error" class="alert alert-danger">
                        {{ message_error }}
                    </div>

                    <!-- Si pas d'abonnement -->
                    <div v-if="!abonnements || abonnements.length === 0" class="d-flex justify-content-center my-5">
                        <div class="card text-center shadow-sm p-4" style="max-width: 400px;">
                            <div class="card-body">
                                <h5 class="card-title mb-3">Aucun abonnement trouvé</h5>
                                <p class="card-text mb-4 text-muted">
                                    Vous n'avez actuellement aucun abonnement actif.
                                </p>
                                <a href="index.php?p=tarif" class="btn btn-primary btn-lg">
                                    Souscrire un abonnement
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Abonnement actuel -->
                    <div v-if="abonnementActuel" class="card shadow-sm mb-4">
                        <div class="card-header bg-primary text-white fw-bold">
                            Mon abonnement actuel
                        </div>
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h5 class="fw-bold mb-1">{{ abonnementActuel.nom_abonnement }}</h5>
                                    <p class="text-muted mb-2">{{ abonnementActuel.description_abonnement }}</p>
                                    <p class="mb-1">
                                        <strong>Prix :</strong> {{ abonnementActuel.prix_abonnement }} € / an
                                    </p>
                                    <p class="mb-1">
                                        <strong>Période :</strong>
                                        du {{ formatDate(abonnementActuel.subscriptionBegin_boxuser) }}
                                        au {{ formatDate(abonnementActuel.subscriptionEnd_boxuser) }}
                                    </p>
                                    <p class="mb-1">
                                        <strong>Statut : </strong>
                                        <span :class="{
                                            'badge bg-success': abonnementActuel.statut === 'actif',
                                            'badge bg-secondary': abonnementActuel.statut === 'expiré'
                                        }">
                                            {{ abonnementActuel.statut }}
                                        </span>
                                    </p>
                                </div>
                                <div class="col-md-4 text-end">
                                    <div class="bg-light p-3 rounded">
                                        <h6 class="fw-bold mb-2">{{ abonnementActuel.name_box }}</h6>
                                        <p class="mb-0 small">
                                            {{ abonnementActuel.streetNameAddress_box }} {{ abonnementActuel.numeroAddress_box }}<br>
                                            {{ abonnementActuel.postBoxAddress_box }}<br>
                                            Tél : {{ abonnementActuel.telephone_box }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-end mb-4" v-if="abonnementActuel">
                        <a href="index.php?p=tarif" class="btn btn-success">
                            Souscrire un nouvel abonnement
                        </a>
                    </div>

                    <!-- Historique -->
                    <div v-if="historique.length > 0" class="card shadow-sm">
                        <div class="card-header bg-light fw-bold">
                            Historique de mes abonnements
                        </div>
                        <div class="card-body">
                            <div class="table-responsive-md">
                                <table id="tableHistoriqueAbonnement" class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Nom</th>
                                            <th>Description</th>
                                            <th>Box</th>
                                            <th>Prix (€)</th>
                                            <th>Début</th>
                                            <th>Fin</th>
                                            <th>Statut</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="abo in historique" :key="abo.id_abonnement">
                                            <td>{{ abo.nom_abonnement }}</td>
                                            <td>{{ abo.description_abonnement }}</td>
                                            <td>{{ abo.name_box }}</td>
                                            <td>{{ abo.prix_abonnement }}</td>
                                            <td>{{ formatDate(abo.subscriptionBegin_boxuser) }}</td>
                                            <td>{{ formatDate(abo.subscriptionEnd_boxuser) }}</td>
                                            <td>
                                                <span :class="{
                                                    'badge bg-success': abo.statut === 'actif',
                                                    'badge bg-secondary': abo.statut === 'expiré'
                                                }">
                                                    {{ abo.statut }}
                                                </span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Librairies -->
                <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
                <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
                
                <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

                <script src="/js/memberSubscriptionView.js"></script>
            </div>
        </div>
    </div>
</section>
