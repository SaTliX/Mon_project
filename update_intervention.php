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
$titre = isset($_POST['titre']) ? $_POST['titre'] : null;
$date_intervention = isset($_POST['date_intervention']) ? $_POST['date_intervention'] : null;
$degre_urgence = isset($_POST['degre_urgence']) ? $_POST['degre_urgence'] : null;
$heure = isset($_POST['heure']) ? $_POST['heure'] : null;
$id_utilisateur_client = isset($_POST['client']) ? $_POST['client'] : null;
$statut = isset($_POST['statut_suivi']) ? $_POST['statut_suivi'] : null;

// Vérifier si les données du formulaire sont valides
if (!$intervention_id || !$titre || !$date_intervention || !$degre_urgence || !$heure || !$id_utilisateur_client || !$statut) {
    $response = array(
        'success' => false,
        'message' => 'Données invalides.'
    );
    echo json_encode($response);
    exit();
}

// Mettre à jour les données de l'intervention dans la base de données
$query = "UPDATE interventions SET titre = ?, date_intervention = ?, degre_urgence = ?, heure = ?, id_utilisateur_client = ?, statut = ? WHERE id_intervention = ?";
$stmt = $conn->prepare($query);

// Lier les paramètres
$stmt->bind_param("ssssssi", $titre, $date_intervention, $degre_urgence, $heure, $id_utilisateur_client, $statut, $intervention_id);

// Exécuter la requête
if (!$stmt->execute()) {
    $response = array(
        'success' => false,
        'message' => 'Erreur lors de la modification de l\'intervention : ' . $conn->error
    );
    echo json_encode($response);
    exit();
}

// Mettre à jour les id_intervenants dans la table interventions_intervenants
$query = "DELETE FROM interventions_intervenants WHERE id_intervention = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $intervention_id);
$stmt->execute();

// Récupérer les nouveaux id_intervenants
$intervenants = isset($_POST['intervenants']) ? $_POST['intervenants'] : array();

// Ajouter les nouveaux id_intervenants à la table interventions_intervenants
foreach ($intervenants as $id_intervenant) {
    $query = "INSERT INTO interventions_intervenants (id_intervention, id_utilisateur_intervenant) VALUES (?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $intervention_id, $id_intervenant);
    $stmt->execute();
}

// Vérifier si la requête a réussi
if ($stmt->affected_rows > 0) {
    $response = array(
        'success' => true,
        'message' => 'Intervention modifiée avec succès.'
    );
} else {
    $response = array(
        'success' => false,
        'message' => 'Erreur lors de la modification de l\'intervention : ' . $stmt->error
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
