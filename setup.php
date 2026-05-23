<?php
/**
 * Setup Vite & Gourmand - Crear BD y tablas
 * Accede a: http://localhost/vite-gourmand/setup.php
 */

$dbHost = 'localhost';
$dbUser = 'root';
$dbPass = '';
$dbName = 'vite_gourmand';

echo "<html><head><meta charset='UTF-8'><style>body{font-family:Arial;background:#f5f5f5;padding:20px;}h1{color:#c0392b;}.success{color:green;background:#e8f5e9;padding:10px;border-radius:5px;margin:10px 0;}.error{color:#c0392b;background:#ffebee;padding:10px;border-radius:5px;margin:10px 0;}</style></head><body>";
echo "<h1>⚙️ Setup Vite & Gourmand</h1>";

try {
    // Conexión sin BD (para crearla)
    $pdo = new PDO(
        "mysql:host=$dbHost",
        $dbUser,
        $dbPass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    echo "<div class='success'>✅ Conexión a MySQL exitosa</div>";

    // Crear BD
    $pdo->exec("CREATE DATABASE IF NOT EXISTS $dbName CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "<div class='success'>✅ Base de datos '$dbName' creada/verificada</div>";

    // Seleccionar BD
    $pdo->exec("USE $dbName");

    // Leer el archivo SQL
    $sqlFile = __DIR__ . '/database/vite_gourmand.sql';
    if (!file_exists($sqlFile)) {
        throw new Exception("Archivo SQL no encontrado: $sqlFile");
    }

    // Parsear y ejecutar SQL
    $sqlContent = file_get_contents($sqlFile);
    
    // Dividir por ";" pero cuidado con comentarios
    $statements = array_filter(
        array_map('trim', preg_split('/;(?=([^\']*\'[^\']*\')*[^\']*$)/', $sqlContent)),
        fn($s) => !empty($s) && !str_starts_with($s, '--') && !str_starts_with($s, '/*')
    );

    $executed = 0;
    foreach ($statements as $statement) {
        if (!empty(trim($statement))) {
            try {
                $pdo->exec($statement);
                $executed++;
            } catch (Exception $e) {
                // Algunos comandos son ignorables (SET, /*!...)
                if (stripos($e->getMessage(), 'syntax error') === false) {
                    // Continuar con siguientes
                }
            }
        }
    }
    echo "<div class='success'>✅ $executed comandos SQL ejecutados</div>";

    // Verificar tablas
    $tables = ['users', 'menus', 'commandes'];
    foreach ($tables as $table) {
        try {
            $result = $pdo->query("SELECT COUNT(*) FROM $table");
            $count = $result->fetchColumn();
            echo "<div class='success'>✅ Tabla '$table' existe ({$count} registros)</div>";
        } catch (Exception $e) {
            echo "<div class='error'>❌ Tabla '$table' no existe</div>";
        }
    }

    // Test de conexión con config.php
    echo "<hr>";
    echo "<h2>Test de Configuración</h2>";
    require_once __DIR__ . '/Includes/config.php';
    echo "<div class='success'>✅ config.php cargado correctamente</div>";

    // Test de sesión
    if (session_status() === PHP_SESSION_ACTIVE) {
        echo "<div class='success'>✅ Sesión iniciada</div>";
    }

    echo "<hr>";
    echo "<div style='background:#fff;padding:20px;border-radius:5px;margin-top:20px;'>";
    echo "<h3>✅ ¡Setup Completado!</h3>";
    echo "<p>Tu aplicación está lista. Accede a:</p>";
    echo "<ul>";
    echo "<li><a href='./public/index.php'>Página de inicio</a></li>";
    echo "<li><a href='./autentification/login.php'>Iniciar sesión</a></li>";
    echo "<li><a href='./autentification/register.php'>Registrarse</a></li>";
    echo "</ul>";
    echo "<p><strong>Cuentas de prueba:</strong></p>";
    echo "<ul>";
    echo "<li>Admin: admi@vite.fr / (contraseña: password)</li>";
    echo "<li>Cliente: client@vite.fr / (contraseña: password)</li>";
    echo "<li>Trabajador: travailleur@vite.fr / (contraseña: password)</li>";
    echo "</ul>";
    echo "</div>";

} catch (PDOException $e) {
    echo "<div class='error'>❌ Error PDO: " . $e->getMessage() . "</div>";
    echo "<p><strong>Soluciones:</strong></p>";
    echo "<ul>";
    echo "<li>Asegúrate de que MySQL/MariaDB está corriendo en localhost:3306</li>";
    echo "<li>Abre XAMPP Control Panel y inicia el servicio MySQL</li>";
    echo "<li>Verifica que el usuario 'root' no tiene contraseña</li>";
    echo "</ul>";
} catch (Exception $e) {
    echo "<div class='error'>❌ Error: " . $e->getMessage() . "</div>";
}

echo "</body></html>";
?>
