<?php
/**
 * Setup Vite & Gourmand - Créer la base de données et les tables
 * Accédez à : http://localhost/vite-gourmand/setup.php
 */

$dbHost = 'localhost';
$dbUser = 'root';
$dbPass = '';
$dbName = 'vite_gourmand';

echo "<html><head><meta charset='UTF-8'><style>body{font-family:Arial;background:#f5f5f5;padding:20px;}h1{color:#c0392b;}.success{color:green;background:#e8f5e9;padding:10px;border-radius:5px;margin:10px 0;}.error{color:#c0392b;background:#ffebee;padding:10px;border-radius:5px;margin:10px 0;}</style></head><body>";
echo "<h1>⚙️ Setup Vite & Gourmand</h1>";

try {
    // Connexion sans base de données (pour la créer)
    $pdo = new PDO(
        "mysql:host=$dbHost",
        $dbUser,
        $dbPass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    echo "<div class='success'>✅ Connexion à MySQL réussie</div>";

    // Créer la base de données
    $pdo->exec("CREATE DATABASE IF NOT EXISTS $dbName CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "<div class='success'>✅ Base de données '$dbName' créée/vérifiée</div>";

    // Sélectionner la base de données
    $pdo->exec("USE $dbName");

    // Lire et exécuter tous les fichiers SQL de la base de données
    $sqlFiles = [
        __DIR__ . '/database/vite_gourmand.sql',
        __DIR__ . '/database/warframes.sql',
    ];

    $executed = 0;
    foreach ($sqlFiles as $sqlFile) {
        if (!file_exists($sqlFile)) {
            throw new Exception("Fichier SQL introuvable : $sqlFile");
        }

        // Parser et exécuter le SQL
        $sqlContent = file_get_contents($sqlFile);
        $statements = array_filter(
            array_map('trim', preg_split('/;(?=([^\']*\'[^\']*\')*[^\']*$)/', $sqlContent)),
            fn($s) => !empty($s) && !str_starts_with($s, '--') && !str_starts_with($s, '/*')
        );

        foreach ($statements as $statement) {
            if (!empty(trim($statement))) {
                try {
                    $pdo->exec($statement);
                    $executed++;
                } catch (Exception $e) {
                    if (stripos($e->getMessage(), 'syntax error') === false) {
                        // Continuar con siguientes
                    }
                }
            }
        }
    }
    echo "<div class='success'>✅ $executed commandes SQL exécutées</div>";

    // Vérifier les tables
    $tables = ['users', 'menus', 'commandes', 'warframes'];
    foreach ($tables as $table) {
        try {
            $result = $pdo->query("SELECT COUNT(*) FROM $table");
            $count = $result->fetchColumn();
            echo "<div class='success'>✅ Table '$table' existe ({$count} enregistrements)</div>";
        } catch (Exception $e) {
            echo "<div class='error'>❌ Table '$table' n'existe pas</div>";
        }
    }

    // Test de configuration avec config.php
    echo "<hr>";
    echo "<h2>Test de configuration</h2>";
    require_once __DIR__ . '/Includes/config.php';
    echo "<div class='success'>✅ config.php chargé correctement</div>";

    // Test de session
    if (session_status() === PHP_SESSION_ACTIVE) {
        echo "<div class='success'>✅ Session démarrée</div>";
    }

    echo "<hr>";
    echo "<div style='background:#fff;padding:20px;border-radius:5px;margin-top:20px;'>";
    echo "<h3>✅ Setup terminé !</h3>";
    echo "<p>Votre application est prête. Accédez à :</p>";
    echo "<ul>";
    echo "<li><a href='./public/index.php'>Page d'accueil</a></li>";
    echo "<li><a href='./autentification/login.php'>Connexion</a></li>";
    echo "<li><a href='./autentification/register.php'>Inscription</a></li>";
    echo "</ul>";
    echo "<p><strong>Comptes de test :</strong></p>";
    echo "<ul>";
    echo "<li>Admin : admi@vite.fr / (mot de passe : password)</li>";
    echo "<li>Client : client@vite.fr / (mot de passe : password)</li>";
    echo "<li>Travailleur : travailleur@vite.fr / (mot de passe : password)</li>";
    echo "</ul>";
    echo "</div>";

} catch (PDOException $e) {
    echo "<div class='error'>❌ Error PDO: " . $e->getMessage() . "</div>";
    echo "<p><strong>Solutions :</strong></p>";
    echo "<ul>";
    echo "<li>Vérifiez que MySQL/MariaDB fonctionne sur localhost:3306</li>";
    echo "<li>Ouvrez le panneau de contrôle XAMPP et démarrez le service MySQL</li>";
    echo "<li>Vérifiez que l'utilisateur 'root' n'a pas de mot de passe</li>";
    echo "</ul>";
} catch (Exception $e) {
    echo "<div class='error'>❌ Error: " . $e->getMessage() . "</div>";
}

echo "</body></html>";
?>
