<?php
require_once "config/db.php";

$token = $_GET['token'] ?? null;

if (!is_string($token) || !preg_match('/^[a-f0-9]{64}$/D', $token)) {
    die("<h2>Lien invalide</h2>");
}

$tokenHash = hash("sha256", $token);

// Vérifier le token + date
$stmt = $db->prepare("
    SELECT *, 
    DATEDIFF(NOW(), dateInvitation_user) AS diff_days
    FROM user
    WHERE tokenInvitation_user = ?
");
$stmt->bind_param("s", $tokenHash);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    die("
        <div style='padding:20px; font-family:sans-serif'>
            <h2>Lien invalide</h2>
            <p>Ce lien ne correspond à aucune invitation.</p>
        </div>
    ");
}

if ($user['diff_days'] > 30) {
    die("
        <div style='padding:20px; font-family:sans-serif'>
            <h2>Lien expiré</h2>
            <p>Votre invitation date de plus de 30 jours.</p>
            <p>Veuillez demander une nouvelle invitation au propriétaire de la box.</p>
        </div>
    ");
}
?>

<section id="memberRegisterApp" v-cloak>
    <div class="container mt-5 mb-5">
        <div class="card shadow">
            <div class="card-header bg-warning fw-bold">
                Finaliser mon inscription
            </div>

            <div class="card-body">

                <div v-if="message_error" class="alert alert-danger">
                    {{message_error}}
                </div>

                <div v-if="message_success" class="alert alert-success">
                    {{message_success}} <br>
                    Redirection dans {{ countdown }} seconde(s)...
                </div>

                <form @submit.prevent="submitRegister" v-if="!message_success">
                    <input type="hidden" id="token" value="<?= htmlspecialchars($token) ?>">

                    <div class="mb-3">
                        <label class="form-label">Prénom</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($user['firstName_user']) ?>" disabled>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nom</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($user['lastName_user']) ?>" disabled>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Adresse email</label>
                        <input type="email" class="form-control" value="<?= htmlspecialchars($user['emailAddress_user']) ?>" disabled>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Mot de passe</label>
                        <div class="input-group">
                            <input type="password" class="form-control" v-model="password" :type="showPassword ? 'text' : 'password'" required>
                            <button class="btn btn-outline-secondary" type="button" @click="showPassword = !showPassword">
                                <i :class="showPassword ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Barre de force -->
                    <div class="mb-2">
                        <div :class="strengthBarClass" style="height:8px; border-radius:4px;"></div>
                        <small>{{ strengthText }}</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Confirmer le mot de passe</label>
                        <input type="password" class="form-control" v-model="passwordConfirm" required>
                    </div>

                    <button class="btn btn-warning w-100"
                        :disabled="isSubmitting || !passwordsMatch"
                    >
                        <span v-if="!isSubmitting">Créer mon compte</span>
                        <span v-else class="spinner-border spinner-border-sm"></span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/vue@3"></script>
    <script src="/js/memberRegister.js"></script>
</section>
