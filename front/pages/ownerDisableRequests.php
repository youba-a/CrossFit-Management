<?php if (!$authOwnerId) { exit; } ?>

<section id="ownerDisableRequestsApp" v-cloak>
    <div class="container-fluid">
        <div class="row flex-nowrap">
            <?php require_once 'front/includes/dashboard_sidebar.php'; ?>
            
            <div class="col py-3">

                <h2 class="mb-4">Membres demandant une désactivation</h2>

                <div v-if="error" class="alert alert-danger">{{ error }}</div>

                <table v-if="users.length > 0" class="table table-striped table-bordered">
                    <thead class="table-white">
                        <tr>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Email</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr v-for="user in users" :key="user.id_user">
                            <td>{{ user.lastName_user }}</td>
                            <td>{{ user.firstName_user }}</td>
                            <td>{{ user.emailAddress_user }}</td>

                            <td>
                                <a :href="'index.php?p=ownerEditMember&id=' + user.id_user" 
                                class="btn btn-warning btn-sm">Gérer</a>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div v-else class="alert alert-info">
                    Aucun membre n’a demandé la désactivation.
                </div>

            </div>
        </div>
    </div>
</section>

<script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
<script src="/js/ownerDisableRequests.js"></script>