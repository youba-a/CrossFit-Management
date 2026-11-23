"use strict";

const tarifApp = Vue.createApp({
  data() {
    return {
      abonnements: [],
      error: "",
    };
  },

  async mounted() {
    await this.fetchAbonnements();
  },

  methods: {
    async fetchAbonnements() {
      try {
        const response = await fetch("./back/API/guest/guestListOfSubscription.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({}),
        });

        const data = await response.json();

        if (Array.isArray(data)) {
          this.abonnements = data;
        } else if (data.data && Array.isArray(data.data)) {
          this.abonnements = data.data;
        } else if (data.error) {
          this.error = data.message || "Erreur lors du chargement des abonnements.";
        } else {
          this.error = "Format de données inattendu.";
        }

      } catch (err) {
        console.error(err);
        this.error = "Erreur serveur lors du chargement des abonnements.";
      }
    },
  },
});

tarifApp.mount("#tarifApp");
