<?php
if (!$authOwnerId) {
    header("Location: index.php?p=dashboard");
    exit;
}
?>

<section id="ownerManageBoxApp" v-cloak>
    <div class="container-fluid">
        <div class="row flex-nowrap">
            <?php require_once 'front/includes/dashboard_sidebar.php'; ?>

            <div class="col py-3">

                <h2 class="mb-4">Gestion des box</h2>

                <!-- Message d'erreur -->
                <div v-if="errorMessage" class="alert alert-danger">
                    {{ errorMessage }}
                </div>

                <!-- Message de succès -->
                <div v-if="successMessage" class="alert alert-success">
                    {{ successMessage }}
                </div>

                <!-- TABLEAU DES BOX -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-warning text-white fw-bold">
                        Box enregistrées
                    </div>

                    <div class="card-body table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Adresse</th>
                                    <th>Téléphone</th>
                                    <th>Capacité</th>
                                    <th>Actif</th>
                                    <th>Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr v-for="b in boxes" :key="b.id_box">
                                    <td>{{ b.name_box }}</td>
                                    <td>
                                        {{ b.streetNameAddress_box }} {{ b.numeroAddress_box }},
                                        {{ b.postalCode_box }} {{ b.town_box }}
                                    </td>
                                    <td>{{ b.telephone_box }}</td>
                                    <td>{{ b.numberPeople_box }}</td>

                                    <td>
                                        <span class="badge" 
                                            :class="b.actif_box == 1 ? 'bg-success' : 'bg-secondary'">
                                            {{ b.actif_box == 1 ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>

                                    <td>
                                        <button class="btn btn-warning btn-sm" @click="openEditModal(b)">
                                            Modifier
                                        </button>

                                        <button class="btn btn-secondary btn-sm ms-2"
                                            @click="toggleBox(b.id_box)">
                                            {{ b.actif_box == 1 ? 'Désactiver' : 'Activer' }}
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- BOUTON AJOUTER -->
                <button class="btn btn-warning mb-4" @click="openAddModal">
                    + Ajouter une box
                </button>

                <!-- MODAL AJOUT / MODIFICATION -->
                <div class="modal fade" id="boxModal" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">

                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title">{{ modalTitle }}</h5>
                                <button type="button" class="btn-close btn-close-white"
                                        data-bs-dismiss="modal"></button>
                            </div>

                            <div class="modal-body">

                            <!-- Message d'erreur -->
                            <div v-if="errorMessageModal" class="alert alert-danger">
                                {{ errorMessageModal }}
                            </div>

                                <div class="mb-2">
                                    <label class="form-label">Nom</label>
                                    <input v-model="form.name_box" class="form-control">
                                </div>

                                <div class="mb-2">
                                    <label class="form-label">Rue</label>
                                    <input v-model="form.streetNameAddress_box" class="form-control">
                                </div>

                                <div class="mb-2">
                                    <label class="form-label">Numéro</label>
                                    <input v-model="form.numeroAddress_box" class="form-control">
                                </div>

                                <div class="mb-2">
                                    <label class="form-label">Boîte (optionnel)</label>
                                    <input v-model="form.postBoxAddress_box" class="form-control">
                                </div>

                                <div class="mb-2">
                                    <label class="form-label">Code postal</label>
                                    <input v-model="form.postalCode_box" class="form-control">
                                </div>

                                <div class="mb-2">
                                    <label class="form-label">Ville</label>
                                    <input v-model="form.town_box" class="form-control">
                                </div>

                                <div class="mb-2">
                                    <label class="form-label">Téléphone</label>
                                    <input v-model="form.telephone_box" class="form-control">
                                </div>

                                <div class="mb-2">
                                    <label class="form-label">Capacité max</label>
                                    <input v-model="form.numberPeople_box" class="form-control">
                                </div>

                            </div>

                            <div class="modal-footer">
                                <button class="btn btn-secondary" data-bs-dismiss="modal">
                                    Annuler
                                </button>
                                <button class="btn btn-success" @click="saveBox">
                                    Enregistrer
                                </button>
                            </div>

                        </div>
                    </div>
                </div>

                <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
                <script src="/js/ownerManageBox.js"></script>

            </div>
        </div>
    </div>
</section>
