<section id="guestListOfCoach" class="container my-5">
    <div class="row text-center mb-4">
        <h2 class="fw-bold">Notre équipe de coachs</h2>
        <p class="text-muted">Découvrez nos coachs passionnés et leurs spécialités</p>
    </div>

    <div id="teamApp">
        <div v-if="loading" class="text-center my-5">
            <div class="spinner-border text-primary" role="status"></div>
            <p class="mt-3">Chargement des coachs...</p>
        </div>

        <div v-if="error" class="alert alert-danger text-center">
            {{ error }}
        </div>

        <div v-if="!loading && !error" class="row">
            <div v-for="coach in coachs" :key="coach.id_coach" class="coach-card">
                <template v-if="coach.avatar_user">
                    <img 
                        :src="'../../img/avatar/' + coach.avatar_user" 
                        :alt="'Photo de ' + coach.firstName_user + ' ' + coach.lastName_user"
                        class="coach-avatar">
                </template>
                <template v-else>
                    <div class="coach-avatar-icon text-secondary me-5">
                        <i class="bi bi-person-circle me-2"></i>
                    </div>
                </template>

                <div class="coach-info">
                    <h5>{{ coach.firstName_user }} {{ coach.lastName_user }}</h5>
                    <div class="specialisation">{{ coach.specialisation_coach }}</div>
                    <div class="niveau">{{ coach.actualLevel_coach }}</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Vue.js -->
<script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
<script src="/js/team.js"></script>
