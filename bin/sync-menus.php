<?php
// Script CLI pour synchroniser les menus SQL vers MongoDB
require_once __DIR__ . '/../Includes/config.php';
require_once __DIR__ . '/../Includes/mongo.php';

$options = getopt('', ['dry-run', 'help']);
if (isset($options['help'])) {
    echo "Usage: php sync-menus.php [--dry-run]\n";
    exit(0);
}
$dryRun = isset($options['dry-run']);

try {
    $stmt = $pdo->prepare("SELECT * FROM menus ORDER BY created_at DESC");
    $stmt->execute();
    $menus = $stmt->fetchAll();
} catch (Throwable $e) {
    fwrite(STDERR, "Erreur lors de la lecture de MySQL : " . $e->getMessage() . "\n");
    exit(2);
}

$total = count($menus);
$synced = 0;
$errors = [];

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
        'updated_at' => new MongoDB\BSON\UTCDateTime()
    ];

    if ($dryRun) {
        echo "[DRY] Syncing SQL id={$menu['id']} title={$menu['titre']}\n";
        $synced++;
        continue;
    }

    try {
        $updated = mongoUpdateOne('menus', ['sql_id' => (int)$menu['id']], $document);
        if (! $updated) {
            mongoInsertOne('menus', $document);
        }
        $synced++;
    } catch (Throwable $e) {
        $errors[] = sprintf('id=%d error=%s', $menu['id'], $e->getMessage());
    }
}

echo "Total menus: $total\n";
echo "Synced: $synced\n";
if (!empty($errors)) {
    fwrite(STDERR, "Errors:\n");
    foreach ($errors as $err) fwrite(STDERR, " - $err\n");
    exit(1);
}

exit(0);
