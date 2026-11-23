<?php
require_once __DIR__ . '/../../back/check_auth.php';
if ($authOwnerId === null) {
    exit;
}
?>

<section id="ownerManageAbonnementsApp">
    <div class="container-fluid">
        <div class="row flex-nowrap">
            <?php require_once __DIR__ . '/../includes/dashboard_sidebar.php'; ?>

            <div class="col py-3">
                <h3 class="fw-bold text-warning mb-4">Gérer les abonnements</h3>

                <!-- ====================== -->
                <!-- 👥 Liste des membres -->
                <!-- ====================== -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-dark text-light">Liste des membres</div>
                    <div class="card-body">
                        <table id="tableMembres" class="table table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Abonnement actuel</th>
                                    <th>Début</th>
                                    <th>Fin</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="m in membres" :key="m.id_user">
                                    <td>{{ m.firstName_user }} {{ m.lastName_user }}</td>
                                    <td>{{ m.currentAbonnement || 'Aucun' }}</td>
                                    <td>{{ formatDate(m.subscriptionBegin_boxuser) || '-' }}</td>
                                    <td>{{ formatDate(m.subscriptionEnd_boxuser) || '-' }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" @click="viewAbonnements(m)">
                                            📋 Historique
                                        </button>
                                    </td>
                                </tr>
                                <tr v-if="membres.length === 0">
                                    <td colspan="5" class="text-center text-muted">Aucun membre trouvé</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- ====================== -->
                <!-- 📜 Historique abonnements -->
                <!-- ====================== -->
                <div v-if="selectedMember" class="card shadow-sm mb-4">
                    <div class="card-header bg-secondary text-light d-flex justify-content-between align-items-center">
                        <span>
                            Abonnements de {{ selectedMember.firstName_user }} {{ selectedMember.lastName_user }}
                        </span>
                        <button class="btn btn-sm btn-warning fw-bold" @click="openAddModal">+ Ajouter</button>
                    </div>
                    <div class="card-body">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Début</th>
                                    <th>Fin</th>
                                    <th>Payé le</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="a in abonnementsMembre" :key="a.id_abonnement">
                                    <td>{{ a.nom_abonnement }}</td>
                                    <td>{{ formatDate(a.subscriptionBegin_boxuser) }}</td>
                                    <td>{{ formatDate(a.subscriptionEnd_boxuser) }}</td>
                                    <td>{{ formatDate(a.datePayment_boxuser) }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-warning me-2" @click="editAbonnement(a)">✏️</button>
                                        <button class="btn btn-sm btn-danger" @click="deleteAbonnement(a)">🗑</button>
                                    </td>
                                </tr>
                                <tr v-if="abonnementsMembre.length === 0">
                                    <td colspan="5" class="text-center text-muted">Aucun abonnement enregistré</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- ====================== -->
                <!-- 🧩 Types d’abonnements -->
                <!-- ====================== -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-dark text-light d-flex justify-content-between align-items-center">
                        <span>Types d’abonnements disponibles</span>
                        <button class="btn btn-sm btn-warning fw-bold" @click="openAddTypeModal">+ Nouveau type</button>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Description</th>
                                    <th>Prix</th>
                                    <th>Actif</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="t in typesAbonnements" :key="t.id_abonnement">
                                    <td>{{ t.nom_abonnement }}</td>
                                    <td>{{ t.description_abonnement }}</td>
                                    <td>{{ t.prix_abonnement }} €</td>
                                    <td>
                                    <i v-if="t.inactive_abonnement == 0" class="bi bi-check-circle-fill text-success"></i>
                                    <i v-else class="bi bi-x-circle-fill text-danger"></i>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-warning me-2" @click="editType(t)">✏️</button>
                                        <button class="btn btn-sm btn-danger" @click="deleteType(t)">🗑</button>
                                    </td>
                                </tr>
                                <tr v-if="typesAbonnements.length === 0">
                                    <td colspan="4" class="text-center text-muted">Aucun type d’abonnement</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ✅ Modal ajout / édition abonnement membre -->
    <div class="modal fade" id="abonnementModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title fw-bold">{{ isEditingAbonnement ? 'Modifier' : 'Ajouter' }} un abonnement</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form @submit.prevent="saveAbonnement">
                        <div class="mb-3">
                            <label class="form-label">Type d’abonnement</label>
                            <select v-model="formAbonnement.id_abonnement" class="form-select" required>
                                <option value="" disabled>Choisir un type</option>
                                <option v-for="t in typesAbonnements" :value="t.id_abonnement">{{ t.nom_abonnement }}</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="boxSelect" class="form-label">Box</label>
                            <select v-model="formAbonnement.id_box" id="boxSelect" class="form-select" required>
                                <option value="">-- Sélectionner une box --</option>
                                <option v-for="b in boxes" :key="b.id_box" :value="b.id_box">
                                    {{ b.name_box }}
                                </option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Date de paiement</label>
                            <input type="date" v-model="formAbonnement.datePayment" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Début de validité</label>
                            <input type="date" v-model="formAbonnement.begin" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Fin de validité</label>
                            <input type="date" v-model="formAbonnement.end" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-warning w-100 fw-bold">Enregistrer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- ✅ Modal ajout / édition type d’abonnement -->
    <div class="modal fade" id="typeModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title fw-bold">{{ isEditingType ? 'Modifier' : 'Créer' }} un type d’abonnement</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form @submit.prevent="saveType">
                        <div class="mb-3">
                            <label class="form-label">Nom</label>
                            <input type="text" v-model="formType.nom" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea v-model="formType.description" class="form-control" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Prix (€)</label>
                            <input type="number" step="0.01" v-model="formType.prix" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-warning w-100 fw-bold">Enregistrer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

     <!-- ✅ Modal de confirmation -->
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
        <div class="modal-header bg-warning text-dark">
            <h5 class="modal-title fw-bold" id="confirmModalLabel">Confirmer l’action</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">{{ confirmMessage }}</div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
            <button type="button" class="btn btn-warning fw-bold" @click="confirmAction">Confirmer</button>
        </div>
        </div>
    </div>
    </div>

    <!-- ✅ Toast -->
    <div class="position-fixed bottom-0 end-0 p-3">
        <div id="toast" class="toast align-items-center text-bg-success border-0" role="alert">
            <div class="d-flex">
                <div class="toast-body">{{ toastMessage }}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    </div>
</section>

<!-- jQuery + DataTables -->
<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script src="https://cdn.datatables.net/2.3.2/js/dataTables.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/2.3.2/css/dataTables.dataTables.min.css">

<!-- Langue française -->
<script>
const dataTableLangFR = {
    "decimal":        ",",
    "thousands":      " ",
    "emptyTable":     "Aucune donnée disponible dans le tableau",
    "info":           "Affichage de _START_ à _END_ sur _TOTAL_ entrées",
    "infoEmpty":      "Affichage de 0 à 0 sur 0 entrée",
    "infoFiltered":   "(filtré à partir de _MAX_ entrées totales)",
    "lengthMenu":     "Afficher _MENU_ entrées",
    "loadingRecords": "Chargement...",
    "processing":     "Traitement...",
    "search":         "Rechercher :",
    "zeroRecords":    "Aucun enregistrement correspondant trouvé",
    "paginate": {
        "first":      "Premier",
        "last":       "Dernier",
        "next":       "Suivant",
        "previous":   "Précédent"
    },
    "aria": {
        "sortAscending":  ": activer pour trier la colonne par ordre croissant",
        "sortDescending": ": activer pour trier la colonne par ordre décroissant"
    }
};
</script>

<script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
<script src="/js/ownerManageAbonnements.js"></script>
