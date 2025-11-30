<?php
// includes/auth.php
session_start([
    'cookie_httponly' => true,
    'cookie_secure' => isset($_SERVER['HTTPS']),
    'use_strict_mode' => true
]);

function is_logged_in(): bool {
    return isset($_SESSION['user_id']);
}

function current_user_id(): ?int {
    return $_SESSION['user_id'] ?? null;
}

function current_user(): ?array {
    if (!is_logged_in()) return null;
    $conn = db_connect();
    $result = pg_execute($conn, "user_get_by_email", [$_SESSION['user_email']]);
    return pg_fetch_assoc($result) ?: null;
}

function require_login() {
    if (!is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}

function login_user(int $user_id, string $email) {
    session_regenerate_id(true);
    $_SESSION['user_id'] = $user_id;
    $_SESSION['user_email'] = $email;
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function logout_user() {
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
}