"use strict";

const ownerManageBoxApp = Vue.createApp({
    data() {
        return {
            boxes: [],
            errorMessage: null,
            successMessage: null,
            errorMessageModal: null,

            modalTitle: "",
            form: {},

            editMode: false,
        };
    },

    methods: {
        loadBoxes() {
            fetch("./back/API/owner/ownerGetBoxes.php")
                .then(r => r.json())
                .then(data => {
                    if (data.error) {
                        this.errorMessage = data.error;
                        return;
                    }
                    this.boxes = data;
                });
        },

        openAddModal() {
            this.errorMessageModal = null;
            this.editMode = false;
            this.modalTitle = "Ajouter une box";

            this.form = {
                name_box: "",
                streetNameAddress_box: "",
                numeroAddress_box: "",
                postBoxAddress_box: "",
                postalCode_box: "",
                town_box: "",
                telephone_box: "",
                numberPeople_box: ""
            };

            new bootstrap.Modal("#boxModal").show();
        },

        openEditModal(box) {
            this.errorMessageModal = null;
            this.editMode = true;
            this.modalTitle = "Modifier la box";

            this.form = {...box};

            new bootstrap.Modal("#boxModal").show();
        },

        saveBox() {
            const url = this.editMode
                ? "./back/API/owner/ownerEditBox.php"
                : "./back/API/owner/ownerAddBox.php";

            fetch(url, {
                method: "POST",
                headers: {"Content-Type": "application/json"},
                body: JSON.stringify(this.form)
            })
                .then(r => r.json())
                .then(data => {
                    if (data.error) {
                        this.errorMessageModal = data.error;
                        return;
                    }
                    this.successMessage = data.message;
                    document.querySelector("#boxModal .btn-close").click();
                    this.loadBoxes();
                });
        },

        toggleBox(id) {
            fetch("./back/API/owner/ownerToggleBox.php", {
                method: "POST",
                headers: {"Content-Type": "application/json"},
                body: JSON.stringify({ id_box: id })
            })
                .then(r => r.json())
                .then(() => this.loadBoxes());
        }
    },

    mounted() {
        this.loadBoxes();
    }
});

ownerManageBoxApp.mount("#ownerManageBoxApp");