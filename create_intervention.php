    <?php
    // Assurez-vous de démarrer la session PHP sur la page
    session_start();

    // Vérifiez si l'utilisateur est connecté en tant que standardiste
    if(isset($_SESSION['user_id']) && $_SESSION['user_type'] == 'standardiste') {
        // L'utilisateur est connecté en tant que standardiste, récupérez son ID
        $idStandardiste = $_SESSION['user_id'];

        // Assurez-vous de configurer correctement votre connexion à la base de données ici
        require_once 'config.php';

        // Récupérer les données du formulaire
        $idClient = $_POST['client']; // Supposons que 'client' contient l'ID du client sélectionné dans le formulaire
        $titre = $_POST['title'];
        $dateIntervention = $_POST['date'];
        $heureIntervention = $_POST['time']; // Nouvelle variable pour l'heure
        $statut = "En attente"; // Valeur par défaut
        $degreUrgence = $_POST['urgency'];

        // Récupérer les intervenants sélectionnés
        $intervenants = isset($_POST['intervenants']) ? $_POST['intervenants'] : [];

        // Préparer la requête d'insertion
        $sql = "INSERT INTO interventions (id_utilisateur_client, titre, date_intervention, heure, statut, degre_urgence, id_utilisateur_standardiste) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";

        // Préparer la déclaration
        $stmt = $conn->prepare($sql);

        // Liage des paramètres
        $stmt->bind_param("isssssi", $idClient, $titre, $dateIntervention, $heureIntervention, $statut, $degreUrgence, $idStandardiste);

        if ($stmt->execute()) {
            // Récupérer l'ID de l'intervention insérée
            $idIntervention = $stmt->insert_id;
        
            // Fermer la déclaration
            $stmt->close();
        
            // Préparer une nouvelle requête pour insérer les intervenants liés à cette intervention
            $sqlInsertIntervenant = "INSERT INTO interventions_intervenants (id_intervention, id_utilisateur_intervenant) VALUES (?, ?)";
            $stmtInsertIntervenant = $conn->prepare($sqlInsertIntervenant);
        
            // Liage des paramètres pour la nouvelle requête
            $stmtInsertIntervenant->bind_param("ii", $idIntervention, $idIntervenant);
        
            // Exécuter la nouvelle requête pour chaque intervenant
            foreach ($intervenants as $idIntervenant) {
                if ($stmtInsertIntervenant->execute()) {
                    $response = array('success' => true, 'message' => 'Nouvelle intervention ajoutée avec succès.');
                } else {
                    $response = array('success' => false, 'message' => 'Erreur lors de l\'ajout de l\'intervention : ' . $conn->error);
                }
            }
        
            // Fermer la déclaration
            $stmtInsertIntervenant->close();
        
            // Renvoie la réponse JSON
            header('Content-Type: application/json');
            echo json_encode($response);
        } else {
            // Renvoie une réponse JSON avec un message d'erreur
            $response = array('success' => false, 'message' => 'Erreur lors de l\'ajout de l\'intervention : ' . $conn->error);
            header('Content-Type: application/json');
            echo json_encode($response);
        }
        
        // Fermer la connexion
        $conn->close();
    } else {
        // Redirigez l'utilisateur vers une page appropriée s'il n'est pas connecté en tant que standardiste
        header("Location: login.php");
        exit;
    }
    ?>
