<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des interventions</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.16/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .intervention-card {
            background-color: #FFFFFF;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12), 0 1px 2px rgba(0, 0, 0, 0.24);
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        .intervention-card:hover {
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1), 0 3px 6px rgba(0, 0, 0, 0.1);
        }
    </style>
    <div class="mb-8">
        <a href="admin_manage_roles.php" class="bg-gray-500 hover:bg-black text-white font-semibold py-2 px-4 rounded-md shadow-md">
            Retour
        </a>
    </div>

    <div class="mt-8">
    <a href="Accueil.html" class="bg-gray-500 hover:bg-black text-white font-semibold py-2 px-4 rounded-md shadow-md">
        Retour au menu
    </a>
    </div>



</head>
<body class="bg-gray-100 p-8">
    <?php
    if (isset($_SESSION['success_message'])) {
        echo '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold">Succès :</strong>
                <span class="block sm:inline">' . $_SESSION['success_message'] . '</span>
            </div>';    }
    ?>

    <div class="max-w-3xl mx-auto">
        <h1 class="text-3xl font-bold mb-8 text-center">Liste des interventions</h1>
        <div class="grid gap-8">
            <?php
            // Assurez-vous de démarrer la session PHP sur la page si nécessaire
            session_start();

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

            // Requête SQL pour récupérer les données des interventions avec les intervenants associés
            $sql = "SELECT i.id_intervention, i.id_utilisateur_client, i.date_intervention, i.heure, i.titre, i.statut, i.degre_urgence, i.commentaire, i.id_utilisateur_standardiste, GROUP_CONCAT(ii.id_utilisateur_intervenant) AS intervenants_ids
                    FROM interventions i
                    LEFT JOIN interventions_intervenants ii ON i.id_intervention = ii.id_intervention
                    GROUP BY i.id_intervention";

            // Exécution de la requête
            $result = $conn->query($sql);

            // Vérification si des résultats ont été trouvés
            if ($result->num_rows > 0) {
                // Affichage des données
                while ($row = $result->fetch_assoc()) {
                    ?>
                    <div class="intervention-card p-6">
                    <div id="success-message-<?php echo $row['id_intervention']; ?>" class="hidden bg-green-100 border border-green-400 text-green-700 px-4 py-3 mb-5 rounded relative" role="alert">
                        <strong class="font-bold">Succès :</strong>
                        <span class="block sm:inline" id="success-message-text-<?php echo $row['id_intervention']; ?>"></span>
                    </div>
                    <div id="error-message-<?php echo $row['id_intervention']; ?>" class="hidden bg-red-100 border border-red-400 text-red-700 px-4 py-3 mb-5 rounded relative" role="alert">
                        <strong class="font-bold">Erreur :</strong>
                        <span class="block sm:inline" id="error-message-text-<?php echo $row['id_intervention']; ?>"></span>
                    </div>


                        <p class="text-gray-800 text-lg font-semibold mb-4">Intervention #<?php echo $row['id_intervention']; ?></p>
                        <div class="flex flex-wrap mb-4">
                            <div class="w-full md:w-1/2 pr-2">
                                <p class="text-gray-600"><strong>Date :</strong> <?php echo $row['date_intervention']; ?></p>
                                <p class="text-gray-600"><strong>Heure :</strong> <?php echo $row['heure']; ?></p>
                                <p class="text-gray-600"><strong>Titre :</strong> <?php echo $row['titre']; ?></p>
                            </div>
                            <div class="w-full md:w-1/2 pl-2">
                                <p class="text-gray-600"><strong>Commentaire :</strong> <?php echo $row['commentaire']; ?></p>
                                <p class="text-gray-600"><strong>Client :</strong> <?php echo $row['id_utilisateur_client']; ?></p>
                                <p class="text-gray-600"><strong>Standardiste :</strong> <?php echo $row['id_utilisateur_standardiste']; ?></p>
                                <p class="text-gray-600"><strong>Intervenants :</strong> <?php echo $row['intervenants_ids']; ?></p>
                            </div>
                        </div>

                        <!-- Formulaire pour modifier le degré d'urgence et le statut -->
                        <form id="update-intervention-form-<?php echo $row['id_intervention']; ?>" action="update_intervention_admin.php" method="POST">
                            <input type="hidden" name="intervention_id" value="<?php echo $row['id_intervention']; ?>">

                            <!-- Degré d'urgence -->
                            <div class="mb-4">
                                <label for="degre_urgence" class="text-gray-600 font-semibold">Degré d'urgence :</label>
                                <select name="degre_urgence" id="degre_urgence" class="border rounded-md py-1 px-2 w-full mt-1 bg-white">
                                    <option value="Faible" <?php if ($row['degre_urgence'] === 'Faible') echo 'selected'; ?> class="text-gray-600 bg-white">Faible</option>
                                    <option value="Moyen" <?php if ($row['degre_urgence'] === 'Moyen') echo 'selected'; ?> class="text-gray-600 bg-white">Moyen</option>
                                    <option value="Eleve" <?php if ($row['degre_urgence'] === 'Eleve') echo 'selected'; ?> class="text-gray-600 bg-white">Élevé</option>
                                </select>
                            </div>

                            <!-- Statut -->
                            <div class="mb-4">
                                <label for="statut" class="text-gray-600 font-semibold">Statut :</label>
                                <select name="statut" id="statut" class="border rounded-md py-1 px-2 w-full mt-1 bg-white">
                                    <option value="En attente" <?php if ($row['statut'] === 'En attente') echo 'selected'; ?> class="text-gray-600 bg-white">En attente</option>
                                    <option value="En cours" <?php if ($row['statut'] === 'En cours') echo 'selected'; ?> class="text-gray-600 bg-white">En cours</option>
                                    <option value="Terminée" <?php if ($row['statut'] === 'Terminée') echo 'selected'; ?> class="text-gray-600 bg-white">Terminée</option>
                                    <option value="Annulée" <?php if ($row['statut'] === 'Annulée') echo 'selected'; ?> class="text-gray-600 bg-white">Annulée</option>
                                </select>
                            </div>

                            <!-- Bouton de soumission -->
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded mt-4">
                                Mettre à jour
                            </button>
                        </form>
                    </div>


                    <?php
                }
            } else {
                // Aucune intervention trouvée
                echo "<p class='text-gray-600 text-center'>Aucune intervention trouvée.</p>";
            }
            

            // Fermeture de la connexion
            $conn->close();
            ?>
            <div id="success-message" class="hidden bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
    <strong class="font-bold">Succès :</strong>
    <span class="block sm:inline" id="success-message-text"></span>
</div>
<script>
$(document).ready(function() {
    // Remplacez '#your-form-id' par l'identifiant de votre formulaire
    $('#intervention_id').on('submit', function(e) {
        e.preventDefault(); // Empêche le comportement par défaut du formulaire

        // Sérialise les données du formulaire
        var formData = $(this).serialize();

        // Envoie la requête AJAX au serveur
        $.ajax({
            type: 'POST',
            url: 'update_intervention_admin.php', // Remplacez par le chemin d'accès correct à votre fichier PHP
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Affiche le message de succès
                    $('#success-message-text').text(response.message);
                    $('#success-message').removeClass('hidden');

                    // Réinitialise le formulaire (facultatif)
                    $('#intervention_id')[0].reset();
                } else {
                    console.error('Erreur lors de la modification de l\'intervention:', response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Erreur lors de l\'envoi de la requête:', error);
            }
        });
    });
});
</script>
<script>
$(document).ready(function() {
    // Sélectionne tous les formulaires de modification d'intervention
    $('[id^="update-intervention-form-"]').on('submit', function(e) {
        e.preventDefault(); // Empêche le comportement par défaut du formulaire

        // Récupère l'ID de l'intervention à partir de l'identifiant du formulaire
        const interventionId = $(this).attr('id').replace('update-intervention-form-', '');

        // Sérialise les données du formulaire
        var formData = $(this).serialize();

        // Envoie la requête AJAX au serveur
        $.ajax({
            type: 'POST',
            url: 'update_intervention_admin.php', // Remplacez par le chemin d'accès correct à votre fichier PHP
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Affiche le message de succès correspondant à l'intervention
                    $('#success-message-text-' + interventionId).text(response.message);
                    $('#success-message-' + interventionId).removeClass('hidden');

                    // Masque le message d'erreur correspondant à l'intervention
                    $('#error-message-' + interventionId).addClass('hidden');

                    // Réinitialise le formulaire (facultatif)
                    $('#update-intervention-form-' + interventionId)[0].reset();
                } else {
                    // Affiche le message d'erreur correspondant à l'intervention
                    $('#error-message-text-' + interventionId).text(response.message);
                    $('#error-message-' + interventionId).removeClass('hidden');

                    // Masque le message de succès correspondant à l'intervention
                    $('#success-message-' + interventionId).addClass('hidden');
                }
            },
            error: function(xhr, status, error) {
                console.error('Erreur lors de l\'envoi de la requête:', error);

                // Affiche un message d'erreur générique correspondant à l'intervention
                $('#error-message-text-' + interventionId).text('Erreur lors de l\'envoi de la requête.');
                $('#error-message-' + interventionId).removeClass('hidden');

                // Masque le message de succès correspondant à l'intervention
                $('#success-message-' + interventionId).addClass('hidden');
            }
        });
    });
});
</script>
        </div>
    </div>
</body>
</html>
