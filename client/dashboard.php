<?php
require_once __DIR__ . '/../Includes/page_bootstrap.php';
require_role('client');
$username = $currentUserEmail ?: 'Client';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon espace - Vite & Gourmand</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="client-page" <?= page_data_attrs() ?>>
<header>
    <nav class="navbar">
        <a href="../public/index.php" class="logo">Vite & Gourmand</a>
        <ul class="nav-links">
            <li><a href="../client/dashboard.php" class="active">Mon espace</a></li>
            <li><a href="../public/menus.php">Menus</a></li>
            <li><a href="../autentification/logout.php" class="btn btn-logout">Déconnexion</a></li>
        </ul>
    </nav>
</header>
<main class="client-main">
    <section class="section">
        <div class="container">
            <h1>Bonjour, <?= htmlspecialchars($username) ?></h1>
            <p>Voici vos commandes récentes.</p>
            <div id="client-dashboard-root">Chargement de vos commandes...</div>
        </div>
    </section>
</main>
<footer>
    <p>© <?= date('Y') ?> Vite & Gourmand</p>
</footer>
<script src="../js/client-dashboard.js" defer></script>
</body>
</html>
