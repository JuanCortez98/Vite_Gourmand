<?php
require_once __DIR__ . '/../Includes/page_bootstrap.php';
require_once __DIR__ . '/../Includes/mongo.php';
require_role('admin');

// CSRF token provided by page_bootstrap
$csrf = $csrfToken;

$message = '';
$type = 'success';

// Actions: sync one
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $message = 'Erreur de sécurité CSRF.';
        $type = 'danger';
    } else {
        if (isset($_POST['action']) && $_POST['action'] === 'sync_one' && isset($_POST['id'])) {
            $id = (int)$_POST['id'];
            $stmt = $pdo->prepare('SELECT * FROM menus WHERE id = :id');
            $stmt->execute([':id' => $id]);
            $menu = $stmt->fetch();
            if ($menu) {
                try {
                    $doc = [
                        'sql_id' => (int)$menu['id'],
                        'titre' => $menu['titre'],
                        'description' => $menu['description'],
                        'theme' => $menu['theme'],
                        'regime' => $menu['regime'],
                        'personnes_minimum' => (int)$menu['personnes_minimum'],
                        'prix' => (float)$menu['prix'],
                        'stock' => (int)$menu['stock'],
                        'updated_at' => new MongoDB\BSON\UTCDateTime()
                    ];

                    $updated = mongoUpdateOne('menus', ['sql_id' => $id], $doc);
                    if (! $updated) mongoInsertOne('menus', $doc);

                    $message = 'Menu synchronisé avec MongoDB.';
                    $type = 'success';
                } catch (Throwable $e) {
                    $message = 'Erreur MongoDB: ' . $e->getMessage();
                    $type = 'danger';
                }
            } else {
                $message = 'Menu introuvable en SQL.';
                $type = 'danger';
            }
        }
    }
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Statut sync Menus - Admin</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/old/admin.css">
    <?php require_once __DIR__ . '/../Includes/page_bootstrap.php'; ?>
</head>
<body <?= page_data_attrs() ?>>
<header>
    <nav class="admin-nav">
        <a href="../public/index.php" class="logo">Vite & Gourmand - Admin</a>
    </nav>
</header>
<main class="admin-main">
    <h1>Statut de synchronisation SQL ⇄ MongoDB</h1>
    <p>Actions: <a href="gestion-menus.php">Retour gestion menus</a></p>
    <div id="menus-sync-root">Chargement des statuts...</div>
</main>
<script src="../js/admin-menus-sync.js" defer></script>
</body>
</html>
