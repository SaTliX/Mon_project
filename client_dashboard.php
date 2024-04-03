<!DOCTYPE html>
<html>
<head>
    <title>Mes Interventions</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</head>
<body class="bg-gray-100">
    <div class="flex items-center justify-between px-6 py-4">
    <a href="Accueil.html" class="bg-gray-500 hover:bg-black text-white font-semibold py-2 px-4 rounded-md shadow-md">
        Retour au menu
    </a>
    <a href="ancienne_intervention.php" class="bg-gray-500 hover:bg-black text-white font-semibold py-2 px-4 rounded-md shadow-md">
        Anciennes interventions
    </a>
    </div>


    <div class="container mx-auto max-w-3xl py-8">
        <?php
        error_reporting(E_ALL);
        ini_set('display_errors', 1);

        // Assurez-vous d'avoir la session démarrée
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Vérifiez si l'utilisateur est connecté en tant que client
        if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'client') {
            // Récupérez l'ID du client depuis la session
            $client_id = $_SESSION['user_id'];

            // Connexion à la base de données
            $conn = new mysqli("127.0.0.1", "root", "", "accords energie");

            // Vérification de la connexion
            if ($conn->connect_error) {
                die("La connexion à la base de données a échoué : " . $conn->connect_error);
            }

            // Récupérer les interventions du client à partir de la table interventions
            $sql = "SELECT * FROM interventions WHERE id_utilisateur_client = $client_id AND statut != 'Terminée'";
            $result = $conn->query($sql);

            // Stocker les interventions dans un tableau
            $interventions = [];
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $interventions[] = $row;
                    echo "<div class='bg-white rounded-md shadow-md p-4 mb-4'>";
                    echo "<h3 class='text-lg font-semibold mb-2'>Intervention #" . $row['id_intervention'] . "</h3>";
                    echo "<p class='mb-2'><strong>Date d'intervention:</strong> " . $row['date_intervention'] . "</p>";
                    echo "<p class='mb-2'><strong>Statut:</strong> " . $row['statut'] . "</p>";
                    echo "<p class='mb-2'><strong>Degré d'urgence:</strong> " . $row['degre_urgence'] . "</p>";
                    echo "<p class='mb-2'><strong>Commentaire:</strong> " . $row['commentaire'] . "</p>";
                    echo "</div>";
                }
            } else {
                echo "<p class='text-red-500'>Aucune intervention trouvée.</p>";
            }

            // Formulaire pour ajouter un commentaire à une intervention existante
            echo "<form id=\"commentForm\" class='bg-white rounded-md shadow-md p-4 mt-8'>";
            echo "<h2 class='text-lg font-semibold mb-4'>Ajouter un commentaire</h2>";
            echo "<div class='mb-4'>";
            echo "<label for='intervention' class='block mb-1'>Sélectionnez l'intervention :</label>";
            echo "<select id='intervention_id' name='intervention_id' class='w-full border rounded-md px-3 py-2' onchange='showCommentField()'>";
            echo "<option value='' selected disabled>Sélectionnez une intervention</option>"; // Option vide avec un libellé par défaut
            // Affichage des options
            if (!empty($interventions)) {
                foreach ($interventions as $intervention) {
                    echo "<option value='" . $intervention['id_intervention'] . "'>Intervention #" . $intervention['id_intervention'] . "</option>";
                }
            } else {
                echo "<option value='' disabled>Aucune intervention trouvée</option>";
            }
            echo "</select>";
            echo "</div>";
            echo "<div id='commentField' class='comment-field' style='display: none;'>";
            echo "<label for='commentaire' class='block mb-1'>Ajouter un commentaire :</label>";
            echo "<textarea id='new_comment' name='new_comment' class='w-full border rounded-md px-3 py-2' rows='4'></textarea>";
            echo "</div>";
            echo "<button type='submit' class='bg-blue-500 text-white px-4 py-2 rounded-md mt-4 hover:bg-blue-600 transition duration-300 ease-in-out'>Ajouter</button>";

        } else {
            // Redirigez l'utilisateur non autorisé vers une autre page
            header("Location: login.php");
            exit();
        }
        ?>
    </div>

    <script>
function showCommentField() {
    var interventionSelect = document.getElementById("intervention_id");
    var commentField = document.getElementById("commentField");

    if (interventionSelect) { // Check if interventionSelect is not null
        var selectedValue = interventionSelect.value;

        if (selectedValue.trim() !== "") {
            commentField.style.display = "block";
        } else {
            commentField.style.display = "none";
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Créer un conteneur pour le message d'alerte
    var alertContainer = document.createElement('div');
    alertContainer.setAttribute('id', 'alertContainer');

    // Ajouter le conteneur au DOM juste avant le formulaire
    var form = document.getElementById('commentForm');
    form.parentNode.insertBefore(alertContainer, form);

    // Écouter l'événement de soumission du formulaire
    document.getElementById('commentForm').addEventListener('submit', function(e) {
        e.preventDefault(); // Empêcher le comportement de soumission par défaut

        // Récupérer les données du formulaire
        var formData = new FormData(this);

        // Effectuer une requête AJAX
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'update_commentaire.php');
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.onload = function() {
            if (xhr.status === 200) {
                var response = JSON.parse(xhr.responseText);
                if (response.success) {
                    // Afficher le message de succès
                    var alertBox = document.createElement('div');
                    alertBox.classList.add('alert', 'alert-success', 'alert-dismissible', 'fade', 'show');
                    alertBox.innerHTML = '<strong>Succès !</strong> ' + response.message + ' <button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
                    alertContainer.innerHTML = ''; // Effacer les messages précédents
                    alertContainer.appendChild(alertBox);
                } else {
                    // Afficher le message d'erreur
                    var alertBox = document.createElement('div');
                    alertBox.classList.add('alert', 'alert-danger', 'alert-dismissible', 'fade', 'show');
                    alertBox.innerHTML = '<strong>Erreur !</strong> ' + response.message + ' <button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
                    alertContainer.innerHTML = ''; // Effacer les messages précédents
                    alertContainer.appendChild(alertBox);
                }
            }
        };
        xhr.send(formData);
    });
});

    </script>
</body>
</html>
