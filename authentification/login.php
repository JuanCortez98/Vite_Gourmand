<?php
require_once '../Includes/config.php';
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        header('Location: ../admin/index.php');
        exit;
    }
    if ($_SESSION['role'] === 'travailleur') {
        header('Location: ../travailleur/dashboard.php');
        exit;
    }
    header('Location: ../client/dashboard.php');
    exit;
}
$csrfToken = $_SESSION['csrf_token'] ?? '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Vite & Gourmand</title>
    <meta name="description" content="Connectez-vous à votre espace personnel pour commander ou gérer vos réservations.">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/old/login.css">
</head>
<body data-page="login" data-csrf-token="<?= htmlspecialchars($csrfToken) ?>">
<main class="login-main">
    <div class="login-card">
        <div class="logo-area">
            <h1>Vite & Gourmand</h1>
            <p>Connectez-vous à votre espace</p>
        </div>
        <div id="auth-message" class="auth-message"></div>
        <form data-auth-form novalidate>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" autocomplete="username" placeholder="exemple@votredomaine.com" required autofocus>
            </div>
            <div class="form-group password-group">
                <label for="password">Mot de passe</label>
                <div class="password-wrapper">
                    <input type="password" id="password" name="password" autocomplete="current-password" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Se connecter</button>
        </form>
        <div class="signup-link">
            Vous n'avez pas de compte ? <a href="register.php">S'inscrire</a>
        </div>
    </div>
</main>
<script src="../js/auth.js" defer></script>
</body>
</html>
