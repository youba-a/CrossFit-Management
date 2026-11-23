"use strict";

const loginModalApp = Vue.createApp({
	data() {
		return {
			login: "",
			password: "",
			message_error: "",     // message de retour (erreur ou succes)
            message_success: "",     // message de retour (erreur ou succes)
		};
	},
	methods: {
        async submitLogin() {
            try {
                const response = await fetch('./back/API/Login/login.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ login: this.login, password: this.password }),
                    credentials: 'include' // essentiel pour gérer le cookie
                });

                const data = await response.json();
                if (data.success) {
                    this.message_success = "Connexion réussie !";
                    // Cookie HttpOnly est stocké automatiquement par le navigateur

                    // redirection vers le dashboard
                    window.location.href = "index.php?p=dashboard";

                } else {
                    this.message_error = data.message;
                }
            } catch (err) {
                this.message_error = "Erreur serveur.";
            }
        }
	}
});

loginModalApp.mount("#loginModal");