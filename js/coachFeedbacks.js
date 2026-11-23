"use strict";

const coachFeedbacksApp = Vue.createApp({
    data() {
        return {
            feedbacks: { data: [] }
        };
    },

    methods: {
        formatDateFR(dateString) {
            const date = new Date(dateString);
            if (isNaN(date)) return dateString;

            return date.toLocaleString("fr-FR", {
                day: "2-digit",
                month: "2-digit",
                year: "numeric",
                hour: "2-digit",
                minute: "2-digit"
            });
        }
    },

    mounted() {
        fetch("./back/API/coach/coachGetFeedbacks.php")
            .then(rep => rep.json())
            .then(data => {
                this.feedbacks = data;

                if (data.success && data.data.length > 0) {
                    this.$nextTick(() => {
                        new DataTable('#tableCoachFeedbacks', {
                            language: {
                                url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/fr-FR.json'
                            },
                            pageLength: 10,
                            responsive: true
                        });
                    });
                }
            })
            .catch(err => {
                console.error("Erreur API :", err);
            });
    }
});

coachFeedbacksApp.mount("#coachFeedbacksApp");