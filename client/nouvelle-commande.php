<?php

require_once __DIR__ . '/../Includes/page_bootstrap.php';
require_role('client');

// Messages
$message = '';
$type = 'success';

// Récupérer les menus disponibles
$stmt = $pdo->prepare("SELECT id, titre, prix FROM menus WHERE stock > 0 ORDER BY titre");
$stmt->execute();
$menus = $stmt->fetchAll();

// Panier (session simple)
if (!isset($_SESSION['panier'])) {
    $_SESSION['panier'] = [];
}

// Ajouter au panier
if (isset($_POST['action']) && $_POST['action'] === 'ajouter_panier') {
    $menu_id = (int)($_POST['menu_id'] ?? 0);
    $quantite = (int)($_POST['quantite'] ?? 1);

    if ($menu_id > 0 && $quantite > 0) {
        $trouve = false;
        foreach ($_SESSION['panier'] as &$item) {
            if ($item['menu_id'] == $menu_id) {
                $item['quantite'] += $quantite;
                $trouve = true;
                break;
            }
        }
        if (!$trouve) {
            $_SESSION['panier'][] = ['menu_id' => $menu_id, 'quantite' => $quantite];
        }
        $message = 'Menu ajouté au panier !';
        $type = 'success';
    } else {
        $message = 'Sélectionnez un menu valide.';
        $type = 'danger';
    }
}

// Retirer du panier
if (isset($_POST['action']) && $_POST['action'] === 'retirer_panier') {
    $menu_id = (int)($_POST['menu_id'] ?? 0);
    foreach ($_SESSION['panier'] as $key => $item) {
        if ($item['menu_id'] == $menu_id) {
            unset($_SESSION['panier'][$key]);
            break;
        }
    }
    $message = 'Menu retiré du panier.';
    $type = 'success';
}

