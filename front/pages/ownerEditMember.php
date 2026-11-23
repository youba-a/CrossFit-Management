<?php
if ($authOwnerId === null) {
    exit;
}
?>
<section id="ownerEditMemberApp" v-cloak>
    <div class="container-fluid">
        <div class="row flex-nowrap">
            <?php
            require_once 'front/includes/dashboard_sidebar.php';
            ?>
            <div class="col py-3">
                <!-- MAIN PAGE BODY -->

                <!-- Modal d’avertissement role owner-->
                <div class="modal fade" id="confirmOwnerModal" tabindex="-1" aria-labelledby="confirmOwnerModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-warning">
                    <div class="modal-header bg-warning text-dark">
                        <h5 class="modal-title fw-bold" id="confirmOwnerModalLabel">
                        ⚠️ Confirmation
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                    </div>

                    <div class="modal-body">
                        <p>
                        Vous êtes sur le point d’attribuer le rôle <strong>Owner</strong> à cet utilisateur.<br>
                        Ce rôle donne un accès complet à la gestion du club.
                        </p>
                        <p class="fw-bold mb-0">Êtes-vous sûr de vouloir continuer ?</p>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Annuler
                        </button>
                        <button type="button" class="btn btn-warning" id="confirmOwnerBtn">
                        Confirmer
                        </button>
                    </div>
                    </div>
                </div>
                </div>


                <!-- Modal d’anonymisation RGPD -->
                <div class="modal fade" id="confirmAnonymizeModal" tabindex="-1" aria-labelledby="confirmAnonymizeModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-danger">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title fw-bold" id="confirmAnonymizeModalLabel">⚠️ Confirmation RGPD</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                    </div>
                    <div class="modal-body">
                        <p>
                        Vous êtes sur le point d’anonymiser ce membre.<br>
                        Toutes ses données personnelles seront supprimées ou rendues anonymes.
                        </p>
                        <p class="fw-bold mb-0">Êtes-vous sûr de vouloir continuer ?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="button" class="btn btn-danger" id="confirmAnonymizeBtn" @click="confirmAnonymize">Confirmer</button>
                    </div>
                    </div>
                </div>
                </div>

                <!-- TOAST DE SUCCÈS -->
                <div class="toast-container position-fixed bottom-0 end-0 p-3">
                <div id="toastEditSuccess" class="toast text-bg-success" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="d-flex">
                    <div class="toast-body">
                        {{message_success}}
                    </div>
                    <button type="button" class="btn-close btn-close-black me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                </div>
                </div>

                <!-- FORMULAIRE D'ÉDITION -->
                <div class="container my-5">
                <div class="card shadow-sm">
                    <div class="card-header bg-warning text-dark fw-bold">
                    Modifier le profil du membre
                    </div>

                    <div class="card-body">
                    <div v-if="message_error" class="alert alert-danger" role="alert">
                        {{message_error}}
                    </div>

                    <form @submit.prevent="submitEditMember">
                        <!-- Identité -->
                        <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="firstName_user" class="form-label">Prénom</label>
                            <input
                            type="text"
                            class="form-control"
                            id="firstName_user"
                            v-model="membre.firstName_user"
                            required
                            >
                        </div>
                        <div class="col-md-6">
                            <label for="lastName_user" class="form-label">Nom</label>
                            <input
                            type="text"
                            class="form-control"
                            id="lastName_user"
                            v-model="membre.lastName_user"
                            required
                            >
                        </div>
                        </div>

                        <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="birthdate_user" class="form-label">Date de naissance</label>
                            <input
                            type="date"
                            class="form-control"
                            id="birthdate_user"
                            v-model="membre.birthdate_user"
                            required
                            >
                        </div>
                        <div class="col-md-4">
                            <label for="gender_user" class="form-label">Genre</label>
                            <select
                            class="form-select"
                            id="gender_user"
                            v-model="membre.gender_user"
                            required
                            >
                            <option value="" disabled>Choisir...</option>
                            <option value="M">Homme</option>
                            <option value="F">Femme</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="emailAddress_user" class="form-label">Email</label>
                            <input
                            type="email"
                            class="form-control"
                            id="emailAddress_user"
                            placeholder="email@example.com"
                            v-model="membre.emailAddress_user"
                            required
                            >
                        </div>
                        </div>

                        <!-- Coordonnées -->
                        <h5 class="mt-4 mb-3">Adresse</h5>
                        <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="streetNameAddress_user" class="form-label">Rue</label>
                            <input
                            type="text"
                            class="form-control"
                            id="streetNameAddress_user"
                            v-model="membre.streetNameAddress_user"
                            placeholder="Nom de la rue"
                            required
                            >
                        </div>
                        <div class="col-md-2">
                            <label for="numeroAddress_user" class="form-label">Numéro</label>
                            <input
                            type="text"
                            class="form-control"
                            id="numeroAddress_user"
                            v-model="membre.numeroAddress_user"
                            placeholder="Numéro"
                            required
                            >
                        </div>
                        <div class="col-md-2">
                            <label for="postBoxAddress_user" class="form-label">Boîte</label>
                            <input
                            type="text"
                            class="form-control"
                            id="postBoxAddress_user"
                            v-model="membre.postBoxAddress_user"
                            placeholder="Boîte"
                            >
                        </div>
                        <div class="col-md-2">
                            <label for="postalCode_user" class="form-label">Code postal</label>
                            <input
                            type="text"
                            class="form-control"
                            id="postalCode_user"
                            v-model="membre.postalCode_user"
                            placeholder="1000"
                            required
                            >
                        </div>
                        </div>

                        <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="town_user" class="form-label">Ville</label>
                            <input
                            type="text"
                            class="form-control"
                            id="town_user"
                            v-model="membre.town_user"
                            placeholder="Ville"
                            required
                            >
                        </div>
                        <div class="col-md-6">
                            <label for="telephone_user" class="form-label">Téléphone</label>
                            <input
                            type="text"
                            class="form-control"
                            id="telephone_user"
                            v-model="membre.telephone_user"
                            placeholder="+32 ..."
                            >
                        </div>
                        </div>

                        <!-- Données physiques -->
                        <h5 class="mt-4 mb-3">Informations physiques</h5>
                        <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="poids_user" class="form-label">Poids (kg)</label>
                            <input
                            type="number"
                            class="form-control"
                            id="poids_user"
                            v-model="membre.poids_user"
                            min="0"
                            step="0.1"
                            >
                        </div>
                        <div class="col-md-6">
                            <label for="taille_user" class="form-label">Taille (cm)</label>
                            <input
                            type="number"
                            class="form-control"
                            id="taille_user"
                            v-model="membre.taille_user"
                            min="0"
                            step="0.1"
                            >
                        </div>
                        </div>

                        <!-- COACH -->
                        <div v-if="membre.isCoach">
                            <h5 class="mt-4 mb-3">Coach</h5>
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="actualLevel_coach" class="form-label">Niveau actuel</label>
                                    <input type="text" class="form-control" id="actualLevel_coach" v-model="membre.actualLevel_coach" placeholder="Ex: Avancé">
                                </div>
                                <div class="col-md-4">
                                    <label for="salary_coach" class="form-label">Salaire (€)</label>
                                    <input type="number" class="form-control" id="salary_coach" v-model="membre.salary_coach" min="0" step="0.01">
                                </div>
                                <div class="col-md-4">
                                    <label for="specialisation_coach" class="form-label">Spécialisation</label>
                                    <input type="text" class="form-control" id="specialisation_coach" v-model="membre.specialisation_coach" placeholder="Ex: Fitness">
                                </div>
                            </div>
                        </div>

                        <!-- OWNER -->
                        <div v-if="membre.isOwner">
                            <h5 class="mt-4 mb-3">Owner</h5>
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="email_owner" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email_owner" v-model="membre.email_owner" placeholder="email@example.com">
                                </div>
                                <div class="col-md-4">
                                    <label for="telephone_owner" class="form-label">Téléphone</label>
                                    <input type="text" class="form-control" id="telephone_owner" v-model="membre.telephone_owner" placeholder="+32 ...">
                                </div>
                                <div class="col-md-4">
                                    <label for="salary_owner" class="form-label">Salaire (€)</label>
                                    <input type="number" class="form-control" id="salary_owner" v-model="membre.salary_owner" min="0" step="0.01">
                                </div>
                            </div>
                        </div>


                        <!-- ROLE -->
                        <h5 class="mt-4 mb-3">Rôle</h5>
                        <div class="row mb-3">
                            <div class="col-md-6 d-flex align-items-center">
                                <input type="checkbox" id="isCoach" v-model="membre.isCoach" class="form-check-input me-2">
                                <label for="isCoach" class="form-label mb-0">Coach</label>
                            </div>
                            <div class="col-md-6 d-flex align-items-center">
                                <input type="checkbox" id="isOwner" v-model="membre.isOwner" @change="toggleOwner(membre)" class="form-check-input me-2">
                                <label for="isOwner" class="form-label mb-0">Owner</label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-warning text-dark fw-bold">
                        Enregistrer les modifications
                        </button>

                        <hr class="my-4">

                        <!-- ACTIONS RGPD -->
                        <div class="card border-danger mt-4">
                        <div class="card-header bg-danger text-white fw-bold">
                            ⚠️ Actions RGPD
                        </div>
                        <div class="card-body">
                            <p class="mb-3">
                            L’anonymisation supprimera ou remplacera toutes les données personnelles de ce membre (nom, adresse, email, etc.).
                            <br>
                            Cette action est <strong>irréversible</strong>.
                            </p>
                            <button 
                            type="button" 
                            class="btn btn-outline-danger fw-bold" 
                            @click="openAnonymizeModal"
                            >
                            <i class="bi bi-person-x me-2"></i> Anonymiser le membre
                            </button>
                        </div>
                        </div>
                    </form>
                    </div>
                </div>
                </div>


                <!-- MAIN PAGE BODY FIN -->
                <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
                <script src="/js/ownerEditMember.js"></script>
            </div>
        </div>
    </div>
</section>