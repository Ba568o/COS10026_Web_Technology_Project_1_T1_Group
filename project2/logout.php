<?php
require_once __DIR__ . '/auth/session.php';
auth_start();
header('Cache-Control: no-store');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}
if (!auth_valid_token($_POST['csrf_token'] ?? null)) {
    http_response_code(403);
    exit('Invalid logout request. Please return to login.php.');
}
$_SESSION = [];
$params = session_get_cookie_params();
setcookie(session_name(), '', ['expires'=>time() - 42000, 'path'=>$params['path'],
    'domain'=>$params['domain'], 'secure'=>$params['secure'], 'httponly'=>$params['httponly'],
    'samesite'=>$params['samesite'] ?? 'Lax']);
session_destroy();
header('Location: login.php', true, 303);
exit;
