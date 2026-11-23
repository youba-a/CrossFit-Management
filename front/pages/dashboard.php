<?php
if ($authUserId === null) {
    exit;
}
?>

<section id="memberDashboardApp" v-cloak>
  <div class="container-fluid">
    <div class="row flex-nowrap">
      <?php require_once 'front/includes/dashboard_sidebar.php'; ?>

     <div class="col py-4">

      <div class="row g-4">

        <!-- === SUBSCRIPTION CARD === -->
        <div class="col-12 col-md-6 col-xl-4">
          <div class="card shadow-sm border-0 rounded-4 h-100">
            <div class="card-header bg-primary text-white fw-bold rounded-top-4">
              <i class="bi bi-person-vcard me-2"></i> Mon abonnement
            </div>

            <div class="card-body" v-if="subscription">
              <h5 class="fw-bold">{{ subscription.nom_abonnement }}</h5>
              <p class="text-muted mb-1">{{ subscription.description_abonnement }}</p>
              <p class="mb-1"><strong>Box :</strong> {{ subscription.name_box }} ({{ subscription.town_box }})</p>
              <p class="mb-1"><strong>Début :</strong> {{ formatDate(subscription.subscriptionBegin_boxuser) }}</p>
              <p class="mb-1"><strong>Fin :</strong> {{ formatDate(subscription.subscriptionEnd_boxuser) }}</p>
              <p class="fw-bold mt-2">{{ subscription.prix_abonnement }} € / an</p>
            </div>

            <div class="card-body text-muted d-flex align-items-center justify-content-center text-center" v-else>
              Aucun abonnement trouvé.
            </div>
          </div>
        </div>


        <!-- === FUTURE SESSIONS === -->
        <div class="col-12 col-md-6 col-xl-4">
          <div class="card shadow-sm border-0 rounded-4 h-100">
            <div class="card-header bg-success text-white fw-bold rounded-top-4">
              <i class="bi bi-calendar-check me-2"></i> Mes prochaines réservations
            </div>

            <div class="card-body p-3" v-if="futureSessions.length > 0">
              <div class="table-responsive">
                <table class="table table-striped table-hover align-middle mb-0">
                  <thead class="table-light">
                    <tr>
                      <th>Date</th>
                      <th>Activité</th>
                      <th>Coach</th>
                      <th>Heure</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="s in futureSessions"
                    :key="s.id_organisation"
                    @click="goToOrganisation(s.id_organisation)"
                    style="cursor:pointer">
                      <td>{{ formatDate(s.begin_organisation) }}</td>
                      <td>
                        <span class="badge px-2" :style="{ backgroundColor: getColor(s.color_typeActivity) }">
                          {{ s.name_typeActivity }}
                        </span>
                      </td>
                      <td>{{ s.coach_firstname }} {{ s.coach_lastname }}</td>
                      <td>{{ formatTime(s.begin_organisation) }} - {{ formatTime(s.end_organisation) }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <div class="card-body text-muted d-flex align-items-center justify-content-center text-center"
                v-else>
              Aucune session réservée.
            </div>
          </div>
        </div>


        <!-- === UPCOMING AVAILABLE SESSIONS === -->
        <div class="col-12 col-md-6 col-xl-4">
          <div class="card shadow-sm border-0 rounded-4 h-100">
            <div class="card-header bg-info text-white fw-bold rounded-top-4">
              <i class="bi bi-calendar-plus me-2"></i> Cours à venir (non inscrits)
            </div>

            <div class="card-body p-3" v-if="upcomingNotRegistered.length > 0">
              <div class="table-responsive">
                <table class="table table-striped table-hover align-middle mb-0">
                  <thead class="table-light">
                    <tr>
                      <th>Date</th>
                      <th>Activité</th>
                      <th>Coach</th>
                      <th>Heure</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="s in upcomingNotRegistered" :key="s.id_organisation">
                      <td>{{ formatDate(s.begin_organisation) }}</td>
                      <td>
                        <span class="badge px-2" :style="{ backgroundColor: getColor(s.color_typeActivity) }">
                          {{ s.name_typeActivity }}
                        </span>
                      </td>
                      <td>{{ s.coach_firstname }} {{ s.coach_lastname }}</td>
                      <td>{{ formatTime(s.begin_organisation) }} - {{ formatTime(s.end_organisation) }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <div class="card-body text-muted d-flex align-items-center justify-content-center text-center"
                v-else>
              Aucun cours disponible.
            </div>
          </div>
        </div>


        <!-- === PAST SESSIONS === (LARGE CARD) -->
        <div class="col-12">
          <div class="card shadow-sm border-0 rounded-4 mb-4">
            <div class="card-header bg-secondary text-white fw-bold rounded-top-4">
              <i class="bi bi-clock-history me-2"></i> Sessions passées & feedbacks
            </div>

            <div class="card-body p-3">
              <div v-if="pastSessions.length === 0" class="text-muted">Aucune session passée.</div>

              <div class="table-responsive" v-else>
                <table class="table table-striped table-hover align-middle mb-0">
                  <thead class="table-light">
                    <tr>
                      <th>Date</th>
                      <th>Activité</th>
                      <th>Coach</th>
                      <th>Feedback</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="s in pastSessions"
                      :key="s.id_organisation"
                      @click="goToOrganisation(s.id_organisation)"
                      style="cursor:pointer">
                      <td>{{ formatDate(s.begin_organisation) }}</td>
                      <td>
                        <span class="badge px-2" :style="{ backgroundColor: getColor(s.color_typeActivity) }">
                          {{ s.name_typeActivity }}
                        </span>
                      </td>
                      <td>{{ s.coach_firstname }} {{ s.coach_lastname }}</td>
                      <td>
                        <span v-if="s.feedbackCoachOnUser_organisationuser">{{ s.feedbackCoachOnUser_organisationuser }}</span>
                        <span v-else>—</span>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

          </div>
        </div>

      </div>
    </div>

    </div>
  </div>
</section>

<style>
.card:hover {
  transform: translateY(-2px);
  transition: all 0.2s ease-in-out;
}
.table-hover tbody tr:hover {
  background-color: #f1f3f5;
}
</style>

<script src="https://unpkg.com/vue@3"></script>
<script src="js/dashboard.js"></script>
