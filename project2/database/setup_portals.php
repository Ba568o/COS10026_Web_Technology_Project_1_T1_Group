<?php
// Run once from CLI: php project2/database/setup_portals.php
// A browser request gets 404 so this script cannot be opened from the website.
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require_once __DIR__ . '/../auth/users.php';
require_once __DIR__ . '/../eoi_helpers.php';
$db = auth_database();
// Assigned HR logins. The password matches the username and is stored as a hash.
$hrAccounts = [
    't1healthhr1' => 't1healthhr1',
    't1healthhr2' => 't1healthhr2',
    't1healthhr3' => 't1healthhr3',
    't1healthhr4' => 't1healthhr4',
    't1healthhr5' => 't1healthhr5',
];
$findHr = $db->prepare('SELECT id FROM hr_users WHERE username = ?');
$insertHr = $db->prepare('INSERT INTO hr_users (username, password_hash) VALUES (?, ?)');
$renameHr = $db->prepare('UPDATE hr_users SET username = ?, password_hash = ? WHERE username = ?');
foreach ($hrAccounts as $username => $password) {
    $shortName = 'hr' . substr($username, -1);
    $findHr->execute([$shortName]);
    if ($findHr->fetch()) $renameHr->execute([$username, password_hash($password, PASSWORD_DEFAULT), $shortName]);
    $findHr->execute([$username]);
    if (!$findHr->fetch()) $insertHr->execute([$username, password_hash($password, PASSWORD_DEFAULT)]);
}
eoi_ensure_table($db);
// Seed the sample applications once. Running setup again does not add them a second time.
$db->exec('CREATE TABLE IF NOT EXISTS app_migrations (name VARCHAR(100) PRIMARY KEY)');
if (!$db->query("SELECT name FROM app_migrations WHERE name='eoi_seed'")->fetch()) {
    $sampleCv = "%PDF-1.1\n1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj\n2 0 obj<</Type/Pages/Count 0>>endobj\ntrailer<</Root 1 0 R>>\n%%EOF";
    $insertEoi = $db->prepare('INSERT INTO eoi (first_name, last_name, date_of_birth, gender, email, phone, street_address, suburb, state, postcode, job_reference, position, skills, other_skills, cv_name, cv_mime, cv, terms, status) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
    foreach (require __DIR__ . '/eoi_seed.php' as $application) {
        $insertEoi->execute([
            $application['first_name'], $application['last_name'], $application['date_of_birth'], $application['gender'],
            $application['email'], $application['phone'], $application['street_address'], $application['suburb'],
            $application['state'], $application['postcode'], $application['job_reference'], $application['position'],
            $application['skills'], $application['other_skills'], $application['cv_name'], 'application/pdf', $sampleCv, 1,
            $application['status'],
        ]);
    }
    $db->exec("INSERT INTO app_migrations VALUES ('eoi_seed')");
}
echo "Portal accounts and applications ready.\n";
