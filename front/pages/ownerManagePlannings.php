<?php
// --- gererCoachs.php ---
require_once __DIR__ . '/../../back/check_auth.php';

if ($authOwnerId === null) {
    exit;
}
?>

<section id="ownerManagePlanningsApp">
    <div class="container-fluid">
        <div class="row flex-nowrap">
            <?php require_once __DIR__ . '/../includes/dashboard_sidebar.php'; ?>

            <div class="col py-3">

                <h3 class="fw-bold text-warning mb-4">Gérer les plannings et les activités</h3>

                <!-- ====================== -->
                <!-- 🧱 Liste des coachs -->
                <!-- ====================== -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-dark text-light">Liste des coachs</div>
                    <div class="card-body">
                        <table class="table table-striped" id="coachTable">
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Niveau</th>
                                    <th>Spécialisation</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="c in coachs" :key="c.id_coach">
                                    <td>{{ c.firstName_user }} {{ c.lastName_user }}</td>
                                    <td>{{ c.actualLevel_coach }}</td>
                                    <td>{{ c.specialisation_coach }}</td>
                                    <td>{{ c.actif_coach == 1 ? "Actif" : "Inactif" }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" @click="showPlanning(c)">
                                            Voir planning
                                        </button>
                                    </td>
                                </tr>
                                <tr v-if="coachs.length === 0">
                                    <td colspan="5" class="text-center text-muted">Aucun coach trouvé</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- ====================== -->
                <!-- 🗓️ Planning du coach -->
                <!-- ====================== -->
                <div class="card shadow-sm mb-4" v-if="selectedCoach">
                    <div class="card-header bg-secondary text-light">
                        Planning de {{ selectedCoach.firstName_user }} {{ selectedCoach.lastName_user }}
                    </div>
                    <div class="card-body">
                        <table class="table table-hover" id="planningTable">
                            <thead>
                                <tr>
                                    <th>Début</th>
                                    <th>Fin</th>
                                    <th>Activité</th>
                                    <th>Box</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="p in planning" :key="p.id_organisation">
                                    <td>{{ formatDate(p.begin_organisation) }}</td>
                                    <td>{{ formatDate(p.end_organisation) }}</td>
                                    <td>{{ p.name_typeActivity }}</td>
                                    <td>{{ p.name_box }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-warning" @click="editOrganisation(p.id_organisation)">✏️</button>
                                        <button class="btn btn-sm btn-danger" @click="deleteOrganisation(p.id_organisation)">🗑</button>
                                    </td>
                                </tr>
                                <tr v-if="planning.length === 0">
                                    <td colspan="5" class="text-center text-muted">Aucune session planifiée</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- ====================== -->
                <!-- 🎨 Liste des activités -->
                <!-- ====================== -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-dark text-light">Liste des types d’activités</div>
                    <div class="card-body">
                        <table class="table table-striped" id="activitiesTable">
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Description</th>
                                    <th>Couleur</th>
                                    <th>Invités autorisés</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="a in activities" :key="a.id_typeActivities">
                                    <td>{{ a.name_typeActivity }}</td>
                                    <td>{{ a.description_typeActivity }}</td>
                                    <td>
                                        <span class="badge" :style="{ background: getColorFromCode(a.color_typeActivity) }">
                                            {{ a.color_typeActivity }}
                                        </span>
                                    </td>
                                    <td>{{ a.allowGuest_typeActivity == 1 ? "✅ Oui" : "❌ Non" }}</td>
                                    <td class="text-nowrap">
                                        <button class="btn btn-sm btn-warning me-1" @click="editActivity(a.id_typeActivities)">✏️</button>
                                        <button 
                                        class="btn btn-sm btn-danger" 
                                        @click="deleteActivity(a.id_typeActivities, a.name_typeActivity)"
                                        >🗑</button>
                                    </td>
                                </tr>
                                <tr v-if="activities.length === 0">
                                    <td colspan="5" class="text-center text-muted">Aucune activité trouvée</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- ====================== -->
                <!-- 🧩 Créer activité -->
                <!-- ====================== -->
                <div class="card shadow-sm" id="activityForm">
                    <div class="card-header bg-warning text-dark fw-bold">Créer une nouvelle activité</div>
                    <div class="card-body">
                        <form @submit="createActivity">
                            <div class="mb-3">
                                <label class="form-label">Nom de l'activité</label>
                                <input type="text" v-model="activityForm.name" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea v-model="activityForm.desc" class="form-control" required></textarea>
                            </div>

                            <div class="mb-3">
                            <label class="form-label">Couleur</label>
                            <div class="d-flex flex-wrap gap-2 mt-2">
                                <div
                                v-for="n in 10"
                                :key="n"
                                class="color-square rounded"
                                :class="{ 'border border-3 border-dark': parseInt(activityForm.color) === n }"
                                :style="{
                                    backgroundColor: getColorFromCode(n),
                                    width: '32px',
                                    height: '32px',
                                    cursor: 'pointer',
                                    transition: 'transform 0.1s ease'
                                }"
                                @click="activityForm.color = n"
                                @mouseover="$event.target.style.transform='scale(1.1)'"
                                @mouseleave="$event.target.style.transform='scale(1)'"
                                ></div>
                            </div>
                            </div>

                            <div class="form-check mb-3">
                                <input type="checkbox" class="form-check-input" v-model="activityForm.allowGuest" id="allowGuest">
                                <label class="form-check-label" for="allowGuest">Autoriser les invités</label>
                            </div>

                            <button type="submit" class="btn btn-warning fw-bold">Enregistrer</button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>

    
    <!-- ✅ Toast -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1055;">
        <div id="liveToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive">
            <div class="d-flex">
                <div class="toast-body">
                    {{ toastMessage }}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    </div>
    
    <!-- ====================== -->
    <!-- ✏️ MODAL ÉDITION ORGANISATION -->
    <!-- ====================== -->
    <div class="modal fade" id="editOrganisationModal" tabindex="-1" aria-labelledby="editOrganisationLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title fw-bold" id="editOrganisationLabel">Modifier le créneau</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body">
                    <form @submit.prevent="saveOrganisationChanges">
                        <input type="hidden" v-model="editOrganisationForm.id">

                        <div class="mb-3">
                            <label for="editBegin" class="form-label">Date et heure de début</label>
                            <input type="datetime-local" v-model="editOrganisationForm.begin" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="editEnd" class="form-label">Date et heure de fin</label>
                            <input type="datetime-local" v-model="editOrganisationForm.end" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="editActivity" class="form-label">Type d’activité</label>
                            <select v-model="editOrganisationForm.id_activity" class="form-select" required>
                                <option v-for="a in activitiesSelect" :value="a.id_activities">{{ a.name_typeActivity }}</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="editBox" class="form-label">Box associée</label>
                            <select v-model="editOrganisationForm.id_box" class="form-select" required>
                                <option v-for="b in boxes" :value="b.id_box">{{ b.name_box }}</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-warning fw-bold w-100">Enregistrer les modifications</button>
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

    <!-- ✏️ MODAL MODIFICATION ACTIVITÉ -->
    <div class="modal fade" id="editActivityModal" tabindex="-1" aria-labelledby="editActivityLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
        <div class="modal-header bg-warning text-dark">
            <h5 class="modal-title fw-bold" id="editActivityLabel">Modifier une activité</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <form @submit.prevent="saveEditedActivity">
            <input type="hidden" v-model="editActivityForm.id">

            <div class="mb-3">
                <label class="form-label">Nom</label>
                <input type="text" v-model="editActivityForm.name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea v-model="editActivityForm.desc" class="form-control" required></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Couleur</label>
                <div class="d-flex flex-wrap gap-2 mt-2">
                <div
                    v-for="n in 10"
                    :key="n"
                    class="color-square rounded"
                    :class="{ 'border border-3 border-dark': parseInt(editActivityForm.color) === n }"
                    :style="{
                    backgroundColor: getColorFromCode(n),
                    width: '32px',
                    height: '32px',
                    cursor: 'pointer'
                    }"
                    @click="editActivityForm.color = n"
                ></div>
                </div>
            </div>

            <div class="form-check mb-3">
                <input type="checkbox" class="form-check-input" v-model="editActivityForm.allowGuest" id="editAllowGuest">
                <label class="form-check-label" for="editAllowGuest">Autoriser les invités</label>
            </div>

            <button type="submit" class="btn btn-warning fw-bold w-100">Enregistrer</button>
            </form>
        </div>
        </div>
    </div>
    </div>
</section>

<!-- Vue.js + App -->
<script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
<script src="/js/ownerManagePlannings.js"></script>
