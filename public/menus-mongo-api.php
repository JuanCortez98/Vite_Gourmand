<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../Includes/mongo.php';

try {
    $menus = mongoQuery('menus', [], ['sort' => ['created_at' => -1]]);
    echo json_encode(['success' => true, 'data' => $menus], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'MongoDB error: ' . $e->getMessage(),
    ], JSON_UNESCAPED_UNICODE);
}
