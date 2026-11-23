"use strict";
const app = Vue.createApp({
    data() {
        return {
            coachs: [],
            loading: true,
            error: null
        };
    },
    mounted() {
        fetch('../../back/API/guest/guestListOfCoach.php')
            .then(res => {
                if (!res.ok) throw new Error("Erreur de chargement des coachs");
                return res.json();
            })
            .then(data => {
                this.coachs = data;
            })
            .catch(err => {
                this.error = err.message;
            })
            .finally(() => {
                this.loading = false;
            });
    }
});
app.mount('#teamApp');