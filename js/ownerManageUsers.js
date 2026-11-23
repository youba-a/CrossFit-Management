"use strict";

const ownerManageUsersApp = Vue.createApp({
	data() {
		return {
			listeUsers: []
		};
	},
	methods: {
        
  
	},

    mounted() {
		fetch("./back/API/owner/ownerListAllUsers.php")
			.then(rep => rep.json())
			.then(rep2 => {
				this.listeUsers = rep2;

                // On initialise DataTable après que Vue ait rendu le tableau
				this.$nextTick(() => {
					new DataTable('#tableOwnerManageUsers', {
						language: {
							url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/fr-FR.json'
						}
					});
				});
			});
	},
});

ownerManageUsersApp.mount("#ownerManageUsersApp");