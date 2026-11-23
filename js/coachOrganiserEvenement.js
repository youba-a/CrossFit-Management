"use strict";

const BASE_URL = "../../back/api/coach";

const coachOrganiserEvenementApp = Vue.createApp({
    data() {
        return {
            activityTypes: [],
            boxes: [],
            selectedActivity: "",
            selectedBox: "",

            begin: "",
            end: "",
            recurring: false,
            weeks: 1,

            message_success: "",
            message_error: "",
        };
    },

    async mounted() {
        await this.loadFormData();
    },

    methods: {
        async loadFormData() {
            this.message_error = "";
            try {
                const res = await fetch(`${BASE_URL}/coachGetActivities.php`, { credentials: 'include' });
                const data = await res.json();
                if (!data.success) throw new Error(data.message || "Erreur de chargement des données.");

                this.activityTypes = data.activityTypes || [];
                this.boxes = data.boxes || [];
            } catch (err) {
                this.message_error = "Erreur de chargement : " + err.message;
            }
        },

        async submitEventForm(event) {
            event.preventDefault();
            this.message_success = "";
            this.message_error = "";

            const beginDate = new Date(this.begin);
            const endDate = new Date(this.end);

            if (!this.begin || !this.end || isNaN(beginDate) || isNaN(endDate) || endDate <= beginDate) {
                this.message_error = "Les dates saisies sont invalides.";
                return;
            }

            if (this.recurring && (isNaN(this.weeks) || this.weeks < 1 || this.weeks > 52)) {
                this.message_error = "Veuillez saisir un nombre de semaines valide (1 à 52).";
                return;
            }

            const payload = {
                id_typeActivities: parseInt(this.selectedActivity),
                id_box: parseInt(this.selectedBox),
                begin: this.begin,
                end: this.end,
                recurring: this.recurring,
                weeks: this.recurring ? parseInt(this.weeks) : 1
            };

            try {
                const res = await fetch(`${BASE_URL}/coachCreateEvent.php`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload),
                    credentials: 'include'
                });
                const data = await res.json();

                if (data.success) {
                    this.message_success = data.message || "Événement créé avec succès.";

                    // Réinitialisation
                    this.selectedActivity = "";
                    this.selectedBox = "";
                    this.begin = "";
                    this.end = "";
                    this.recurring = false;
                    this.weeks = 1;

                    this.$nextTick(() => {
                        const toastEl = document.getElementById('toastEventSuccess');
                        if (toastEl) {
                            const toast = new bootstrap.Toast(toastEl);
                            toast.show();
                        }
                    });
                } else {
                    this.message_error = data.message || "Erreur lors de la création de l'événement.";
                }
            } catch (err) {
                console.error(err);
                this.message_error = "Erreur serveur. Impossible de contacter l’API.";
            }
        },
    },
});

coachOrganiserEvenementApp.mount("#coachOrganiserEvenementApp");
