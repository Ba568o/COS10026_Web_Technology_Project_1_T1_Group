<?php
session_start();
require_once __DIR__ . '/eoi_helpers.php';
require_once __DIR__ . '/job_matches.php';
require_once __DIR__ . '/settings.php';
$receipt = $_SESSION['eoi_receipt'] ?? null;
if (!$receipt) {
    header('Location: apply.php');
    exit;
}
$matches = ['rows' => [], 'total' => 0, 'page' => 1, 'pages' => 1, 'skill' => '', 'q' => '', 'user_skills' => job_known_skills($receipt['skills'] ?? []), 'stored' => 0, 'error' => true];
try {
    $matches = job_match_list(database_connection(), $receipt['skills'] ?? [], $_GET);
} catch (PDOException $error) {
    error_log('Job match database error: ' . $error->getMessage());
}
$labels = job_skill_labels();
$filterSkills = $matches['user_skills'] ?: array_keys($labels);
$pageTitle = 'Application received - T1 Health & Wellness';
$pageStylesheet = 'apply.css';
$bodyClass = 'apply-page';
$currentPage = 'apply.php';
include __DIR__ . '/header.inc';
include __DIR__ . '/nav.inc';
?>
<main id="main-content">
  <div class="container confirm-layout">
    <section class="application-feedback">
      <h1>Application received / Đã nhận đơn ứng tuyển</h1>
      <p>Thank you! Your application has been saved. / Đơn của bạn đã được lưu thành công.</p>
      <p>Your EOInumber / Mã đơn: <strong><?= eoi_escape((string) $receipt['number']) ?></strong></p>
      <p>Job reference / Mã công việc: <strong><?= eoi_escape((string) $receipt['job']) ?></strong></p>
      <p>Status / Trạng thái: <strong>New</strong></p>
      <p>Keep this number for your records. / Vui lòng lưu lại mã đơn này.</p>
    </section>

    <section class="match-jobs" aria-labelledby="match-heading">
      <p class="eyebrow">
        <span class="language-en">NEXT STEP</span>
        <span class="language-vi">BƯỚC TIẾP THEO</span>
      </p>
      <h2 id="match-heading">
        <span class="language-en">Roles that match your skills</span>
        <span class="language-vi">Vị trí khớp với kỹ năng của bạn</span>
      </h2>
      <p class="match-intro">
        <?php if ($matches['user_skills']): ?>
          <span class="language-en">These open roles use at least one skill you selected. The closest matches are listed first.</span>
          <span class="language-vi">Các vị trí đang tuyển dưới đây dùng ít nhất một kỹ năng bạn đã chọn. Việc khớp nhiều nhất được xếp trước.</span>
        <?php else: ?>
          <span class="language-en">You did not select a skill, so every open role is listed. Use the filter to narrow it.</span>
          <span class="language-vi">Bạn chưa chọn kỹ năng, nên mọi vị trí đang tuyển đều được liệt kê. Hãy dùng bộ lọc để thu hẹp.</span>
        <?php endif; ?>
      </p>

      <?php foreach (['en' => ['Skill', 'All of your skills', 'All open roles', 'Search', 'Role, company or city', 'Show roles'], 'vi' => ['Kỹ năng', 'Mọi kỹ năng của bạn', 'Mọi vị trí đang tuyển', 'Tìm', 'Vị trí, công ty hoặc thành phố', 'Xem việc']] as $lang => $copy): ?>
        <form class="match-filters language-<?= $lang ?>" method="get" action="eoi_confirmation.php">
          <label for="match-skill-<?= $lang ?>"><?= eoi_escape($copy[0]) ?>
            <select id="match-skill-<?= $lang ?>" name="skill">
              <option value=""><?= eoi_escape($matches['user_skills'] ? $copy[1] : $copy[2]) ?></option>
              <?php foreach ($filterSkills as $skill): ?>
                <option value="<?= eoi_escape($skill) ?>" <?= $matches['skill'] === $skill ? 'selected' : '' ?>><?= eoi_escape($labels[$skill][$lang === 'en' ? 0 : 1]) ?></option>
              <?php endforeach; ?>
            </select>
          </label>
          <label for="match-q-<?= $lang ?>"><?= eoi_escape($copy[3]) ?>
            <input id="match-q-<?= $lang ?>" name="q" maxlength="80" value="<?= eoi_escape($matches['q']) ?>" placeholder="<?= eoi_escape($copy[4]) ?>">
          </label>
          <button class="btn btn-submit" type="submit"><?= eoi_escape($copy[5]) ?></button>
        </form>
      <?php endforeach; ?>

      <?php if ($matches['error']): ?>
        <p>
          <span class="language-en">The role list could not be loaded. Please try again later.</span>
          <span class="language-vi">Không tải được danh sách vị trí. Vui lòng thử lại sau.</span>
        </p>
      <?php elseif (!$matches['rows']): ?>
        <p>
          <?php if ($matches['stored'] === 0): ?>
            <span class="language-en">No roles are stored yet.</span>
            <span class="language-vi">Chưa có vị trí nào trong cơ sở dữ liệu.</span>
          <?php else: ?>
            <span class="language-en">No open roles match this filter.</span>
            <span class="language-vi">Không có vị trí nào khớp bộ lọc này.</span>
          <?php endif; ?>
        </p>
      <?php else: ?>
        <ol class="match-list">
          <?php foreach ($matches['rows'] as $job): ?>
            <li class="match-card">
              <p class="match-ref"><?= eoi_escape($job['reference']) ?>
                <?php if (strtoupper((string) $receipt['job']) === strtoupper($job['reference'])): ?>
                  <span class="match-applied">
                    <span class="language-en">Applied</span>
                    <span class="language-vi">Đã nộp</span>
                  </span>
                <?php endif; ?>
              </p>
              <h3>
                <span class="language-en"><?= eoi_escape($job['title'][0]) ?></span>
                <span class="language-vi"><?= eoi_escape($job['title'][1]) ?></span>
              </h3>
              <p class="match-meta">
                <span class="language-en"><?= eoi_escape($job['company'][0]) ?> · <?= eoi_escape($job['place'][0]) ?></span>
                <span class="language-vi"><?= eoi_escape($job['company'][1]) ?> · <?= eoi_escape($job['place'][1]) ?></span>
              </p>
              <ul class="match-skills">
                <?php foreach ($job['overlap'] as $skill): ?>
                  <?php $label = $labels[$skill] ?? [$skill, $skill]; ?>
                  <li>
                    <span class="language-en"><?= eoi_escape($label[0]) ?></span>
                    <span class="language-vi"><?= eoi_escape($label[1]) ?></span>
                  </li>
                <?php endforeach; ?>
              </ul>
              <a class="btn btn-submit" href="apply.php?job=<?= eoi_escape($job['reference']) ?>#skip-to-application">
                <span class="language-en">Apply for this role</span>
                <span class="language-vi">Ứng tuyển vị trí này</span>
              </a>
            </li>
          <?php endforeach; ?>
        </ol>
      <?php endif; ?>

      <nav class="match-pages" aria-label="Matching roles pages">
        <?php if ($matches['page'] > 1): ?>
          <a href="<?= eoi_escape(job_match_href($matches, $matches['page'] - 1)) ?>">
            <span class="language-en">Previous</span>
            <span class="language-vi">Trước</span>
          </a>
        <?php endif; ?>
        <span>
          <span class="language-en">Page <?= (int) $matches['page'] ?> of <?= (int) $matches['pages'] ?> · <?= (int) $matches['total'] ?> roles</span>
          <span class="language-vi">Trang <?= (int) $matches['page'] ?> / <?= (int) $matches['pages'] ?> · <?= (int) $matches['total'] ?> vị trí</span>
        </span>
        <?php if ($matches['page'] < $matches['pages']): ?>
          <a href="<?= eoi_escape(job_match_href($matches, $matches['page'] + 1)) ?>">
            <span class="language-en">Next</span>
            <span class="language-vi">Sau</span>
          </a>
        <?php endif; ?>
      </nav>
    </section>
  </div>
</main>
<?php include __DIR__ . '/footer.inc'; ?>
