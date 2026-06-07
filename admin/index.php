<?php
require_once __DIR__ . '/../Includes/page_bootstrap.php';
require_role('admin');
$username = $currentUserEmail ?: 'Administrateur';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord Admin - Vite & Gourmand</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/old/admin.css">
</head>
<body class="admin-page" <?= page_data_attrs() ?>>
<header class="admin-header">
    <nav class="admin-nav">
        <a href="../public/index.php" class="logo">Vite & Gourmand - Admin</a>
        <ul class="admin-links">
            <li><a href="../admin/index.php" class="active">Dashboard</a></li>
            <li><a href="../admin/gestion-utilisateurs.php">Utilisateurs</a></li>
            <li><a href="../admin/gestion-menus.php">Menus</a></li>
            <li><a href="../admin/gestion-commandes.php">Commandes</a></li>
        </ul>
        <div class="admin-user">
            <span>Bonjour, <?= htmlspecialchars($username) ?></span>
            <a href="../authentification/logout.php" class="btn btn-logout">Déconnexion</a>
        </div>
    </nav>
</header>
<main class="admin-main">
    <h1>Bienvenue dans l'espace administrateur</h1>
    <p class="admin-welcome">Vous gérez actuellement le site en tant qu'administrateur.</p>
    <div id="admin-dashboard-root">Chargement des statistiques...</div>
    <section class="admin-actions">
        <h2>Actions rapides</h2>
        <div class="actions-grid">
            <a href="../admin/gestion-utilisateurs.php" class="action-card"><h3>Gérer les utilisateurs</h3><p>Ajouter, modifier ou supprimer des comptes</p></a>
            <a href="../admin/gestion-menus.php" class="action-card"><h3>Gérer les menus</h3><p>Ajouter, modifier ou mettre à jour les stocks</p></a>
            <a href="../admin/gestion-commandes.php" class="action-card"><h3>Gérer les commandes</h3><p>Suivre et modifier le statut des commandes</p></a>
        </div>
    </section>
</main>
<footer><p>© <?= date('Y') ?> Vite & Gourmand – Espace Admin</p></footer>
<script src="../js/admin-dashboard.js" defer></script>
</body>
</html>
