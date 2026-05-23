<?php
$page = $page ?? $_GET['page'] ?? 'home';
$validPages = ['home', 'about', 'menus', 'menus-mongo', 'menus-combined', 'warframes', 'legal', 'terms'];
if (!in_array($page, $validPages, true)) {
    $page = 'home';
}
$bodyClassMap = [
    'home' => 'index-page',
    'about' => 'about-page',
    'menus' => 'menus-page',
    'menus-mongo' => 'menus-page',
    'menus-combined' => 'menus-page',
    'warframes' => 'warframes-page',
    'legal' => 'legal-page',
    'terms' => 'legal-page',
];
$bodyClass = $bodyClassMap[$page] ?? 'page-shell';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title id="page-title">Vite &amp; Gourmand</title>
    <meta name="description" id="page-description" content="Traiteur familial à Bordeaux.">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/old/page-d'accueil.css">
    <link rel="stylesheet" href="../css/old/about.css">
    <link rel="stylesheet" href="../css/old/menus.css">
    <link rel="stylesheet" href="../css/old/legal.css">
</head>
<?php require_once __DIR__ . '/../Includes/page_bootstrap.php'; ?>
<body class="<?= htmlspecialchars($bodyClass) ?>" <?= page_data_attrs() ?> data-page="<?= htmlspecialchars($page) ?>">
<header class="header sticky-top">
    <nav class="navbar">
        <div class="logo"><a href="index.php">Vite &amp; Gourmand</a></div>
        <ul class="nav-links" id="navMenu">
            <li><a href="index.php" data-page-link="home">Accueil</a></li>
            <li><a href="about.php" data-page-link="about">À propos</a></li>
            <li><a href="menus.php" data-page-link="menus">Menus SQL</a></li>
            <li><a href="menus-mongo.php" data-page-link="menus-mongo">Menus MongoDB</a></li>
            <li><a href="menus-combined.php" data-page-link="menus-combined">Menus SQL + MongoDB</a></li>
            <li><a href="warframes.php" data-page-link="warframes">Warframes</a></li>
        </ul>
        <div class="auth-buttons" id="auth-root"></div>
        <button class="hamburger" aria-label="Ouvrir le menu" onclick="toggleMenu()">
            <i class="bi bi-list"></i>
        </button>
    </nav>
</header>

<main id="app-root" class="main-content">
    <div class="container">
        <p>Chargement...</p>
    </div>
</main>

<footer>
    <div class="container">
        <div class="footer-links">
            <a href="mentions-legales.php">Mentions légales</a>
            <a href="conditions-generales.php">Conditions générales</a>
        </div>
        <p>© <?= date('Y') ?> Vite &amp; Gourmand - Tous droits réservés</p>
    </div>
</footer>

<script>
function toggleMenu() {
    document.getElementById('navMenu').classList.toggle('active');
}

document.addEventListener('click', function(event) {
    const menu = document.getElementById('navMenu');
    const hamburger = document.querySelector('.hamburger');
    if (!menu.contains(event.target) && !hamburger.contains(event.target)) {
        menu.classList.remove('active');
    }
});
</script>
<script src="../js/app.js" defer></script>
</body>
</html>
