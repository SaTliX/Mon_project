<?php
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

// Récupérer la liste des intervenants
$sql = "SELECT IDUtilisateur, Nom FROM utilisateurs WHERE Role = 'intervenant'";
$result = $conn->query($sql);

// Vérifier si la requête a réussi
if ($result->num_rows > 0) {
    // Créer un tableau associatif pour stocker les données des intervenants
    $intervenants = [];
    while ($row = $result->fetch_assoc()) {
        $intervenants[] = $row;
    }
    
    // Renvoyer les données des intervenants au format JSON
    echo json_encode($intervenants);
} else {
    // Si aucun intervenant n'est trouvé, renvoyer un message d'erreur
    echo json_encode(["error" => "Aucun intervenant trouvé."]);
}

// Fermer la connexion
$conn->close();
?>
