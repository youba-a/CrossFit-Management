"use strict";

const memberSubscriptionViewApp = Vue.createApp({
    data() {
        return {
            abonnements: [],
            abonnementActuel: null,
            historique: [],
            message_error: null
        };
    },
    methods: {
        formatDate(dateStr) {
            if (!dateStr) return '—';
            const d = new Date(dateStr);
            return d.toLocaleDateString('fr-FR', { year: 'numeric', month: 'long', day: 'numeric' });
        },
        fetchAbonnements() {
            fetch('./back/API/member/memberGetSubscriptions.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({})
            })
                .then(resp => resp.json())
                .then(data => {
                    if (!data.success) {
                        this.message_error = "Impossible de charger les abonnements.";
                        return;
                    }

                    this.abonnements = data.data || [];

                    if (this.abonnements.length > 0) {
                        // Le plus récent = début le plus tardif
                        this.abonnements.sort((a, b) =>
                            new Date(b.subscriptionBegin_boxuser) - new Date(a.subscriptionBegin_boxuser)
                        );

                        this.abonnementActuel = this.abonnements[0];
                        this.historique = this.abonnements.slice(1);
                        
                        // Init DataTable
                        this.$nextTick(() => {
                            $('#tableHistoriqueAbonnement').DataTable({
                                language: { url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/fr-FR.json' },
                                pageLength: 5,
                                lengthChange: false,
                                responsive: true,
                                autoWidth: false, // empêche DataTables de forcer certaines largeurs
                                columnDefs: [
                                    { responsivePriority: 1, targets: 0 }, // priorité pour colonnes importantes
                                    { responsivePriority: 2, targets: -1 } // par exemple, la colonne statut
                                ]
                            });
                        });
                    }
                })
                .catch(err => {
                    console.error(err);
                    this.message_error = "Erreur lors du chargement des données.";
                });
        }
    },
    mounted() {
        this.fetchAbonnements();
    }
});

memberSubscriptionViewApp.mount("#memberSubscriptionViewApp");
