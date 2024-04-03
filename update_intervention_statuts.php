<?php
// Connexion à la base de données (à remplacer avec vos propres informations de connexion)
require_once 'config.php';

// Vérifier si les clés intervention_id et new_status existent dans le tableau $_POST
if(isset($_POST['intervention_id']) && isset($_POST['new_status'])) {
    $interventionId = $_POST['intervention_id'];
    $newStatus = $_POST['new_status'];

    // Création d'une connexion à la base de données
    $conn = new mysqli($serveur, $utilisateur, $motDePasse, $baseDeDonnees);

    // Vérification de la connexion
    if ($conn->connect_error) {
        die("La connexion à la base de données a échoué : " . $conn->connect_error);
    }

    // Préparer et exécuter la requête SQL pour mettre à jour le statut de l'intervention
    $sql = "UPDATE interventions SET statut = ? WHERE id_intervention = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $newStatus, $interventionId);

    if ($stmt->execute()) {
        // Succès de la mise à jour du statut
        echo json_encode(array("success" => true));
    } else {
        // Erreur lors de la mise à jour du statut
        echo json_encode(array("success" => false, "message" => "Erreur lors de la mise à jour du statut de l'intervention : " . $conn->error));
    }

    // Fermer la connexion à la base de données
    $stmt->close();
    $conn->close();
} else {
    // Clés manquantes dans le tableau $_POST
    echo json_encode(array("success" => false, "message" => "Clés manquantes dans la requête POST."));
}


?>
