const userBannerApp = Vue.createApp({
	data() {
		return {
			user: null,
			loading: true
		};
	},
	methods: {
		logout() {
			fetch("/back/API/login/logout.php")
				.then(() => window.location.href = "index.php");
		}
	},
	mounted() {
		fetch("/back/API/member/memberGetMiniProfile.php")
			.then(res => {
				if (!res.ok) throw new Error();
				return res.json();
			})
			.then(data => {
				this.user = data.data;
			})
			.catch(() => {
				this.user = null;
			})
			.finally(() => {
				this.loading = false;
			});
	}
});
userBannerApp.mount("#userBannerApp");