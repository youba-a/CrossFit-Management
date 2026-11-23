// memberSubscriptionPurchase.js
"use strict";

const { createApp } = Vue;

createApp({
    data() {
    return {
        abonnement: null,
        loading: true,
        message_error: "",
        message_success: "",
        subscriptionBegin: "",
        minDate: "",
        maxDate: "",
        minDateDisplay: "",
        submitting: false,

        // NEW
        userBoxes: [],
        selectedBox: "",
    };
},
    computed: {
        subscriptionEnd() {
            if (!this.subscriptionBegin) return null;
            const d = new Date(this.subscriptionBegin + "T00:00:00");
            // add one year, subtract 1 day if you want inclusive year
            const end = new Date(d);
            end.setFullYear(end.getFullYear() + 1);
            end.setDate(end.getDate() - 1);
            return end;
        },
        subscriptionEndDisplay() {
            if (!this.subscriptionEnd) return "";
            return this.toFrenchDate(this.subscriptionEnd);
        }
    },
    methods: {
        toFrenchDate(d) {
            const day = String(d.getDate()).padStart(2, "0");
            const month = String(d.getMonth() + 1).padStart(2, "0");
            const year = d.getFullYear();
            return `${day}/${month}/${year}`;
        },
        async loadData() {
            this.loading = true;
            this.message_error = "";

            try {
                const subId = window.__SUB_ID__ || null;
                if (!subId) {
                    this.message_error = "Abonnement non précisé.";
                    this.loading = false;
                    return;
                }

                const res = await fetch(`/back/API/member/subscription/getSubscriptionInfo.php?id_abonnement=${encodeURIComponent(subId)}`, {
                    credentials: 'include'
                });
                const data = await res.json();
                if (!data.success) {
                    this.message_error = data.message || "Erreur lors du chargement.";
                    this.loading = false;
                    return;
                }

                this.abonnement = data.abonnement;
                // compute min date: max(today, next day after user's last subscription end)
                const today = new Date();
                const todayISO = this.toISODate(today);

                let min = todayISO;
                if (data.lastSubscriptionEnd) {
                    // lastSubscriptionEnd comes as yyyy-mm-dd
                    const last = new Date(data.lastSubscriptionEnd + "T00:00:00");
                    // choose day after last end
                    last.setDate(last.getDate() + 1);
                    const lastISO = this.toISODate(last);
                    if (lastISO > min) min = lastISO;
                }

                this.minDate = min;
                this.minDateDisplay = this.toFrenchDate(new Date(min + "T00:00:00"));
                // default chosen date = minDate
                this.subscriptionBegin = this.minDate;

                this.loading = false;
            } catch (err) {
                console.error(err);
                this.message_error = "Erreur serveur lors de la récupération des informations.";
                this.loading = false;
            }
        },

        toISODate(d) {
            const y = d.getFullYear();
            const m = String(d.getMonth() + 1).padStart(2, "0");
            const day = String(d.getDate()).padStart(2, "0");
            return `${y}-${m}-${day}`;
        },

        formatPrice(p) {
            if (p === null || p === undefined) return "";
            return parseFloat(p).toFixed(2).replace('.', ',');
        },

        goBack() {
            window.history.back();
        },

        async initiatePayment() {
            this.message_error = "";
            if (!this.subscriptionBegin) {
                this.message_error = "Veuillez choisir une date de début.";
                return;
            }
            if (this.subscriptionBegin < this.minDate) {
                this.message_error = "La date sélectionnée est antérieure à la date minimale autorisée.";
                return;
            }

            if (!this.selectedBox) {
                this.message_error = "Veuillez choisir une box.";
                return;
            }

            this.submitting = true;
            try {
                const payload = {
                    id_abonnement: window.__SUB_ID__,
                    subscriptionBegin: this.subscriptionBegin,
                    id_box: this.selectedBox
                };

                const res = await fetch('/back/API/member/subscription/initiatePayment.php', {
                    method: 'POST',
                    credentials: 'include',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });
                const data = await res.json();
                if (!data.success) {
                    this.message_error = data.message || "Erreur lors de l'initialisation du paiement.";
                    this.submitting = false;
                    return;
                }

                if (data.redirectUrl) {
                    // redirection vers le fournisseur de paiement
                    window.location.href = data.redirectUrl;
                    return;
                } else {
                    this.message_success = "Paiement initié, mais aucune URL de redirection fournie.";
                    this.submitting = false;
                }
            } catch (err) {
                console.error(err);
                this.message_error = "Erreur réseau lors de l'appel API.";
                this.submitting = false;
            }
        },
        async loadUserBoxes() {
            try {
                const res = await fetch('/back/API/member/subscription/getBoxes.php', { credentials: 'include' });
                const data = await res.json();

                if (!data.success) {
                    this.message_error = data.message || "Impossible de charger vos box.";
                    return;
                }

                this.userBoxes = data.boxes;

                // Si une seule box → pré sélection automatique
                if (this.userBoxes.length === 1) {
                    this.selectedBox = this.userBoxes[0].id_box;
                }

            } catch (err) {
                console.error(err);
                this.message_error = "Erreur de communication avec le serveur.";
            }
        }
    },
    mounted() {
        this.loadUserBoxes();
        this.loadData();
    }
}).mount('#memberSubscriptionPurchaseApp');
