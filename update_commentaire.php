<?php
require_once 'config.php';

// Vérifier si les données ont été envoyées en méthode POST
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    $response = array(
        'success' => false,
        'message' => 'Requête invalide.'
    );
    echo json_encode($response);
    exit();
}

// Récupérer les données du formulaire
$intervention_id = isset($_POST['intervention_id']) ? $_POST['intervention_id'] : null;
$new_comment = isset($_POST['new_comment']) ? $_POST['new_comment'] : null;

// Vérifier si les données sont valides
if (!$intervention_id || !$new_comment) {
    $response = array(
        'success' => false,
        'message' => 'Données invalides.'
    );
    echo json_encode($response);
    exit();
}

// Mettre à jour le commentaire dans la base de données
$query = "UPDATE interventions SET commentaire = ? WHERE id_intervention = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("si", $new_comment, $intervention_id);

// Vérifier si la requête a été préparée correctement
if (!$stmt) {
    $response = array(
        'success' => false,
        'message' => 'Erreur lors de la préparation de la requête.'
    );
    echo json_encode($response);
    exit();
}

// Exécuter la requête
$stmt->execute();

// Vérifier si la requête a réussi
if ($stmt->affected_rows > 0) {
    $response = array(
        'success' => true,
        'message' => 'Commentaire mis à jour avec succès.'
    );
} else {
    $response = array(
        'success' => false,
        'message' => 'Erreur lors de la mise à jour du commentaire.'
    );
}

// Écrire la réponse dans le fichier de journalisation d'erreurs
error_log('Réponse du serveur : ' . print_r($response, true), 0);

// Définir l'en-tête Content-Type
header('Content-Type: application/json');

// Renvoie la réponse au format JSON
echo json_encode($response);

// Fermer la connexion à la base de données
$stmt->close();
$conn->close();
