<section id="tarif" class="container py-5">
  <div class="text-center mb-5">
    <h2 class="titre-section">Nos Tarifs</h2>
    <p class="sous-titre">Des formules adaptées à tous les niveaux — rejoignez la communauté CrossFit !</p>
  </div>

  <div id="tarifApp">
    <div v-if="error" class="alert alert-danger text-center">{{ error }}</div>

    <div class="row g-4 justify-content-center">
      <div
        v-for="abo in abonnements"
        :key="abo.id_abonnement"
        class="col-md-6 col-lg-4"
      >
        <div class="tarif-card">
          <div class="tarif-header">
            <h3 class="tarif-nom">{{ abo.nom_abonnement }}</h3>
            <div class="tarif-price">
              <span class="prix">{{ abo.prix_abonnement }}</span>
              <span class="euro">€/an</span>
            </div>
          </div>

          <div class="tarif-body" style="white-space: pre-line">{{ abo.description_abonnement }}</div>

          <div class="tarif-footer">
            <a class="btn-abo"
              :href="'index.php?p=memberSubscriptionPurchase&id_abonnement=' + abo.id_abonnement">
                Choisir cette offre
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Scripts -->
<script src="/js/tarif.js"></script>
