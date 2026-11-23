const memberDashboardApp = Vue.createApp({
    data() {
        return {
            subscription: null,
            futureSessions: [],
            pastSessions: [],
            upcomingNotRegistered: []
        };
    },

    mounted() {
        this.fetchDashboard();
    },

    methods: {

        goToOrganisation(id) {
            window.location.href = `index.php?p=memberOrganisationView&id_organisation=${id}`;
        },

        fetchDashboard() {
            fetch('/back/API/member/memberGetDashboard.php', { credentials: 'include' })
                .then(res => res.json())
                .then(data => {
                    if (data.error) return;

                    this.subscription = data.subscription;
                    this.futureSessions = data.futureSessions;
                    this.pastSessions = data.pastSessions;
                    this.upcomingNotRegistered = data.upcomingNotRegistered;
                });
        },

        formatDate(dateStr) {
            return new Date(dateStr).toLocaleDateString();
        },

        formatTime(dateStr) {
            return new Date(dateStr).toLocaleTimeString([], { hour: "2-digit", minute: "2-digit" });
        },

        getColor(code) {
            const map = {
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
            return map[code] || map.default;
        }
    }
});

memberDashboardApp.mount('#memberDashboardApp');