// Passer la commande (il s'enregistre automaticament  en BD)
if (isset($_POST['action']) && $_POST['action'] === 'passer_commande') {
    $adresse = trim($_POST['adresse'] ?? '');

    if (!empty($_SESSION['panier']) && !empty($adresse)) {
        $total = 0;
        foreach ($_SESSION['panier'] as $item) {
            $stmt = $pdo->prepare("SELECT prix FROM menus WHERE id = :id");
            $stmt->execute([':id' => $item['menu_id']]);
            $menu = $stmt->fetch();
            $total += $menu['prix'] * $item['quantite'];
        }

        $stmt = $pdo->prepare("
            INSERT INTO commandes (user_id, total, status, adresse_livraison)
            VALUES (:user_id, :total, 'en_cours', :adresse)
        ");
        $ok = $stmt->execute([
            ':user_id' => $_SESSION['user_id'],
            ':total' => $total,
            ':adresse' => $adresse
        ]);

        if ($ok) {
            // Vider le panier
            $_SESSION['panier'] = [];
            $message = 'Commande passée avec succès !';
            $type = 'success';
            header('Location: ../client/dashboard.php');
            exit;
        } else {
            $message = 'Erreur lors de la commande.';
            $type = 'danger';
        }
    } else {
        $message = 'Le panier est vide ou l\'adresse est manquante.';
        $type = 'danger';
    }
}

// Calculer le total d'achat
$total_panier = 0;
foreach ($_SESSION['panier'] as $item) {
    $stmt = $pdo->prepare("SELECT prix FROM menus WHERE id = :id");
    $stmt->execute([':id' => $item['menu_id']]);
    $menu = $stmt->fetch();
    $total_panier += $menu['prix'] * $item['quantite'];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouvelle commande - Vite & Gourmand</title>

    <!-- Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- CSS -->
    <link rel="stylesheet" href="../css/style.css">

    <!-- Style spécifique en cas de que ca ne marche pas style.css -->
    <style>
        body { background: #f8fafc; font-family: 'Poppins', sans-serif; color: #1e293b; margin: 0; }
        .navbar { padding: 1.4rem 3rem; background: #1e293b; box-shadow: 0 6px 20px rgba(0,0,0,0.2); }
        .logo { font-size: 2.4rem; font-weight: 700; color: #c0392b; text-decoration: none; transition: transform 0.3s; }
        .logo:hover { transform: scale(1.05); }
        .nav-links { display: flex; gap: 2rem; align-items: center; }
        .nav-links a { color: #e2e8f0; font-weight: 500; padding: 0.5rem 1rem; border-radius: 6px; transition: all 0.3s; }
        .nav-links a:hover { color: #c0392b; background: rgba(192,57,43,0.1); }
        main { max-width: 1100px; margin: 4rem auto; padding: 0 1.5rem; }
        h1 { color: #c0392b; text-align: center; margin-bottom: 2.5rem; font-weight: 600; }
        .section { background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 8px 25px rgba(0,0,0,0.1); margin-bottom: 2rem; }
        .menu-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.5rem; }
        .menu-card { border: 1px solid #e2e8f0; border-radius: 10px; padding: 1.2rem; background: #fff; transition: transform 0.3s, box-shadow 0.3s; }
        .menu-card:hover { transform: translateY(-5px); box-shadow: 0 12px 30px rgba(0,0,0,0.12); }
        .menu-title { font-size: 1.3rem; font-weight: 600; color: #c0392b; margin-bottom: 0.5rem; }
        .menu-price { font-size: 1.2rem; font-weight: 500; color: #2d3748; margin-bottom: 0.8rem; }
        .menu-form { display: flex; align-items: center; gap: 1rem; }
        .menu-form input { width: 60px; padding: 0.6rem; border: 1px solid #cbd5e1; border-radius: 6px; text-align: center; }
        .btn-add { background: #c0392b; color: white; padding: 0.6rem 1.2rem; border: none; border-radius: 6px; cursor: pointer; transition: background 0.3s; }
        .btn-add:hover { background: #a93226; }
        .cart-item { display: flex; justify-content: space-between; align-items: center; padding: 1rem 0; border-bottom: 1px solid #e2e8f0; }
        .cart-total { font-size: 1.4rem; font-weight: 600; color: #c0392b; text-align: right; margin: 1.5rem 0; }
        .btn-primary { background: #c0392b; color: white; padding: 1rem 2.5rem; font-size: 1.1rem; border: none; border-radius: 8px; cursor: pointer; transition: all 0.3s; box-shadow: 0 4px 10px rgba(192,57,43,0.2); }
        .btn-primary:hover { background: #a93226; transform: translateY(-2px); box-shadow: 0 8px 20px rgba(192,57,43,0.3); }
        .btn-danger { background: #e74c3c; color: white; padding: 0.6rem 1.2rem; border: none; border-radius: 6px; cursor: pointer; }
        .btn-danger:hover { background: #c0392b; }
        .alert { padding: 1.2rem; border-radius: 8px; margin-bottom: 2rem; text-align: center; font-weight: 500; }
        .alert-success { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
        .alert-danger { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        @media (max-width: 768px) {
            .navbar { flex-direction: column; padding: 1.2rem; gap: 1rem; }
            .nav-links { flex-direction: column; text-align: center; gap: 1rem; }
            main { margin: 2rem auto; padding: 0 1rem; }
            .menu-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body <?= page_data_attrs() ?>>

<header>
    <nav class="navbar">
        <a href="../public/index.php" class="logo">Vite & Gourmand</a>
        <ul class="nav-links">
            <li><a href="../client/dashboard.php">Mon espace</a></li>
            <li><a href="../authentification/logout.php" class="btn btn-logout">Déconnexion</a></li>
        </ul>
    </nav>
</header>

<main>
    <h1>Passer une nouvelle commande</h1>
    <div class="section">
        <h2>Menus disponibles</h2>
        <div id="menus-root" class="menu-grid">Chargement...</div>
    </div>

    <div class="section">
        <h2>Votre panier</h2>
        <div id="cart-root">Chargement du panier...</div>

        <div class="form-section" style="margin-top:1rem;">
            <h2>Adresse de livraison</h2>
            <form id="checkout-form">
                <div class="form-row">
                    <label>Adresse complète</label>
                    <textarea name="adresse" rows="4" required placeholder="Ex: 19 Rue Test, 33000 Bordeaux"></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Passer la commande</button>
            </form>
        </div>
    </div>
</main>

<script src="../js/client-orders.js" defer></script>

</body>
</html>