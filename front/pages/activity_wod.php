<?php include_once __DIR__ . "/../includes/header.php"; ?>

<main>

<!-- SECTION HERO -->
<section id="activity-hero" class="container">
    <div class="row">
        <div class="col text-center">
            <h2 class="mt-5 mb-5 fw-bold">WOD – Workout of the Day</h2>
        </div>
    </div>

    <div class="row align-items-center mb-4">
        <!-- Texte -->
        <div class="col-lg-6 col-sm-12">
            <p class="lead">
                Lorem ipsum dolor sit amet consectetur adipisicing elit. Repudiandae fugit
                impedit necessitatibus, voluptatibus eveniet temporibus explicabo iste
                officiis numquam aspernatur.
            </p>

            <p>
                Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ea omnis quas nihil
                dignissimos laborum tempore tempora nobis, voluptatum inventore cumque sed quia
                molestiae eaque.
            </p>

            <a href="index.php?p=tarif" class="btn btn-outline-primary btn-lg mt-3 me-3">
                Consulter les tarifs
            </a>

            <a href="index.php?p=freeTrial" class="btn btn-primary btn-lg mt-3">
                Réserver une séance gratuite
            </a>
        </div>

        <!-- Image -->
        <div class="col-lg-6 col-sm-12 d-flex justify-content-center mt-4 mt-lg-0">
            <img src="/img/activity/weightlifting/photo_weightlifting3.jpg" class="img-fluid rounded shadow" alt="Salle de CrossFit">
        </div>
    </div>
</section>



<!-- SECTION DETAILS -->
<section id="activity-details" style="background-color: var(--couleur-secondaire); padding: 60px 0;">
    <div class="container">
        <div class="row">
            <h3 class="mb-4 fw-semibold">Ce qui vous attend pendant un WOD</h3>
        </div>

        <div class="row">

            <div class="col-lg-4 col-sm-12 mb-4">
                <div class="p-4 rounded shadow-sm" style="background: var(--couleur-primaire); min-height: 180px;">
                    <h5 class="fw-bold">🔥 Intensité progressive</h5>
                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Tempore, sed.</p>
                </div>
            </div>

            <div class="col-lg-4 col-sm-12 mb-4">
                <div class="p-4 rounded shadow-sm" style="background: var(--couleur-primaire); min-height: 180px;">
                    <h5 class="fw-bold">💪 Travail complet</h5>
                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Quaerat, fuga.</p>
                </div>
            </div>

            <div class="col-lg-4 col-sm-12 mb-4">
                <div class="p-4 rounded shadow-sm" style="background: var(--couleur-primaire); min-height: 180px;">
                    <h5 class="fw-bold">👥 Ambiance motivante</h5>
                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Eaque, odit!</p>
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




<?php ; include_once __DIR__ . "/../includes/footer.php"?>
