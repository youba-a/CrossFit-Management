"use strict";

const calendarApp = Vue.createApp({
	data() {
		return {
			listeOrganisation: []
		};
	},
	methods: {
        getColorFromCode(code) {
            const colorMap = {
                1: "#f9b233", // orange vif
                2: "#00b0f0", // bleu clair
                3: "#e6007e", // rose magenta
                4: "#3fc380", // vert menthe
                5: "#9b59b6", // violet doux
                6: "#e74c3c", // rouge vif
                7: "#f1c40f", // jaune citron
                8: "#34495e", // bleu foncé
                9: "#1abc9c", // turquoise
                10: "#bdc3c7", // gris clair
                default: "#7f8c8d" // gris neutre - défaut
            };
            return colorMap[code] || colorMap.default;
        },

        initCalendar() {
            // Transformation des données API en events FullCalendar
            const events = this.listeOrganisation.map(org => ({
                id: org.id_organisation,
                title: `${org.type_activity} - ${org.coach_name}`,
                start: org.begin_organisation,
                end: org.end_organisation,
                description: org.description_activity,
                extendedProps: {
                    box: org.box_name,
                    address: `${org.numero} ${org.street}, ${org.postal_code} ${org.town}`
                },
                color: this.getColorFromCode(org.color_activity)
            }));

            const calendarEl = document.getElementById('calendar');
            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'timeGridWeek',
                locale: 'fr',
                themeSystem: 'bootstrap5',
                slotMinTime: "07:00:00",
                slotMaxTime: "22:00:00",
                allDaySlot: false,
                nowIndicator: true,
                height: 'auto',
                expandRows: false, // <— empêche l'expansion pour remplir une hauteur fixe
                eventDisplay: 'auto',
                firstDay: 1,

                // En-tête personnalisée : seulement flèches + titre
                headerToolbar: {
                left: 'prev,next today', // navigation
                center: 'title',          // titre de la semaine
                right: ''                 // ← vide = pas de boutons
                },
                events: events,
                eventClick: function(info) {
                    //redirection vers la page de l'organisation
                    window.location.href = 'index.php?p=freeTrial';

                    info.jsEvent.preventDefault();
                },
            });

            calendar.render();
        }    
	},

    mounted() {
		fetch("./back/API/guest/guestDiscoveryOrganisation.php")
			.then(rep => rep.json())
			.then(rep2 => {
				this.listeOrganisation = rep2;

                this.initCalendar();
			});
	},
});

calendarApp.mount("#calendar");