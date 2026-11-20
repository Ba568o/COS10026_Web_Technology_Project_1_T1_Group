<?php
require_once __DIR__ . '/auth/session.php';
require_once __DIR__ . '/auth/registration.php';
auth_start();
header('Cache-Control: no-store');
if (auth_logged_in()) { header('Location: ' . (($_SESSION['role'] ?? '') === 'manager' ? 'manage.php' : 'jobs.php')); exit; }
$errors = [];
$username = is_string($_POST['username'] ?? null) ? trim($_POST['username']) : '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = registration_errors($username, $_POST['password'] ?? null, $_POST['confirmation'] ?? null);
    if (!auth_valid_token($_POST['csrf_token'] ?? null)) $errors[] = 'Form expired. Please try again. / Vui lòng thử lại.';
    if (!$errors) {
        try {
            register_applicant(auth_database(), $username, $_POST['password']);
            $_SESSION['signup_success'] = true;
            header('Location: login.php', true, 303); exit;
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') $errors[] = 'Username unavailable. / Tên đăng nhập đã được sử dụng.';
            else { error_log($e->getMessage()); $errors[] = 'Registration unavailable. Try again later. / Chưa thể tạo tài khoản, vui lòng thử lại sau.'; }
        }
    }
}
$pageTitle = 'Create account - T1'; $pageStylesheet = 'login.css'; $bodyClass = 'login-page'; $currentPage = 'signup.php';
include __DIR__ . '/header.inc'; include __DIR__ . '/nav.inc';
?>
<main id="main-content" class="login-layout">
<section class="login-intro"><p class="login-eyebrow">YOUR FUTURE AT T1</p><h1>Create your account.<br>Tạo tài khoản.</h1><p>Save your CV and find your next role.<br>Lưu CV và tìm công việc phù hợp.</p></section>
<section class="login-card"><h2>Sign up / Đăng ký</h2>
<?php if ($errors): ?><div role="alert" class="login-error"><?php foreach ($errors as $error): ?><p><?= auth_escape($error) ?></p><?php endforeach; ?></div><?php endif; ?>
<form method="post" action="signup.php" novalidate>
<input type="hidden" name="csrf_token" value="<?= auth_escape(auth_token()) ?>">
<label for="username">Username / Tên đăng nhập</label><input id="username" name="username" autocomplete="username" value="<?= auth_escape($username) ?>">
<label for="password">Password / Mật khẩu</label><input type="password" id="password" name="password" autocomplete="new-password">
<label for="confirmation">Confirm password / Nhập lại mật khẩu</label><input type="password" id="confirmation" name="confirmation" autocomplete="new-password">
<button>Create account / Tạo tài khoản</button></form>
<p><a href="login.php">Already registered? Sign in / Đăng nhập</a></p></section></main>
<?php include __DIR__ . '/footer.inc'; ?>
