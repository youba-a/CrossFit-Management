<?php
if ($authOwnerId === null) {
    exit;
}
?>

<section id="ownerAddMemberApp" v-cloak>
    <div class="container-fluid">
        <div class="row flex-nowrap">
            <?php
            require_once 'front/includes/dashboard_sidebar.php';
            ?>
            <div class="col py-3">
                <!-- MAIN PAGE BODY -->
                <div class="toast-container position-fixed bottom-0 end-0 p-3">
                    <div id="toastInviteSuccess" class="toast text-bg-success" role="alert" aria-live="assertive" aria-atomic="true">
                        <div class="d-flex">
                        <div class="toast-body">
                            {{message_success}}
                        </div>
                        <button type="button" class="btn-close btn-close-black me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                        </div>
                    </div>
                </div>


                <div class="container my-5">
                    <div class="card shadow-sm">
                        <div class="card-header bg-warning text-dark fw-bold">
                            Ajouter un nouveau membre
                        </div>
                        <div class="card-body">
                            <div v-if="message_error" class="alert alert-danger" role="alert">
                                {{message_error}}
                            </div>
                            <form @submit.prevent="submitInviteMember">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="prenom" class="form-label">Prénom</label>
                                        <input 
                                            type="text" 
                                            class="form-control" 
                                            id="prenom" 
                                            placeholder="Prénom" 
                                            v-model="membre.prenom"
                                            required
                                        >
                                    </div>
                                    <div class="col-md-6">
                                        <label for="nom" class="form-label">Nom</label>
                                        <input 
                                            type="text" 
                                            class="form-control" 
                                            id="nom" 
                                            placeholder="Nom"
                                            v-model="membre.nom"
                                            required
                                        >
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label for="dob" class="form-label">Date de naissance</label>
                                        <input 
                                            type="date" 
                                            class="form-control" 
                                            id="dob"
                                            v-model="membre.dob"
                                            required
                                        >
                                    </div>
                                    <div class="col-md-4">
                                        <label for="genre" class="form-label">Genre</label>
                                        <select 
                                            class="form-select" 
                                            id="genre"
                                            v-model="membre.genre"
                                            required
                                        >
                                            <option value="" disabled>Choisir...</option>
                                            <option value="homme">Homme</option>
                                            <option value="femme">Femme</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="email" class="form-label">Email</label>
                                        <input 
                                            type="email" 
                                            class="form-control" 
                                            id="email" 
                                            placeholder="email@example.com"
                                            v-model="membre.email"
                                            required
                                        >
                                    </div>
                                </div>

                                <h5 class="mt-4 mb-3">Adresse</h5>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="street" class="form-label">Rue</label>
                                        <input 
                                            type="text" 
                                            class="form-control" 
                                            id="street" 
                                            placeholder="Nom de la rue"
                                            v-model="membre.street"
                                            required
                                        >
                                    </div>
                                    <div class="col-md-2">
                                        <label for="numero" class="form-label">Numéro</label>
                                        <input 
                                            type="text" 
                                            class="form-control" 
                                            id="numero" 
                                            placeholder="Numéro"
                                            v-model="membre.numero"
                                            required
                                        >
                                    </div>
                                    <div class="col-md-2">
                                        <label for="boite" class="form-label">Boîte</label>
                                        <input 
                                            type="text" 
                                            class="form-control" 
                                            id="boite" 
                                            placeholder="Boîte"
                                            v-model="membre.boite"
                                        >
                                    </div>
                                    <div class="col-md-2">
                                        <label for="postalcode" class="form-label">Code Postal</label>
                                        <input 
                                            type="text" 
                                            class="form-control" 
                                            id="postalcode" 
                                            placeholder="1000"
                                            v-model="membre.postalcode"
                                            required
                                        >
                                    </div>
                                </div>

                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <label for="town" class="form-label">Ville</label>
                                        <input 
                                            type="text" 
                                            class="form-control" 
                                            id="town" 
                                            placeholder="Ville"
                                            v-model="membre.town"
                                            required
                                        >
                                    </div>
                                </div>

                                <button :disabled="submitting" type="submit" class="btn btn-warning text-dark fw-bold">
                                    <span v-if="submitting" class="spinner-border spinner-border-sm me-2" role="status"></span>
                                    {{ submitting ? "Envoi en cours..." : "Envoyer l'invitation" }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- MAIN PAGE BODY FIN -->
                <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
                <script src="/js/ownerAddMember.js"></script>
            </div>
        </div>
    </div>
</section>













