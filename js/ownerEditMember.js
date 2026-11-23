"use strict";

const ownerEditMemberApp = Vue.createApp({
    data() {
        return {
            id_user: null,
            membre: {
                id_user: null,
                lastName_user: "",
                firstName_user: "",
                birthdate_user: "",
                gender_user: "",
                poids_user: null,
                taille_user: null,
                streetNameAddress_user: "",
                numeroAddress_user: "",
                postBoxAddress_user: "",
                postalCode_user: "",
                town_user: "",
                telephone_user: "",
                emailAddress_user: "",
                isCoach: "",
                isOwner:"",
                actualLevel_coach: "",
                actif_coach: "",
                salary_coach: "",
                specialisation_coach: "",
                email_owner: "",
                telephone_owner: "",
                salary_owner: "",
            },
            message_success: "",
            message_error: "",
        };
    },
    methods: {
        async submitEditMember() {
            this.message_success = "";
            this.message_error = "";

            const payload = {
                id_user: this.membre.id_user, // l'ID de l’utilisateur à modifier

                // --- Champs User ---
                lastName_user: this.membre.lastName_user,
                firstName_user: this.membre.firstName_user,
                birthdate_user: this.membre.birthdate_user,
                gender_user: this.membre.gender_user,
                poids_user: this.membre.poids_user,
                taille_user: this.membre.taille_user,
                streetNameAddress_user: this.membre.streetNameAddress_user,
                numeroAddress_user: this.membre.numeroAddress_user,
                postBoxAddress_user: this.membre.postBoxAddress_user,
                postalCode_user: this.membre.postalCode_user,
                town_user: this.membre.town_user,
                telephone_user: this.membre.telephone_user,
                emailAddress_user: this.membre.emailAddress_user,

                // --- Champs Coach ---
                isCoach: this.membre.isCoach, // true / false
                actualLevel_coach: this.membre.actualLevel_coach,
                salary_coach: this.membre.salary_coach,
                specialisation_coach: this.membre.specialisation_coach,

                // --- Champs Owner ---
                isOwner: this.membre.isOwner, // true / false
                email_owner: this.membre.email_owner,
                telephone_owner: this.membre.telephone_owner,
                salary_owner: this.membre.salary_owner,
            };

            try {
                const response = await fetch('./back/API/owner/ownerUpdateUser.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload),
                    credentials: 'include'
                });

                const data = await response.json();

                if (data.success) {
                    this.message_success = data.message || "Profil mis à jour avec succès.";

                    // Attendre que le DOM soit mis à jour, puis déclencher le toast
                    this.$nextTick(() => {
                        const toastEl = document.getElementById('toastEditSuccess');
                        if (toastEl) {
                            const toast = new bootstrap.Toast(toastEl);
                            toast.show();
                        }
                    });

                } else {
                    this.message_error = data.message || "Erreur lors de la mise à jour.";
                }
            } catch (err) {
                console.error(err);
                this.message_error = "Erreur serveur. Impossible de contacter l’API.";
            }
        },

        showConfirmOwnerModal(user, onConfirm) {
            // user = l'utilisateur qu'on veut rendre Owner
            // onConfirm = callback exécuté si l'admin confirme

            // Crée ou récupère le modal Bootstrap
            const modalEl = document.getElementById('confirmOwnerModal');
            const modal = new bootstrap.Modal(modalEl);

            // Nettoie les anciens listeners pour éviter les doublons
            const confirmBtn = document.getElementById('confirmOwnerBtn');
            const newBtn = confirmBtn.cloneNode(true);
            confirmBtn.parentNode.replaceChild(newBtn, confirmBtn);

            // Attache l’action au clic de "Confirmer"
            newBtn.addEventListener('click', () => {
                modal.hide();
                if (typeof onConfirm === 'function') onConfirm(user);
            });

            // Affiche le modal
            modal.show();
        },

        toggleOwner(user) {
            const originalValue = user.isOwner;
            let confirmed = false; // ⚡ variable pour savoir si l'admin a confirmé

            if (user.isOwner) {
                // On vient de cocher → afficher le modal
                this.showConfirmOwnerModal(user, () => {
                    // Callback : l'admin confirme → on garde la case cochée
                    confirmed = true;
                    user.isOwner = true;
                });

                // Si on ferme le modal sans confirmer → rollback
                const modalEl = document.getElementById('confirmOwnerModal');
                modalEl.addEventListener('hidden.bs.modal', () => {
                    if (!confirmed) {
                        user.isOwner = false; // décocher la case si non confirmé
                    }
                }, { once: true });
            } else {
                // On décoche → pas de modal, on garde false
                // rien à faire
            }
        },

        openAnonymizeModal() {
            const modal = new bootstrap.Modal(document.getElementById('confirmAnonymizeModal'));
            modal.show();
        },

        anonymizeMember() {
            fetch('/back/API/owner/ownerAnonymizeUser.php', {
            method: 'POST',
            body: JSON.stringify({ id_user: this.membre.id_user }),
            headers: { 'Content-Type': 'application/json' }
            })
            .then(res => res.json())
            .then(data => {
            if (data.success) {
                this.message_success = "Le membre a été anonymisé avec succès.";
                const toast = new bootstrap.Toast(document.getElementById('toastEditSuccess'));
                toast.show();
            } else {
                this.message_error = data.message || "Une erreur est survenue lors de l’anonymisation.";
            }
            });
        },

        confirmAnonymize() {
            const modalEl = document.getElementById('confirmAnonymizeModal');
            const modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) modal.hide(); // ferme le modal proprement
            this.anonymizeMember();  // lance la requête d’anonymisation
        }

    },
    mounted() {
        // Récupération de l'id_user dans l'URL
        const params = new URLSearchParams(window.location.search);
        this.id_user = params.get("id");

        if (!this.id_user) {
            console.error("Aucun id_user trouvé dans l'URL");
            return;
        }

        // Récupération du profil au chargement
        fetch("./back/API/owner/ownerGetUserByID.php", 
        {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({ id_user: this.id_user })
        })
        .then(rep => rep.json())
        .then(rep2 => {
            if (rep2.success && rep2.data) 
            {
                this.membre = {
                    ...this.membre,
                    ...rep2.data,
                    // Conversion des 0/1 en booléens directement
                    isCoach: rep2.data.isCoach === 1,
                    isOwner: rep2.data.isOwner === 1
                };

                console.log("Valeur de membre :", this.membre);
            } 
            else 
            {
                console.error("Réponse API invalide :", rep2);
            }
        })
        .catch(err => console.error("Erreur API:", err));
    },
});

ownerEditMemberApp.mount("#ownerEditMemberApp");
