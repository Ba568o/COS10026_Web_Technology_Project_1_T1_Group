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
    if (!$errors) {
        try {
            register_applicant(auth_database(), $username, $_POST['password']);
            $_SESSION['signup_success'] = true;
            header('Location: user_login.php', true, 303); exit;
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
<section class="login-intro" aria-labelledby="signup-heading">
<p class="login-eyebrow"><span class="language-en">YOUR FUTURE AT T1</span><span class="language-vi">TƯƠNG LAI CỦA BẠN TẠI T1</span></p>
<h1 id="signup-heading"><span class="language-en">Create your account.</span><span class="language-vi">Tạo tài khoản.</span></h1>
<p><span class="language-en">Save your details and come back to the roles that fit you.</span><span class="language-vi">Lưu thông tin và quay lại với những vị trí phù hợp.</span></p>
</section>
<section class="login-card" aria-labelledby="signup-form-heading">
<div class="login-card-top"><span class="login-lock" aria-hidden="true"><i class="fa-solid fa-user-plus"></i></span><span class="language-en">NEW ACCOUNT</span><span class="language-vi">TÀI KHOẢN MỚI</span></div>
<h2 id="signup-form-heading"><span class="language-en">Sign up</span><span class="language-vi">Đăng ký</span></h2>
<p class="login-description"><span class="language-en">Username: 3–50 characters, starting with a letter. Password: more than 8 characters.</span><span class="language-vi">Tên đăng nhập: 3–50 ký tự, bắt đầu bằng chữ. Mật khẩu dài hơn 8 ký tự.</span></p>
<?php if ($errors): ?><div role="alert" class="login-error"><?php foreach ($errors as $error): ?><p><?= auth_language_pair($error) ?></p><?php endforeach; ?></div><?php endif; ?>
<form method="post" action="signup.php" novalidate>
<label for="username"><span class="language-en">Username</span><span class="language-vi">Tên đăng nhập</span></label>
<input id="username" name="username" autocomplete="username" autocapitalize="none" spellcheck="false" value="<?= auth_escape($username) ?>">
<label for="password"><span class="language-en">Password</span><span class="language-vi">Mật khẩu</span></label>
<input type="password" id="password" name="password" autocomplete="new-password">
<label for="confirmation"><span class="language-en">Confirm password</span><span class="language-vi">Nhập lại mật khẩu</span></label>
<input type="password" id="confirmation" name="confirmation" autocomplete="new-password">
<button type="submit"><span class="language-en">Create account</span><span class="language-vi">Tạo tài khoản</span></button>
</form>
<p class="login-switch"><a href="user_login.php"><span class="language-en">Already registered? Sign in</span><span class="language-vi">Đã có tài khoản? Đăng nhập</span></a></p>
</section></main>
<?php include __DIR__ . '/footer.inc'; ?>
