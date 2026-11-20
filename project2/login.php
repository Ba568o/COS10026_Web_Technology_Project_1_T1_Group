<?php
require_once __DIR__ . '/auth/session.php';
auth_start();
$pageTitle = 'Login - T1'; $pageStylesheet = 'login.css'; $bodyClass = 'login-page'; $currentPage = 'login.php';
include __DIR__ . '/header.inc'; include __DIR__ . '/nav.inc';
?>
<main id="main-content" class="login-layout">
<section class="login-intro" aria-labelledby="chooser-heading">
<p class="login-eyebrow"><span class="language-en">T1 CAREERS</span><span class="language-vi">TUYỂN DỤNG T1</span></p>
<h1 id="chooser-heading"><span class="language-en">Welcome back.</span><span class="language-vi">Chào mừng trở lại.</span></h1>
<p><span class="language-en">Choose the account that matches what you came here to do.</span><span class="language-vi">Chọn loại tài khoản phù hợp với việc bạn cần làm.</span></p>
<div class="login-team-note">
<img src="images/logo.png" alt="T1" width="72" height="40">
<div>
<strong><span class="language-en">One team. Two ways in.</span><span class="language-vi">Một đội ngũ. Hai cách vào.</span></strong>
<p><span class="language-en">Job seekers can create an account. HR uses an account that is already assigned.</span><span class="language-vi">Người tìm việc có thể tạo tài khoản. HR dùng tài khoản đã được cấp.</span></p>
</div>
</div>
</section>
<section class="login-card" aria-labelledby="portal-heading">
<div class="login-card-top"><span class="login-lock" aria-hidden="true"><i class="fa-solid fa-right-to-bracket"></i></span><span class="language-en">SIGN IN</span><span class="language-vi">ĐĂNG NHẬP</span></div>
<?php if (auth_logged_in()): ?>
<h2 id="portal-heading"><span class="language-en">You're signed in</span><span class="language-vi">Bạn đang đăng nhập</span></h2>
<p class="login-user"><?= auth_escape($_SESSION['username']) ?></p>
<p class="login-actions"><a href="<?= $_SESSION['role'] === 'manager' ? 'manage.php' : 'index.php' ?>"><span class="language-en">Continue</span><span class="language-vi">Tiếp tục</span> <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a></p>
<form action="logout.php" method="post"><button type="submit"><span class="language-en">Log out</span><span class="language-vi">Đăng xuất</span></button></form>
<?php else: ?>
<h2 id="portal-heading"><span class="language-en">Choose an account</span><span class="language-vi">Chọn tài khoản</span></h2>
<div class="portal-choices">
<a class="portal-choice" href="user_login.php">
<span class="portal-icon" aria-hidden="true"><i class="fa-solid fa-user"></i></span>
<span class="portal-copy"><strong><span class="language-en">Job seeker</span><span class="language-vi">Người tìm việc</span></strong><span><span class="language-en">Sign in or create an account, then browse roles as usual.</span><span class="language-vi">Đăng nhập hoặc tạo tài khoản, rồi xem việc như bình thường.</span></span></span>
<i class="fa-solid fa-arrow-right portal-arrow" aria-hidden="true"></i>
</a>
<a class="portal-choice portal-choice-hr" href="hr_login.php">
<span class="portal-icon" aria-hidden="true"><i class="fa-solid fa-briefcase"></i></span>
<span class="portal-copy"><strong><span class="language-en">HR</span><span class="language-vi">Nhà tuyển dụng</span></strong><span><span class="language-en">For assigned accounts. Review applications and open CVs.</span><span class="language-vi">Dành cho tài khoản được cấp. Xem hồ sơ và mở CV.</span></span></span>
<i class="fa-solid fa-arrow-right portal-arrow" aria-hidden="true"></i>
</a>
</div>
<?php endif; ?>
</section>
</main>
<?php include __DIR__ . '/footer.inc'; ?>
