<?php
if ($authUserId === null) {
    exit;
}
?>

<section id="memberOrganisationViewApp" v-cloak>
    <div class="container-fluid">
        <div class="row flex-nowrap">
            <?php require_once 'front/includes/dashboard_sidebar.php'; ?>
            <div class="col py-3">

                <!-- MAIN PAGE BODY -->
                <div v-if="organisation" class="container my-5">

                    <!-- Informations de l'événement -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-primary text-white fw-bold">
                            Détail de l'évènement
                        </div>
                        <div class="card-body">
                            <div class="row mb-3 p-3 rounded" style="background-color: rgba(13,110,253,0.1);">
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
                            </div>
                        </div>
                    </div>

                    <!-- Liste des membres -->
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white fw-bold">
                            Membres participants ({{ organisation.total_members || 0 }})
                        </div>
                        <div class="card-body">
                            <div v-if="organisation.members.length > 0" class="row g-3">
                                <div class="col-6 col-md-4 col-lg-3" v-for="member in organisation.members" :key="member.id_user">
                                    <div class="card text-center border-0 shadow-sm h-100 py-3">
                                        
                                        <!-- Avatar ou icône par défaut -->
                                        <template v-if="member.avatar_user">
                                            <img :src="'/img/avatar/' + member.avatar_user"
                                                class="rounded-circle mx-auto d-block"
                                                style="width:80px; height:80px; object-fit:cover;">
                                        </template>
                                        <template v-else>
                                            <i class="bi bi-person-circle text-secondary" style="font-size:80px;"></i>
                                        </template>

                                        <!-- Prénom -->
                                        <div class="card-body">
                                            <h6 class="card-title mb-0">{{ member.firstName_user }}</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="alert alert-info">
                                Aucun membre inscrit pour le moment.
                            </div>
                        </div>
                    </div>

                    <div class="card shadow-sm mt-4">
                        <div class="card-header bg-primary text-white fw-bold">
                            Donnez-nous un feedback sur cet évènement!
                        </div>

                        <div class="card-body">

                            <!-- Si un feedback existe déjà -->
                            <div v-if="organisation.feedback_event" class="alert alert-info">
                                <strong>Votre feedback existant :</strong><br>
                                {{ organisation.feedback_event }}
                            </div>

                            <!-- Formulaire -->
                            <label class="form-label mt-2">Ajouter / Modifier votre feedback :</label>
                            <textarea class="form-control" rows="4"
                                    v-model="feedbackText"
                                    placeholder="Écrivez ici votre avis sur l'évènement..."></textarea>

                            <button class="btn btn-success mt-3"
                                    :disabled="isSubmitting || !String(feedbackText).trim()"
                                    @click="sendFeedback">
                                <span v-if="!isSubmitting">Envoyer</span>
                                <span v-else>Envoi en cours.</span>
                            </button>

                            <!-- Messages -->
                            <div v-if="feedbackSuccess" class="alert alert-success mt-3">
                                {{ feedbackSuccess }}
                            </div>
                            <div v-if="feedbackError" class="alert alert-danger mt-3">
                                {{ feedbackError }}
                            </div>

                        </div>
                    </div>

                    <!-- Feedback sur le coach -->
                    <div class="card shadow-sm mt-4">
                        <div class="card-header bg-primary text-white fw-bold">
                            Feedback sur le coach
                        </div>

                        <div class="card-body">

                            <div v-if="organisation.feedback_coach" class="alert alert-info">
                                <strong>Feedback existant :</strong><br>
                                {{ organisation.feedback_coach }}
                            </div>

                            <label class="form-label mt-2">Ajouter / Modifier votre feedback pour le coach:</label>
                            <textarea class="form-control" rows="4"
                                    v-model="coachFeedbackText"
                                    placeholder="Écrivez ici votre avis sur le coach de cet évènement..."></textarea>

                            <button class="btn btn-success mt-3"
                                    :disabled="isSubmittingCoach || !String(coachFeedbackText).trim()"
                                    @click="sendCoachFeedback">
                                <span v-if="!isSubmittingCoach">Envoyer</span>
                                <span v-else>Envoi...</span>
                            </button>

                            <div v-if="coachFeedbackSuccess" class="alert alert-success mt-3">
                                {{ coachFeedbackSuccess }}
                            </div>
                            <div v-if="coachFeedbackError" class="alert alert-danger mt-3">
                                {{ coachFeedbackError }}
                            </div>

                        </div>
                    </div>
                    
                </div>
                
                <div v-else-if="message_error" class="alert alert-danger my-5 text-center">
                    {{ message_error }}
                </div>

                <div v-else class="text-center my-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Chargement.</span>
                    </div>
                </div>

                

            </div>
        </div>
    </div>
</section>

<!-- Dépendances -->
                <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
                <script src="/js/memberOrganisationView.js"></script>
