<header>
    <nav class="navbar navbar-expand-lg shadow-sm">
        <div class="container-fluid">
            <div id="headerLogo"><img src="/img/logo/crossfit_logo.png" alt="CrossFit Mind Steel Bastogne, box de CrossFit avec coaching certifié et entraînements fonctionnels"></div>
            <h1 class="visuallyhidden">CrossFit Mind Steel Bastogne : Box CrossFit, coaching personnel et communauté motivante</h1>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/index.php">Home</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="activitesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Activités
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="activitesDropdown">
                            <li><a class="dropdown-item" href="/index.php?p=activity_wod">WOD</a></li>
                            <li><a class="dropdown-item" href="/index.php?p=activity_strength">Strength</a></li>
                            <li><a class="dropdown-item" href="/index.php?p=activity_weightlifting">Weightlifting</a></li>
                            <li><a class="dropdown-item" href="/index.php?p=activity_gym">Gym</a></li>
                            <li><a class="dropdown-item" href="/index.php?p=activity_kids">Kids</a></li>
                            <li><a class="dropdown-item" href="/index.php?p=activity_teens">Teens</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="infosDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Infos
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="infosDropdown">
                            <li><a class="dropdown-item" href="/index.php?p=calendar">Horaire</a></li>
                            <li><a class="dropdown-item" href="/index.php?p=tarif">Tarif</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/index.php?p=team">L'équipe</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link me-5" href="/index.php?p=contact">Nous contacter</a>
                    </li>

                    <li class="nav-item dropdown" id="userBannerApp" v-cloak>
                        <template v-if="!loading">
                            <!-- Si connecté -->
                            <div v-if="user" class="dropdown">
                                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userMenu"
                                role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <div id="headerLogoAvatar">
                                        <img v-if="user.avatar_user" 
                                            :src="'/img/avatar/' + user.avatar_user"
                                            alt="Avatar" 
                                            class="rounded-circle me-2">
                                        <i v-else class="bi bi-person-circle me-2"></i>
                                    </div>
                                    <span>{{ user.firstName_user }}</span>
                                </a>

                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenu">
                                    <li><a class="dropdown-item" href="/index.php?p=dashboard">Dashboard</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item text-danger" href="#" @click.prevent="logout">Déconnexion</a></li>
                                </ul>
                            </div>

                            <!-- Si non connecté -->
                            <div v-else>
                                <a href="#" class="btn btn-danger ms-2" data-bs-toggle="modal" data-bs-target="#loginModal">Connexion</a>
                            </div>
                        </template>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>

<script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
<script src="/js/userBanner.js"></script>