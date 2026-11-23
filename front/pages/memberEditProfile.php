<?php
if ($authUserId === null) {
    exit;
}
?>

<section id="memberEditProfileApp" v-cloak>
    <div class="container-fluid">
        <div class="row flex-nowrap">
            <?php require_once 'front/includes/dashboard_sidebar.php'; ?>
            <div class="col py-3">
                <!-- MAIN PAGE BODY -->

                <!-- MODAL CONFIRMATION -->
                <div class="modal fade" id="modalAnonymize" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content border-danger">
                            <div class="modal-header bg-danger text-white">
                                <h5 class="modal-title">Confirmer l’anonymisation</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>

                            <div class="modal-body">
                                <p>
                                    Cette action va :
                                </p>
                                <ul>
                                    <li>Désactiver votre compte</li>
                                    <li>Supprimer/anonymiser vos données personnelles</li>
                                </ul>
                                <p class="text-danger fw-bold mb-0">Cette action est définitive.</p>
                            </div>

                            <div class="modal-footer">
                                <button class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                <button class="btn btn-danger fw-bold" @click="confirmAnonymization">Confirmer</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="toast-container position-fixed bottom-0 end-0 p-3">
                    <div id="toastUpdateSuccess" class="toast text-bg-success" role="alert" aria-live="assertive" aria-atomic="true">
                        <div class="d-flex">
                            <div class="toast-body">
                                Votre profil a bien été mis à jour.
                            </div>
                            <button type="button" class="btn-close btn-close-black me-2 m-auto" data-bs-dismiss="toast" aria-label="Fermer"></button>
                        </div>
                    </div>
                </div>

                <div class="toast-container position-fixed bottom-0 end-0 p-3">
                    <div id="toastDisableSuccess" class="toast text-bg-success" role="alert" aria-live="assertive" aria-atomic="true">
                        <div class="d-flex">
                            <div class="toast-body">
                                Votre demande de désactivation a été enregistrée.
                            </div>
                            <button type="button" class="btn-close btn-close-black me-2 m-auto" data-bs-dismiss="toast" aria-label="Fermer"></button>
                        </div>
                    </div>
                </div>

                <div class="container my-5">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white fw-bold">
                            Modifier mes informations de membre
                        </div>
                        <div class="card-body">
                            <div v-if="message_error" class="alert alert-danger" role="alert">
                                {{message_error}}
                            </div>

                            <form @submit.prevent="submitMemberUpdateProfile" method="post">

                                <!-- EMAIL -->
                                <h5 class="mt-4 mb-3">Adresse e-mail</h5>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="emailAddress_user" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="emailAddress_user" v-model="membre.emailAddress_user" placeholder="exemple@email.com">
                                    </div>
                                </div>

                                <!-- MOT DE PASSE -->
                                <h5 class="mt-3 mb-3">Modifier le mot de passe</h5>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="password" class="form-label">Nouveau mot de passe (laissez vide si aucun changement)</label>
                                        <input type="password" class="form-control" id="password" v-model="membre.password_user" placeholder="Mot de passe">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="confirm_password" class="form-label">Confirmer le mot de passe</label>
                                        <input type="password" class="form-control" id="confirm_password" v-model="membre.confirm_password" placeholder="Confirmer le mot de passe">
                                    </div>
                                </div>

                                <!-- AVATAR -->
                                <h5 class="mt-4 mb-3">Photo de profil</h5>
                                <div class="row mb-3 align-items-center">
                                    <div class="col-md-3">
                                        <div class="d-flex align-items-center">
                                            <div class="position-relative">
                                                <!-- Si l'utilisateur a un avatar -->
                                                <img v-if="membre.avatar_user"
                                                :src="membre.avatar_user.startsWith('blob:') ? membre.avatar_user : '/img/avatar/' + membre.avatar_user"
                                                alt="Avatar"
                                                class="rounded-circle border shadow-sm"
                                                style="width: 120px; height: 120px; object-fit: cover;">
                                                    
                                                <!-- Sinon, icône par défaut -->
                                                <i v-else class="bi bi-person-circle text-secondary" style="font-size: 6rem;"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="avatar_user" class="form-label">Changer d’avatar</label>
                                        <input type="file" class="form-control" id="avatar_user" @change="handleAvatarUpload">
                                    </div>
                                </div>

                                <!-- ADRESSE -->
                                <h5 class="mt-4 mb-3">Adresse</h5>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="streetNameAddress_user" class="form-label">Rue</label>
                                        <input type="text" class="form-control" id="streetNameAddress_user" v-model="membre.streetNameAddress_user" placeholder="Nom de la rue">
                                    </div>
                                    <div class="col-md-2">
                                        <label for="numeroAddress_user" class="form-label">Numéro</label>
                                        <input type="text" class="form-control" id="numeroAddress_user" v-model="membre.numeroAddress_user" placeholder="Numéro">
                                    </div>
                                    <div class="col-md-2">
                                        <label for="postBoxAddress_user" class="form-label">Boîte</label>
                                        <input type="text" class="form-control" id="postBoxAddress_user" v-model="membre.postBoxAddress_user" placeholder="Boîte">
                                    </div>
                                    <div class="col-md-2">
                                        <label for="postalCode_user" class="form-label">Code postal</label>
                                        <input type="text" class="form-control" id="postalCode_user" v-model="membre.postalCode_user" placeholder="1000">
                                    </div>
                                </div>

                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <label for="town_user" class="form-label">Ville</label>
                                        <input type="text" class="form-control" id="town_user" v-model="membre.town_user" placeholder="Ville">
                                    </div>
                                </div>

                                <!-- CONTACT -->
                                <h5 class="mt-4 mb-3">Contacts</h5>
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label for="telephone_user" class="form-label">Téléphone principal</label>
                                        <input type="tel" class="form-control" id="telephone_user" v-model="membre.telephone_user" placeholder="Ex: +32 475 12 34 56">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="firstContactTelephone_user" class="form-label">Premier contact d'urgence</label>
                                        <input type="tel" class="form-control" id="firstContactTelephone_user" v-model="membre.firstContactTelephone_user" placeholder="Ex: +32 489 23 45 67">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="secondContactTelephone_user" class="form-label">Second contact d'urgence</label>
                                        <input type="tel" class="form-control" id="secondContactTelephone_user" v-model="membre.secondContactTelephone_user" placeholder="Ex: +32 491 98 76 54">
                                    </div>
                                </div>

                                <!-- INFOS PHYSIQUES -->
                                <h5 class="mt-4 mb-3">Informations physiques</h5>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="poids_user" class="form-label">Poids (kg)</label>
                                        <input type="number" step="0.1" class="form-control" id="poids_user" v-model="membre.poids_user" placeholder="Ex: 70">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="taille_user" class="form-label">Taille (cm)</label>
                                        <input type="number" step="1" class="form-control" id="taille_user" v-model="membre.taille_user" placeholder="Ex: 175">
                                    </div>
                                </div>

                                <!-- BLESSURES -->
                                <h5 class="mt-4 mb-3">État de santé</h5>
                                <div class="row mb-4">
                                    <div class="col-md-12">
                                        <label for="wound_user" class="form-label">Blessures ou remarques médicales</label>
                                        <textarea class="form-control" id="wound_user" v-model="membre.wound_user" rows="3" placeholder="Décrire les blessures, allergies, ou informations médicales importantes..."></textarea>
                                    </div>
                                </div>

                                <!-- COACH -->
                                <div v-if="membre.isCoach">
                                <h5 class="mt-4 mb-3">Informations Coach</h5>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="specialisation_coach" class="form-label">Spécialisation</label>
                                            <input type="text" class="form-control" id="specialisation_coach" v-model="membre.specialisation_coach" placeholder="Ex: CrossFit, Haltérophilie, Fitness">
                                        </div>
                                    </div>
                                </div>

                                <!-- OWNER -->
                                <div v-if="membre.isOwner">
                                    <h5 class="mt-4 mb-3">Informations Propriétaire</h5>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="email_owner" class="form-label">Email propriétaire</label>
                                            <input type="email" class="form-control" id="email_owner" v-model="membre.email_owner" placeholder="exemple@email.com">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="telephone_owner" class="form-label">Téléphone propriétaire</label>
                                            <input type="tel" class="form-control" id="telephone_owner" v-model="membre.telephone_owner" placeholder="Ex: +32 475 12 34 56">
                                        </div>
                                    </div>

                                    <div class="row mb-4">
                                        <div class="col-md-4">
                                            <label for="salary_owner" class="form-label">Salaire propriétaire (€)</label>
                                            <input type="number" step="0.01"class="form-control" id="salary_owner" v-model="membre.salary_owner" placeholder="Ex: 2500">
                                        </div>
                                    </div>
                                </div>

                                <!-- ACTIONS RGPD -->
                                <div class="card border-danger mt-4 mb-4">
                                    <div class="card-header bg-danger text-white fw-bold">
                                        ⚠️ Actions RGPD
                                    </div>
                                    <div class="card-body">
                                        <p class="mb-3">
                                            Vous pouvez demander la <strong>désactivation</strong> et l’<strong>anonymisation</strong> de votre compte.
                                            <br>
                                            Cette action est <strong>irréversible</strong>.
                                        </p>

                                        <!-- Si la demande est déjà faite -->
                                        <div v-if="membre.needDisabled == 1" class="alert alert-success">
                                            Votre demande a été prise en compte.
                                        </div>

                                        <!-- Sinon, afficher le bouton -->
                                        <button 
                                            v-else
                                            type="button" 
                                            class="btn btn-outline-danger fw-bold" 
                                            @click="openAnonymizeModal"
                                        >
                                            <i class="bi bi-person-x me-2"></i> Désactiver & anonymiser mon compte
                                        </button>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary text-white fw-bold">
                                    Enregistrer les informations
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
                <script src="/js/memberEditProfile.js"></script>
            </div>
        </div>
    </div>
</section>
