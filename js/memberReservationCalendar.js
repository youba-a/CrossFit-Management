"use strict";

const memberReservationCalendarApp = Vue.createApp({
    data() {
        return {
            listeOrganisation: [],
            dataTable: null,
            calendar: null
        };
    },

    methods: {
        // formatage simple en FR
        formatDateForTable(datetimeStr) {
            // datetimeStr attendu "YYYY-MM-DD HH:MM:SS"
            try {
                const iso = datetimeStr.replace(' ', 'T');
                const d = new Date(iso);
                return d.toLocaleString('fr-FR', {
                    year: 'numeric', month: '2-digit', day: '2-digit',
                    hour: '2-digit', minute: '2-digit'
                });
            } catch (e) {
                return datetimeStr;
            }
        },

        getColorFromCode(code) {
            const colorMap = {
                1: "#f9b233", 2: "#00b0f0", 3: "#e6007e", 4: "#3fc380",
                5: "#9b59b6", 6: "#e74c3c", 7: "#f1c40f", 8: "#34495e",
                9: "#1abc9c", 10: "#bdc3c7"
            };
            return colorMap[code] || "#7f8c8d";
        },

        // Initialisation FullCalendar (idempotent)
        initCalendar() {
            const events = this.listeOrganisation.map(org => ({
                id: org.id_organisation,
                title: org.activite,
                start: org.debut,
                end: org.fin,
                extendedProps: { coach: org.coach, lieu: org.lieu },
                color: this.getColorFromCode(org.color)
            }));

            const calendarEl = document.getElementById('calendar');
            if (this.calendar) {
                this.calendar.destroy();
                this.calendar = null;
                calendarEl.innerHTML = "";
            }

            this.calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'timeGridWeek',
                locale: 'fr',
                themeSystem: 'bootstrap5',
                slotMinTime: "07:00:00",
                slotMaxTime: "22:00:00",
                allDaySlot: false,
                nowIndicator: true,
                height: 'auto',
                eventDisplay: 'auto',
                firstDay: 1,
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                events: events,
                eventClick: function(info) {
                    info.jsEvent.preventDefault();
                    window.location.href = 'index.php?p=memberOrganisationView&id_organisation=' + info.event.id;
                },
            });

            this.calendar.render();
        },

        // Initialise la DataTable UNE SEULE FOIS
        initDataTableOnce() {
            // Si déjà initialisée, on ne la ré-init pas
            if (this.dataTable) {
                return;
            }

            // Initialisation DataTable vide (les données seront ajoutées via reloadDataTable)
            this.dataTable = $('#eventsTable').DataTable({
                data: [],
                columns: [
                    { title: "Date début" },
                    { title: "Activité" },
                    { title: "Coach" },
                    { title: "Lieu" },
                    { title: "Statut" },
                    { title: "ID", visible: false }   // colonne cachée
                ],
                order: [[0, "asc"]],
                pageLength: 10,
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.13.8/i18n/fr-FR.json"
                },
                autoWidth: false,
                responsive: true
            });

            // -------------------------------------------------------
            // Rendre les lignes cliquables (AJOUT IMPORTANT)
            // -------------------------------------------------------
            $('#eventsTable tbody').on('click', 'tr', (e) => {
                const rowData = this.dataTable.row(e.currentTarget).data();
                if (!rowData) return;

                const id = rowData[5]; // colonne cachée ID
                window.location.href = 'index.php?p=memberOrganisationView&id_organisation=' + id;
            });

            // Optionnel : curseur pointeur sur les lignes
            $('#eventsTable tbody').css('cursor', 'pointer');
        },

        // Re-remplit la DataTable à partir de listeOrganisation
        reloadDataTable() {
            if (!this.dataTable) {
                this.initDataTableOnce();
            }

            const now = new Date();
            const rows = this.listeOrganisation.map(ev => {
                const startDate = new Date(ev.debut.replace(' ', 'T'));

                let statutBadge;
                if (startDate > now) {
                    statutBadge = '<span class="badge bg-success">À venir</span>';
                } else {
                    statutBadge = '<span class="badge bg-danger">Passé</span>';
                }

                return [
                    this.formatDateForTable(ev.debut),
                    ev.activite,
                    ev.coach || "",
                    ev.lieu || "",
                    statutBadge,
                    ev.id_organisation
                ];
            });

            // replace data proprement sans détruire l'instance
            this.dataTable.clear();
            if (rows.length) this.dataTable.rows.add(rows);
            this.dataTable.draw(false);

            // Ajustements utiles si table était cachée puis visible
            setTimeout(() => {
                try {
                    this.dataTable.columns.adjust().draw(false);
                } catch (e) {
                    // ignore si pas supporté
                }
            }, 10);
        },

        // Setup des onglets
        setupTabs() {
            const appThis = this;
            const tabs = document.querySelectorAll('#viewTabs .nav-link');
            const calendarView = document.getElementById("calendarView");
            const tableView = document.getElementById("tableView");

            tabs.forEach(tab => {
                tab.addEventListener("click", function(e) {
                    e.preventDefault();
                    tabs.forEach(t => t.classList.remove("active"));
                    this.classList.add("active");

                    if (this.dataset.view === "calendar") {
                        calendarView.style.display = "block";
                        tableView.style.display = "none";

                        // redimensionnement calendar (si nécessaire)
                        if (appThis.calendar) {
                            setTimeout(() => appThis.calendar.updateSize(), 50);
                        }
                    } else {
                        calendarView.style.display = "none";
                        tableView.style.display = "block";

                        // recharge les données dans la DataTable (ne recrée pas l'instance)
                        appThis.reloadDataTable();
                    }
                });
            });
        }
    },

    mounted() {
        // on initialise la table une fois (avec data vide)
        this.initDataTableOnce();

        fetch("./back/API/member/memberGetEvents.php")
            .then(rep => {
                if (!rep.ok) throw new Error("Réponse réseau non OK");
                return rep.json();
            })
            .then(rep2 => {
                this.listeOrganisation = rep2.evenementsFuturs || [];

                // calendar + table
                this.initCalendar();
                this.reloadDataTable();
                this.setupTabs();
            })
            .catch(err => {
                console.error("Erreur API :", err);
            });
    },
});

memberReservationCalendarApp.mount("#memberReservationCalendarApp");
