<section id="forgotPasswordApp" v-cloak>
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-6">

                <!-- CARD -->
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white fw-bold">
                        Mot de passe oublié
                    </div>

                    <div class="card-body">

                        <!-- MESSAGE ERREUR -->
                        <div v-if="errorMessage" class="alert alert-danger" role="alert">
                            {{ errorMessage }}
                        </div>

                        <!-- MESSAGE SUCCESS -->
                        <div v-if="successMessage" class="alert alert-success" role="alert">
                            {{ successMessage }}
                        </div>

                        <form @submit.prevent="submitForgotPassword">

                            <div class="mb-3">
                                <label for="email" class="form-label">Adresse email</label>
                                <input type="email"
                                       id="email"
                                       class="form-control"
                                       placeholder="exemple@email.com"
                                       v-model="email"
                                       required>
                            </div>

                            <button :disabled="submitting" type="submit" class="btn btn-primary fw-bold w-100">
                                <span v-if="submitting" class="spinner-border spinner-border-sm me-2" role="status"></span>
                                {{ submitting ? "Envoi en cours..." : "Envoyer le lien de réinitialisation" }}
                            </button>
                        </form>

                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- TOAST SUCCESS -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="toastSuccess" class="toast text-bg-success" role="alert">
            <div class="d-flex">
                <div class="toast-body">
                    Votre demande a été envoyée.
                </div>
                <button type="button" class="btn-close btn-close-black me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    </div>
</section>


<!-- Vue -->
<script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>

<!-- Ton JS -->
<script src="js/memberForgotPassword.js"></script>
