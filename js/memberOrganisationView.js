"use strict";

const memberOrganisationViewApp = Vue.createApp({
    data() {
        return {
            organisation: null,
            message_error: null,

            // Feedback
            feedbackText: "",
            feedbackSuccess: null,
            feedbackError: null,
            isSubmitting: false,

            coachFeedbackText: "",
            coachFeedbackSuccess: null,
            coachFeedbackError: null,
            isSubmittingCoach: false
        };
    },
    methods: {
        formatDateTime(dateStr) {
            if (!dateStr) return '—';
            const d = new Date(dateStr);
            return d.toLocaleString('fr-FR', {
                dateStyle: 'short',
                timeStyle: 'short'
            });
        },

        fetchOrganisation() {
            const urlParams = new URLSearchParams(window.location.search);
            const idOrganisation = urlParams.get('id_organisation');
            if (!idOrganisation) {
                this.message_error = "ID de l'organisation manquant.";
                return;
            }

            fetch(`./back/API/member/memberGetOrganisation.php?id_organisation=${idOrganisation}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json'
                }
            })
                .then(resp => resp.json())
                .then(data => {
                    if (data.error) {
                        this.message_error = data.error;
                        return;
                    }

                    this.organisation = data;

                    // Pré-remplir feedback
                    this.feedbackText = data.feedback_event || "";
                    this.coachFeedbackText = data.feedback_coach || "";
                })
                .catch(err => {
                    console.error(err);
                    this.message_error = "Erreur lors du chargement des données.";
                });
        },

        sendFeedback() {
            this.feedbackSuccess = null;
            this.feedbackError = null;
            this.isSubmitting = true;

            fetch("./back/API/member/memberSendFeedback.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({
                    id_organisation: this.organisation.id_organisation,
                    feedback: this.feedbackText
                })
            })
                .then(resp => resp.json())
                .then(data => {
                    if (data.error) {
                        this.feedbackError = data.error;
                    } else {
                        this.feedbackSuccess = "Votre feedback a bien été enregistré !";
                        this.organisation.user_feedback = this.feedbackText;
                    }
                })
                .catch(err => {
                    console.error(err);
                    this.feedbackError = "Erreur lors de l'envoi du feedback.";
                })
                .finally(() => {
                    this.isSubmitting = false;
                });
        },

        sendCoachFeedback() {
            this.coachFeedbackSuccess = null;
            this.coachFeedbackError = null;
            this.isSubmittingCoach = true;

            fetch("./back/API/member/memberSendFeedbackCoach.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({
                    id_organisation: this.organisation.id_organisation,
                    feedback: this.coachFeedbackText
                })
            })
                .then(r => r.json())
                .then(data => {
                    if (data.error) {
                        this.coachFeedbackError = data.error;
                    } else 
                    {
                        this.coachFeedbackSuccess = "Feedback envoyé!";
                        this.organisation.user_feedback_coach = this.coachFeedbackText;
                    }
                })
                .catch(() => {
                    this.coachFeedbackError = "Erreur serveur.";
                })
                .finally(() => {
                    this.isSubmittingCoach = false;
                });
        }

    },

    mounted() {
        this.fetchOrganisation();
    }
});

memberOrganisationViewApp.mount("#memberOrganisationViewApp");
