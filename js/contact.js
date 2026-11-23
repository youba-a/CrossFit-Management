"use strict";

const contactApp = Vue.createApp({
    data() {
        return {
            form: {
                guestFirstName: "",
                guestLastName: "",
                guestEmail: "",
                subject: "",
                message: "",
            },
            success: "",
            error: "",
            submitting: false,
        };
    },

    methods: {
        async sendForm() {
            this.success = "";
            this.error = "";
            this.submitting = true;

            // Validation simple côté front
            if (
                !this.form.guestFirstName.trim() ||
                !this.form.guestLastName.trim() ||
                !this.form.guestEmail.trim() ||
                !this.form.subject.trim() ||
                !this.form.message.trim()
            ) {
                this.error = "Veuillez remplir tous les champs obligatoires.";
                return;
            }

            const payload = {
                guestFirstName: this.form.guestFirstName.trim(),
                guestLastName: this.form.guestLastName.trim(),
                guestEmail: this.form.guestEmail.trim(),
                subject: this.form.subject.trim(),
                message: this.form.message.trim(),
            };

            try {
                const response = await fetch("./back/API/guest/guestContact.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify(payload),
                    credentials: "include",
                });

                const data = await response.json();

                if (data.success) {
                    this.success = data.message || "Message envoyé avec succès !";

                    // Réinitialiser le formulaire
                    this.form = {
                        guestFirstName: "",
                        guestLastName: "",
                        guestEmail: "",
                        subject: "",
                        message: "",
                    };

                    // Afficher le toast Bootstrap
                    this.$nextTick(() => {
                        const toastEl = document.getElementById("toastSuccess");
                        if (toastEl) {
                            const toast = new bootstrap.Toast(toastEl);
                            toast.show();
                        }
                    });
                } else {
                    this.error = data.error || "Erreur lors de l'envoi du message.";
                }
            } catch (err) {
                console.error(err);
                this.error = "Erreur serveur. Impossible de contacter l’API.";
            }
            finally {
                this.submitting = false;
            }

        },
    },
});

contactApp.mount("#contactApp");