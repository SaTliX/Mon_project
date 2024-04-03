<?php
// Assurez-vous d'avoir la session démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vérifiez si les données du formulaire sont présentes
if (isset($_POST['id_intervention'], $_POST['commentaire'])) {
    // code pour ajouter le commentaire à la base de données

    // Récupérez l'ID du client depuis la session
    $standardiste_id = $_SESSION['user_id'];

    // Récupérez l'ID de l'intervention depuis le formulaire
    $intervention_id = $_POST['id_intervention'];

    // Récupérez le commentaire depuis le formulaire
    $commentaire = $_POST['commentaire'];

    // Connexion à la base de données
    $conn = new mysqli("127.0.0.1", "root", "", "accords energie");

    // Vérification de la connexion
    if ($conn->connect_error) {
        die("La connexion à la base de données a échoué : " . $conn->connect_error);
    }

        // Mettez à jour le commentaire de l'intervention
        $updateSql = "UPDATE interventions SET commentaire = ? WHERE id_intervention = ?";
        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->bind_param("si", $commentaire, $intervention_id);
        $updateStmt->execute();

    if ($updateStmt->affected_rows > 0) {
        // Retournez une réponse JSON indiquant que le commentaire a été ajouté avec succès
        $response = array('success' => true, 'message' => 'Le commentaire a été ajouté avec succès.');
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    } else {
        // Retournez une réponse JSON indiquant une erreur lors de l'ajout du commentaire
        $response = array('success' => false, 'message' => 'Une erreur s\'est produite lors de l\'ajout du commentaire : ' . $conn->error);
        echo json_encode($response);
    }

    // Fermez les statements et la connexion à la base de données
    $updateStmt->close();
    $conn->close();
} else {
    // Retournez une réponse JSON indiquant que les données du formulaire sont manquantes
    $response = array('success' => false, 'message' => 'Les données du formulaire sont manquantes.');
    echo json_encode($response);
}
?>
