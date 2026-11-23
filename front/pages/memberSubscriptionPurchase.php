<?php
if ($authUserId === null) {
    exit;
}


$id_abonnement = isset($_GET['id_abonnement']) ? intval($_GET['id_abonnement']) : 0;
if ($id_abonnement <= 0) {
    echo "Identifiant d'abonnement invalide.";
    exit;
}
?>

<section id="memberSubscriptionPurchaseApp" v-cloak class="container my-5">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white fw-bold">
                    Récapitulatif - Achat d'un abonnement
                </div>
                <div class="card-body">
                    <div v-if="message_error" class="alert alert-danger">{{ message_error }}</div>
                    <div v-if="message_success" class="alert alert-success">{{ message_success }}</div>

                    <div v-if="loading" class="text-center py-4">
                        <div class="spinner-border" role="status" aria-hidden="true"></div>
                        <div class="mt-2">Chargement...</div>
                    </div>

                    <div v-else>
                        <div v-if="abonnement" class="mb-4">
                            <h3 class="mb-1">{{ abonnement.nom_abonnement }}</h3>
                            <p class="text-muted mb-2">{{ abonnement.description_abonnement }}</p>
                            <p class="fs-4 fw-bold">{{ formatPrice(abonnement.prix_abonnement) }} €</p>
                        </div>

                        <form @submit.prevent="initiatePayment">
                            <div class="mb-3" v-if="userBoxes.length > 0">
                                <label class="form-label fw-bold">Choisissez votre box</label>
                                <select class="form-select" v-model="selectedBox" required>
                                    <option disabled value="">— Sélectionner une box —</option>
                                    <option v-for="box in userBoxes" :value="box.id_box">{{ box.name_box }} — {{ box.town_box }}</option>
                                </select>
                            </div>

                            <div class="alert alert-warning" v-else>
                                Aucune box trouvée. Vous devez être associé à au moins une box pour vous abonner.
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Date de début de l'abonnement</label>
                                <input
                                    type="date"
                                    class="form-control"
                                    v-model="subscriptionBegin"
                                    :min="minDate"
                                    :max="maxDate"
                                    required
                                />
                                <div class="form-text">
                                    L'abonnement dure 1 an. La date choisie doit être au moins le <strong>{{ minDateDisplay }}</strong>.
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Date de fin (calculée)</label>
                                <input type="text" class="form-control" :value="subscriptionEndDisplay" readonly />
                                <div class="form-text">Durée : 1 an (automatique).</div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <button type="button" class="btn btn-secondary me-2" @click="goBack">
                                        <i class="bi bi-arrow-left"></i> Retour
                                    </button>
                                </div>

                                <div>
                                    <button :disabled="submitting" type="submit" class="btn btn-success fw-bold">
                                        <span v-if="submitting" class="spinner-border spinner-border-sm me-2" role="status"></span>
                                        Payer {{ abonnement ? ('— ' + formatPrice(abonnement.prix_abonnement) + '€') : '' }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <!-- Vue 3 -->
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    
    <!-- Passer l'id abonnement au JS -->
    <script>
        window.__SUB_ID__ = <?= json_encode($id_abonnement, JSON_HEX_TAG) ?>;
    </script>
    <script src="/js/memberSubscriptionPurchase.js"></script>
</section>
