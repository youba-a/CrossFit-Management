const { createApp } = Vue;

createApp({
    data() {
        return {
            email: "",
            errorMessage: "",
            successMessage: "",
            submitting: false
        };
    },

    methods: {
        async submitForgotPassword() {

            this.errorMessage = "";
            this.successMessage = "";

            if (!this.email) {
                this.errorMessage = "Veuillez entrer une adresse email.";
                return;
            }

            this.submitting = true;

            try {
                const response = await fetch("/back/API/login/memberResetPasswordRequest.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({ email: this.email })
                });

                const data = await response.json();

                if (data.success) {
                    this.successMessage = data.message;

                    // TOAST Bootstrap
                    const toastEl = document.getElementById("toastSuccess");
                    const toast = new bootstrap.Toast(toastEl);
                    toast.show();

                    this.email = "";
                } else {
                    this.errorMessage = data.message || "Une erreur est survenue.";
                }

            } catch (e) {
                this.errorMessage = "Impossible de contacter le serveur.";
            }
            finally {
                this.submitting = false;
            }
        }
    }
}).mount("#forgotPasswordApp");
