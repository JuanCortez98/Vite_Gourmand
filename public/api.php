<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../Includes/config.php';
require_once __DIR__ . '/../Includes/Models.php';

function getRequestData(): array
{
    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
    if (stripos($contentType, 'application/json') !== false) {
        $payload = json_decode(file_get_contents('php://input'), true);
        return is_array($payload) ? $payload : [];
    }
    return $_POST;
}

function sendJson(array $payload, int $status = 200): void
{
    http_response_code($status);
    echo json_encode($payload);
    exit;
}

function authResponse(bool $ok, string $message = '', array $data = []): void
{
    sendJson(['ok' => $ok, 'message' => $message, 'data' => $data], $ok ? 200 : 400);
}

function verifyCsrfTokenValue(?string $token): void
{
    if (empty($token) || $token !== ($_SESSION['csrf_token'] ?? '')) {
        sendJson(['ok' => false, 'message' => 'Erreur de sécurité CSRF.'], 403);
    }
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function requireAdmin(): void
{
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
        sendJson(['ok' => false, 'message' => 'Accès refusé.'], 403);
    }
}

$resource = $_GET['resource'] ?? '';
$data = getRequestData();

try {
    if ($resource === 'menus') {
        $stmt = $pdo->prepare('SELECT id, titre, description, theme, regime, personnes_minimum, prix, stock, created_at, updated_at FROM menus ORDER BY created_at DESC');
        $stmt->execute();
        $rows = $stmt->fetchAll();
        sendJson(['ok' => true, 'data' => $rows]);
    }

    if ($resource === 'menu' && isset($_GET['id'])) {
        $id = (int)$_GET['id'];
        $stmt = $pdo->prepare('SELECT * FROM menus WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        sendJson(['ok' => true, 'data' => $row]);
    }

    if ($resource === 'home') {
        $stmt = $pdo->prepare('SELECT id, titre, description, prix FROM menus ORDER BY created_at DESC LIMIT 3');
        $stmt->execute();
        $rows = $stmt->fetchAll();
        sendJson(['ok' => true, 'data' => $rows]);
    }

    if ($resource === 'session') {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        sendJson(['ok' => true, 'data' => [
            'loggedIn' => isset($_SESSION['user_id']),
            'email' => $_SESSION['email'] ?? null,
            'role' => $_SESSION['role'] ?? null,
            'csrf_token' => $_SESSION['csrf_token'],
        ]]);
    }

    if ($resource === 'auth-login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        verifyCsrfTokenValue($data['csrf_token'] ?? null);
        $email = trim($data['email'] ?? '');
        $password = $data['password'] ?? '';

        if ($email === '' || $password === '') {
            authResponse(false, 'Veuillez remplir tous les champs.');
        }

        $stmt = $pdo->prepare('SELECT id, email, password_hash, role FROM users WHERE email = :email');
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password_hash'])) {
            authResponse(false, 'Email ou mot de passe incorrect.');
        }

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        sendJson(['ok' => true, 'data' => ['role' => $user['role']]]);
    }

    if ($resource === 'auth-register' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        verifyCsrfTokenValue($data['csrf_token'] ?? null);
        $email = trim($data['email'] ?? '');
        $password = $data['password'] ?? '';
        $confirm = $data['confirm_password'] ?? '';

        if ($email === '' || $password === '' || $confirm === '') {
            authResponse(false, 'Veuillez remplir tous les champs.');
        }

        if ($password !== $confirm) {
            authResponse(false, 'Les mots de passe ne correspondent pas.');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            authResponse(false, 'Email invalide.');
        }

        if (strlen($password) < 6) {
            authResponse(false, 'Le mot de passe doit contenir au moins 6 caractères.');
        }

        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = :email');
        $stmt->execute(['email' => $email]);
        if ($stmt->fetch()) {
            authResponse(false, 'Cet email est déjà utilisé.');
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('INSERT INTO users (email, password_hash, role) VALUES (:email, :password_hash, :role)');
        $ok = $stmt->execute(['email' => $email, 'password_hash' => $passwordHash, 'role' => 'client']);

        if (!$ok) {
            authResponse(false, 'Erreur lors de l inscription.');
        }

        authResponse(true, 'Inscription réussie. Vous pouvez maintenant vous connecter.');
    }

    if ($resource === 'auth-logout' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
        authResponse(true, 'Déconnecté avec succès.');
    }

    if ($resource === 'admin-stats') {
        requireAdmin();

        $stats = [
            'totalUsers' => (int)$pdo->query('SELECT COUNT(*) FROM users')->fetchColumn(),
            'totalOrders' => (int)$pdo->query('SELECT COUNT(*) FROM commandes')->fetchColumn(),
            'ordersInProgress' => (int)$pdo->query("SELECT COUNT(*) FROM commandes WHERE status = 'en_cours'")->fetchColumn(),
            'menusLowStock' => (int)$pdo->query('SELECT COUNT(*) FROM menus WHERE stock <= 5')->fetchColumn(),
        ];
        sendJson(['ok' => true, 'data' => $stats]);
    }

    if ($resource === 'admin-users') {
        requireAdmin();
        $stmt = $pdo->prepare('SELECT id, email, role, created_at FROM users ORDER BY created_at DESC');
        $stmt->execute();
        sendJson(['ok' => true, 'data' => $stmt->fetchAll()]);
    }

    if ($resource === 'admin-user' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        requireAdmin();
        verifyCsrfTokenValue($data['csrf_token'] ?? null);

        $email = trim($data['email'] ?? '');
        $password = $data['password'] ?? '';
        $role = $data['role'] ?? 'client';

        if ($email === '' || $password === '') {
            authResponse(false, 'Veuillez remplir tous les champs.');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            authResponse(false, 'Email invalide.');
        }
        if (strlen($password) < 6) {
            authResponse(false, 'Le mot de passe doit contenir au moins 6 caractères.');
        }

        $check = $pdo->prepare('SELECT id FROM users WHERE email = :email');
        $check->execute(['email' => $email]);
        if ($check->fetch()) {
            authResponse(false, 'Cet email est déjà utilisé.');
        }

        $stmt = $pdo->prepare('INSERT INTO users (email, password_hash, role) VALUES (:email, :password_hash, :role)');
        $ok = $stmt->execute(['email' => $email, 'password_hash' => password_hash($password, PASSWORD_DEFAULT), 'role' => $role]);
        authResponse($ok, $ok ? 'Utilisateur ajouté.' : 'Erreur lors de l ajout.');
    }

    if ($resource === 'admin-user-delete' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        requireAdmin();
        verifyCsrfTokenValue($data['csrf_token'] ?? null);
        $id = (int)($data['id'] ?? 0);

        if ($id <= 0) {
            authResponse(false, 'ID invalide.');
        }
        if ($id === ($_SESSION['user_id'] ?? 0)) {
            authResponse(false, 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        $stmt = $pdo->prepare('DELETE FROM users WHERE id = :id');
        $stmt->execute(['id' => $id]);
        authResponse($stmt->rowCount() > 0, $stmt->rowCount() > 0 ? 'Utilisateur supprimé.' : 'Utilisateur introuvable.');
    }

    if ($resource === 'admin-menus') {
        requireAdmin();
        $stmt = $pdo->prepare('SELECT * FROM menus ORDER BY created_at DESC');
        $stmt->execute();
        sendJson(['ok' => true, 'data' => $stmt->fetchAll()]);
    }

    if ($resource === 'admin-menu' && $_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
        requireAdmin();
        $id = (int)$_GET['id'];
        $stmt = $pdo->prepare('SELECT * FROM menus WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $menu = $stmt->fetch();
        sendJson(['ok' => true, 'data' => $menu]);
    }

    if ($resource === 'admin-menu' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        requireAdmin();
        verifyCsrfTokenValue($data['csrf_token'] ?? null);

        require_once __DIR__ . '/../Includes/mongo.php';

        $id = (int)($data['id'] ?? 0);
        $titre = trim($data['titre'] ?? '');
        $description = trim($data['description'] ?? '');
        $theme = $data['theme'] ?? 'classique';
        $regime = $data['regime'] ?? 'classique';
        $personnes_minimum = max(1, (int)($data['personnes_minimum'] ?? 1));
        $prix = floatval($data['prix'] ?? 0);
        $stock = max(0, (int)($data['stock'] ?? 0));

        if ($titre === '' || $prix <= 0 || $personnes_minimum <= 0) {
            authResponse(false, 'Champs invalides ou manquants.');
        }

        if ($id > 0) {
            $stmt = $pdo->prepare('UPDATE menus SET titre = :titre, description = :description, theme = :theme, regime = :regime, personnes_minimum = :personnes_minimum, prix = :prix, stock = :stock, updated_at = NOW() WHERE id = :id');
            $ok = $stmt->execute(['titre' => $titre, 'description' => $description, 'theme' => $theme, 'regime' => $regime, 'personnes_minimum' => $personnes_minimum, 'prix' => $prix, 'stock' => $stock, 'id' => $id]);
            if ($ok) {
                try {
                    $updated = mongoUpdateOne('menus', ['sql_id' => $id], [
                        'titre' => $titre,
                        'description' => $description,
                        'theme' => $theme,
                        'regime' => $regime,
                        'personnes_minimum' => $personnes_minimum,
                        'prix' => $prix,
                        'stock' => $stock,
                        'updated_at' => new MongoDB\BSON\UTCDateTime(),
                    ]);
                    if (! $updated) {
                        mongoInsertOne('menus', [
                            'sql_id' => $id,
                            'titre' => $titre,
                            'description' => $description,
                            'theme' => $theme,
                            'regime' => $regime,
                            'personnes_minimum' => $personnes_minimum,
                            'prix' => $prix,
                            'stock' => $stock,
                        ]);
                    }
                } catch (Throwable $e) {
                    // ignore Mongo sync failures for now
                }
            }
            authResponse($ok, $ok ? 'Menu modifié.' : 'Erreur lors de la modification.');
        }

        $stmt = $pdo->prepare('INSERT INTO menus (titre, description, theme, regime, personnes_minimum, prix, stock) VALUES (:titre, :description, :theme, :regime, :personnes_minimum, :prix, :stock)');
        $ok = $stmt->execute(['titre' => $titre, 'description' => $description, 'theme' => $theme, 'regime' => $regime, 'personnes_minimum' => $personnes_minimum, 'prix' => $prix, 'stock' => $stock]);
        $newId = $pdo->lastInsertId();
        if ($ok) {
            try {
                mongoInsertOne('menus', [
                    'sql_id' => (int)$newId,
                    'titre' => $titre,
                    'description' => $description,
                    'theme' => $theme,
                    'regime' => $regime,
                    'personnes_minimum' => $personnes_minimum,
                    'prix' => $prix,
                    'stock' => $stock,
                ]);
            } catch (Throwable $e) {
                // ignore Mongo sync failures
            }
        }
        authResponse($ok, $ok ? 'Menu ajouté.' : 'Erreur lors de l ajout.');
    }

    if ($resource === 'admin-menu-delete' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        requireAdmin();
        verifyCsrfTokenValue($data['csrf_token'] ?? null);
        $id = (int)($data['id'] ?? 0);

        if ($id <= 0) {
            authResponse(false, 'ID invalide.');
        }

        $stmt = $pdo->prepare('DELETE FROM menus WHERE id = :id');
        $stmt->execute(['id' => $id]);
        if ($stmt->rowCount() > 0) {
            require_once __DIR__ . '/../Includes/mongo.php';
            try {
                mongoDeleteOne('menus', ['sql_id' => $id]);
            } catch (Throwable $e) {
                // ignore Mongo sync failures
            }
        }
        authResponse($stmt->rowCount() > 0, $stmt->rowCount() > 0 ? 'Menu supprimé.' : 'Menu introuvable.');
    }

    if ($resource === 'admin-menus-sync' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        requireAdmin();
        verifyCsrfTokenValue($data['csrf_token'] ?? null);
        require_once __DIR__ . '/../Includes/mongo.php';
        $stmt = $pdo->prepare('SELECT * FROM menus ORDER BY created_at DESC');
        $stmt->execute();
        $menus = $stmt->fetchAll();
        $synced = 0;
        foreach ($menus as $menu) {
            $document = [
                'sql_id' => (int)$menu['id'],
                'titre' => $menu['titre'],
                'description' => $menu['description'],
                'theme' => $menu['theme'],
                'regime' => $menu['regime'],
                'personnes_minimum' => (int)$menu['personnes_minimum'],
                'prix' => (float)$menu['prix'],
                'stock' => (int)$menu['stock'],
                'updated_at' => new MongoDB\BSON\UTCDateTime(),
            ];
            try {
                $updated = mongoUpdateOne('menus', ['sql_id' => (int)$menu['id']], $document);
                if (! $updated) {
                    mongoInsertOne('menus', $document);
                }
                $synced++;
            } catch (Throwable $e) {
                // ignore individual errors
            }
        }
        sendJson(['ok' => true, 'data' => ['synced' => $synced]]);
    }

    // Public endpoint: list available menus (stock > 0)
    if ($resource === 'menus-available') {
        sendJson(['ok' => true, 'data' => MenuModel::fetchAvailable($pdo)]);
    }

    // Client: create an order from client-side cart
    if ($resource === 'client-order' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
            authResponse(false, 'Accès refusé.');
        }
        verifyCsrfTokenValue($data['csrf_token'] ?? null);

        $cart = $data['cart'] ?? [];
        $adresse = trim($data['adresse'] ?? '');
        if (empty($cart) || $adresse === '') {
            authResponse(false, 'Panier vide ou adresse manquante.');
        }

        $ok = OrderModel::create($pdo, (int)$_SESSION['user_id'], $cart, $adresse);
        authResponse($ok, $ok ? 'Commande passée avec succès.' : 'Erreur lors de la création de la commande.');
    }

    // Admin: sync a single menu to MongoDB
    if ($resource === 'admin-menu-sync-one' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        requireAdmin();
        verifyCsrfTokenValue($data['csrf_token'] ?? null);
        require_once __DIR__ . '/../Includes/mongo.php';
        $id = (int)($data['id'] ?? 0);
        if ($id <= 0) {
            authResponse(false, 'ID invalide.');
        }
        $stmt = $pdo->prepare('SELECT * FROM menus WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $menu = $stmt->fetch();
        if (! $menu) {
            authResponse(false, 'Menu introuvable.');
        }
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
                'updated_at' => new MongoDB\BSON\UTCDateTime(),
            ];
            $updated = mongoUpdateOne('menus', ['sql_id' => $id], $doc);
            if (! $updated) mongoInsertOne('menus', $doc);
            authResponse(true, 'Synchronisation terminée.');
        } catch (Throwable $e) {
            authResponse(false, 'Erreur MongoDB: ' . $e->getMessage());
        }
    }

    if ($resource === 'admin-orders') {
        requireAdmin();
        $stmt = $pdo->prepare('SELECT c.id, u.email AS client_email, c.created_at, c.total, c.status, c.adresse_livraison FROM commandes c LEFT JOIN users u ON c.user_id = u.id ORDER BY c.created_at DESC');
        $stmt->execute();
        sendJson(['ok' => true, 'data' => $stmt->fetchAll()]);
    }

    // Admin: provide combined status between SQL menus and Mongo
    if ($resource === 'admin-menus-sync-status') {
        requireAdmin();
        try {
            require_once __DIR__ . '/../Includes/mongo.php';
            $stmt = $pdo->prepare('SELECT id, titre FROM menus ORDER BY created_at DESC');
            $stmt->execute();
            $sqlMenus = $stmt->fetchAll();
            $sqlIds = array_map(function ($m) { return (int)$m['id']; }, $sqlMenus);
            $mongoDocs = [];
            if (!empty($sqlIds)) {
                $docs = mongoQuery('menus', ['sql_id' => ['$in' => $sqlIds]], []);
                foreach ($docs as $d) {
                    if (isset($d['sql_id'])) $mongoDocs[(int)$d['sql_id']] = $d;
                }
            }
            $out = array_map(function ($m) use ($mongoDocs) {
                $mid = (int)$m['id'];
                $doc = $mongoDocs[$mid] ?? null;
                return [
                    'id' => $mid,
                    'titre' => $m['titre'],
                    'mongo_exists' => $doc ? true : false,
                    'mongo_updated' => $doc && isset($doc['updated_at']) ? (is_array($doc['updated_at']) ? json_encode($doc['updated_at']) : (string)$doc['updated_at']) : null,
                ];
            }, $sqlMenus);
            sendJson(['ok' => true, 'data' => $out]);
        } catch (Throwable $e) {
            sendJson(['ok' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
        }
    }

    if ($resource === 'admin-order' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        requireAdmin();
        verifyCsrfTokenValue($data['csrf_token'] ?? null);
        $id = (int)($data['id'] ?? 0);
        $status = trim($data['status'] ?? '');

        if ($id <= 0 || !in_array($status, ['en_cours', 'prete', 'servie', 'annulee'], true)) {
            authResponse(false, 'Données invalides.');
        }

        $stmt = $pdo->prepare('UPDATE commandes SET status = :status WHERE id = :id');
        $stmt->execute(['status' => $status, 'id' => $id]);
        authResponse($stmt->rowCount() > 0, $stmt->rowCount() > 0 ? 'État mis à jour.' : 'Aucune modification.');
    }

    if ($resource === 'admin-order-delete' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        requireAdmin();
        verifyCsrfTokenValue($data['csrf_token'] ?? null);
        $id = (int)($data['id'] ?? 0);

        if ($id <= 0) {
            authResponse(false, 'ID invalide.');
        }

        $stmt = $pdo->prepare('DELETE FROM commandes WHERE id = :id');
        $stmt->execute(['id' => $id]);
        authResponse($stmt->rowCount() > 0, $stmt->rowCount() > 0 ? 'Commande supprimée.' : 'Commande introuvable.');
    }

    if ($resource === 'client-orders') {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
            authResponse(false, 'Accès refusé.');
        }

        $stmt = $pdo->prepare('SELECT id, created_at, total, status, adresse_livraison FROM commandes WHERE user_id = :user_id ORDER BY created_at DESC');
        $stmt->execute(['user_id' => $_SESSION['user_id']]);
        sendJson(['ok' => true, 'data' => $stmt->fetchAll()]);
    }

    if ($resource === 'worker-orders') {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'travailleur') {
            authResponse(false, 'Accès refusé.');
        }

        $stmt = $pdo->prepare('SELECT id, created_at, total, status, adresse_livraison FROM commandes WHERE status = :status ORDER BY created_at DESC');
        $stmt->execute(['status' => 'en_cours']);
        sendJson(['ok' => true, 'data' => $stmt->fetchAll()]);
    }

    if ($resource === 'menus-mongo') {
        try {
            require_once __DIR__ . '/../Includes/mongo.php';
            $menus = mongoQuery('menus', [], ['sort' => ['created_at' => -1]]);
            sendJson(['ok' => true, 'data' => $menus]);
        } catch (Throwable $e) {
            sendJson(['ok' => false, 'error' => 'MongoDB non disponible: ' . $e->getMessage()]);
        }
    }

    sendJson(['ok' => false, 'error' => 'Unknown resource'], 400);
} catch (Throwable $e) {
    sendJson(['ok' => false, 'error' => $e->getMessage()], 500);
}
?>
