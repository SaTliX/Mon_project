<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des interventions</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.16/dist/tailwind.min.css" rel="stylesheet">
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
</head>
<body class="bg-gray-100 p-8">
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
                        <p class="text-gray-800 text-lg font-semibold mb-4">Intervention #<?php echo $row['id_intervention']; ?></p>
                        <p class="text-gray-600"><strong>Date :</strong> <?php echo $row['date_intervention']; ?></p>
                        <p class="text-gray-600"><strong>Heure :</strong> <?php echo $row['heure']; ?></p>
                        <p class="text-gray-600"><strong>Titre :</strong> <?php echo $row['titre']; ?></p>
                        <p class="text-gray-600"><strong>Statut :</strong> <?php echo $row['statut']; ?></p>
                        <p class="text-gray-600"><strong>Degré d'urgence :</strong> <?php echo $row['degre_urgence']; ?></p>
                        <p class="text-gray-600"><strong>Commentaire :</strong> <?php echo $row['commentaire']; ?></p>
                        <p class="text-gray-600"><strong>Client :</strong> <?php echo $row['id_utilisateur_client']; ?></p>
                        <p class="text-gray-600"><strong>Standardiste :</strong> <?php echo $row['id_utilisateur_standardiste']; ?></p>
                        <p class="text-gray-600"><strong>Intervenants :</strong> <?php echo $row['intervenants_ids']; ?></p>
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
        </div>
    </div>
</body>
</html>
