<?php
require_once __DIR__ . '/auth/session.php';
auth_start();
header('Cache-Control: no-store');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}
$_SESSION = [];
$params = session_get_cookie_params();
setcookie(session_name(), '', ['expires'=>time() - 42000, 'path'=>$params['path'],
    'domain'=>$params['domain'], 'secure'=>$params['secure'], 'httponly'=>$params['httponly'],
    'samesite'=>$params['samesite'] ?? 'Lax']);
session_destroy();
header('Location: login.php', true, 303);
exit;
