"use strict";

/**
 * Vue.js App - Formulaire de demande d’essai gratuit
 * Objectif : charger les séances disponibles et envoyer les données au back-end.
 */

const freeTrialApp = Vue.createApp({
  data() {
    return {
      // Données du formulaire
      form: {
        nom_discovery: '',
        prenom_discovery: '',
        email_discovery: '',
        telephone_discovery: '',
        note_discovery: '',
        idOrganisation_discovery: '',
        accepted: false,
      },

      // Liste des activités dynamiques
      activities: [],

      // Messages utilisateur
      success: "",
      error: "",
    };
  },

  async mounted() {
    await this.fetchActivities();
  },

  methods: {
    /**
     *  Charger la liste des séances depuis l’API
     */
    async fetchActivities() {
      try {
        const response = await fetch("./back/API/guest/guestDiscoveryOrganisation.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({}),
        });

        const data = await response.json();

        if (Array.isArray(data)) {
          // Filtrer uniquement les activités permettant les invités
          const filtered = data.filter(act => act.allowGuest_activity == 1);

          this.activities = filtered.map(act => ({
            id: act.id_organisation,
            label: `${new Date(act.begin_organisation).toLocaleDateString("fr-BE", {
              weekday: "short",
              day: "2-digit",
              month: "short",
              hour: "2-digit",
              minute: "2-digit",
            })} — ${act.type_activity} avec ${act.coach_name} (${act.box_name})`,
            raw: act,
          }));

        } else if (data.error) {
          this.error = data.error;
        } else {
          this.error = "Réponse inattendue du serveur.";
        }

      } catch (err) {
        console.error(err);
        this.error = "Erreur serveur lors du chargement des séances.";
      }
    },

    /**
     *  Envoi du formulaire au back-end
     */
    async sendForm() {
      this.success = "";
      this.error = "";

      if (
        !this.form.nom_discovery.trim() ||
        !this.form.prenom_discovery.trim() ||
        !this.form.email_discovery.trim() ||
        !this.form.telephone_discovery.trim() ||
        !this.form.idOrganisation_discovery ||
        !this.form.accepted
      ) {
        this.error = "Veuillez remplir tous les champs obligatoires et accepter les conditions.";
        return;
      }

      try {
        const response = await fetch("./back/API/guest/guestRequestDiscovery.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify(this.form),
        });

        const data = await response.json();

        if (data.success) {
          this.success = data.message || "Votre demande a été envoyée avec succès !";
          this.form = {
            nom_discovery: "",
            prenom_discovery: "",
            email_discovery: "",
            telephone_discovery: "",
            note_discovery: "",
            idOrganisation_discovery: "",
            accepted: false,
          };

          // Affiche le toast de succès Bootstrap
          this.$nextTick(() => {
            const toastEl = document.getElementById("toastSuccess");
            if (toastEl) {
              const toast = new bootstrap.Toast(toastEl);
              toast.show();
            }
          });
        } else {
          this.error = data.message || "Erreur lors de l'envoi du formulaire.";
        }
      } catch (err) {
        console.error(err);
        this.error = "Erreur serveur. Impossible de contacter l’API.";
      }
    },
  },
});

freeTrialApp.mount("#freeTrialApp");
