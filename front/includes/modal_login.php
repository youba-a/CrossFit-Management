<?php
// la variable GET login permet de dire a la page qu'il faut afficher le modal, utile lorsque qu'on redirige l'user sur la page de login après un mauvais identifiant.
$showLoginModal = isset($_GET['login']) && $_GET['login'] == '1';

// la variable GET logout permet de dire a la page qu'il faut afficher le toast message de logout
$showLogoutToast = isset($_GET['logout']) && $_GET['logout'] == '1';
?>



<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true" v-cloak>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
        
            <div class="modal-header">
                <h5 class="modal-title" id="loginModalLabel">Login</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body">
                  <div v-if="message_error" class="alert alert-danger" role="alert">
                  {{message_error}}
                  </div>

                <form @submit.prevent="submitLogin" method="post">
                <div class="mb-3">
                    <label for="login" class="form-label">Adresse email</label>
                    <input v-model="login" type="text" class="form-control" id="login" name="login" placeholder="Entrer adresse email" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Mot de passe</label>
                    <input v-model="password" type="password" class="form-control" id="password" name="password" placeholder="Entrer mot de passe" required>
                </div>
                <!-- Lien mot de passe perdu -->
                <div class="mb-3 text-end">
                    <a class="link-primary small" href="index.php?p=memberForgotPassword">Mot de passe perdu ?</a>
                </div>
                <button type="submit" class="btn btn-success">Se connecter</button>
                </form>
            </div>  
            
        </div>
    </div>
</div>


<?php if ($showLoginModal):
    // affichage du modal de login
    ?>
<script>
  document.addEventListener("DOMContentLoaded", function () {
    const modal = new bootstrap.Modal(document.getElementById('loginModal'));
    modal.show();
  });
</script>
<?php endif; ?>


<?php if ($showLogoutToast): 
    // affichage du toast de logout
    ?>
<div class="toast-container position-fixed bottom-0 end-0 p-3">
  <div id="toastLogout" class="toast text-bg-info" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="d-flex">
      <div class="toast-body">
        Vous vous êtes déconnecté.
      </div>
      <button type="button" class="btn-close btn-close-black me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
  </div>
</div>

<!-- Script de fermeture automatique du toast -->
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const toastEl = document.getElementById('toastLogout');
    if (toastEl) {
      const toast = new bootstrap.Toast(toastEl, {
        delay: 5000,      // 5000 millisecondes = 5 secondes
        autohide: true    // s'assurer que le toast se ferme automatiquement
      });
      toast.show();
    }
  });
</script>
<?php endif; ?>

<script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
<script src="/js/modal_login.js"></script>




