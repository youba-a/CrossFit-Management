"use strict";

const resetPasswordApp = Vue.createApp({
    data() {
        return {
            form: {
                newPassword: "",
                confirmPassword: "",
            },
            token: null,
            message_error: "",
            message_success: "",
            tokenValid: false,
            loading: true
        };
    },

    methods: {
        getTokenFromURL() {
            const urlParams = new URLSearchParams(window.location.search);
            this.token = urlParams.get("token");
        },

        async checkToken() {
            if (!this.token) {
                this.message_error = "Lien invalide.";
                this.loading = false;
                return;
            }

            try {
                const res = await fetch(`/back/API/member/memberResetPasswordCheckToken.php?token=${encodeURIComponent(this.token)}`);
                const data = await res.json();

                if (data.success) {
                    this.tokenValid = true;
                } else {
                    this.message_error = data.message;
                }
            } catch (err) {
                this.message_error = "Erreur serveur lors de la vérification du token.";
            }

            this.loading = false;
        },

        async submitResetPassword() {
            this.message_error = "";
            this.message_success = "";

            if (!this.tokenValid) {
                this.message_error = "Token invalide.";
                return;
            }

            if (this.form.newPassword !== this.form.confirmPassword) {
                this.message_error = "Les mots de passe ne correspondent pas.";
                return;
            }

            try {
                const response = await fetch("/back/API/member/memberResetPasswordFinal.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({
                        token: this.token,
                        newPassword: this.form.newPassword
                    }),
                });

                const data = await response.json();

                if (data.success) {
                    this.message_success = data.message;

                    this.$nextTick(() => {
                        const t = new bootstrap.Toast(document.getElementById('toastSuccess'));
                        t.show();
                    });

                    setTimeout(() => {
                        window.location.href = "/index.php?login=1";
                    }, 3000);

                } else {
                    this.message_error = data.message;
                }

            } catch (err) {
                this.message_error = "Erreur serveur. Impossible de contacter l’API.";
            }
        }
    },

    async mounted() {
        this.getTokenFromURL();
        await this.checkToken();
    }
});

resetPasswordApp.mount("#resetPasswordApp");
