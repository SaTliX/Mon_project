<?php
session_start();

// Détruire toutes les variables de session
session_unset();

// Détruire la session
session_destroy();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Déconnexion</title>
    <script>
        // Afficher une alerte pour indiquer la déconnexion
        alert('Vous êtes maintenant déconnecté.');
        // Rediriger l'utilisateur vers la page d'accueil après avoir cliqué sur OK dans l'alerte
        window.location.href = "accueil.html";
    </script>
</head>
<body>
</body>
</html>
