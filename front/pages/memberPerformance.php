<?php
if ($authUserId === null) {
    exit;
}
?>

<section id="memberPerformanceApp" v-cloak>
  <div class="container-fluid">
    <div class="row flex-nowrap">
      <?php require_once 'front/includes/dashboard_sidebar.php'; ?>

      <div class="col py-4 responsive">

        <!-- === SUMMARY WIDGETS === -->
        <div class="row mb-4 g-3">
          <!-- Sessions totales -->
          <div class="col-md-3">
            <div class="card shadow-sm border-0 rounded-4 h-100">
              <div class="card-header bg-primary text-white d-flex align-items-center rounded-top-4">
                <i class="bi bi-calendar-check me-2"></i> Sessions totales
              </div>
              <div class="card-body text-center">
                <h3 class="fw-bold mb-1">{{ filteredPerformances.length }}</h3>
                <small class="text-muted">Nombre total de sessions</small>
              </div>
            </div>
          </div>

          <!-- Streak de régularité -->
          <div class="col-md-3">
            <div class="card shadow-sm border-0 rounded-4 h-100">
              <div class="card-header bg-success text-white d-flex align-items-center rounded-top-4">
                <i class="bi bi-bar-chart-line me-2"></i> Streak de régularité
              </div>
              <div class="card-body text-center">
                <h3 class="fw-bold mb-1">{{ consistencyStreak }} sem.</h3>
                <small class="text-muted">Nombre de semaines consécutives</small>
              </div>
            </div>
          </div>

          <!-- Moyenne / semaine -->
          <div class="col-md-3">
            <div class="card shadow-sm border-0 rounded-4 h-100">
              <div class="card-header bg-warning text-white d-flex align-items-center rounded-top-4">
                <i class="bi bi-clock me-2"></i> Moyenne / semaine
              </div>
              <div class="card-body text-center">
                <h3 class="fw-bold mb-1">{{ averagePerWeek }}</h3>
                <small class="text-muted">Sessions par semaine</small>
              </div>
            </div>
          </div>

          <!-- Activité favorite -->
          <div class="col-md-3">
            <div class="card shadow-sm border-0 rounded-4 h-100">
              <div class="card-header bg-info text-white d-flex align-items-center rounded-top-4">
                <i class="bi bi-star-fill me-2"></i> Activité favorite
              </div>
              <div class="card-body text-center">
                <h5 class="fw-bold mb-1">{{ favoriteActivity || '—' }}</h5>
                <small class="text-muted">Votre activité la plus pratiquée</small>
              </div>
            </div>
          </div>

          <!-- Dernier feedback -->
          <div class="col-md-3 mt-3">
            <div class="card shadow-sm border-0 rounded-4 h-100">
              <div class="card-header bg-secondary text-white d-flex align-items-center rounded-top-4">
                <i class="bi bi-chat-dots me-2"></i> Dernier feedback
              </div>
              <div class="card-body text-center">
                <p class="small mb-0 text-secondary" v-if="lastFeedback">{{ lastFeedback }}</p>
                <p class="small mb-0 text-secondary" v-else>—</p>
              </div>
            </div>
          </div>
        </div>

        <!-- === CHART === -->
        <div class="card shadow-sm my-4 border-0 rounded-4">
        <div class="card-header bg-primary text-white fw-bold d-flex justify-content-between align-items-center rounded-top-4 py-2">
            <i class="bi bi-bar-chart-line me-2"></i>
            <span>Mes performances</span>
            <select v-model="filterRange" class="form-select w-auto form-select-sm">
            <option value="7">7 derniers jours</option>
            <option value="30">30 derniers jours</option>
            <option value="90">3 derniers mois</option>
            <option value="all">Tout</option>
            </select>
        </div>
        <div class="card-body py-3 d-flex justify-content-center">
            <div style="max-width: 600px; width: 100%;">
            <canvas id="performanceChart" height="120"></canvas>
            </div>
        </div>
        </div>

        <!-- === HISTORY TABLE === -->
        <div class="card shadow-sm my-4 border-0 rounded-4">
        <div class="card-header bg-primary text-white fw-bold d-flex align-items-center rounded-top-4">
            <i class="bi bi-clock-history me-2"></i>
            Historique des entraînements
        </div>
        <div class="card-body p-3">
            <div class="table-responsive">
            <table class="table table-striped align-middle mb-0 table-hover">
                <thead class="table-light">
                <tr>
                    <th>Date</th>
                    <th>Activité</th>
                    <th>Coach</th>
                    <th>Performance</th>
                    <th>Feedback</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="p in filteredPerformances" :key="p.date_session">
                    <td>{{ new Date(p.date_session).toLocaleDateString() }}</td>
                    <td>{{ p.activity_type }}</td>
                    <td>{{ p.coach_firstname }} {{ p.coach_lastname }}</td>
                    <td>{{ p.performance_organisationuser }}</td>
                    <td>
                    <span v-if="p.feedbackCoachOnUser_organisationuser" 
                            :title="p.feedbackCoachOnUser_organisationuser">
                        💬
                    </span>
                    <span v-else>—</span>
                    </td>
                </tr>
                </tbody>
            </table>
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

  .card-header i {
    font-size: 1.2rem;
  }
</style>

<script src="https://unpkg.com/vue@3"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="js/memberPerformance.js"></script>
