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

// Exécuter une requête SQL pour récupérer les interventions avec les informations sur les intervenants
$sql = "SELECT i.*, GROUP_CONCAT(ii.id_utilisateur_intervenant) AS intervenants_ids
        FROM interventions i
        LEFT JOIN interventions_intervenants ii ON i.id_intervention = ii.id_intervention
        GROUP BY i.id_intervention";

$resultat = $conn->query($sql);

// Vérifier si des résultats ont été trouvés
if ($resultat->num_rows > 0) {
    // Créer un tableau pour stocker les interventions
    $interventions = array();

    // Parcourir les résultats et les stocker dans le tableau
    while ($row = $resultat->fetch_assoc()) {
        // Ajouter chaque intervention au tableau
        $interventions[] = $row;
    }

    // Renvoyer les données au format JSON
    echo json_encode($interventions);
} else {
    echo "Aucune intervention trouvée.";
}

// Fermer la connexion
$conn->close();
?>
