<?php
require_once __DIR__ . '/../settings.php';
/** Opens the shared connection and makes sure the login table and marker account exist. */
function auth_database(): PDO
{
    $db = database_connection();
    $db->exec("CREATE TABLE IF NOT EXISTS users (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password_hash VARCHAR(255) NOT NULL,
        role ENUM('applicant','manager') NOT NULL DEFAULT 'applicant',
        failed_attempts INT UNSIGNED NOT NULL DEFAULT 0,
        locked_until DATETIME NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    if (!$db->query("SHOW COLUMNS FROM users LIKE 'role'")->fetch()) {
        $db->exec("ALTER TABLE users ADD role ENUM('applicant','manager') NOT NULL DEFAULT 'applicant'");
    }
    // The marker signs in as admin / admin. The password is stored only as a hash.
    $existing = $db->prepare('SELECT id, password_hash, role FROM users WHERE username = ?');
    $existing->execute(['admin']);
    $admin = $existing->fetch();
    if (!$admin) {
        $insert = $db->prepare("INSERT INTO users (username, password_hash, role) VALUES ('admin', ?, 'manager') ON DUPLICATE KEY UPDATE username = username");
        $insert->execute([password_hash('admin', PASSWORD_DEFAULT)]);
    }
    return $db;
}
/** Serialize attempts for a user so concurrent requests cannot bypass lockout. */
function auth_verify(PDO $db, string $username, string $password): ?array
{
    $db->beginTransaction();
    try {
        $stmt = $db->prepare('SELECT *, (locked_until > NOW()) AS locked FROM users WHERE username = ? FOR UPDATE');
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        // A dummy hash keeps nonexistent-user checks comparable in cost.
        $hash = $user ? $user['password_hash'] : '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.';
        $matches = password_verify($password, $hash);
        if (!$user || $user['locked']) {
            $db->commit();
            return null;
        }
        if (!$matches) {
            $attempts = $user['locked_until'] !== null ? 1 : (int) $user['failed_attempts'] + 1;
            $update = $db->prepare('UPDATE users SET failed_attempts = ?, locked_until = CASE WHEN ? >= 5 THEN DATE_ADD(NOW(), INTERVAL 5 MINUTE) ELSE NULL END WHERE id = ?');
            $update->execute([$attempts, $attempts, $user['id']]);
            $db->commit();
            return null;
        }
        $hash = password_needs_rehash($user['password_hash'], PASSWORD_DEFAULT) ? password_hash($password, PASSWORD_DEFAULT) : $user['password_hash'];
        $update = $db->prepare('UPDATE users SET failed_attempts = 0, locked_until = NULL, password_hash = ? WHERE id = ?');
        $update->execute([$hash, $user['id']]);
        $db->commit();
        return ['id'=>$user['id'], 'username'=>$user['username'], 'role'=>$user['role'] ?? 'applicant'];
    } catch (Throwable $error) {
        if ($db->inTransaction()) $db->rollBack();
        throw $error;
    }
}
