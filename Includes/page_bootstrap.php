<?php
// Shared page bootstrap: session, csrf token, and helper helpers for page shells
require_once __DIR__ . '/config.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$csrfToken = $_SESSION['csrf_token'];
$currentUserId = (int)($_SESSION['user_id'] ?? 0);
$currentUserRole = $_SESSION['role'] ?? '';
$currentUserEmail = $_SESSION['email'] ?? '';

function require_role(string $role): void
{
    if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== $role) {
        header('Location: ../autentification/login.php');
        exit;
    }
}

function page_data_attrs(): string
{
    global $csrfToken, $currentUserId, $currentUserRole, $currentUserEmail;
    return 'data-csrf-token="' . htmlspecialchars($csrfToken) . '" data-current-user-id="' . intval($currentUserId) . '" data-current-role="' . htmlspecialchars($currentUserRole) . '" data-current-email="' . htmlspecialchars($currentUserEmail) . '"';
}

?>
