<?php
require_once __DIR__ . '/../Includes/page_bootstrap.php';
require_role('admin');
$csrfToken = $csrfToken ?? $_SESSION['csrf_token'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des commandes - Admin - Vite & Gourmand</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/old/admin.css">
</head>
<body class="admin-page" <?= page_data_attrs() ?>>
<header>
    <nav class="admin-nav" aria-label="Navigation principale">
        <a href="../public/index.php" class="logo">Vite & Gourmand - Admin</a>
        <ul class="admin-links">
            <li><a href="../admin/index.php">Dashboard</a></li>
            <li><a href="../admin/gestion-utilisateurs.php">Utilisateurs</a></li>
            <li><a href="../admin/gestion-menus.php">Menus</a></li>
            <li><a href="../admin/gestion-commandes.php" class="active">Commandes</a></li>
        </ul>
        <div class="admin-user">
            <span>Bonjour, <?= htmlspecialchars($_SESSION['email'] ?? 'Admin') ?></span>
            <a href="../authentification/logout.php" class="btn btn-logout">Déconnexion</a>
        </div>
    </nav>
</header>
<main class="admin-main">
    <h1>Gestion des commandes</h1>
    <div id="admin-orders-message" class="alert" style="display:none;"></div>
    <section>
        <div id="admin-orders-root">Chargement des commandes...</div>
    </section>
</main>
<script src="../js/admin-orders.js" defer></script>
</body>
</html>
