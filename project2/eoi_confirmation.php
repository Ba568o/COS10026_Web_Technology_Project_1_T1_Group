<?php
session_start();
require_once __DIR__ . '/eoi_helpers.php';
$receipt = $_SESSION['eoi_receipt'] ?? null;
if (!$receipt) {
    header('Location: apply.php');
    exit;
}
$pageTitle = 'Application received - T1 Health & Wellness';
$pageStylesheet = 'apply.css';
$bodyClass = 'apply-page';
$currentPage = 'apply.php';
include __DIR__ . '/header.inc';
include __DIR__ . '/nav.inc';
?>
<main id="main-content" class="container application-feedback">
    <h1>Application received / Đã nhận đơn ứng tuyển</h1>
    <p>Thank you! Your application has been saved. / Đơn của bạn đã được lưu thành công.</p>
    <p>Your EOInumber / Mã đơn: <strong><?= eoi_escape((string) $receipt['number']) ?></strong></p>
    <p>Job reference / Mã công việc: <strong><?= eoi_escape($receipt['job']) ?></strong></p>
    <p>Status / Trạng thái: <strong>New</strong></p>
    <p>Keep this number for your records. / Vui lòng lưu lại mã đơn này.</p>
    <a class="btn btn-submit" href="jobs.php">View jobs / Xem công việc</a>
</main>
<?php include __DIR__ . '/footer.inc'; ?>
