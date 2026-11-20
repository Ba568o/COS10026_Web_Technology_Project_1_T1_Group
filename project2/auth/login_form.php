<?php
require_once dirname(__DIR__) . '/auth/session.php';
auth_start();
header('Cache-Control: no-store');
$portal = $portal ?? 'user';
$loginPath = $portal === 'hr' ? 'hr_login.php' : 'user_login.php';
$registered = !empty($_SESSION['signup_success']);
unset($_SESSION['signup_success']);
$error = '';
$username = '';
$loggedIn = auth_logged_in();
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$loggedIn) {
    $username = is_string($_POST['username'] ?? null) ? trim($_POST['username']) : '';
    // Passwords are checked exactly as entered; do not trim or HTML-escape them.
    $passwordInput = is_string($_POST['password'] ?? null) ? $_POST['password'] : '';
    if ($username === '' || strlen($username) > 50 || $passwordInput === '' || strlen($passwordInput) > 4096) {
        $error = 'Enter a valid username and password. / Vui lòng nhập tên đăng nhập và mật khẩu hợp lệ.';
    } else {
        try {
            require_once dirname(__DIR__) . '/auth/users.php';
            $user = auth_verify(auth_database(), $username, $passwordInput, $portal);
            if ($user) {
                session_regenerate_id(true);
                $_SESSION['auth_version'] = 2;
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['last_active'] = time();
                $home = $user['role'] === 'manager' ? 'manage.php' : 'index.php';
                $destination = $_SESSION['return_to'] ?? $home;
                $allowed = ['index.php', 'jobs.php', 'apply.php', 'about.php'];
                if ($user['role'] === 'manager') $allowed = array_merge($allowed, ['manage.php']);
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
$isHr = $portal === 'hr';
$pageTitle = $isHr ? 'HR Login - T1 Health & Wellness' : 'Applicant Login - T1 Health & Wellness';
$pageDescription = $isHr ? 'Sign in with an assigned HR account.' : 'Sign in to your T1 job seeker account.';
$pageStylesheet = 'login.css';
$bodyClass = 'login-page';
$currentPage = 'login.php';
include dirname(__DIR__) . '/header.inc';
include dirname(__DIR__) . '/nav.inc';
?>
<main id="main-content" class="login-layout">
    <section class="login-intro" aria-labelledby="login-heading">
        <?php if ($isHr): ?>
        <p class="login-eyebrow"><span class="language-en">HR WORKSPACE</span><span class="language-vi">KHU VỰC NHÂN SỰ</span></p>
        <h1 id="login-heading"><span class="language-en">HR sign in.</span><span class="language-vi">Đăng nhập HR.</span></h1>
        <p><span class="language-en">Use the account assigned to you. HR registration is not available.</span><span class="language-vi">Dùng tài khoản được cấp. HR không có phần đăng ký.</span></p>
        <?php else: ?>
        <p class="login-eyebrow"><span class="language-en">PEOPLE BEHIND BETTER CARE</span><span class="language-vi">CON NGƯỜI LÀM NÊN SỰ KHÁC BIỆT</span></p>
        <h1 id="login-heading"><span class="language-en">Welcome back.</span><span class="language-vi">Chào mừng trở lại.</span></h1>
        <p><span class="language-en">Sign in to your job seeker account.</span><span class="language-vi">Đăng nhập vào tài khoản tìm việc của bạn.</span></p>
        <?php endif; ?>
        <div class="login-team-note">
            <img src="images/logo.png" alt="T1" width="72" height="40">
            <div>
                <strong><span class="language-en">Great care starts with great people.</span><span class="language-vi">Chăm sóc tận tâm bắt đầu từ con người.</span></strong>
                <p><span class="language-en">Together, we build a healthier tomorrow.</span><span class="language-vi">Cùng nhau xây dựng một ngày mai khỏe mạnh hơn.</span></p>
            </div>
        </div>
        <?php if ($isHr): ?>
        <a class="login-jobs-link" href="login.php"><span class="language-en">Choose a different account →</span><span class="language-vi">Chọn loại tài khoản khác →</span></a>
        <?php else: ?>
        <a class="login-jobs-link" href="jobs.php"><span class="language-en">Looking for a role? Explore jobs →</span><span class="language-vi">Bạn đang tìm việc? Xem các vị trí →</span></a>
        <?php endif; ?>
    </section>
    <section class="login-card" aria-labelledby="form-heading">
        <div class="login-card-top"><span class="login-lock" aria-hidden="true"><i class="fa-solid fa-lock"></i></span><span class="language-en"><?= $isHr ? 'HR PORTAL' : 'CAREER PORTAL' ?></span><span class="language-vi"><?= $isHr ? 'CỔNG NHÂN SỰ' : 'CỔNG VIỆC LÀM' ?></span></div>
        <?php if ($loggedIn && $isHr && ($_SESSION['role'] ?? '') !== 'manager'): ?>
            <h2 id="form-heading"><span class="language-en">Job seeker session</span><span class="language-vi">Đang đăng nhập người tìm việc</span></h2>
            <p class="login-description"><span class="language-en">Sign out before using an HR account.</span><span class="language-vi">Hãy đăng xuất trước khi dùng tài khoản HR.</span></p>
            <p class="login-user"><?= auth_escape((string) $_SESSION['username']) ?></p>
            <form action="logout.php" method="post">
                <button type="submit"><span class="language-en">Sign out</span><span class="language-vi">Đăng xuất</span></button>
            </form>
        <?php elseif ($loggedIn && !$isHr && ($_SESSION['role'] ?? '') === 'manager'): ?>
            <h2 id="form-heading"><span class="language-en">You're signed in as HR</span><span class="language-vi">Đang đăng nhập HR</span></h2>
            <p class="login-actions"><a href="manage.php"><span class="language-en">EOIs and CVs</span><span class="language-vi">Hồ sơ và CV</span></a></p>
            <p class="login-user"><?= auth_escape((string) $_SESSION['username']) ?></p>
            <form action="logout.php" method="post">
                <button type="submit"><span class="language-en">Sign out</span><span class="language-vi">Đăng xuất</span></button>
            </form>
        <?php elseif ($loggedIn): ?>
            <h2 id="form-heading"><span class="language-en">You're signed in</span><span class="language-vi">Đăng nhập thành công</span></h2>
            <p class="login-actions"><?php if (($_SESSION['role'] ?? '') === 'manager'): ?><a href="manage.php"><span class="language-en">EOIs and CVs</span><span class="language-vi">Hồ sơ và CV</span></a><?php else: ?><a href="jobs.php"><span class="language-en">Find jobs</span><span class="language-vi">Tìm việc</span></a><a href="apply.php"><span class="language-en">Apply with CV</span><span class="language-vi">Ứng tuyển kèm CV</span></a><?php endif; ?></p>
            <p class="login-user"><?= auth_escape((string) $_SESSION['username']) ?></p>
            <p><span class="language-en">Your session is active.</span><span class="language-vi">Phiên đăng nhập đang hoạt động.</span></p>
            <form action="logout.php" method="post">
                <button type="submit"><span class="language-en">Sign out</span><span class="language-vi">Đăng xuất</span></button>
            </form>
        <?php else: ?>
            <h2 id="form-heading"><?php if ($isHr): ?><span class="language-en">HR sign in</span><span class="language-vi">Đăng nhập HR</span><?php else: ?><span class="language-en">Job seeker sign in</span><span class="language-vi">Đăng nhập người tìm việc</span><?php endif; ?></h2>
            <p class="login-description"><?php if ($isHr): ?><span class="language-en">Enter the username and password assigned to you.</span><span class="language-vi">Nhập tên và mật khẩu đã được cấp.</span><?php else: ?><span class="language-en">Enter your details to continue.</span><span class="language-vi">Nhập thông tin để tiếp tục.</span><?php endif; ?></p>
            <?php if ($registered): ?><p class="login-success" role="status"><span class="language-en">Account created. Please sign in.</span><span class="language-vi">Đã tạo tài khoản. Vui lòng đăng nhập.</span></p><?php endif; ?>
            <?php if (!$isHr): ?><p class="login-switch"><a href="signup.php"><span class="language-en">Create an account</span><span class="language-vi">Tạo tài khoản mới</span></a></p><?php else: ?><p class="login-description"><span class="language-en">There is no registration on this page.</span><span class="language-vi">Trang này không có phần đăng ký.</span></p><?php endif; ?>
            <?php if ($error): ?><p class="login-error" id="login-error" role="alert"><?= auth_language_pair($error) ?></p><?php endif; ?>
            <form action="<?= auth_escape($loginPath) ?>" method="post" novalidate <?= $error ? 'aria-describedby="login-error"' : '' ?>>
                <label for="username"><span class="language-en">Username</span><span class="language-vi">Tên đăng nhập</span></label>
                <input id="username" name="username" type="text" autocomplete="username" autocapitalize="none" spellcheck="false" value="<?= auth_escape($username) ?>" aria-required="true">
                <label for="password"><span class="language-en">Password</span><span class="language-vi">Mật khẩu</span></label>
                <input id="password" name="password" type="password" autocomplete="current-password" aria-required="true">
                <button type="submit"><span class="language-en">Sign in</span><span class="language-vi">Đăng nhập</span></button>
            </form>
        <?php endif; ?>
        <p class="login-footnote"><i class="fa-solid fa-shield-halved" aria-hidden="true"></i> <?php if ($isHr): ?><span class="language-en">Assigned HR access only</span><span class="language-vi">Chỉ tài khoản HR được cấp</span><?php else: ?><span class="language-en">Find your next opportunity with T1</span><span class="language-vi">Tìm cơ hội tiếp theo cùng T1</span><?php endif; ?></p>
    </section>
</main>
<?php include dirname(__DIR__) . '/footer.inc'; ?>
