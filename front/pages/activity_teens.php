<?php include_once __DIR__ . "/../includes/header.php"; ?>

<main>

<!-- SECTION HERO -->
<section id="teens-hero" class="container">
    <div class="row">
        <div class="col text-center">
            <h2 class="mt-5 mb-3 fw-bold">CrossFit Teens</h2>
            <p class="lead">Une activité sportive pensée pour les ados de 13 à 17 ans</p>
        </div>
    </div>

    <div class="row align-items-center mt-4 mb-4">

        <!-- Images -->
        <div class="col-lg-6 col-sm-12 mb-4">
            <div class="d-flex gap-3">
                <img src="/img/activity/weightlifting/photo_weightlifting3.jpg" 
                     class="img-fluid w-50 rounded shadow" 
                     alt="Salle de CrossFit">
                <img src="/img/activity/weightlifting/photo_weightlifting3.jpg" 
                     class="img-fluid w-50 rounded shadow" 
                     alt="Salle de CrossFit">
            </div>
        </div>

        <!-- Texte -->
        <div class="col-lg-6 col-sm-12">
            <p class="lead">
                Lorem ipsum dolor sit amet consectetur adipisicing elit. Doloremque ratione 
                ipsum accusantium aspernatur dolores quidem beatae.
            </p>

            <p>
                Lorem ipsum dolor sit amet consectetur adipisicing elit. Quidem impedit 
                molestias repellendus laborum dignissimos adipisci vero esse optio.
            </p>

            <p>
                Lorem ipsum dolor sit amet consectetur adipisicing elit. Illum repellat 
                odit aut inventore temporibus numquam eos facilis.
            </p>

            <!-- Boutons -->
            <a href="index.php?p=tarif" class="btn btn-outline-primary btn-lg mt-3 me-3">
                Consulter les tarifs
            </a>

            <a href="index.php?p=freeTrial" class="btn btn-primary btn-lg mt-3">
                Réserver une séance gratuite
            </a>
        </div>

    </div>
</section>



<!-- SECTION DETAILS -->
<section id="teens-details" style="background-color: var(--couleur-secondaire); padding: 60px 0;">
    <div class="container">
        <div class="row">
            <h3 class="mb-4 fw-semibold">Ce que propose le programme Teens</h3>
        </div>

        <div class="row">

            <div class="col-lg-4 col-sm-12 mb-4">
                <div class="p-4 rounded shadow-sm" style="background: var(--couleur-primaire); min-height: 180px;">
                    <h5 class="fw-bold">🏋️ Renforcement adapté</h5>
                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Fugiat, ipsum.</p>
                </div>
            </div>

            <div class="col-lg-4 col-sm-12 mb-4">
                <div class="p-4 rounded shadow-sm" style="background: var(--couleur-primaire); min-height: 180px;">
                    <h5 class="fw-bold">🤸 Mobilité & coordination</h5>
                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Eum, debitis.</p>
                </div>
            </div>

            <div class="col-lg-4 col-sm-12 mb-4">
                <div class="p-4 rounded shadow-sm" style="background: var(--couleur-primaire); min-height: 180px;">
                    <h5 class="fw-bold">👥 Esprit d’équipe</h5>
                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Amet, molestiae.</p>
                </div>
            </div>

        </div>
    </div>
</section>



<!-- SECTION GALERIE -->
<section id="teens-gallery" class="container mt-5 mb-5">
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
