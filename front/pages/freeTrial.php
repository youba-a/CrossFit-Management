
<!-- ==========================================================
  Page : Essai gratuit - CrossFit Mind Steel Bastogne
  Description : Affiche le formulaire Vue.js de demande d’essai
============================================================== -->

<!-- SECTION : Formulaire d’essai gratuit -->
<section id="freeTrial" class="container my-5" style="max-width: 900px;">
  <div class="text-center mb-5">
    <h2 class="fw-bold">Demandez votre essai gratuit</h2>
    <p class="text-muted">Choisissez une séance découverte et remplissez vos informations pour réserver votre place.</p>
  </div>

  <div id="freeTrialApp">
    <form @submit.prevent="sendForm" class="p-4 border rounded-4 shadow-sm bg-white">

      <!-- Infos personnelles -->
      <div class="row mb-3">
        <div class="col-md-6">
          <label for="prenom_discovery" class="form-label">Prénom *</label>
          <input v-model="form.prenom_discovery" type="text" id="prenom_discovery" class="form-control" required>
        </div>
        <div class="col-md-6">
          <label for="nom_discovery" class="form-label">Nom *</label>
          <input v-model="form.nom_discovery" type="text" id="nom_discovery" class="form-control" required>
        </div>
      </div>

      <div class="mb-3">
        <label for="email_discovery" class="form-label">Adresse e-mail *</label>
        <input v-model="form.email_discovery" type="email" id="email_discovery" class="form-control" placeholder="exemple@mail.com" required>
      </div>

      <div class="mb-3">
        <label for="telephone_discovery" class="form-label">Téléphone *</label>
        <input v-model="form.telephone_discovery" type="tel" id="telephone_discovery" class="form-control" placeholder="+32 470 00 00 00" required>
      </div>

      <!-- Séance découverte -->
      <div class="mb-3">
        <label for="idOrganisation_discovery" class="form-label">Séance découverte *</label>
        <select v-model="form.idOrganisation_discovery" id="idOrganisation_discovery" class="form-select" required>
          <option disabled value="">Sélectionnez une séance</option>
          <option v-for="act in activities" :key="act.id" :value="act.id">
            {{ act.label }}
          </option>
        </select>
      </div>

      <!-- Notes -->
      <div class="mb-3">
        <label for="note_discovery" class="form-label">Notes (facultatif)</label>
        <textarea v-model="form.note_discovery" id="note_discovery" class="form-control" rows="3" placeholder="Vos remarques ou préférences..."></textarea>
      </div>

      <!-- Conditions -->
      <div class="form-check mb-3">
        <input v-model="form.accepted" type="checkbox" id="accepted" class="form-check-input">
        <label for="accepted" class="form-check-label">
          J'accepte les <a href="#">conditions générales</a>.
        </label>
      </div>

      <!-- Messages -->
      <div v-if="error" class="alert alert-danger py-2">{{ error }}</div>
      <div v-if="success" class="alert alert-success py-2">{{ success }}</div>

      <!-- Bouton -->
      <div class="d-grid">
        <button type="submit" class="btn btn-primary btn-lg">Envoyer ma demande</button>
      </div>
    </form>

    <!-- Toast de confirmation -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1055">
      <div id="toastSuccess" class="toast align-items-center text-bg-success border-0" role="alert">
        <div class="d-flex">
          <div class="toast-body">Demande envoyée avec succès !</div>
          <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Vue.js -->
<script src="https://unpkg.com/vue@3.3.4/dist/vue.global.prod.js"></script>

<!-- Vue.js -->
<script src="/js/freeTrial.js"></script>
