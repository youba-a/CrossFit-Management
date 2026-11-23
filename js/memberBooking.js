"use strict";

const memberBookingApp = Vue.createApp({
    data() {
        return {
            eventsList: [],      // événements transformés pour FullCalendar
            rawOrgs: [],         // données brutes renvoyées par l'API
            selectedEvent: null, // l’événement sélectionné
            bookingModal: null,  // instance bootstrap modal
            bookingToast: null,  // instance bootstrap toast
            message_error: "",
            calendar: null, // <- instance FullCalendar
            dataTable: null,
            loading: true,
        };
    },
    methods: {
        getColorFromCode(code) {
            const colorMap = {
                1: "#f9b233",
                2: "#00b0f0",
                3: "#e6007e",
                4: "#3fc380",
                5: "#9b59b6",
                6: "#e74c3c",
                7: "#f1c40f",
                8: "#34495e",
                9: "#1abc9c",
                10:"#bdc3c7",
                default: "#7f8c8d"
            };
            return colorMap[code] || colorMap.default;
        },

        transformOrgsToEvents() {
            this.eventsList = this.rawOrgs.map(org => ({
                id: org.id_organisation,
                title: `${org.name_typeActivity} — ${org.name_box}`,
                start: org.begin_organisation,
                end: org.end_organisation,
                extendedProps: {
                    description: org.description_typeActivity,
                    address: `${org.numeroAddress_box} ${org.streetNameAddress_box}, ${org.postalCode_box} ${org.town_box}`,
                    spotsRemaining: org.spotsRemaining,
                    capacity: org.capacity
                },
                color: this.getColorFromCode(org.color_typeActivity),
                display: org.spotsRemaining <= 0 ? 'background' : 'auto'
            }));
        },

        initCalendar() {
            const calendarEl = document.getElementById('calendar');
            if (!calendarEl) return;

            if (!this.calendar) { // si pas déjà initialisé
                this.calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'timeGridWeek',
                    locale: 'fr',
                    slotMinTime: "07:00:00",
                    slotMaxTime: "22:00:00",
                    allDaySlot: false,
                    nowIndicator: true,
                    height: 'auto',
                    firstDay: 1,
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,timeGridDay'
                    },
                    events: this.eventsList,
                    eventClick: (info) => {
                        if (!info.jsEvent) return;
                        info.jsEvent.preventDefault();
                        this.openBookingModal(info.event);
                    },
                });

                this.calendar.render();
            } else {
                // Si le calendrier existe déjà, on ne fait rien ici
                // On mettra à jour les events via addEventSource/removeAllEvents
            }
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

                const id = rowData[4];

                // retrouver l'événement dans eventsList
                const event = this.calendar.getEventById(id);

                if (event) {
                    this.openBookingModal(event);
                } else {
                    // si pas trouvé, on redirige comme avant
                    window.location.href = 'index.php?p=memberOrganisationView&id_organisation=' + id;
                }
            });

            // Optionnel : curseur pointeur sur les lignes
            $('#eventsTable tbody').css('cursor', 'pointer');
        },

        reloadDataTable() {
            if (!this.dataTable) {
                this.initDataTableOnce();
            }

            const now = new Date();

            const rows = this.rawOrgs.map(org => {
                const startDate = new Date(org.begin_organisation.replace(' ', 'T'));

                let statutBadge;
                if (startDate > now) {
                    statutBadge = '<span class="badge bg-success">À venir</span>';
                } else {
                    statutBadge = '<span class="badge bg-danger">Passé</span>';
                }

                return [
                    // Date début
                    new Date(org.begin_organisation).toLocaleString("fr-FR"),

                    // Activité
                    org.name_typeActivity,

                    // Lieu
                    `${org.name_box} (${org.postalCode_box} ${org.town_box})`,

                    // Statut
                    statutBadge,

                    // ID caché
                    org.id_organisation
                ];
            });

            this.dataTable.clear();
            if (rows.length) this.dataTable.rows.add(rows);
            this.dataTable.draw(false);

            setTimeout(() => {
                try {
                    this.dataTable.columns.adjust().draw(false);
                } catch (e) {}
            }, 10);
        },

        openBookingModal(ev) {
            if (!ev) return;

            const props = ev.extendedProps;

            if (props.spotsRemaining <= 0) {
                alert("Désolé, ce cours est complet.");
                return;
            }

            this.selectedEvent = ev;

            const modalTextEl = document.getElementById("bookingModalText");
            if (modalTextEl) {
                modalTextEl.style.whiteSpace = "pre-line";
                modalTextEl.textContent =
                    `${ev.title}
                    Début : ${ev.start.toLocaleString()}
                    Fin : ${ev.end.toLocaleString()}
                    Adresse : ${props.address}
                    Places restantes : ${props.spotsRemaining}`;
            }

            this.message_error = "";

            if (this.bookingModal) this.bookingModal.show();
        },

        loadEvents() {
            fetch('/back/API/member/memberBookingView.php')
                .then(r => r.json())   // <-- ne bloque plus si status ≠ 200
                .then(json => {
                    this.rawOrgs = json.data || [];
                    this.transformOrgsToEvents();

                    this.setupTabs();
                    this.reloadDataTable();

                    this.$nextTick(() => {
                        if (this.calendar) {
                            this.calendar.removeAllEvents();
                            this.calendar.addEventSource(this.eventsList);
                        } else {
                            this.initCalendar();
                        }
                    });

                    this.loading = false;
                })
                .catch(err => {
                    console.error('Erreur get events', err);
                    alert(err.message || 'Impossible de charger les cours.');
                    this.loading = false;
                });
        },

        confirmBooking() {
            const ev = this.selectedEvent;
            if (!ev) return;

            fetch('/back/API/member/memberBookEvent.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id_organisation: ev.id })
            })
            .then(r => r.json().then(json => ({ status: r.status, body: json })))
            .then(({ status, body }) => {

                if (status === 201 || body.success) {
                    const toastEl = document.getElementById("bookingSuccessToast");
                    if (toastEl) {
                        const toastBody = toastEl.querySelector('.toast-body');
                        if (toastBody) toastBody.innerText = body.message || "Réservation effectuée !";
                    }

                    if (this.bookingToast) this.bookingToast.show();
                    if (this.bookingModal) this.bookingModal.hide();

                    setTimeout(() => this.loadEvents(), 500);

                } else {
                    this.message_error = body.message;
                }
            })
            .catch(err => {
                console.error(err);
                alert("Erreur réseau pendant la réservation.");
            });
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

        this.initDataTableOnce();
        this.loadEvents();

        document.addEventListener("DOMContentLoaded", () => {
        const modalEl = document.getElementById('bookingConfirmModal');
        if (modalEl) this.bookingModal = new bootstrap.Modal(modalEl);

        const toastEl = document.getElementById('bookingSuccessToast');
        if (toastEl) this.bookingToast = new bootstrap.Toast(toastEl);
        });

        const confirmBtn = document.getElementById('confirmBookingBtn');
        if (confirmBtn) {
            confirmBtn.addEventListener('click', () => {
                this.confirmBooking();
            });
        }
    }
});

memberBookingApp.mount("#memberBookingApp");
