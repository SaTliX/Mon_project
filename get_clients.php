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

// Créer un tableau pour stocker les clients
$clients = array();

// Vérifier si des résultats ont été trouvés
if ($resultat->num_rows > 0) {
    // Parcourir les résultats et les stocker dans le tableau
    while ($row = $resultat->fetch_assoc()) {
        $clients[] = $row;
    }
}

// Renvoyer les données au format JSON
echo json_encode($clients);

// Fermer la connexion
$conn->close();
?>
