<?php
// register.php
session_start();

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer les données du formulaire
    $name = $_POST["name"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    
    // Récupérer le rôle à partir du formulaire (si défini)
    $role = isset($_POST["Role"]) ? $_POST["ole"] : "client";

    // Informations de connexion à la base de données
    $server = "127.0.0.1";  // Votre serveur de base de données
    $user = "root";  // Votre nom d'utilisateur MySQL
    $password_db = "";  // Votre mot de passe MySQL
    $database = "accords energie";  // Le nom de votre base de données

    // Création d'une connexion à la base de données
    $conn = new mysqli($server, $user, $password_db, $database);

    // Vérification de la connexion
    if ($conn->connect_error) {
        die("La connexion à la base de données a échoué : " . $conn->connect_error);
    }

    // Hasher le mot de passe
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);


    // Préparer et exécuter la requête SQL
    $stmt = $conn->prepare("INSERT INTO utilisateurs (Nom, Email, MotDePasse, Role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $hashedPassword, $role);

    if ($stmt->execute()) {
        echo "Inscription réussie !";
    } else {
        echo "Erreur d'inscription : " . $stmt->error;
    }

    // Fermer la connexion
    $stmt->close();
    $conn->close();


}
?>
