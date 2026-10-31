<?php
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

// Session sudah ada, tapi pastikan language.php punya sesi juga
if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'id';
}

require_once __DIR__ . '/language.php';
require_once __DIR__ . '/../config/database.php';

function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}

function base_url(string $path = ''): string
{
    return BASE_URL . '/' . ltrim($path, '/');
}

function flash(string $key, ?string $message = null): ?string
{
    if ($message !== null) {
        $_SESSION['flash'][$key] = $message;
        return null;
    }
    $msg = $_SESSION['flash'][$key] ?? null;
    unset($_SESSION['flash'][$key]);
    return $msg;
}

function old(string $key, $default = ''): string
{
    return $_SESSION['old'][$key] ?? $default;
}

function flash_old(array $data): void
{
    $_SESSION['old'] = $data;
}

function clear_old(): void
{
    unset($_SESSION['old']);
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function csrf_check(): bool
{
    return isset($_POST['csrf_token'])
        && hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token']);
}

function is_logged_in(): bool
{
    return isset($_SESSION['user_id']);
}

function current_user(): ?array
{
    static $user = null;
    if (!is_logged_in()) return null;
    if ($user === null) {
        require_once __DIR__ . '/../config/database.php';
        $user = fetch_one('SELECT id, username, nama, role FROM users WHERE id = ?', [$_SESSION['user_id']]);
    }
    return $user;
}

function require_login(): void
{
    if (!is_logged_in()) {
        flash('error', 'Silakan login terlebih dahulu.');
        redirect(base_url('login.php'));
    }
}

function attempt_login(string $username, string $password): bool
{
    require_once __DIR__ . '/../config/database.php';
    $user = fetch_one('SELECT * FROM users WHERE username = ? LIMIT 1', [$username]);

    if (!$user) {
        return false;
    }

    $hash = $user['password'];

    // Hash bcrypt (password_hash / password_verify)
    if (password_verify($password, $hash)) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int) $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['nama'] = $user['nama'];
        $_SESSION['role'] = $user['role'];
        return true;
    }

    // Legacy MD5 (dari database.sql versi lama) - upgrade otomatis ke bcrypt
    if (strlen($hash) === 32 && ctype_xdigit($hash) && hash_equals($hash, md5($password))) {
        $newHash = password_hash($password, PASSWORD_DEFAULT);
        query('UPDATE users SET password = ? WHERE id = ?', [$newHash, $user['id']]);
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int) $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['nama'] = $user['nama'];
        $_SESSION['role'] = $user['role'];
        return true;
    }

    return false;
}

function logout(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
}
