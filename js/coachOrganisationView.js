"use strict";

const coachOrganisationViewApp = Vue.createApp({
    data() {
        return {
            organisation: null,
            message_error: null
        };
    },
    methods: {
        formatDateTime(dateStr) {
            if (!dateStr) return '—';
            const d = new Date(dateStr);
            return d.toLocaleString('fr-FR', { dateStyle: 'short', timeStyle: 'short' });
        },
        fetchOrganisation() {
            const urlParams = new URLSearchParams(window.location.search);
            const idOrganisation = urlParams.get('id_organisation');
            if (!idOrganisation) {
                this.message_error = "ID de l'organisation manquant.";
                return;
            }

            fetch(`./back/API/coach/coachGetOrganisation.php`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ id_organisation: idOrganisation })
            })
                .then(resp => resp.json())
                .then(data => {
                    if (data.error) {
                        this.message_error = data.error;
                        return;
                    }
                    this.organisation = data;

                    // Init DataTable après rendu
                    this.$nextTick(() => {
                        $('#tableOrganisationMembers').DataTable({
                            language: { url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/fr-FR.json' },
                            pageLength: 10,
                            lengthChange: false
                        });
                    });
                })
                .catch(err => {
                    console.error(err);
                    this.message_error = "Erreur lors du chargement des données.";
                });
        }
    },
    mounted() {
        this.fetchOrganisation();
    }
});

coachOrganisationViewApp.mount("#coachOrganisationViewApp");
