<?php
/** Starts the session once, with a cookie the browser script cannot read. */
function auth_start(): void
{
    // Do nothing when a page already started the session.
    if (session_status() === PHP_SESSION_ACTIVE) return;
    ini_set('session.use_strict_mode', '1');
    // HttpOnly keeps page scripts from reading the session cookie.
    session_set_cookie_params(['httponly'=>true, 'secure'=>!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off', 'samesite'=>'Lax']);
    session_start();
}
/** Encodes text so it is safe to print inside HTML. */
function auth_escape(string $value): string
{
    // Encode quotes and tags before the value is printed in HTML.
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Prints one language at a time from an "English / Tiếng Việt" message. */
function auth_language_pair(string $value): string
{
    $parts = explode(' / ', $value, 2);
    $english = auth_escape($parts[0]);
    $vietnamese = auth_escape($parts[1] ?? $parts[0]);
    return '<span class="language-en">' . $english . '</span><span class="language-vi">' . $vietnamese . '</span>';
}
/** True when a login session exists and has been used within 30 minutes. */
function auth_logged_in(): bool
{
    // Version 2 ignores sessions created by the older login code.
    if (empty($_SESSION['user_id']) || ($_SESSION['auth_version'] ?? 0) !== 2) return false;
    // 1800 seconds is the 30-minute idle limit.
    if (time() - ($_SESSION['last_active'] ?? 0) > 1800) {
        unset($_SESSION['user_id'], $_SESSION['username'], $_SESSION['role'], $_SESSION['last_active']);
        return false;
    }
    $_SESSION['last_active'] = time();
    return true;
}
/** Sends a visitor who is not signed in back to the login chooser. */
function auth_require_login(): void
{
    auth_start();
    header('Cache-Control: no-store');
    if (!auth_logged_in()) {
        // Save this page name so login can return here afterwards.
        $_SESSION['return_to'] = basename($_SERVER['SCRIPT_NAME'] ?? 'index.php');
        header('Location: login.php');
        exit;
    }
}

/** Allows the page only for an HR account that still exists in hr_users. */
function auth_require_hr(): void
{
    auth_require_login();
    // Applicant sessions are rejected before any HR query runs.
    if (($_SESSION['role'] ?? '') !== 'manager') {
        http_response_code(403);
        exit('HR access only / Chỉ dành cho HR.');
    }
    require_once __DIR__ . '/users.php';
    // The id and username must still match a real hr_users row.
    $check = auth_database()->prepare('SELECT id FROM hr_users WHERE id = ? AND username = ?');
    $check->execute([$_SESSION['user_id'], $_SESSION['username']]);
    if (!$check->fetch()) {
        unset($_SESSION['user_id'], $_SESSION['role']);
        header('Location: hr_login.php'); exit;
    }
}
