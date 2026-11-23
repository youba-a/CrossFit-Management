
<section id="about" class="container">
    <div class="row">
        <div class="col text-center">
            <h2 class="mt-5 mb-5 fw-bold">Bienvenue chez CrossFit Mind Steel Bastogne</h2>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-4 col-sm-12">
            <div class="d-flex gap-2 mb-2">
                <img src="/img/aboutUs/photo_about_1.jpg" alt="Facade de la salle de CrossFit" class="img-fluid w-50">
                <img src="/img/activity/weightlifting/photo_weightlifting3.jpg" alt="Salle de CrossFit" class="img-fluid w-50">
            </div>
            
        </div>
        <div class="OurStory col-lg-8 col-sm-12">
            <h3>Notre histoire</h3>
            <p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Molestiae hic qui ab sequi reprehenderit quos esse tempore cum illum facilis officiis exercitationem veritatis, at earum ullam fugiat quasi. Eaque, obcaecati.
            Nam commodi quae suscipit minima. Blanditiis impedit eveniet voluptate cupiditate ipsum vero quo, voluptatum quas recusandae culpa odit dolores aperiam consequuntur vitae, minus ipsam aliquam iure voluptatibus? Maiores, natus sunt.</p>
            <p>Lorem ipsum dolor, sit amet consectetur adipisicing elit. Numquam modi quisquam labore sed! Inventore, aliquid. Ullam est maiores modi ea labore nesciunt impedit illo, obcaecati mollitia nam inventore, facere nisi.</p>
            <p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Quia esse voluptate illum, corporis ratione provident rerum sed? Neque earum fuga accusantium ad. Tenetur nostrum tempore, ab illum tempora eos? Molestiae?
            Qui ut reiciendis dolorum nostrum eius error delectus quam, sint voluptate beatae voluptatibus, exercitationem ducimus dicta tempora! Voluptatum, qui, placeat voluptate sunt incidunt libero fuga ullam a non sed accusantium.
            Magni omnis consectetur eos excepturi reiciendis delectus tempore illum labore commodi praesentium deleniti distinctio exercitationem ab totam ea quos cum soluta optio, hic ullam iste. Consequatur itaque temporibus magni sed!</p>
            
        </div>
    </div>
</section>

<section id="discovery">
    <div class="container mt-5">
        <div class="row">
            <h2 class="mt-4">Venez découvrir par vous-même !</h2>
        </div>
        <div class="row">
            <div class="col-lg-6 order-lg-2 d-flex justify-content-center">
                <img src="/img/activity/weightlifting/photo_weightlifting3.jpg" alt="Salle de CrossFit" class="img-fluid">
            </div>

            <div class="col-lg-6 order-lg-1">
                <p class="lead mb-3">
                Vous hésitez encore ? Rien de mieux qu’une séance découverte pour ressentir
                l’énergie, l’ambiance et la qualité de nos activités. C’est l’occasion idéale
                de rencontrer notre équipe, de poser toutes vos questions et de voir si notre
                approche correspond à vos attentes.
                </p>
                <p class="mb-4">
                Que vous soyez débutant curieux ou déjà passionné, cet essai gratuit est fait
                pour vous. Venez découvrir par vous-même ce qui nous rend différents et
                laissez-vous surprendre !
                </p>
                <a href="index.php?p=freeTrial" class="btn btn-primary btn-lg mb-4">Demander mon essai gratuit</a>
            </div>
        </div>
    </div>
</section>

<!-- Carrousel -->
<section id="carousel">
<div class="banner-container">
    <div class="banner-track" id="bannerTrack">
        <img src="/img/activity/weightlifting/photo_weightlifting3.jpg" alt="Salle de CrossFit">
        <img src="/img/aboutUs/photo_about_1.jpg" alt="Entrée de la salle de CrossFit">
        <img src="/img/activity/weightlifting/photo_weightlifting3.jpg" alt="">
        <img src="/img/aboutUs/photo_about_1.jpg" alt="">
    </div>
</div>
</section>

<script>
  // Drag horizontal pour la banderole
  const track = document.getElementById('bannerTrack');
  let isDown = false;
  let startX;
  let scrollLeft;

  track.addEventListener('mousedown', (e) => {
    isDown = true;
    startX = e.pageX - track.offsetLeft;
    scrollLeft = track.scrollLeft;
    track.style.cursor = 'grabbing';
  });

  track.addEventListener('mouseleave', () => {
    isDown = false;
    track.style.cursor = 'grab';
  });

  track.addEventListener('mouseup', () => {
    isDown = false;
    track.style.cursor = 'grab';
  });

  track.addEventListener('mousemove', (e) => {
    if(!isDown) return;
    e.preventDefault();
    const x = e.pageX - track.offsetLeft;
    const walk = (x - startX) * 2; // vitesse du drag
    track.scrollLeft = scrollLeft - walk;
  });
</script>