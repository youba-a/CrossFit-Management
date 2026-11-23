"use strict";

const ownerAddMemberApp = Vue.createApp({
    data() {
        return {
            membre: {
                prenom: "",
                nom: "",
                dob: "",
                genre: "",
                email: "",
                street: "",
                numero: "",
                boite: "",
                postalcode: "",
                town: "",
            },
            message_success: "",
            message_error: "",
            submitting: false,  // pour spin anim
        };
    },
    methods: {
        async submitInviteMember(event) {
            event.preventDefault(); // Empêche le rechargement du formulaire
            this.message_success = "";
            this.message_error = "";
            this.submitting = true; // active l'anim du spin

            const payload = {
                prenom: this.membre.prenom.trim(),
                nom: this.membre.nom.trim(),
                dob: this.membre.dob,
                genre: this.membre.genre,
                email: this.membre.email.trim(),
                street: this.membre.street.trim(),
                numero: this.membre.numero.trim(),
                boite: this.membre.boite.trim(),
                postalcode: this.membre.postalcode.trim(),
                town: this.membre.town.trim(),
            };

            try {
                const response = await fetch('./back/API/owner/ownerAddMember.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload),
                    credentials: 'include'
                });

                const data = await response.json();

                if (data.success) {
                    this.message_success = data.message || "Invitation envoyée avec succès.";

                    // Réinitialiser le formulaire
                    this.membre = {
                        prenom: "",
                        nom: "",
                        dob: "",
                        genre: "",
                        email: "",
                        street: "",
                        numero: "",
                        boite: "",
                        postalcode: "",
                        town: "",
                    };

                    // Attendre la mise à jour du DOM avant d'afficher le toast
                    this.$nextTick(() => {
                        const toastEl = document.getElementById('toastInviteSuccess');
                        if (toastEl) {
                            const toast = new bootstrap.Toast(toastEl);
                            toast.show();
                        }
                    });
                } else {
                    this.message_error = data.message || "Erreur lors de l'invitation.";
                }
            } catch (err) {
                console.error(err);
                this.message_error = "Erreur serveur. Impossible de contacter l’API.";
            } finally {
                this.submitting = false; // désactive l'anim du spin
            }
        }
    },
});

ownerAddMemberApp.mount("#ownerAddMemberApp");
