<?php
require_once __DIR__ . '/auth/session.php';
auth_start();
header('Cache-Control: no-store');
$registered = !empty($_SESSION['signup_success']);
unset($_SESSION['signup_success']);
$error = '';
$username = '';
$loggedIn = auth_logged_in();
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$loggedIn) {
    $username = is_string($_POST['username'] ?? null) ? trim($_POST['username']) : '';
    // Passwords are checked exactly as entered; do not trim or HTML-escape them.
    $passwordInput = is_string($_POST['password'] ?? null) ? $_POST['password'] : '';
    if (!auth_valid_token($_POST['csrf_token'] ?? null)) {
        $error = 'Your form expired. Please try again. / Biểu mẫu đã hết hạn. Vui lòng thử lại.';
    } elseif ($username === '' || strlen($username) > 50 || $passwordInput === '' || strlen($passwordInput) > 4096) {
        $error = 'Enter a valid username and password. / Vui lòng nhập tên đăng nhập và mật khẩu hợp lệ.';
    } else {
        try {
            require_once __DIR__ . '/auth/users.php';
            $user = auth_verify(auth_database(), $username, $passwordInput);
            if ($user) {
                session_regenerate_id(true);
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['last_active'] = time();
                $_SESSION['auth_token'] = bin2hex(random_bytes(32));
                $home = $user['role'] === 'manager' ? 'manage.php' : 'jobs.php';
                $destination = $_SESSION['return_to'] ?? $home;
                $allowed = ['index.php', 'jobs.php', 'apply.php', 'about.php'];
                if ($user['role'] === 'manager') $allowed[] = 'manage.php';
                if (!in_array($destination, $allowed, true)) $destination = $home;
                unset($_SESSION['return_to']);
                header('Location: ' . $destination, true, 303);
                exit;
            }
            $error = 'Incorrect credentials or temporarily locked account. Please try again later. / Sai thông tin hoặc tài khoản tạm khóa. Vui lòng thử lại sau.';
        } catch (PDOException $exception) {
            error_log('Applicant login database error: ' . $exception->getMessage());
            http_response_code(503);
            $error = 'Login is temporarily unavailable. Please try again later. / Đăng nhập tạm thời không khả dụng. Vui lòng thử lại sau.';
        }
    }
}
$pageTitle = 'Applicant Login - T1 Health & Wellness';
$pageDescription = 'Sign in to your T1 job seeker account.';
$pageStylesheet = 'login.css';
$bodyClass = 'login-page';
$currentPage = 'login.php';
include __DIR__ . '/header.inc';
include __DIR__ . '/nav.inc';
?>
<main id="main-content" class="login-layout">
    <section class="login-intro" aria-labelledby="login-heading">
        <p class="login-eyebrow"><span class="language-en">PEOPLE BEHIND BETTER CARE</span><span class="language-vi">CON NGƯỜI LÀM NÊN SỰ KHÁC BIỆT</span></p>
        <h1 id="login-heading"><span class="language-en">Welcome back.</span><span class="language-vi">Chào mừng trở lại.</span></h1>
        <p><span class="language-en">Sign in to your job seeker account.</span><span class="language-vi">Đăng nhập vào tài khoản tìm việc của bạn.</span></p>
        <div class="login-team-note">
            <img src="images/logo.png" alt="T1" width="72" height="40">
            <div>
                <strong><span class="language-en">Great care starts with great people.</span><span class="language-vi">Chăm sóc tận tâm bắt đầu từ con người.</span></strong>
                <p><span class="language-en">Together, we build a healthier tomorrow.</span><span class="language-vi">Cùng nhau xây dựng một ngày mai khỏe mạnh hơn.</span></p>
            </div>
        </div>
        <a class="login-jobs-link" href="jobs.php"><span class="language-en">Looking for a role? Explore jobs →</span><span class="language-vi">Bạn đang tìm việc? Xem các vị trí →</span></a>
    </section>
    <section class="login-card" aria-labelledby="form-heading">
        <div class="login-card-top"><span class="login-lock" aria-hidden="true"><i class="fa-solid fa-lock"></i></span><span class="language-en">CAREER PORTAL</span><span class="language-vi">CỔNG VIỆC LÀM</span></div>
        <?php if ($loggedIn): ?>
            <h2 id="form-heading"><span class="language-en">You're signed in</span><span class="language-vi">Đăng nhập thành công</span></h2>
            <p><a href="apply.php">Apply with CV / Ứng tuyển kèm CV</a> · <a href="jobs.php">Find jobs / Tìm việc</a><?php if (($_SESSION['role'] ?? '') === 'manager'): ?> · <a href="manage.php">Manage EOIs</a><?php endif; ?></p>
            <p><?= auth_escape((string) $_SESSION['username']) ?></p>
            <p><span class="language-en">Your session is active.</span><span class="language-vi">Phiên đăng nhập đang hoạt động.</span></p>
            <form action="logout.php" method="post">
                <input type="hidden" name="csrf_token" value="<?= auth_escape(auth_token()) ?>">
                <button type="submit"><span class="language-en">Sign out</span><span class="language-vi">Đăng xuất</span></button>
            </form>
        <?php else: ?>
            <h2 id="form-heading"><span class="language-en">Sign in</span><span class="language-vi">Đăng nhập</span></h2>
            <p class="login-description"><span class="language-en">Welcome to your workspace. Enter your details to continue.</span><span class="language-vi">Chào mừng đến không gian làm việc. Nhập thông tin để tiếp tục.</span></p>
            <?php if ($registered): ?><p role="status">Account created. Please sign in. / Đã tạo tài khoản. Vui lòng đăng nhập.</p><?php endif; ?>
            <p><a href="signup.php">New here? Create an account / Tạo tài khoản mới</a></p>
            <?php if ($error): ?><p class="login-error" id="login-error" role="alert"><?= auth_escape($error) ?></p><?php endif; ?>
            <form action="login.php" method="post" novalidate <?= $error ? 'aria-describedby="login-error"' : '' ?>>
                <input type="hidden" name="csrf_token" value="<?= auth_escape(auth_token()) ?>">
                <label for="username"><span class="language-en">Username</span><span class="language-vi">Tên đăng nhập</span></label>
                <input id="username" name="username" type="text" autocomplete="username" autocapitalize="none" spellcheck="false" value="<?= auth_escape($username) ?>" aria-required="true">
                <label for="password"><span class="language-en">Password</span><span class="language-vi">Mật khẩu</span></label>
                <input id="password" name="password" type="password" autocomplete="current-password" aria-required="true">
                <button type="submit"><span class="language-en">Sign in</span><span class="language-vi">Đăng nhập</span></button>
            </form>
        <?php endif; ?>
        <p class="login-footnote"><i class="fa-solid fa-shield-halved" aria-hidden="true"></i> <span class="language-en">Find your next opportunity with T1</span><span class="language-vi">Tìm cơ hội tiếp theo cùng T1</span></p>
    </section>
</main>
<?php include __DIR__ . '/footer.inc'; ?>
