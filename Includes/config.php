<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('DB_HOST', 'localhost');
define('DB_NAME', 'vite_gourmand');
define('DB_USER', 'root');
define('DB_PASS', ''); 


try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, 
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,      
            PDO::ATTR_EMULATE_PREPARES   => false,                 
        ]
    );
} catch (PDOException $e) {
    
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

// Créer token CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function verifyCsrfToken() {
    if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        http_response_code(403);
        die('Erreur de sécurité CSRF. Veuillez réessayer.');
    }
    // Régénérer le token après utilisation (plus sécurisé)
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>   