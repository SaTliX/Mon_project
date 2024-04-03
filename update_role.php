    <?php
    // Assurez-vous de configurer correctement votre connexion à la base de données ici
    require_once 'config.php';
    // Vérifier si des données ont été envoyées via POST
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Vérifier si les données nécessaires sont présentes
        if (isset($_POST["user_id"]) && isset($_POST["new_role"])) {
            // Récupérer les données du formulaire
            $userId = $_POST["user_id"];
            $newRole = $_POST["new_role"];

            // Préparer et exécuter la requête de mise à jour du rôle
                $sql = "UPDATE utilisateurs SET Role = ? WHERE IDUtilisateur = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $newRole, $userId);
            
            if ($stmt->execute()) {
                echo json_encode(array("success" => true, "message" => "Le rôle de l'utilisateur a été mis à jour avec succès."));
            } else {
                echo json_encode(array("success" => false, "message" => "Erreur lors de la mise à jour du rôle de l'utilisateur."));
            }

            // Fermer la requête préparée
            $stmt->close();
        } else {
            echo json_encode(array("success" => false, "message" => "Les données nécessaires sont manquantes."));
        }
    } else {
        echo json_encode(array("success" => false, "message" => "Méthode de requête incorrecte."));
    }

    // Fermer la connexion
    $conn->close();
    ?>
