"use strict";

const app = Vue.createApp({
    data() {
        return {
            users: [],
            error: null
        };
    },
    methods: {
        formatDate(d) {
            return new Date(d).toLocaleDateString("fr-FR");
        },
        fetchUsers() {
            fetch("./back/API/owner/ownerGetDisableRequests.php")
                .then(r => r.json())
                .then(data => {
                    if (data.error) {
                        this.error = data.error;
                        return;
                    }
                    this.users = data.users;
                })
                .catch(() => {
                    this.error = "Erreur lors du chargement.";
                });
        }
    },
    mounted() {
        this.fetchUsers();
    }
});

app.mount("#ownerDisableRequestsApp");