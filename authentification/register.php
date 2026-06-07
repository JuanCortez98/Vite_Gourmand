<?php
require_once '../Includes/config.php';
if (isset($_SESSION['user_id'])) {
    header('Location: ../public/index.php');
    exit;
}
$csrfToken = $_SESSION['csrf_token'] ?? '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - Vite & Gourmand</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/old/register.css">
</head>
<body data-page="register" data-csrf-token="<?= htmlspecialchars($csrfToken) ?>">
<header>
    <nav class="navbar">
        <div class="logo"><a href="../public/index.php">Vite & Gourmand</a></div>
        <ul class="nav-links">
            <li><a href="../public/index.php">Accueil</a></li>
            <li><a href="../public/about.php">À propos</a></li>
            <li><a href="../public/menus.php">Menus</a></li>
        </ul>
        <div class="auth-buttons"><a href="../authentification/login.php" class="btn btn-login">Se connecter</a></div>
    </nav>
</header>
<main class="register-main">
    <div class="register-container">
        <h1>Inscription</h1>
        <p>Créez votre compte pour commander ou gérer vos réservations</p>
        <div id="auth-message" class="auth-message"></div>
        <form data-auth-form>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required placeholder="votre@email.com">
            </div>
            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" required placeholder="Au moins 6 caractères">
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirmer le mot de passe</label>
                <input type="password" id="confirm_password" name="confirm_password" required placeholder="Répétez le mot de passe">
            </div>
            <button type="submit" class="btn btn-primary">S'inscrire</button>
        </form>
        <p class="login-link">Vous avez déjà un compte ? <a href="../authentification/login.php">Se connecter</a></p>
    </div>
</main>
<script src="../js/auth.js" defer></script>
</body>
</html>
