"use strict";

const ownerManagePlanningsApp = Vue.createApp({
    data() {
        return {
            BASE_URL: "../../back/api/owner",

            // --- Données principales ---
            coachs: [],
            activities: [],
            planning: [],
            selectedCoach: null,

            // --- Activité ---
            activityForm: {
                name: "",
                desc: "",
                color: 0,
                allowGuest: false,
            },

            editActivityForm: {
                id: "",
                name: "",
                desc: "",
                color: 0,
                allowGuest: false,
                },
            editActivityModal: null,

            // --- Édition organisation ---
            editOrganisationModal: null,
            editOrganisationForm: {
                id: "",
                begin: "",
                end: "",
                id_box: "",
                id_activity: "",
            },
            boxes: [],
            activitiesSelect: [],

            // --- Messages et états ---
            message_error: "",
            message_success: "",
            loading: false,
            confirmModal: null,
            confirmMessage: "",
            confirmCallback: null,
            toastInstance: null,
            toastMessage: "",
        };
    },

    mounted() {
        this.loadCoachs();
        this.loadActivities();
    },

    methods: {
        // =============================
        // 🧱 1. Charger la liste des coachs
        // =============================
        async loadCoachs() {
            try {
                const res = await fetch(`${this.BASE_URL}/ownerManage.php?action=list_coachs`);
                const data = await res.json();
                this.coachs = Array.isArray(data) ? data : [];
            } catch (err) {
                console.error("Erreur loadCoachs:", err);
            }
        },

        // =============================
        // 🎨 2. Charger la liste des activités
        // =============================
        async loadActivities() {
            try {
                const res = await fetch("/back/API/owner/ownerManage.php?action=get_all_activities");
                const data = await res.json();
                this.activities = Array.isArray(data) ? data : [];
            } catch (err) {
                console.error("Erreur loadActivities:", err);
            }
        },

        // =============================
        // 🧩 3. Préremplir le formulaire d’activité
        // =============================
        async editActivity(id) {
            try {
                const res = await fetch("/back/API/owner/ownerManage.php?action=get_all_activities");
                const data = await res.json();
                const act = data.find(a => a.id_typeActivities == id);
                if (!act) return alert("Activité introuvable");

                this.activityForm = {
                    id: act.id_typeActivities,
                    originalName: act.name_typeActivity,
                    name: act.name_typeActivity,
                    desc: act.description_typeActivity,
                    color: act.color_typeActivity,
                    allowGuest: act.allowGuest_typeActivity == 1,
                };

                this.$nextTick(() => {
                    const el = document.getElementById("activityForm");
                    if (el) {
                        el.scrollIntoView({ behavior: "smooth" });
                        el.classList.add("border", "border-warning");
                        setTimeout(() => el.classList.remove("border", "border-warning"), 2000);
                    }
                });
            } catch (err) {
                console.error("Erreur editActivity:", err);
            }
        },

        // =============================
        // 🗓️ 4. Afficher le planning du coach
        // =============================
        async showPlanning(coach) {
            this.selectedCoach = coach;
            try {
                const res = await fetch(`${this.BASE_URL}/ownerManage.php?action=get_planning&id_coach=${coach.id_coach}`);
                const data = await res.json();
                this.planning = Array.isArray(data) ? data : [];
            } catch (err) {
                console.error("Erreur showPlanning:", err);
            }
        },

        // =============================
        // ❌ 5. Supprimer une organisation
        // =============================
        async deleteOrganisation(idOrganisation) {
            this.showConfirm("Supprimer cette session du planning ?", async () => {
                try {
                    const res = await fetch(`${this.BASE_URL}/ownerManage.php?action=delete_organisation&id=${idOrganisation}`, { method: "POST" });
                    const data = await res.json();
                    if (data.success) {
                        this.showToast("Organisation supprimée ✅");
                        this.showPlanning(this.selectedCoach);
                    } else {
                        this.showToast("Erreur lors de la suppression ❌", false);
                    }
                } catch (err) {
                    console.error("Erreur deleteOrganisation:", err);
                    this.showToast("Erreur technique ❌", false);
                }
            });
        },

        // =============================
        // 🧩 6. Créer une activité
        // =============================

        async createActivity(e) {
            e.preventDefault();
            const { name, desc, color, allowGuest } = this.activityForm;
            if (!name || !desc) {
                this.showToast("Merci de remplir tous les champs requis ❌", false);
                return;
            }

            const payload = {
                name,
                desc,
                color: parseInt(color) || 0,
                allowGuest: allowGuest ? 1 : 0,
            };

            this.showConfirm("Créer cette nouvelle activité ?", async () => {
                try {
                const res = await fetch("/back/API/owner/ownerManage.php?action=save_activity", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify(payload),
                });
                const data = await res.json();
                if (data.success) {
                    this.showToast("Nouvelle activité créée ✅");
                    this.resetActivityForm();
                    this.loadActivities();
                } else {
                    this.showToast("Erreur lors de la création ❌", false);
                }
                } catch (err) {
                console.error("Erreur createActivity:", err);
                this.showToast("Erreur technique ❌", false);
                }
            });
        },

        async editActivity(id) {
        try {
            const res = await fetch("/back/API/owner/ownerManage.php?action=get_all_activities");
            const data = await res.json();
            const act = data.find(a => a.id_typeActivities == id);
            if (!act) return this.showToast("Activité introuvable ❌", false);

            this.editActivityForm = {
            id: act.id_typeActivities,
            name: act.name_typeActivity,
            desc: act.description_typeActivity,
            color: act.color_typeActivity,
            allowGuest: act.allowGuest_typeActivity == 1,
            };

            if (!this.editActivityModal)
            this.editActivityModal = new bootstrap.Modal(document.getElementById("editActivityModal"));
            this.editActivityModal.show();
        } catch (err) {
            console.error("Erreur editActivity:", err);
        }
        },

        async saveEditedActivity() {
            const { id, name, desc, color, allowGuest } = this.editActivityForm;

            if (!id || !name || !desc) {
                this.showToast("Champs manquants ❌", false);
                return;
            }

            const payload = {
                id,
                name,
                desc,
                color: parseInt(color) || 0,
                allowGuest: allowGuest ? 1 : 0,
            };

            try {
                const res = await fetch("/back/API/owner/ownerManage.php?action=save_activity", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify(payload),
                });
                const data = await res.json();

                if (data.success) {
                    this.showToast("Activité mise à jour ✅");
                    this.loadActivities();
                    this.editActivityModal.hide();
                } else {
                    this.showToast("Erreur lors de la mise à jour ❌", false);
                }
            } catch (err) {
                console.error("Erreur saveEditedActivity:", err);
                this.showToast("Erreur technique ❌", false);
            }
        },

        // =============================
        // ❌ Supprimer une activité (avec nom dans le modal)
        // =============================
        async deleteActivity(idActivity, nameActivity) {
            const msg = `Voulez-vous vraiment supprimer l’activité « ${nameActivity} » ?`;
            this.showConfirm(msg, async () => {
                try {
                    const res = await fetch(`/back/API/owner/ownerManage.php?action=delete_activity&id=${idActivity}`, { method: "POST" });
                    const data = await res.json();

                    if (data.success) {
                        this.showToast(data.message);
                        this.loadActivities();
                    } else {
                        this.showToast(data.message || "Erreur lors de la suppression ❌", false);
                    }
                } catch (err) {
                    console.error("Erreur deleteActivity:", err);
                    this.showToast("Erreur technique ❌", false);
                }
            });
        },

        resetActivityForm() {
            this.activityForm = {
                id: "",
                originalName: "",
                name: "",
                desc: "",
                color: 0,
                allowGuest: false,
            };
        },

        // =============================
        // ✏️ 7. Édition d’une organisation
        // =============================
        async editOrganisation(idOrganisation) {
            try {
                const res = await fetch(`${this.BASE_URL}/ownerGetOrganisation.php?id=${idOrganisation}`);
                const data = await res.json();

                if (!data.success) {
                    alert("Organisation introuvable ❌");
                    return;
                }

                const orga = data.organisation;
                this.editOrganisationForm = {
                    id: orga.id_organisation,
                    begin: this.formatForInput(orga.begin_organisation),
                    end: this.formatForInput(orga.end_organisation),
                    id_box: orga.id_box,
                    id_activity: orga.id_activities,
                };

                await this.populateBoxes(orga.id_box);
                await this.populateActivities(orga.id_activities);

                if (!this.editOrganisationModal)
                    this.editOrganisationModal = new bootstrap.Modal(document.getElementById("editOrganisationModal"));
                this.editOrganisationModal.show();
            } catch (err) {
                console.error("Erreur editOrganisation:", err);
            }
        },

        async populateBoxes(selectedId = 0) {
            const res = await fetch("/back/api/owner/ownerManage.php?action=get_boxes");
            const data = await res.json();
            this.boxes = data;
        },

        async populateActivities(selectedId = 0) {
            const res = await fetch("/back/api/owner/ownerManage.php?action=get_activities");
            const data = await res.json();
            this.activitiesSelect = data;
        },

        async saveOrganisationChanges() {
            const payload = { ...this.editOrganisationForm };

            try {
                const res = await fetch("/back/api/owner/ownerManage.php?action=update_organisation", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify(payload),
                });
                const data = await res.json();

                if (data.success) {
                    this.showToast("Organisation mise à jour ✅");
                    this.editOrganisationModal.hide();
                    this.showPlanning(this.selectedCoach);
                } else {
                    this.showToast("Erreur lors de la mise à jour ❌", false);
                }
            } catch (err) {
                console.error("Erreur saveOrganisationChanges:", err);
                this.showToast("Erreur technique ❌", false);
            }
        },

        // Helpers
        getColorFromCode(code) {
            const colorMap = {
                1: "#f9b233",
                2: "#00b0f0",
                3: "#e6007e",
                4: "#3fc380",
                5: "#9b59b6",
                6: "#e74c3c",
                7: "#f1c40f",
                8: "#34495e",
                9: "#1abc9c",
                10: "#bdc3c7",
                default: "#7f8c8d"
            };
            return colorMap[code] || colorMap.default;
        },

        formatDate(dateStr) {
            const d = new Date(dateStr);
            return d.toLocaleString("fr-FR", {
                day: "2-digit",
                month: "2-digit",
                year: "numeric",
                hour: "2-digit",
                minute: "2-digit"
            });
        },

        formatForInput(dateStr) {
        const d = new Date(dateStr);
        return new Date(d.getTime() - d.getTimezoneOffset() * 60000)
            .toISOString()
            .slice(0, 16);
        },

        // =============================
        // 🧩  Utilitaires modals & toasts
        // =============================
        showConfirm(message, callback) {
            this.confirmMessage = message;
            this.confirmCallback = callback;
            if (!this.confirmModal)
                this.confirmModal = new bootstrap.Modal(document.getElementById("confirmModal"));
            this.confirmModal.show();
        },

        confirmAction() {
            if (this.confirmCallback) this.confirmCallback();
            this.confirmModal.hide();
        },

        showToast(message, success = true) {
            this.toastMessage = message;
            const toastEl = document.getElementById("liveToast");
            toastEl.classList.toggle("text-bg-success", success);
            toastEl.classList.toggle("text-bg-danger", !success);
            if (!this.toastInstance)
                this.toastInstance = new bootstrap.Toast(toastEl);
            this.toastInstance.show();
        },
    },
});

ownerManagePlanningsApp.mount("#ownerManagePlanningsApp");
