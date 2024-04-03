<?php
$interventionId = $_GET['id'];

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

    // Exécuter une requête SQL pour récupérer l'intervention avec les informations sur les intervenants
    $sql = "SELECT i.*, i.statut, GROUP_CONCAT(ii.id_utilisateur_intervenant) AS intervenants_ids
            FROM interventions i
            LEFT JOIN interventions_intervenants ii ON i.id_intervention = ii.id_intervention
            WHERE i.id_intervention = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $interventionId);
$stmt->execute();

$resultat = $stmt->get_result();

// Vérifier si des résultats ont été trouvés
if ($resultat->num_rows > 0) {
    // Récupérer la première ligne du résultat
    $row = $resultat->fetch_assoc();

    // Renvoyer les données au format JSON
    echo json_encode($row);
} else {
    echo "Aucune intervention trouvée.";
}

// Fermer la connexion
$conn->close();
?>
