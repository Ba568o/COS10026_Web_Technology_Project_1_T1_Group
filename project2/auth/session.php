<?php
function auth_start(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) return;
    ini_set('session.use_strict_mode', '1');
    session_set_cookie_params(['httponly'=>true, 'secure'=>!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off', 'samesite'=>'Lax']);
    session_start();
}
function auth_escape(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
function auth_token(): string
{
    if (empty($_SESSION['auth_token'])) $_SESSION['auth_token'] = bin2hex(random_bytes(32));
    return $_SESSION['auth_token'];
}
function auth_valid_token($token): bool
{
    return is_string($token) && isset($_SESSION['auth_token']) && hash_equals($_SESSION['auth_token'], $token);
}
function auth_logged_in(): bool
{
    if (empty($_SESSION['user_id'])) return false;
    if (time() - ($_SESSION['last_active'] ?? 0) > 1800) {
        unset($_SESSION['user_id'], $_SESSION['username'], $_SESSION['role'], $_SESSION['last_active']);
        return false;
    }
    $_SESSION['last_active'] = time();
    return true;
}
function auth_require_login(): void
{
    auth_start();
    header('Cache-Control: no-store');
    if (!auth_logged_in()) {
        $_SESSION['return_to'] = basename($_SERVER['SCRIPT_NAME'] ?? 'index.php');
        header('Location: login.php');
        exit;
    }
}
