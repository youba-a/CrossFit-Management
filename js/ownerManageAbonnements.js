"use strict";

const app = Vue.createApp({
    data() {
        return {
            BASE_URL: "../../back/api/owner/abonnements",

            membres: [],
            selectedMember: null,
            abonnementsMembre: [],
            typesAbonnements: [],
            boxes: [], // 🆕 liste des box récupérées depuis getAllBox.php

            formAbonnement: {
                id_boxuser: "",
                id_abonnement: "",
                id_box: "", // 🆕 nouvelle propriété : la box choisie
                datePayment: "",
                begin: "",
                end: ""
            },
            formType: {
                id: "",
                nom: "",
                description: "",
                prix: ""
            },

            abonnementModal: null,
            typeModal: null,
            isEditingAbonnement: false,
            isEditingType: false,

            toastInstance: null,
            toastMessage: "",

            //modal confirmation
            confirmModal: null,      // 🆕 instance du modal
            confirmMessage: "",      // 🆕 texte du message affiché
            confirmAction: null,     // 🆕 callback à exécuter si l'utilisateur confirme
        };
    },

    mounted() {
        this.loadMembres();
        this.loadTypes();
        this.loadBoxes(); // 🆕 on charge aussi les box

        this.$nextTick(() => {
            this.initDataTableMembres();
            this.initDataTableTypes();
        });
    },

    methods: {
        initDataTables() {
            setTimeout(() => {
                ['#tableMembres'].forEach(id => {
                    if ($.fn.DataTable.isDataTable(id)) {
                        $(id).DataTable().destroy();
                    }
                });

                $('#tableMembres').DataTable({
                    language: dataTableLangFR,
                    order: []
                });
            }, 300);
        },

        openConfirmModal(message, actionCallback) {
            this.confirmMessage = message;
            this.confirmAction = async () => {
                await actionCallback();
                this.confirmModal.hide();
            };
            if (!this.confirmModal)
                this.confirmModal = new bootstrap.Modal(document.getElementById("confirmModal"));
            this.confirmModal.show();
        },

        async loadMembres() {
            const res = await fetch(`${this.BASE_URL}/getMembersList.php`);
            const data = await res.json();
            this.membres = data.data || [];
            this.$nextTick(() => this.initDataTables());
        },

        async loadBoxes() { // 🆕
            try {
                const res = await fetch(`${this.BASE_URL}/getAllBox.php`);
                const data = await res.json();
                if (data.success) {
                    this.boxes = data.data;
                } else {
                    this.showToast("Erreur de chargement des box ❌", false);
                }
            } catch (e) {
                console.error(e);
                this.showToast("Impossible de charger les box ❌", false);
            }
        },

        async viewAbonnements(member) {
            this.selectedMember = member;
            const res = await fetch(`${this.BASE_URL}/getMemberAbonnements.php`, {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ id_user: member.id_user })
            });
            const data = await res.json();
            this.abonnementsMembre = data.data || [];
            this.$nextTick(() => this.initDataTables());
        },

        async loadTypes() {
            const res = await fetch(`${this.BASE_URL}/abonnementTypes.php`, {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ action: "list" })
            });
            const data = await res.json();
            this.typesAbonnements = data.data || [];
            this.$nextTick(() => this.initDataTables());
        },

        openAddModal() {
            this.formAbonnement = { 
                id_boxuser: "",
                id_abonnement: "",
                id_box: "", // 🆕
                datePayment: "",
                begin: "",
                end: "" 
            };
            this.isEditingAbonnement = false;
            if (!this.abonnementModal)
                this.abonnementModal = new bootstrap.Modal(document.getElementById("abonnementModal"));
            this.abonnementModal.show();
        },

        editAbonnement(ab) {
            this.formAbonnement = {
                id_boxuser: ab.id_boxuser,
                id_abonnement: ab.id_abonnement,
                id_box: ab.idBox_boxuser,
                datePayment: ab.datePayment_boxuser,
                begin: ab.subscriptionBegin_boxuser,
                end: ab.subscriptionEnd_boxuser
            };
            this.isEditingAbonnement = true;

            if (!this.abonnementModal)
                this.abonnementModal = new bootstrap.Modal(document.getElementById("abonnementModal"));
            this.abonnementModal.show();
        },

        async saveAbonnement() {
            // 🆕 id_box vient du select dans le formulaire
            const payload = {
                action: this.isEditingAbonnement ? "edit" : "add",
                id_boxuser: this.isEditingAbonnement ? this.formAbonnement.id_boxuser : undefined,
                id_user: this.selectedMember.id_user,
                id_box: this.formAbonnement.id_box,
                id_abonnement: this.formAbonnement.id_abonnement,
                datePayment_boxuser: this.formAbonnement.datePayment,
                subscriptionBegin_boxuser: this.formAbonnement.begin,
                subscriptionEnd_boxuser: this.formAbonnement.end
            };

            const res = await fetch(`${this.BASE_URL}/addEditDeleteMemberAbonnement.php`, {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(payload)
            });
            const data = await res.json();

            if (data.success) {
                this.showToast(data.message);
                this.abonnementModal.hide();
                this.viewAbonnements(this.selectedMember);
                this.loadMembres(); // recharge la liste des membres
            } else {
                this.showToast(data.message || "Erreur ❌", false);
            }
        },

        async deleteAbonnement(ab) {
            this.openConfirmModal("Supprimer cet abonnement ?", async () => {
                const payload = { action: "delete", id_boxuser: ab.id_boxuser };
                const res = await fetch(`${this.BASE_URL}/addEditDeleteMemberAbonnement.php`, {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify(payload)
                });
                const data = await res.json();
                if (data.success) {
                    this.showToast("Abonnement supprimé ✅");
                    this.viewAbonnements(this.selectedMember);
                    this.loadMembres(); // recharge la liste des membres
                } else {
                    this.showToast("Erreur ❌", false);
                }
            });
        },

        openAddTypeModal() {
            this.formType = { id: "", nom: "", description: "", prix: "" };
            this.isEditingType = false;
            if (!this.typeModal)
                this.typeModal = new bootstrap.Modal(document.getElementById("typeModal"));
            this.typeModal.show();
        },

        editType(t) {
            this.formType = { 
                id: t.id_abonnement, 
                nom: t.nom_abonnement, 
                description: t.description_abonnement, 
                prix: t.prix_abonnement 
            };
            this.isEditingType = true;
            if (!this.typeModal)
                this.typeModal = new bootstrap.Modal(document.getElementById("typeModal"));
            this.typeModal.show();
        },

        async saveType() {
            const payload = {
                action: this.isEditingType ? "edit" : "add",
                id: this.formType.id,
                nom: this.formType.nom,
                description: this.formType.description,
                prix: parseFloat(this.formType.prix)
            };

            const res = await fetch(`${this.BASE_URL}/abonnementTypes.php`, {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(payload)
            });
            const data = await res.json();

            if (data.success) {
                this.showToast(data.message);
                this.typeModal.hide();
                this.loadTypes();
            } else {
                this.showToast(data.message || "Erreur ❌", false);
            }
        },

        async deleteType(t) {
            this.openConfirmModal(`Désactiver le type « ${t.nom_abonnement} » ?`, async () => {
                const res = await fetch(`${this.BASE_URL}/abonnementTypes.php`, {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ action: "delete", id: t.id_abonnement })
                });
                const data = await res.json();
                if (data.success) {
                    this.showToast(data.message);
                    this.loadTypes();
                } else {
                    this.showToast("Erreur ❌", false);
                }
            });
        },

        showToast(message, success = true) {
            this.toastMessage = message;
            const toastEl = document.getElementById("toast");
            toastEl.classList.toggle("text-bg-success", success);
            toastEl.classList.toggle("text-bg-danger", !success);
            if (!this.toastInstance)
                this.toastInstance = new bootstrap.Toast(toastEl);
            this.toastInstance.show();
        },

        formatDate(d) {
            if (!d) return "";
            const date = new Date(d);
            return date.toLocaleDateString("fr-FR");
        }
    }
});

app.mount("#ownerManageAbonnementsApp");
