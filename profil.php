    <?php
    session_start();

    // Vérifier si l'utilisateur est connecté
    if (!isset($_SESSION["user_id"])) {
        header("Location: login.php");
        exit();
    }

    // Inclure votre code HTML pour la page de profil
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Profil</title>
    </head>
    <body>
        <h2>Profil de l'utilisateur</h2>
        <p>Bienvenue, <?= $_SESSION["user_id"] ?>!</p>
        <!-- Affichez d'autres informations spécifiques à l'utilisateur ici -->
        <a href="logout.php">Se déconnecter</a>
    </body>
    </html>
