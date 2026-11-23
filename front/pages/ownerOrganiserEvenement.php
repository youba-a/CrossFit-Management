<?php
if ($authOwnerId === null) {
    exit;
}
?>

<section id="organiserEvenementApp">
    <div class="container-fluid">
        <div class="row flex-nowrap">
            <?php require_once __DIR__ . '/../includes/dashboard_sidebar.php'; ?>
            <div class="col py-3">

                <!-- TOAST -->
                <div class="toast-container position-fixed bottom-0 end-0 p-3">
                    <div id="toastEventSuccess" class="toast text-bg-success" role="alert" aria-live="assertive" aria-atomic="true">
                        <div class="d-flex">
                            <div class="toast-body" id="toastMessage">{{ message_success }}</div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                        </div>
                    </div>
                </div>

                <div class="container my-5">
                    <div class="card shadow-sm">
                        <div class="card-header bg-warning text-dark fw-bold">
                            Organiser un nouvel événement
                        </div>
                        <div class="card-body">

                            <div v-if="message_error" class="alert alert-danger">{{ message_error }}</div>

                            <form @submit="submitEventForm">

                                <!-- Coach -->
                                <div class="mb-3">
                                    <label class="form-label">Choisir un coach</label>
                                    <select v-model="selectedCoach" @change="onCoachChange" class="form-select" required>
                                        <option value="">-- Sélectionnez un coach --</option>
                                        <option v-for="c in coaches" :key="c.id_coach" :value="c.id_coach">
                                            {{ c.firstName_user }} {{ c.lastName_user }}
                                        </option>
                                    </select>
                                </div>

                                <!-- Coach Info -->
                                <div v-if="coachInfo" class="p-3 border rounded bg-light mb-3">
                                    <strong>Niveau :</strong> {{ coachInfo.actualLevel_coach }}<br>
                                    <strong>Spécialisation :</strong> {{ coachInfo.specialisation_coach }}
                                </div>

                                <!-- Activity Type -->
                                <div class="mb-3">
                                    <label class="form-label">Type d'activité</label>
                                    <select v-model="selectedActivity" class="form-select" required>
                                        <option value="">-- Sélectionnez un type d'activité --</option>
                                        <option v-for="a in activityTypes" :key="a.id_typeActivities" :value="a.id_typeActivities">
                                            {{ a.name_typeActivity }}
                                        </option>
                                    </select>
                                </div>

                                <!-- Box -->
                                <div class="mb-3">
                                    <label class="form-label">Box associée</label>
                                    <select v-model="selectedBox" class="form-select" required>
                                        <option value="">-- Sélectionnez votre box --</option>
                                        <option v-for="b in boxes" :key="b.id_box" :value="b.id_box">
                                            {{ b.box_label }}
                                        </option>
                                    </select>
                                </div>

                                <!-- Dates -->
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Date et heure de début</label>
                                        <input type="datetime-local" v-model="begin" class="form-control" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Date et heure de fin</label>
                                        <input type="datetime-local" v-model="end" class="form-control" required>
                                    </div>
                                </div>

                                <!-- Recurrence -->
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="recurringSwitch" v-model="recurring">
                                    <label class="form-check-label" for="recurringSwitch">Événement récurrent (hebdomadaire)</label>
                                </div>

                                <div v-if="recurring" class="bg-success-subtle border border-success-subtle rounded p-3 mb-3">
                                    <label class="form-label">Nombre de semaines à répéter</label>
                                    <input type="number" v-model.number="weeks" class="form-control" style="max-width: 200px;" min="1" max="52">
                                    <p class="text-muted small mt-1 mb-0">Chaque semaine sera créée comme un événement séparé (max 52).</p>
                                </div>

                                <button type="submit" class="btn btn-warning text-dark fw-bold">
                                    Créer l'événement
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <script src="https://cdn.jsdelivr.net/npm/vue@3/dist/vue.global.prod.js"></script>
                <script src="/js/ownerOrganiserEvenement.js"></script>
            </div>
        </div>
    </div>
</section>
