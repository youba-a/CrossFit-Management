<section id="resetPasswordApp" v-cloak>
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-6">

                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white fw-bold">
                        Réinitialiser mon mot de passe
                    </div>

                    <div class="card-body">
                        <!-- LOADING -->
                        <div v-if="loading" class="text-center py-4">
                            <div class="spinner-border text-primary"></div>
                            <p class="mt-2">Vérification du lien...</p>
                        </div>

                        <!-- MESSAGE ERREUR TOKEN -->
                        <div v-if="!loading && message_error" class="alert alert-danger">
                            {{ message_error }}
                        </div>

                        <!-- FORMULAIRE SI TOKEN VALIDE -->
                        <div v-if="!loading && tokenValid">
                            <div v-if="message_success" class="alert alert-success">
                                {{ message_success }}
                            </div>

                            <form @submit.prevent="submitResetPassword">
                                <div class="mb-3">
                                    <label for="newPassword" class="form-label">Nouveau mot de passe</label>
                                    <input type="password" class="form-control" id="newPassword" v-model="form.newPassword" required>
                                </div>

                                <div class="mb-3">
                                    <label for="confirmPassword" class="form-label">Confirmer le mot de passe</label>
                                    <input type="password" class="form-control" id="confirmPassword" v-model="form.confirmPassword" required>
                                </div>

                                <button type="submit" class="btn btn-primary w-100 fw-bold">
                                    Réinitialiser mon mot de passe
                                </button>
                            </form>
                        </div>
                    </div>

                </div>

                <!-- TOAST SUCCESS -->
                <div class="toast-container position-fixed bottom-0 end-0 p-3">
                    <div id="toastSuccess" class="toast text-bg-success" role="alert">
                        <div class="d-flex">
                            <div class="toast-body">
                                Mot de passe réinitialisé avec succès !
                            </div>
                            <button type="button" class="btn-close btn-close-black me-2 m-auto" data-bs-dismiss="toast"></button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script src="/js/memberResetPassword.js"></script>
</section>
