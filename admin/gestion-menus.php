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
    <title>Gestion des menus - Admin - Vite & Gourmand</title>
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
            <li><a href="../admin/gestion-menus.php" class="active">Menus</a></li>
            <li><a href="../admin/gestion-commandes.php">Commandes</a></li>
        </ul>
        <div class="admin-user">
            <span>Bonjour, <?= htmlspecialchars($_SESSION['email'] ?? 'Admin') ?></span>
            <a href="../autentification/logout.php" class="btn btn-logout">Déconnexion</a>
        </div>
    </nav>
</header>
<main class="admin-main">
    <h1>Gestion des menus</h1>
    <section class="form-section">
        <h2>Ajouter / Modifier un menu</h2>
        <div id="admin-menus-message" class="alert" style="display:none;"></div>
        <form id="admin-menu-form" class="admin-form">
            <input type="hidden" name="id" value="">
            <div class="form-row">
                <label>Titre</label>
                <input type="text" name="titre" required>
            </div>
            <div class="form-row">
                <label>Description</label>
                <textarea name="description" rows="5"></textarea>
            </div>
            <div class="form-row">
                <label>Thème</label>
                <select name="theme">
                    <option value="classique">Classique</option>
                    <option value="Noel">Noël</option>
                    <option value="Paques">Pâques</option>
                    <option value="evenement">Événement</option>
                </select>
            </div>
            <div class="form-row">
                <label>Régime</label>
                <select name="regime">
                    <option value="classique">Classique</option>
                    <option value="vegetarien">Végétarien</option>
                    <option value="vegan">Vegan</option>
                    <option value="sans_gluten">Sans gluten</option>
                </select>
            </div>
            <div class="form-row">
                <label>Personnes minimum</label>
                <input type="number" name="personnes_minimum" min="1" value="2" required>
            </div>
            <div class="form-row">
                <label>Prix (€)</label>
                <input type="number" name="prix" step="0.01" min="0" required>
            </div>
            <div class="form-row">
                <label>Stock</label>
                <input type="number" name="stock" min="0" value="10" required>
            </div>
            <button type="submit" name="submit_button" class="btn btn-primary">Ajouter</button>
        </form>
        <button id="admin-menus-sync" class="btn btn-secondary" style="margin-top:1rem;">Synchroniser tous les menus vers MongoDB</button>
    </section>
    <section>
        <h2>Liste des menus</h2>
        <div id="admin-menus-root">Chargement des menus...</div>
    </section>
</main>
<script src="../js/admin-menus.js" defer></script>
</body>
</html>
