<?php include_once __DIR__ . "/../includes/header.php"; ?>

<main>

    <!-- SECTION HERO -->
    <section id="activity-hero" class="container">
        <div class="row">
            <div class="col text-center">
                <h2 class="mt-5 mb-5 fw-bold">Gym</h2>
            </div>
        </div>

        <div class="row align-items-center mb-4">

            <!-- Texte -->
            <div class="col-lg-6 col-sm-12">
                <p class="lead">
                    Découvrez un espace dédié au développement de votre force, de votre stabilité 
                    et de votre contrôle du mouvement grâce à un entraînement gymnique complet et progressif.
                </p>

                <p>
                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Qui ratione 
                    suscipit unde velit laborum! Corporis eum, voluptatum repellendus 
                    perferendis totam accusamus reprehenderit earum vel exercitationem.
                </p>

                <a href="index.php?p=tarif" class="btn btn-outline-primary btn-lg mt-3 me-3">
                    Consulter les tarifs
                </a>

                <a href="index.php?p=freeTrial" class="btn btn-primary btn-lg mt-3">
                    Réserver une séance gratuite
                </a>
            </div>

            <!-- Images -->
            <div class="col-lg-6 col-sm-12 d-flex justify-content-center mt-4 mt-lg-0">
                <img src="/img/activity/weightlifting/photo_weightlifting3.jpg" class="img-fluid rounded shadow" alt="Salle de CrossFit">
            </div>

        </div>
    </section>

    <!-- SECTION DETAILS -->
    <section id="activity-details" style="background-color: var(--couleur-secondaire); padding: 60px 0;">
        <div class="container">

            <div class="row">
                <h3 class="mb-4 fw-semibold">Ce que vous allez travailler en Gym</h3>
            </div>

            <div class="row">

                <div class="col-lg-4 col-sm-12 mb-4">
                    <div class="p-4 rounded shadow-sm" style="background: var(--couleur-primaire); min-height: 180px;">
                        <h5 class="fw-bold">🤸‍♂️ Contrôle du corps</h5>
                        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Qui ratione suscipit unde.</p>
                    </div>
                </div>

                <div class="col-lg-4 col-sm-12 mb-4">
                    <div class="p-4 rounded shadow-sm" style="background: var(--couleur-primaire); min-height: 180px;">
                        <h5 class="fw-bold">💪 Force et stabilité</h5>
                        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Perferendis, pariatur.</p>
                    </div>
                </div>

                <div class="col-lg-4 col-sm-12 mb-4">
                    <div class="p-4 rounded shadow-sm" style="background: var(--couleur-primaire); min-height: 180px;">
                        <h5 class="fw-bold">👌 Mobilité avancée</h5>
                        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Asperiores, illum!</p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- SECTION GALERIE -->
    <section id="activity-gallery" class="container mt-5 mb-5">
        <div class="row">
            <h3 class="mb-4 fw-semibold">En images</h3>
        </div>

        <div class="row g-4">

            <div class="col-lg-6 col-sm-12">
                <img src="/img/activity/weightlifting/photo_weightlifting3.jpg" class="img-fluid rounded shadow" alt="Salle de CrossFit">
            </div>

            <div class="col-lg-6 col-sm-12">
                <img src="/img/activity/weightlifting/photo_weightlifting3.jpg" class="img-fluid rounded shadow" alt="Salle de CrossFit">
            </div>

        </div>
    </section>

</main>

<?php include_once __DIR__ . "/../includes/footer.php"; ?>

