<?php
// Fichier de modèles simples pour séparer la logique de la base de données.
// Cette couche objet relationnelle donne une interface métier aux tables SQL.
// Les classes sont des POPO (Plain Old PHP Objects) qui encapsulent les opérations SQL
// et la logique de transaction tout en restant légères.

class MenuModel
{
    public static function fetchAvailable(PDO $pdo): array
    {
        $stmt = $pdo->prepare('SELECT id, titre, description, prix, stock FROM menus WHERE stock > 0 ORDER BY titre');
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function fetchById(PDO $pdo, int $id): ?array
    {
        $stmt = $pdo->prepare('SELECT * FROM menus WHERE id = :id');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public static function decreaseStock(PDO $pdo, int $menuId, int $quantity): bool
    {
        if ($quantity <= 0) {
            return false;
        }
        $stmt = $pdo->prepare('UPDATE menus SET stock = GREATEST(0, stock - :quantity) WHERE id = :id');
        return $stmt->execute(['quantity' => $quantity, 'id' => $menuId]);
    }
}

class OrderModel
{
    public static function create(PDO $pdo, int $userId, array $cart, string $address): bool
    {
        if (empty($cart) || $address === '') {
            return false;
        }

        $total = 0.0;
        foreach ($cart as $item) {
            $menuId = (int)($item['menu_id'] ?? 0);
            $quantity = max(1, (int)($item['quantity'] ?? 0));
            $menu = MenuModel::fetchById($pdo, $menuId);
            if (!$menu || $menu['stock'] < $quantity) {
                return false;
            }
            $total += $menu['prix'] * $quantity;
        }

        try {
            $pdo->beginTransaction();
            $stmt = $pdo->prepare('INSERT INTO commandes (user_id, total, status, adresse_livraison) VALUES (:user_id, :total, :status, :adresse)');
            $created = $stmt->execute([
                'user_id' => $userId,
                'total' => $total,
                'status' => 'en_cours',
                'adresse' => $address,
            ]);

            if (!$created) {
                $pdo->rollBack();
                return false;
            }

            foreach ($cart as $item) {
                $menuId = (int)($item['menu_id'] ?? 0);
                $quantity = max(1, (int)($item['quantity'] ?? 0));
                if (!MenuModel::decreaseStock($pdo, $menuId, $quantity)) {
                    $pdo->rollBack();
                    return false;
                }
            }

            $pdo->commit();
            return true;
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            return false;
        }
    }
}

class WarframeModel
{
    public static function fetchAll(PDO $pdo): array
    {
        $stmt = $pdo->prepare('SELECT id, name, category, description, file_path, created_at FROM warframes ORDER BY created_at DESC');
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function fetchById(PDO $pdo, int $id): ?array
    {
        $stmt = $pdo->prepare('SELECT * FROM warframes WHERE id = :id');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public static function create(PDO $pdo, string $name, string $category, string $description, string $filePath): bool
    {
        if ($name === '' || $category === '' || $filePath === '') {
            return false;
        }

        $stmt = $pdo->prepare('INSERT INTO warframes (name, category, description, file_path) VALUES (:name, :category, :description, :file_path)');
        return $stmt->execute([
            'name' => $name,
            'category' => $category,
            'description' => $description,
            'file_path' => $filePath,
        ]);
    }
}
