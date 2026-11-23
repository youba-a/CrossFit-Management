"use strict";

const memberRegisterApp = Vue.createApp({
    data() {
        return {
            password: "",
            passwordConfirm: "",
            message_error: "",
            message_success: "",
            isSubmitting: false,
            showPassword: false,
            countdown: 5
        };
    },
    computed: {
        // Vérifie si les mots de passe correspondent
        passwordsMatch() {
            return this.password && this.password === this.passwordConfirm;
        },

        // Analyse de la force du mot de passe
        strength() {
            let score = 0;
            if (this.password.length >= 8) score++;
            if (/[A-Z]/.test(this.password)) score++;
            if (/[0-9]/.test(this.password)) score++;
            if (/[^A-Za-z0-9]/.test(this.password)) score++;
            return score;
        },

        strengthBarClass() {
            return [
                "w-100",
                this.strength <= 1 ? "bg-danger" :
                this.strength === 2 ? "bg-warning" :
                "bg-success"
            ];
        },

        strengthText() {
            return this.strength <= 1
                ? "Sécurité : Faible"
                : this.strength === 2
                ? "Sécurité : Moyenne"
                : "Sécurité : Forte";
        }
    },
    methods: {
        async submitRegister() {
            this.message_error = "";
            this.isSubmitting = true;

            if (!this.passwordsMatch) {
                this.message_error = "Les mots de passe ne correspondent pas.";
                this.isSubmitting = false;
                return;
            }

            const payload = {
                token: document.getElementById("token").value,
                password: this.password
            };

            try {
                const response = await fetch("/back/API/member/memberSubmitRegister.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify(payload)
                });

                const data = await response.json();

                if (data.success) {
                    this.message_success = "Inscription réussie !";
                    this.startCountdown(data.redirect);
                } else {
                    this.message_error = data.message;
                }
            } catch (err) {
                this.message_error = "Erreur serveur.";
            }

            this.isSubmitting = false;
        },

        startCountdown(redirectUrl) {
            const interval = setInterval(() => {
                this.countdown--;
                if (this.countdown <= 0) {
                    clearInterval(interval);
                    window.location.href = redirectUrl;
                }
            }, 1000);
        }
    }
});

memberRegisterApp.mount("#memberRegisterApp");
