<?php
require_once __DIR__ . '/../settings.php';
/** Opens MySQL and creates the user and HR tables if they are missing. */
function auth_database(): PDO
{
    $db = database_connection();
    // Create the applicant table on the first call.
    $db->exec("CREATE TABLE IF NOT EXISTS users (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password_hash VARCHAR(255) NOT NULL,
        failed_attempts INT UNSIGNED NOT NULL DEFAULT 0,
        locked_until DATETIME NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    // Users are applicants only. Drop the old role column when it is still there.
    if ($db->query("SHOW COLUMNS FROM users LIKE 'role'")->fetch()) {
        $db->exec('ALTER TABLE users DROP COLUMN role');
    }
    // HR accounts live in their own table and are not created by signup.
    $db->exec("CREATE TABLE IF NOT EXISTS hr_users (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password_hash VARCHAR(255) NOT NULL,
        failed_attempts INT UNSIGNED NOT NULL DEFAULT 0,
        locked_until DATETIME NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    return $db;
}
/** Checks a password against the stored hash. User and HR use separate tables. */
function auth_verify(PDO $db, string $username, string $password, string $portal = 'user'): ?array
{
    // Each portal checks only its own table.
    $table = $portal === 'hr' ? 'hr_users' : 'users';
    $stmt = $db->prepare("SELECT id, username, password_hash FROM $table WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    // password_verify compares the typed password with the stored hash.
    if (!$user || !password_verify($password, $user['password_hash'])) {
        return null;
    }
    return ['id' => $user['id'], 'username' => $user['username'], 'role' => $portal === 'hr' ? 'manager' : 'applicant'];
}
