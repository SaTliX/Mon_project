<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Liste des clients</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.16/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
  <div class="container mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-8">
      <h1 class="text-4xl font-bold text-gray-800">Liste des clients</h1>
    </div>
    <div class="overflow-x-auto rounded-lg shadow-lg">
      <table class="w-full table-auto bg-white text-left divide-y divide-gray-200">
        <thead class="bg-gray-200">
          <tr>
            <th class="px-6 py-3 text-xs font-semibold tracking-wider uppercase text-gray-600">ID</th>
            <th class="px-6 py-3 text-xs font-semibold tracking-wider uppercase text-gray-600">Nom</th>
            <th class="px-6 py-3 text-xs font-semibold tracking-wider uppercase text-gray-600">Email</th>
            <!-- Ajoutez d'autres colonnes si nécessaire -->
          </tr>
        </thead>
        <tbody>
          <?php
          // Établir une connexion à la base de données (à adapter avec vos informations de connexion)
          $serveur = "127.0.0.1";
          $utilisateur = "root";
          $motDePasse = "";
          $baseDeDonnees = "accords energie";
          $conn = new mysqli($serveur, $utilisateur, $motDePasse, $baseDeDonnees);

          // Vérifier la connexion
          if ($conn->connect_error) {
            die("La connexion à la base de données a échoué : " . $conn->connect_error);
          }

          // Exécuter une requête SQL pour récupérer les clients
          $sql = "SELECT * FROM utilisateurs WHERE role = 'client'";
          $resultat = $conn->query($sql);

          // Vérifier si des résultats ont été trouvés
          if ($resultat->num_rows > 0) {
            // Afficher les données des clients
            while ($row = $resultat->fetch_assoc()) {
              echo "<tr class='hover:bg-gray-100'>";
              echo "<td class='px-6 py-4 whitespace-nowrap text-sm font-medium'>" . $row["IDUtilisateur"] . "</td>";
              echo "<td class='px-6 py-4 whitespace-nowrap text-sm font-medium'>" . $row["Nom"] . "</td>";
              echo "<td class='px-6 py-4 whitespace-nowrap text-sm font-medium'>" . $row["Email"] . "</td>";
              // Ajoutez d'autres colonnes si nécessaire
              echo "</tr>";
            }
          } else {
            echo "<tr><td colspan='3' class='px-6 py-4 text-sm text-gray-500'>Aucun client trouvé.</td></tr>";
          }
          // Fermer la connexion
          $conn->close();
          ?>
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>
