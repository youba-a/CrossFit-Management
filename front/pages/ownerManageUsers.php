<?php
if ($authOwnerId === null) {
    exit;
}
?>

<section id="ownerManageUsersApp">
    <div class="container-fluid">
        <div class="row flex-nowrap">
            <?php
            require_once 'front/includes/dashboard_sidebar.php';
            ?>
            <div class="col py-3">
                <!-- MAIN PAGE BODY -->
                 <h2>Gestion des utilisateurs</h2>
                <table id="tableOwnerManageUsers" class="table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Prénom</th>
                            <th>Nom</th>
                            <th>Email</th>
                            <th>Rôle</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="user in listeUsers.data" :key="user.id_user">
                            <td>{{ user.id_user }}</td>
                            <td>{{ user.firstName_user }}</td>
                            <td>{{ user.lastName_user }}</td>
                            <td>{{ user.emailAddress_user }}</td>
                            <td>{{ user.role}}</td>
                            <td>
                                <!-- Icône cliquable pour aller à la fiche utilisateur -->
                                <a :href="'index.php?p=ownerEditMember&id=' + user.id_user" class="btn btn-primary btn-sm">
                                    <i class="bi bi-person-circle"></i> Modifier
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <!-- MAIN PAGE BODY FIN -->
                <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
                <script src="https://cdn.datatables.net/2.3.2/js/dataTables.min.js"></script>

                
                <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
                <script src="/js/ownerManageUsers.js"></script>

            </div>
        </div>
    </div>
</section>

