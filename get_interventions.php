<?php
header('Content-Type: application/json');

// Établir une connexion à la base de données (à adapter avec vos informations de connexion)
require_once 'config.php';
session_start();

// Vérifiez que la session contient l'ID de l'intervenant connecté
if (!isset($_SESSION['user_id'])) {
    echo "Vous n'êtes pas autorisé à accéder à cette page.";
    exit();
}

$sortColumn = isset($_GET['sortColumn']) ? $_GET['sortColumn'] : '';
$sortOrder = isset($_GET['sortOrder']) ? $_GET['sortOrder'] : 'asc';

// Définir une colonne par défaut pour l'ordre de tri si $sortColumn est vide
if (empty($sortColumn)) {
    $sortColumn = 'id_intervention';
}

// Vérifier que la colonne est valide pour éviter les injections SQL
$allowed_columns = array('id_intervention', 'id_utilisateur_client', 'degre_urgence', 'statut', 'date_intervention', 'intervenants_ids');
if (!in_array($sortColumn, $allowed_columns)) {
    $sortColumn = 'id_intervention';
}

// Exécuter la requête SQL avec la colonne et l'ordre de tri spécifiés
$sql = "SELECT i.*, GROUP_CONCAT(ii.id_utilisateur_intervenant) AS intervenants_ids
        FROM interventions i
        LEFT JOIN interventions_intervenants ii ON i.id_intervention = ii.id_intervention
        WHERE FIND_IN_SET(" . $_SESSION['user_id'] . ", i.id_utilisateur_standardiste)
        GROUP BY i.id_intervention
        ORDER BY " . $sortColumn . " " . $sortOrder;

$resultat = $conn->query($sql);

// Vérifier si des résultats ont été trouvés
if ($resultat->num_rows > 0) {
    // Créer un tableau pour stocker les interventions
    $interventions = array();

    // Parcourir les résultats et les stocker dans le tableau
    while ($row = $resultat->fetch_assoc()) {
        // Vérifier si le champ intervenants est défini
        $row['intervenants'] = isset($row['intervenants']) ? $row['intervenants'] : array();
        $interventions[] = $row;
    }

    // Renvoyer les données au format JSON
    echo json_encode($interventions);
} else {
    echo "Aucune intervention trouvée.";
}

// Fermer la connexion
$conn->close();
