<?php 
    $pageTitle = "Strength – CrossFit Mind Steel Bastogne";
    include_once __DIR__ . "/../includes/header.php";
?>

<main>

<!-- SECTION HERO -->
<section id="strength-hero" class="container">
    <div class="row">
        <div class="col text-center">
            <h2 class="mt-5 mb-5 fw-bold">Strength</h2>
        </div>
    </div>

    <div class="row align-items-center mb-4">

        <!-- Texte -->
        <div class="col-lg-6 col-sm-12">
            <p class="lead">
                Développez votre puissance, votre force maximale et votre contrôle grâce à nos séances axées sur le renforcement musculaire pur.
            </p>

            <p>
                Lorem ipsum dolor sit amet consectetur adipisicing elit. 
                Voluptatum aperiam dolor dignissimos amet inventore dolore 
                earum impedit, temporibus quia neque nulla obcaecati aliquid
                deserunt sint blanditiis assumenda perspiciatis similique animi.
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
            <img src="/img/activity/weightlifting/photo_weightlifting3.jpg" 
                 class="img-fluid rounded shadow" 
                 alt="Salle de CrossFit">
        </div>

    </div>
</section>



<!-- SECTION DETAILS -->
<section id="strength-details" style="background-color: var(--couleur-secondaire); padding: 60px 0;">
    <div class="container">
        <div class="row">
            <h3 class="mb-4 fw-semibold">Ce que vous allez travailler en Strength</h3>
        </div>

        <div class="row">

            <div class="col-lg-4 col-sm-12 mb-4">
                <div class="p-4 rounded shadow-sm" style="background: var(--couleur-primaire); min-height: 180px;">
                    <h5 class="fw-bold">🏋️‍♂️ Force maximale</h5>
                    <p>Améliorez vos performances sur les mouvements fondamentaux : squat, deadlift, presses.</p>
                </div>
            </div>

            <div class="col-lg-4 col-sm-12 mb-4">
                <div class="p-4 rounded shadow-sm" style="background: var(--couleur-primaire); min-height: 180px;">
                    <h5 class="fw-bold">⚙️ Technique & contrôle</h5>
                    <p>Renforcez votre posture, votre stabilité et votre exécution pour mieux progresser.</p>
                </div>
            </div>

            <div class="col-lg-4 col-sm-12 mb-4">
                <div class="p-4 rounded shadow-sm" style="background: var(--couleur-primaire); min-height: 180px;">
                    <h5 class="fw-bold">🔥 Progression mesurable</h5>
                    <p>Des blocs structurés pour suivre votre évolution et atteindre vos objectifs.</p>
                </div>
            </div>

        </div>
    </div>
</section>



<!-- SECTION GALERIE -->
<section id="strength-gallery" class="container mt-5 mb-5">
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
