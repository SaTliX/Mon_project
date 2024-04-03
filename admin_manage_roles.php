<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Rôles</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body class="bg-gray-100">
    <div class="container mx-auto max-w-4xl py-8">
        <!-- En-tête avec des boutons -->
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-2xl font-semibold">Gestion des Rôles</h2>
            <div class="space-x-4">
                <!-- Bouton pour accéder aux interventions -->
                <a href="admin_interventions.php" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-md">Accéder aux Interventions</a>
                <!-- Bouton pour se déconnecter -->
                <a href="logout.php" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded-md">Déconnexion</a>
            </div>
        </div>

        <!-- Formulaire de recherche -->
        <form class="mb-4">
            <label for="search_user" class="block mb-1">Rechercher un utilisateur :</label>
            <input type="text" id="search_user" name="search_user" required class="w-full border rounded-md px-3 py-2">
            <button type="button" onclick="searchUser()" class="w-full bg-blue-500 text-white py-2 rounded-md hover:bg-blue-600 transition duration-300 ease-in-out">Rechercher</button>
        </form>

        <!-- Formulaire de tri -->
        <form class="mb-4">
            <label for="sort_by" class="block mb-1">Trier par :</label>
            <select id="sort_by" name="sort_by" class="border-gray-300 border rounded-md py-2 px-4 mr-2">
                <option value="Nom">Nom</option>
                <option value="Role">Rôle</option>
            </select>
            <button type="button" onclick="sortUsers()" class="bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600 transition duration-300 ease-in-out">Trier</button>
        </form>

        <!-- Liste des utilisateurs -->
        <div id="users_list">
            <?php
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                ob_start();
            }

            // Assurez-vous de configurer correctement votre connexion à la base de données ici
            $serveur = "127.0.0.1";
            $utilisateur = "root";
            $motDePasse = "";
            $baseDeDonnees = "accords energie";

            // Création d'une connexion à la base de données
            $conn = new mysqli($serveur, $utilisateur, $motDePasse, $baseDeDonnees);

            // Vérification de la connexion
            if ($conn->connect_error) {
                die("La connexion à la base de données a échoué : " . $conn->connect_error);
            }

            // Récupérer la liste des utilisateurs et trier si nécessaire
            $sql = "SELECT IDUtilisateur, Nom, Role FROM utilisateurs";
            if (isset($_POST['sort_by'])) {
                $sort_by = $_POST['sort_by'];
                $sql .= " ORDER BY $sort_by";
            } elseif (isset($_POST['search_user'])) {
                $search_user = $_POST['search_user'];
                $sql .= " WHERE Nom LIKE '%$search_user%'";
            }
            $result = $conn->query($sql);

            // Vérifier si la requête a réussi
            if ($result->num_rows > 0) {
                // Afficher chaque utilisateur dans un formulaire
                ob_start(); // Commence à mettre en mémoire tampon la sortie
                while ($row = $result->fetch_assoc()) {
                    ?>
                    <form id="form_<?php echo $row['IDUtilisateur']; ?>" class="border p-4 mb-4 flex flex-col lg:flex-row justify-between items-center">
                        <input type="hidden" name="user_id" value="<?php echo $row['IDUtilisateur']; ?>">
                        <div class="mb-4 lg:mb-0">
                            <p class="text-lg font-semibold mb-2"><?php echo $row['Nom']; ?></p>
                            <p class="text-gray-600">Rôle actuel: <?php echo $row['Role']; ?></p>
                            <label for="new_role">Nouveau Rôle:</label>
                            <select name="new_role" class="border-gray-300 border rounded-l py-2 px-4 mr-2">
                                <option value="client">Client</option>
                                <option value="standardiste">Standardiste</option>
                                <option value="intervenant">Intervenant</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <button type="button" onclick="updateRole(<?php echo $row['IDUtilisateur']; ?>)" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">Modifier le Rôle</button>
                    </form>
                    <hr>
                    <?php
                }
                $users_html = ob_get_clean(); // Récupère le contenu du tampon et l'efface
                echo $users_html; // Affiche la liste des utilisateurs
            } else {
                echo "Aucun utilisateur trouvé.";
            }

            // Fermer la connexion
            $conn->close();
            ?>
        </div>
    </div>

    <script>
        // Fonction pour mettre à jour le rôle avec jQuery
        function updateRole(userId) {
            var newRole = $("#form_" + userId + " select[name='new_role']").val();

            console.log("Nouveau rôle sélectionné : ", newRole); // Ajout de console.log pour déboguer

            $.ajax({
                type: "POST",
                url: "update_role.php",
                data: { user_id: userId, new_role: newRole },
                dataType: "json",
                success: function(response) {
                    console.log("Réponse AJAX réussie : ", response); // Ajout de console.log pour déboguer
                    if (response.success) {
                        alert(response.message);
                        location.reload();
                    } else {
                        alert("Erreur : " + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Erreur AJAX :", status, error);
                }
            });
        }

        // Fonction pour trier les utilisateurs par nom ou par rôle
        function sortUsers() {
            var sortBy = $("#sort_by").val();

            console.log("Option de tri sélectionnée : ", sortBy); // Ajout de console.log pour déboguer

            $.ajax({
                type: "POST",
                url: "admin_manage_roles.php",
                data: { sort_by: sortBy },
                success: function(response) {
                    console.log("Réponse AJAX réussie : ", response); // Ajout de console.log pour déboguer
                    $("#users_list").html(response);
                },
                error: function(xhr, status, error) {
                    console.error("Erreur AJAX :", status, error);
                }
            });
        }

        // Fonction pour rechercher un utilisateur
        function searchUser() {
            var searchTerm = $("#search_user").val();

            console.log("Terme de recherche : ", searchTerm); // Ajout de console.log pour déboguer

            $.ajax({
                type: "POST",
                url: "admin_manage_roles.php",
                data: { search_user: searchTerm },
                success: function(response) {
                    console.log("Réponse AJAX réussie : ", response); // Ajout de console.log pour déboguer
                    $("#users_list").html(response);
                },
                error: function(xhr, status, error) {
                    console.error("Erreur AJAX :", status, error);
                }
            });
        }

        // Vérifie si la fonction de tri des utilisateurs est déjà liée à l'événement de clic
        if (!$._data($("#sort_by")[0], 'events')) {
            $("#sort_by").on("change", function() {
                sortUsers();
            });
        }

        // Vérifie si la fonction de recherche des utilisateurs est déjà liée à l'événement de clic
        if (!$._data($("#search_user")[0], 'events')) {
            $("#search_user").on("keyup", function() {
                searchUser();
            });
        }
    </script>
</body>
</html>
