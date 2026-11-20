<?php
require_once __DIR__ . '/users.php';
/** Returns the signup error messages, or an empty list when the input is valid. */
function registration_errors($username, $password, $confirmation): array
{
    $errors = [];
    // Start with a letter, then letters, digits or underscore. Length is 3–50.
    if (!is_string($username) || !preg_match('/^[A-Za-z][A-Za-z0-9_]{2,49}$/D', $username)) $errors[] = 'Username: 3–50 characters, start with a letter, then letters, numbers or underscores. / Tên đăng nhập: 3–50 ký tự, bắt đầu bằng chữ, sau đó là chữ, số hoặc gạch dưới.';
    // These names belong to assigned HR accounts.
    if (is_string($username) && in_array(strtolower($username), ['admin', 't1healthhr1', 't1healthhr2', 't1healthhr3', 't1healthhr4', 't1healthhr5'], true)) $errors[] = 'Please choose another username. / Vui lòng chọn tên khác.';
    // More than 8 characters. 72 bytes is the longest password bcrypt will use.
    if (!is_string($password) || strlen($password) <= 8 || strlen($password) > 72 || str_contains($password, "\0")) $errors[] = 'Password: more than 8 characters, up to 72. / Mật khẩu: dài hơn 8 ký tự, tối đa 72.';
    if (!is_string($confirmation) || $password !== $confirmation) $errors[] = 'Passwords do not match. / Mật khẩu không khớp.';
    return $errors;
}
/** Stores a new job seeker. The password is saved as a hash, not plain text. */
function register_applicant(PDO $db, string $username, string $password): void
{
    $stmt = $db->prepare('INSERT INTO users (username, password_hash) VALUES (?, ?)');
    // Store a one-way hash. The plain password is not saved.
    $stmt->execute([$username, password_hash($password, PASSWORD_DEFAULT)]);
}
