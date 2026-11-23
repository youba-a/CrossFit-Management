<section id="contact" class="container my-5" style="max-width: 900px;">
  <div class="text-center mb-5">
    <h2 class="fw-bold">Nous contacter</h2>
    <p class="text-muted">Une question ? Envie d'essayer une séance ? Écrivez-nous !</p>
  </div>

  <div id="contactApp">
    <div class="row g-4">
      <!-- Formulaire -->
      <div class="col-lg-6 col-sm-12">
        <form @submit.prevent="sendForm" class="p-4 border rounded-4 shadow-sm bg-white">
          <div class="row mb-3">
            <div class="col-md-6">
              <label for="guestFirstName" class="form-label">Prénom *</label>
              <input v-model="form.guestFirstName" type="text" id="guestFirstName" class="form-control" placeholder="Votre prénom" required>
            </div>
            <div class="col-md-6">
              <label for="guestLastName" class="form-label">Nom *</label>
              <input v-model="form.guestLastName" type="text" id="guestLastName" class="form-control" placeholder="Votre nom" required>
            </div>
          </div>

          <div class="mb-3">
            <label for="guestEmail" class="form-label">Adresse e-mail *</label>
            <input v-model="form.guestEmail" type="email" id="guestEmail" class="form-control" placeholder="exemple@mail.com" required>
          </div>

          <div class="mb-3">
            <label for="subject" class="form-label">Sujet *</label>
            <input v-model="form.subject" type="text" id="subject" class="form-control" placeholder="Sujet de votre message" required>
          </div>

          <div class="mb-3">
            <label for="message" class="form-label">Message *</label>
            <textarea v-model="form.message" id="message" class="form-control" rows="5" placeholder="Votre message..." required></textarea>
          </div>

          <!-- Messages d'erreur / succès -->
          <div v-if="error" class="alert alert-danger py-2">{{ error }}</div>
          <div v-if="success" class="alert alert-success py-2">{{ success }}</div>

          <div class="d-grid">
            <button :disabled="submitting" type="submit" class="btn btn-primary btn-lg">
                <span v-if="submitting" class="spinner-border spinner-border-sm me-2" role="status"></span>
                {{ submitting ? "Envoi en cours..." : "Envoyer le message" }}
            </button>
          </div>
        </form>
      </div>

      <!-- Carte Google Map -->
      <div class="col-lg-6 col-sm-12">
        <div class="map-container rounded-4 shadow-sm overflow-hidden" style="height: 100%; min-height: 400px;">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d6208.0515278433395!2d5.6910101937360595!3d50.00233882010477!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47c018f7c022f7a3%3A0xd2dd1c1016cbe025!2sRte%20de%20Marche%20165%2C%206600%20Bastogne!5e1!3m2!1sfr!2sbe!4v1761699727066!5m2!1sfr!2sbe" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
      </div>
    </div>

    <!-- Toast Bootstrap -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1055">
      <div id="toastSuccess" class="toast align-items-center text-bg-success border-0" role="alert">
        <div class="d-flex">
          <div class="toast-body">
            Message envoyé avec succès !
          </div>
          <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
      </div>
    </div>
  </div>
</section>

<script src="https://unpkg.com/vue@3.3.4/dist/vue.global.prod.js"></script>
<script src="/js/contact.js"></script>