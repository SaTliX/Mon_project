<?php
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

    // Récupérer les interventions terminées du client à partir de la table interventions
    $sql = "SELECT * FROM interventions WHERE id_utilisateur_client = $client_id AND statut = 'Terminée'";
    $result = $conn->query($sql);

    // Stocker les interventions dans un tableau
    $interventions = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $interventions[] = $row;
        }
    } else {
        $interventions = [];
    }
} else {
    // Redirigez l'utilisateur non autorisé vers une autre page
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Anciennes Interventions</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="bg-gray-100">
<div class="flex flex-col items-start space-y-4 px-6 py-4">
    <a href="Accueil.html" class="bg-gray-500 hover:bg-black text-white font-semibold py-2 px-4 rounded-md shadow-md">
        Retour au menu
    </a>
    <a href="client_dashboard.php" class="bg-gray-500 hover:bg-black text-white font-semibold py-2 px-4 rounded-md shadow-md">
        Retour
    </a>
</div>


    <div class="container mx-auto max-w-3xl py-8">
        <?php if (!empty($interventions)) : ?>
            <?php foreach ($interventions as $intervention) : ?>
                <div class="bg-white rounded-md shadow-md p-4 mb-4">
                    <h3 class="text-lg font-semibold mb-2">Intervention #<?= htmlspecialchars($intervention['id_intervention']) ?></h3>
                    <p class="mb-2"><strong>Date d'intervention:</strong> <?= htmlspecialchars($intervention['date_intervention']) ?></p>
                    <p class="mb-2"><strong>Statut:</strong> <?= htmlspecialchars($intervention['statut']) ?></p>
                    <p class="mb-2"><strong>Degré d'urgence:</strong> <?= htmlspecialchars($intervention['degre_urgence']) ?></p>
                    <p class="mb-2"><strong>Commentaire:</strong> <?= htmlspecialchars($intervention['commentaire']) ?></p>
                </div>
            <?php endforeach; ?>
        <?php else : ?>
            <div class="alert alert-danger" role="alert">
  Aucune intervention trouvée.
</div>
        <?php endif; ?>
    </div>
</body>
</html>
