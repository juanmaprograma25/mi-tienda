<?php
// includes/functions.php
require_once 'db.php';
require_once 'auth.php';

function h(string $str): string {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

function redirect(string $url) {
    header("Location: $url");
    exit;
}

function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf(string $token): bool {
    return is_logged_in() && hash_equals($_SESSION['csrf_token'], $token);
}

function is_image(array $file): bool {
    $allowed = ['image/jpeg', 'image/jpg', 'image/png'];
    return in_array($file['type'], $allowed) && $file['error'] === 0;
}

function save_image(array $file): ?string {
    if (!is_image($file) || $file['size'] > 2*1024*1024) return null; // 2MB max

    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = bin2hex(random_bytes(16)) . '.' . $ext;
    $destination = __DIR__ . '/../uploads/' . $filename;

    if (move_uploaded_file($file['tmp_name'], $destination)) {
        return 'uploads/' . $filename;
    }
    return null;
}

function flash(string $message, string $type = 'success') {
    $_SESSION['flash'] = ['message' => $message, 'type' => $type];
}

function display_flash() {
    if (isset($_SESSION['flash'])) {
        echo '<div class="alert alert-' . $_SESSION['flash']['type'] . '">' . h($_SESSION['flash']['message']) . '</div>';
        unset($_SESSION['flash']);
    }
}