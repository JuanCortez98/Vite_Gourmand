<?php
require_once __DIR__ . '/../Includes/page_bootstrap.php';
require_role('travailleur');
$username = $currentUserEmail ?: 'Travailleur';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord Travailleur - Vite & Gourmand</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="worker-page" <?= page_data_attrs() ?>>
<header>
    <nav class="navbar">
        <a href="../public/index.php" class="logo">Vite & Gourmand</a>
        <ul class="nav-links">
            <li><a href="../travailleur/dashboard.php" class="active">Dashboard</a></li>
            <li><a href="../authentification/logout.php" class="btn btn-logout">Déconnexion</a></li>
        </ul>
    </nav>
</header>
<main class="worker-main">
    <section class="section">
        <div class="container">
            <h1>Bonjour, <?= htmlspecialchars($username) ?></h1>
            <p>Voici les commandes en cours à préparer.</p>
            <div id="worker-dashboard-root">Chargement des commandes...</div>
        </div>
    </section>
</main>
<footer>
    <p>© <?= date('Y') ?> Vite & Gourmand</p>
</footer>
<script src="../js/worker-dashboard.js" defer></script>
</body>
</html>
