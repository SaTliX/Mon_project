<?php
// Se connecter à la base de données
require_once 'config.php';

// Vérifier si les données du formulaire ont été envoyées
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    $response = array(
        'success' => false,
        'message' => 'Requête invalide.'
    );
    echo json_encode($response);
    exit();
}

// Récupérer les données du formulaire de modification
$intervention_id = isset($_POST['intervention_id']) ? $_POST['intervention_id'] : null;
$degre_urgence = isset($_POST['degre_urgence']) ? $_POST['degre_urgence'] : null;
$statut = isset($_POST['statut']) ? $_POST['statut'] : null;

// Écrire les données du formulaire dans le fichier de journalisation d'erreurs
error_log('Données du formulaire : ' . print_r($_POST, true), 0);

// Vérifier si les données du formulaire sont valides
if (!$intervention_id || !$degre_urgence || !$statut) {
    $response = array(
        'success' => false,
        'message' => 'Données invalides.'
    );
    echo json_encode($response);
    exit();
}

// Mettre à jour les données de l'intervention dans la base de données
$query = "UPDATE interventions SET degre_urgence = ?, statut = ? WHERE id_intervention = ?";
$stmt = $conn->prepare($query);

// Vérifier si la requête a été préparée correctement
if (!$stmt) {
    $response = array(
        'success' => false,
        'message' => 'Erreur lors de la préparation de la requête.'
    );
    echo json_encode($response);
    exit();
}

// Lier les paramètres et exécuter la requête
$stmt->bind_param("ssi", $degre_urgence, $statut, $intervention_id);
$stmt->execute();

// Vérifier si la requête a réussi
if ($stmt->affected_rows > 0) {
    $response = array(
        'success' => true,
        'message' => 'Intervention modifiée avec succès.'
    );
} else {
    $response = array(
        'success' => false,
        'message' => 'Aucune modification n\'a été effectuée.'
    );
}

// Écrire la réponse dans le fichier de journalisation d'erreurs
error_log('Réponse du serveur : ' . print_r($response, true), 0);

// Renvoie la réponse au format JSON
echo json_encode($response);

// Fermer la connexion à la base de données
$stmt->close();
$conn->close();
