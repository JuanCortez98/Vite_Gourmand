<?php
require_once __DIR__ . '/../Includes/page_bootstrap.php';
require_role('admin');

$csrfToken = $csrfToken ?? $_SESSION['csrf_token'];
$currentUserId = $currentUserId ?? (int)($_SESSION['user_id'] ?? 0);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des utilisateurs - Admin - Vite & Gourmand</title>
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
            <li><a href="../admin/gestion-utilisateurs.php" class="active">Utilisateurs</a></li>
            <li><a href="../admin/gestion-menus.php">Menus</a></li>
            <li><a href="../admin/gestion-commandes.php">Commandes</a></li>
        </ul>
        <div class="admin-user">
            <span>Bonjour, <?= htmlspecialchars($_SESSION['email'] ?? 'Admin') ?></span>
            <a href="../autentification/logout.php" class="btn btn-logout">Déconnexion</a>
        </div>
    </nav>
</header>
<main class="admin-main">
    <h1>Gestion des utilisateurs</h1>
    <section class="form-section">
        <h2>Ajouter un utilisateur</h2>
        <div id="admin-users-message" class="alert" style="display:none;"></div>
        <form id="admin-user-form" class="admin-form">
            <div class="form-row">
                <label>Email</label>
                <input type="email" name="email" required placeholder="exemple@domaine.com">
            </div>
            <div class="form-row">
                <label>Mot de passe</label>
                <input type="password" name="password" required placeholder="Au moins 6 caractères">
            </div>
            <div class="form-row">
                <label>Rôle</label>
                <select name="role">
                    <option value="client">Client</option>
                    <option value="travailleur">Travailleur</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Ajouter</button>
        </form>
    </section>
    <section>
        <h2>Liste des utilisateurs</h2>
        <div id="admin-users-root">Chargement des utilisateurs...</div>
    </section>
</main>
<script src="../js/admin-users.js" defer></script>
</body>
</html>
