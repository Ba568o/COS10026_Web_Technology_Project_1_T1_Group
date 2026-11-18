<?php
require_once __DIR__ . '/users.php';
function registration_errors($username, $password, $confirmation): array
{
    $errors = [];
    if (!is_string($username) || !preg_match('/^[A-Za-z0-9_]{3,50}$/D', $username)) $errors[] = 'Username: 3–50 letters, numbers or underscores. / Tên đăng nhập: 3–50 chữ, số hoặc dấu gạch dưới.';
    if (is_string($username) && strtolower($username) === 'admin') $errors[] = 'Please choose another username. / Vui lòng chọn tên khác.';
    if (!is_string($password) || strlen($password) < 8 || strlen($password) > 72 || str_contains($password, "\0")) $errors[] = 'Password: 8–72 bytes. / Mật khẩu: từ 8 đến 72 byte.';
    if (!is_string($confirmation) || $password !== $confirmation) $errors[] = 'Passwords do not match. / Mật khẩu không khớp.';
    return $errors;
}
function register_applicant(PDO $db, string $username, string $password): void
{
    $stmt = $db->prepare('INSERT INTO users (username, password_hash) VALUES (?, ?)');
    $stmt->execute([$username, password_hash($password, PASSWORD_DEFAULT)]);
}
