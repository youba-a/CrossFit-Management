"use strict";

const memberEditProfileApp = Vue.createApp({
    data() {
        return {
            membre: {
                password_user: "",
                confirm_password: "",
                poids_user: null,
                taille_user: null,
                streetNameAddress_user: "",
                numeroAddress_user: "",
                postBoxAddress_user: "",
                postalCode_user: "",
                town_user: "",
                telephone_user: "",
                firstContactTelephone_user: "",
                secondContactTelephone_user: "",
                emailAddress_user: "",
                wound_user: "",
                avatar_user: "",
                // --- Champs Coach ---
                specialisation_coach: "",

                // --- Champs Owner ---
                email_owner: "",
                telephone_owner: "",
                salary_owner: null,

                // --- Flags ---
                isCoach: false,
                isOwner: false,
            },
            message_success: "",
            message_error: "",
            avatarFile: null,
            anonymizeModal: null,
        };
    },
    methods: {
        handleAvatarUpload(event) {
            const file = event.target.files[0];
            if (file && file.type.startsWith('image/')) {
                this.avatarFile = file;
                this.membre.avatar_user = URL.createObjectURL(file);
            } else {
                this.avatarFile = null;
                this.membre.avatar_user = null; // pas d’aperçu
            }
        },

        openAnonymizeModal() {
            const modal = new bootstrap.Modal(document.getElementById("modalAnonymize"));
            modal.show();
        },

        confirmAnonymization() {
            const modal = bootstrap.Modal.getInstance(document.getElementById("modalAnonymize"));
            if (modal) modal.hide();

            this.disableAccount();
        },

        async disableAccount() {
            fetch('/back/API/member/memberDisableAccountRequest.php', {
                method: 'POST'
            })
            .then(r => r.json())
            .then(res => {
                if (res.success) {
                    new bootstrap.Toast(document.getElementById('toastDisableSuccess')).show();
                    this.memberGetProfile();
                    
                } else {
                    this.message_error = res.message;
                }
            })
            .catch(err => console.error("Erreur API:", err));
        },

        async submitMemberUpdateProfile() {
            this.message_success = "";
            this.message_error = "";

            if (this.membre.password_user !== this.membre.confirm_password) {
                this.message_error = "Les mots de passe ne correspondent pas.";
                return;
            }

            const formData = new FormData();
            Object.entries(this.membre).forEach(([key, value]) => {
                if (key !== "confirm_password") formData.append(key, value ?? "");
            });

            if (this.avatarFile) {
                formData.append("avatar_file", this.avatarFile);
            }

            try {
                const response = await fetch('./back/API/member/memberUpdateProfile.php', {
                    method: 'POST',
                    body: formData,
                    credentials: 'include'
                });

                const data = await response.json();

                if (data.success) {
                    this.message_success = data.message || "Profil mis à jour avec succès.";

                    this.$nextTick(() => {
                        const toastEl = document.getElementById('toastUpdateSuccess');
                        if (toastEl) new bootstrap.Toast(toastEl).show();
                    });
                } else {
                    this.message_error = data.message || "Erreur lors de la mise à jour.";
                }
            } catch (err) {
                console.error(err);
                this.message_error = "Erreur serveur. Impossible de contacter l’API.";
            }
        },
        memberGetProfile() {
            fetch("./back/API/member/memberGetProfile.php", { credentials: 'include' })
                .then(rep => rep.json())
                .then(rep2 => {
                    this.membre = { ...this.membre, ...rep2 };
                })
                .catch(err => console.error("Erreur API:", err));
        },
    },
    mounted() {
        this.memberGetProfile();
    },
});

memberEditProfileApp.mount("#memberEditProfileApp");
