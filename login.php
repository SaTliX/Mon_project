<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Définir une variable pour stocker le message d'erreur
$error_message = "";

// Vérifier si la clé "REQUEST_METHOD" est définie dans le tableau $_SERVER
if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer les données du formulaire
    $email = $_POST["email"];
    $password = $_POST["password"];

    // Informations de connexion à la base de données
    $serveur = "127.0.0.1";  // Votre serveur de base de données
    $utilisateur = "root";  // Votre nom d'utilisateur MySQL
    $motDePasse = "";  // Votre mot de passe MySQL
    $baseDeDonnees = "accords energie";  // Le nom de votre base de données

    // Création d'une connexion à la base de données
    $conn = mysqli_connect($serveur, $utilisateur, $motDePasse, $baseDeDonnees);

    // Vérification de la connexion
    if (!$conn) {
        die("La connexion à la base de données a échoué : " . mysqli_connect_error());
    }

    // Utilisez une requête préparée pour éviter les injections SQL
    $stmt = mysqli_prepare($conn, "SELECT IDUtilisateur, MotDePasse, Role FROM utilisateurs WHERE Email = ?");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);

    // Gestion des erreurs
    if (mysqli_stmt_error($stmt)) {
        die("Erreur de requête : " . mysqli_stmt_error($stmt));
    }

    // Récupérez les résultats
    mysqli_stmt_store_result($stmt);

    // Vérifiez s'il y a une correspondance
    if (mysqli_stmt_num_rows($stmt) > 0) {
        // Récupérez le mot de passe haché depuis la base de données
        mysqli_stmt_bind_result($stmt, $userId, $hashedPassword, $userRole);
        mysqli_stmt_fetch($stmt);

        // Vérifiez si le mot de passe est correct en utilisant password_verify
        if (password_verify($password, $hashedPassword)) {
            // L'utilisateur est authentifié avec succès
            // Stockez des informations sur l'utilisateur dans la session
            $_SESSION["user_id"] = $userId;
            $_SESSION["user_type"] = $userRole;

            // Redirigez l'utilisateur en fonction de son rôle
            switch ($userRole) {
                case "client":
                    header("Location: client_dashboard.php");
                    exit();
                case "admin":
                    header("Location: admin_manage_roles.php");
                    exit();
                case "standardiste":
                    header("Location: standardiste.html");
                    exit();
                case "intervenant":
                    header("Location: intervenants.php");
                    exit();
                default:
                    header("Location: dashboard.php");
                    exit();
            }
        } else {
            // Mot de passe incorrect
            $error_message = "Mot de passe incorrect. Veuillez réessayer.";
        }
    } else {
        // Utilisateur non trouvé
        $error_message = "Aucun utilisateur trouvé avec cet e-mail.";
    }

    // Fermez la connexion lorsque vous avez terminé
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <a href="accueil.html" class="fixed top-0 left-0 z-50 bg-white p-4 border border-gray-400 rounded-lg text-black m-4">Retour au Menu</a>
    <div class="container mx-auto max-w-md py-20">
        <h2 class="text-2xl font-semibold mb-6 text-center">Connexion</h2>
        <?php if ($error_message): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold">Erreur !</strong>
                <span class="block sm:inline"><?php echo $error_message; ?></span>
            </div>
        <?php endif; ?>
        <form action="login.php" method="post" class="space-y-4">
            <div>
                <label for="email" class="block mb-1">E-mail :</label>
                <input type="email" id="email" name="email" required class="w-full border rounded-md px-3 py-2">
            </div>
            <div>
                <label for="password" class="block mb-1">Mot de passe :</label>
                <input type="password" id="password" name="password" required class="w-full border rounded-md px-3 py-2">
            </div>
            <button type="submit" class="w-full bg-blue-500 text-white py-2 rounded-md hover:bg-blue-600 transition duration-300 ease-in-out">Se Connecter</button>
        </form>
        <p class="text-center mt-4">Vous n'avez pas de compte ? <a href="register.html" class="text-blue-500 hover:underline">S'inscrire ici</a></p>
    </div>
</body>
</html>
