<?php
require_once __DIR__ . '/auth/session.php';
require_once __DIR__ . '/includes/lang.php';
require_once __DIR__ . '/database/jobs_helpers.php';

if (!headers_sent()) {
    header('Vary: Cookie');
    header('Cache-Control: private, no-cache');
}

$isDark = (($_COOKIE['t1_theme'] ?? '') === 'dark');

$pageTitle = t1_text('T1 Health & Wellness - Job Opportunities', 'T1 Health & Wellness - Cơ hội tuyển dụng');
$pageDescription = t1_text(
    'Explore open digital healthcare roles at T1 Health & Wellness.',
    'Khám phá các vị trí chăm sóc sức khỏe số đang tuyển tại T1 Health & Wellness.'
);
$bodyClass = $isDark ? 'jobs-page dark-mode' : 'jobs-page light-mode';
$pageStylesheet = 'jobs.css';
$bodyId = 'top';
$currentPage = basename($_SERVER['SCRIPT_NAME'] ?? 'jobs.php');

$filterGroups = [
    'salary' => [
        'legend' => ['en' => 'Salary', 'vi' => 'Lương'],
        'id_prefix' => 'salary-',
        'legacy' => null, // legacy query param name for backward compatibility
        'options' => [
            'all' => ['All salaries', 'Tất cả mức lương'],
            'under-1200' => ['Under $1,200', 'Dưới $1,200'],
            '400-800' => ['$400 - $800', '$400 - $800'],
            '800-1200' => ['$800 - under $1,200', '$800 - dưới $1,200'],
            '1200-2000' => ['$1,200 - $2,000', '$1,200 - $2,000'],
            'over-2000' => ['Over $2,000', 'Trên $2,000'],
            'negotiable' => ['Negotiable', 'Thỏa thuận'],
            'other' => ['Other (custom USD range)', 'Khác (tự chọn khoảng USD)'],
        ],
    ],
    'experience' => [
        'legend' => ['en' => 'Experience', 'vi' => 'Kinh nghiệm'],
        'id_prefix' => 'experience-',
        'legacy' => null,
        'options' => [
            'all' => ['All experience levels', 'Tất cả mức kinh nghiệm'],
            'none' => ['No experience', 'Không yêu cầu kinh nghiệm'],
            '1-year' => ['1 year', '1 năm'],
            '2-years' => ['2 years', '2 năm'],
            '3-years' => ['3 years', '3 năm'],
            '5-years' => ['5 years', '5 năm'],
            '8-years' => ['8 years', '8 năm'],
        ],
    ],
    'location' => [
        'legend' => ['en' => 'Location', 'vi' => 'Địa điểm'],
        'id_prefix' => 'location-',
        'legacy' => null,
        'options' => [
            'all' => ['All locations', 'Tất cả địa điểm'],
            'hcm' => ['Ho Chi Minh City', 'Thành phố Hồ Chí Minh'],
            'hanoi' => ['Hanoi', 'Hà Nội'],
            'danang' => ['Da Nang', 'Đà Nẵng'],
            'cantho' => ['Can Tho', 'Cần Thơ'],
        ],
    ],
    'job_type' => [
        'legend' => ['en' => 'Job type', 'vi' => 'Loại công việc'],
        'id_prefix' => 'job-type-',
        'legacy' => 'job-type',
        'options' => [
            'all' => ['All job types', 'Tất cả loại công việc'],
            'support' => ['Customer support', 'Hỗ trợ khách hàng'],
            'design' => ['Product design', 'Thiết kế sản phẩm'],
            'development' => ['Software development', 'Phát triển phần mềm'],
            'qa' => ['Quality assurance', 'Đảm bảo chất lượng'],
            'analytics' => ['Data analytics', 'Phân tích dữ liệu'],
            'content' => ['Content writing', 'Viết nội dung'],
        ],
    ],
];

$readParam = static function (string $key, array $allowed, ?string $legacyKey = null): string {
    $raw = $_GET[$key] ?? ($legacyKey !== null ? ($_GET[$legacyKey] ?? null) : null);
    if (!is_string($raw)) {
        return 'all';
    }
    $val = trim($raw);
    return $val !== '' && in_array($val, $allowed, true) ? $val : 'all';
};

$readSalaryBound = static function (string $key): ?int {
    $raw = $_GET[$key] ?? null;
    if (!is_string($raw)) {
        return null;
    }
    $val = trim($raw);
    if ($val === '') {
        return null;
    }
    $parsed = filter_var($val, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0, 'max_range' => 100000]]);
    return $parsed === false ? null : (int) $parsed;
};

$selected = [];
foreach ($filterGroups as $param => $group) {
    $selected[$param] = $readParam($param, array_keys($group['options']), $group['legacy']);
}
$salary = $selected['salary'];
$experience = $selected['experience'];
$location = $selected['location'];
$jobType = $selected['job_type'];

$salaryMin = $readSalaryBound('salary_min');
$salaryMax = $readSalaryBound('salary_max');
$salarySwapped = false;
if ($salary === 'other' && $salaryMin !== null && $salaryMax !== null && $salaryMin > $salaryMax) {
    [$salaryMin, $salaryMax] = [$salaryMax, $salaryMin];
    $salarySwapped = true;
}
$rawQ = is_string($_GET['q'] ?? null) ? trim($_GET['q']) : '';
$q = $rawQ === '' ? '' : (function_exists('mb_substr') ? mb_substr($rawQ, 0, 100, 'UTF-8') : substr($rawQ, 0, 100));

$allJobs = jobs_fetch_all();
$jobMatchesQuery = static function (array $job, string $needle): bool {
    $haystacks = [
        $job['reference'] ?? '',
        $job['title']['en'] ?? '',
        $job['title']['vi'] ?? '',
        $job['company']['en'] ?? '',
        $job['company']['vi'] ?? '',
        $job['category_heading']['en'] ?? '',
        $job['category_heading']['vi'] ?? '',
        $job['location_display']['en'] ?? '',
        $job['location_display']['vi'] ?? '',
        $job['description']['en'] ?? '',
        $job['description']['vi'] ?? '',
    ];
    foreach (['responsibilities', 'requirements', 'benefits'] as $listKey) {
        foreach (['en', 'vi'] as $langKey) {
            foreach ($job[$listKey][$langKey] ?? [] as $item) {
                $haystacks[] = (string) $item;
            }
        }
    }
    $hasMb = function_exists('mb_stripos');
    foreach ($haystacks as $text) {
        $str = (string) $text;
        if ($str === '') {
            continue;
        }
        $matched = $hasMb ? (mb_stripos($str, $needle, 0, 'UTF-8') !== false) : (stripos($str, $needle) !== false);
        if ($matched) {
            return true;
        }
    }
    return false;
};

$filteredJobs = array_values(array_filter($allJobs, static function (array $job) use (
    $salary, $salaryMin, $salaryMax, $experience, $location, $jobType, $q, $jobMatchesQuery
): bool {
    if (!jobs_matches_salary($job, $salary, $salaryMin, $salaryMax)) {
        return false;
    }
    if ($experience !== 'all' && ($job['experience_key'] ?? '') !== $experience) {
        return false;
    }
    if ($location !== 'all' && ($job['location_key'] ?? '') !== $location) {
        return false;
    }
    if ($jobType !== 'all' && ($job['job_type_key'] ?? '') !== $jobType) {
        return false;
    }
    // shortcut: plain substring match until Phase E adds includes/search_engine.php fuzzy scoring
    if ($q !== '' && !$jobMatchesQuery($job, $q)) {
        return false;
    }
    return true;
}));

$summaryNotes = [];
if ($salarySwapped) {
    $summaryNotes[] = t1_text(
        'Salary range normalized to $' . number_format((int) $salaryMin) . ' - $' . number_format((int) $salaryMax) . '.',
        'Khoảng lương đã được chuẩn hóa thành $' . number_format((int) $salaryMin) . ' - $' . number_format((int) $salaryMax) . '.'
    );
} elseif ($salary === 'other' && $salaryMin === null && $salaryMax === null) {
    $summaryNotes[] = t1_text(
        'Enter Min or Max USD to narrow custom salary results.',
        'Nhập mức tối thiểu hoặc tối đa (USD) để thu hẹp khoảng lương.'
    );
}
if ($q !== '') {
    $summaryNotes[] = t1_text('Keyword: "', 'Từ khóa: "') . $q . '"';
}
$activeSummary = implode(' ', $summaryNotes);

include __DIR__ . '/header.inc';
include __DIR__ . '/nav.inc';
?>
<main id="main-content">
      <!-- HERO: page introduction and online healthcare illustration. -->
      <section class="hero" aria-labelledby="jobs-heading">
        <!-- Background animation -->
        <div class="hero-background" aria-hidden="true">
          <div class="hero-glow"></div>
          <div class="hero-orb orb-one"></div>
          <div class="hero-orb orb-two"></div>
        </div>

        <div class="container hero-layout">
          <!-- Left content -->
          <div class="hero-content">
            <p class="tag"><?= t1_e('DIGITAL HEALTHCARE', 'CHĂM SÓC SỨC KHỎE SỐ') ?></p>

            <h1 id="jobs-heading">
              <?= t1_text('Better Health<br />Smarter Care', 'Sức khỏe tốt hơn<br />Chăm sóc thông minh hơn') ?>
            </h1>

            <p class="hero-text">
              <?= t1_e(
                  'T1 is a digital health and wellness provider delivering accessible online health information, convenient appointment booking and patient-focused digital services.',
                  'T1 là đơn vị cung cấp dịch vụ sức khỏe và chăm sóc sức khỏe số, mang đến thông tin y tế trực tuyến dễ tiếp cận, đặt lịch hẹn thuận tiện và các dịch vụ số lấy bệnh nhân làm trung tâm.'
              ) ?>
            </p>

            <a class="btn btn-submit" href="apply.php">
              <?= t1_e('APPLY NOW', 'ỨNG TUYỂN NGAY') ?>
              <i class="fa-solid fa-arrow-right-long" aria-hidden="true"></i>
            </a>
          </div>

          <!-- Right: Shared digital-health animation for Jobs, Apply, and About pages. -->
          <div class="page-hero-animation jobs-hero-animation" aria-label="<?= t1_e('Digital healthcare careers illustration', 'Minh họa cơ hội nghề nghiệp chăm sóc sức khỏe số') ?>">
            <!-- Main floating interface card -->
            <div class="animation-card career-main-card">
              <div class="career-card-header">
                <span class="career-card-icon" aria-hidden="true">
                  <i class="fa-solid fa-briefcase-medical"></i>
                </span>
                <span class="career-card-heading"><?= t1_e('CAREER OPPORTUNITIES', 'CƠ HỘI NGHỀ NGHIỆP') ?></span>
                <span class="career-card-menu" aria-hidden="true">
                  <i class="fa-solid fa-ellipsis"></i>
                </span>
              </div>

              <div class="career-card-content">
                <div class="career-card-copy">
                  <span class="career-eyebrow"><?= t1_e('BUILD YOUR FUTURE', 'XÂY DỰNG TƯƠNG LAI') ?></span>
                  <strong><?= t1_e('Make an impact with T1', 'Cùng T1 tạo nên khác biệt') ?></strong>
                  <p><?= t1_e('Grow your skills in digital healthcare.', 'Phát triển kỹ năng trong lĩnh vực chăm sóc sức khỏe số.') ?></p>
                </div>

                <div class="career-team-illustration" aria-hidden="true">
                  <span class="team-person person-one"></span>
                  <span class="team-person person-two"></span>
                  <span class="team-person person-three"></span>
                </div>
              </div>

              <div class="career-card-footer">
                <span class="career-status-dot"></span>
                <span><?= t1_e('Opportunities at T1', 'Cơ hội tại T1') ?></span>
                <i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i>
              </div>
            </div>

            <!-- Floating job notification -->
            <div class="animation-card career-notification-card">
              <span class="notification-icon" aria-hidden="true">
                <i class="fa-solid fa-user-plus"></i>
              </span>
              <span class="notification-copy">
                <strong><?= t1_e('Join our team', 'Gia nhập đội ngũ') ?></strong>
                <small><?= t1_e('Your next step starts here', 'Hành trình mới bắt đầu tại đây') ?></small>
              </span>
              <i class="fa-solid fa-check notification-check" aria-hidden="true"></i>
            </div>

            <!-- Floating digital-health symbol -->
            <div class="animation-orbit-icon orbit-health" aria-hidden="true">
              <i class="fa-solid fa-heart-pulse"></i>
            </div>

            <!-- Floating growth symbol -->
            <div class="animation-orbit-icon orbit-growth" aria-hidden="true">
              <i class="fa-solid fa-chart-line"></i>
            </div>

            <!-- Decorative connection points for the CSS animation -->
            <span class="animation-particle particle-one" aria-hidden="true"></span>
            <span class="animation-particle particle-two" aria-hidden="true"></span>
            <span class="animation-particle particle-three" aria-hidden="true"></span>
          </div>
        </div>
      </section>

      <section class="job-layout" aria-label="<?= t1_e('Job opportunities', 'Cơ hội việc làm') ?>">
        <div id="job-list">
          <div class="job-results-bar" role="status" aria-live="polite">
            <p class="job-results-count">
              <?= t1_e(
                  'Showing ' . count($filteredJobs) . ' of ' . count($allJobs) . ' jobs',
                  'Hiển thị ' . count($filteredJobs) . ' / ' . count($allJobs) . ' việc làm'
              ) ?>
            </p>
            <?php if ($activeSummary !== ''): ?>
            <p class="job-results-summary"><?= auth_escape($activeSummary) ?></p>
            <?php endif; ?>
          </div>

          <?php if (empty($filteredJobs)): ?>
          <div class="job-empty-state" role="status" aria-live="polite">
            <h2><?= t1_e('No matching jobs found', 'Không tìm thấy công việc phù hợp') ?></h2>
            <p><?= t1_e(
                'Try adjusting your USD salary range, keyword, or filter selections to view more open positions.',
                'Hãy thử điều chỉnh khoảng lương USD, từ khóa hoặc các lựa chọn bộ lọc để xem thêm vị trí đang tuyển.'
            ) ?></p>
            <a href="jobs.php#job-list" class="clear-filters"><?= t1_e('Reset all filters', 'Xóa tất cả bộ lọc') ?></a>
          </div>
          <?php else: ?>
            <?php foreach ($filteredJobs as $job): ?>
              <?php
              $ref = (string) ($job['reference'] ?? '');
              $titleText = t1_pick($job['title'] ?? []);
              $detailsId = 'show_details_' . strtolower($ref);
              $isNegotiable = ($job['salary_min'] ?? null) === null || ($job['salary_max'] ?? null) === null;
              // shortcut: keep non-VND filter classes on cards until styles/jobs.css L286-288 is removed in Phase B step 2
              $cardClasses = trim(implode(' ', array_filter([
                  'job-card',
                  !empty($job['location_key']) ? 'location-' . $job['location_key'] : '',
                  !empty($job['job_type_key']) ? 'job-' . $job['job_type_key'] : '',
                  !empty($job['experience_key']) ? 'experience-' . $job['experience_key'] : '',
                  $isNegotiable ? 'salary-negotiable' : '',
              ])));
              $responsibilities = t1_pick_list($job['responsibilities'] ?? []);
              $requirements = t1_pick_list($job['requirements'] ?? []);
              $benefits = t1_pick_list($job['benefits'] ?? []);
              ?>
          <article id="job-<?= auth_escape($ref) ?>" class="<?= auth_escape($cardClasses) ?>">
            <div class="ref-code">
              <p>
                <?= t1_e('Job ID:', 'Mã việc làm:') ?>
                <span class="ref-code-value"><strong><?= auth_escape($ref) ?></strong></span>
              </p>
            </div>
            <h2><?= auth_escape($titleText) ?></h2>
            <p class="company-name"><?= auth_escape(t1_pick($job['company'] ?? [])) ?></p>
            <h3><?= auth_escape(t1_pick($job['category_heading'] ?? [])) ?></h3>
            <p><?= auth_escape(t1_pick($job['description'] ?? [])) ?></p>
            <div class="job-meta">
              <p>
                <span class="meta-label"><?= t1_e('Reports to', 'Báo cáo cho') ?></span>
                <?= auth_escape(t1_pick($job['reports_to'] ?? [])) ?>
              </p>
              <p>
                <span class="meta-label"><?= t1_e('Salary', 'Lương') ?></span>
                <?= auth_escape(t1_pick($job['salary_display'] ?? [])) ?>
              </p>
              <p>
                <span class="meta-label"><?= t1_e('Location', 'Địa điểm') ?></span>
                <?= auth_escape(t1_pick($job['location_display'] ?? [])) ?>
              </p>
              <p class="experience-meta">
                <span class="meta-label"><?= t1_e('Experience', 'Kinh nghiệm') ?></span>
                <?= auth_escape(t1_pick($job['experience_display'] ?? [])) ?>
              </p>
            </div>
            <div class="job-details">
              <h3><?= t1_e('Key Responsibilities', 'Trách nhiệm chính') ?></h3>
              <ul>
                <?php foreach ($responsibilities as $item): ?>
                <li><?= auth_escape((string) $item) ?></li>
                <?php endforeach; ?>
              </ul>
              <h3><?= t1_e('Requirements', 'Yêu cầu') ?></h3>
              <ul>
                <?php foreach ($requirements as $item): ?>
                <li><?= auth_escape((string) $item) ?></li>
                <?php endforeach; ?>
              </ul>
              <?php if (!empty($benefits)): ?>
              <h3><?= t1_e('Benefits', 'Phúc lợi') ?></h3>
              <ul>
                <?php foreach ($benefits as $item): ?>
                <li><?= auth_escape((string) $item) ?></li>
                <?php endforeach; ?>
              </ul>
              <?php endif; ?>
            </div>
            <input type="checkbox" id="<?= auth_escape($detailsId) ?>" class="show-details-checkbox visually-hidden" />
            <label for="<?= auth_escape($detailsId) ?>" class="details-toggle-label">
              <span class="visually-hidden"><?= auth_escape(t1_text('Toggle job details for ', 'Mở hoặc đóng thông tin công việc cho ') . $titleText) ?></span>
              <i class="fa-solid fa-caret-down arrow" aria-hidden="true"></i>
            </label>
            <a class="job-apply-button" href="apply.php?job=<?= auth_escape(rawurlencode($ref)) ?>#skip-to-application">
              <?= t1_e('Apply for this job', 'Ứng tuyển công việc này') ?>
            </a>
          </article>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
        <div class="FilterSidebar-Container">
          <input class="filter-toggle visually-hidden" type="checkbox" id="filter-toggle" />
          <label class="filter-toggle-label" for="filter-toggle">
            <span><?= t1_e('Filter jobs', 'Lọc việc làm') ?></span>
            <i class="fa-solid fa-sliders" aria-hidden="true"></i>
          </label>
          <search id="job-filter" aria-labelledby="filter-heading">
            <form method="get" action="jobs.php#job-list" autocomplete="off" aria-labelledby="filter-heading">
              <fieldset>
                <legend id="filter-heading" class="filter-main-title"><?= t1_e('FILTER', 'BỘ LỌC') ?></legend>

                <button type="submit" class="apply-filters-btn"><?= t1_e('Apply Filters', 'Áp dụng bộ lọc') ?></button>
                <a href="jobs.php#job-list" class="clear-filters"><?= t1_e('Clear filters', 'Xóa bộ lọc') ?></a>

                <div class="filter-group filter-search-group">
                  <label for="job-keyword-search"><?= t1_e('Keyword or Job ID', 'Từ khóa hoặc mã việc làm') ?></label>
                  <input
                    type="search"
                    id="job-keyword-search"
                    name="q"
                    value="<?= auth_escape($q) ?>"
                    maxlength="100"
                    autocorrect="off"
                    autocapitalize="none"
                    spellcheck="false"
                    enterkeyhint="search"
                    placeholder="<?= t1_e('Filter jobs by keyword or code...', 'Lọc việc theo từ khóa hoặc mã...') ?>"
                  />
                </div>

                <?php foreach ($filterGroups as $paramName => $group): ?>
                <fieldset class="filter-group">
                  <legend><?= auth_escape(t1_pick($group['legend'] ?? [])) ?></legend>
                  <?php foreach ($group['options'] as $optValue => $labels): ?>
                    <?php
                    $inputId = $group['id_prefix'] . $optValue;
                    $optLabel = t1_text($labels[0], $labels[1]);
                    ?>
                  <label for="<?= auth_escape($inputId) ?>">
                    <input
                      type="radio"
                      id="<?= auth_escape($inputId) ?>"
                      name="<?= auth_escape($paramName) ?>"
                      value="<?= auth_escape($optValue) ?>"
                      <?= $selected[$paramName] === $optValue ? 'checked' : '' ?>
                    />
                    <?= auth_escape($optLabel) ?>
                  </label>
                  <?php endforeach; ?>
                  <?php if ($paramName === 'salary'): ?>
                  <div class="salary-custom-range">
                    <div class="salary-bound">
                      <label for="salary-min"><?= t1_e('Min USD', 'Tối thiểu (USD)') ?></label>
                      <input
                        type="number"
                        id="salary-min"
                        name="salary_min"
                        min="0"
                        max="100000"
                        step="50"
                        inputmode="numeric"
                        value="<?= $salaryMin !== null ? auth_escape((string) $salaryMin) : '' ?>"
                      />
                    </div>
                    <div class="salary-bound">
                      <label for="salary-max"><?= t1_e('Max USD', 'Tối đa (USD)') ?></label>
                      <input
                        type="number"
                        id="salary-max"
                        name="salary_max"
                        min="0"
                        max="100000"
                        step="50"
                        inputmode="numeric"
                        value="<?= $salaryMax !== null ? auth_escape((string) $salaryMax) : '' ?>"
                      />
                    </div>
                  </div>
                  <?php endif; ?>
                </fieldset>
                <?php endforeach; ?>
              </fieldset>
            </form>
          </search>

          <aside class="sidebar">
            <section class="tips-box">
              <h3><?= t1_e('Application Tips', 'Mẹo ứng tuyển') ?></h3>
              <ol>
                <li><?= t1_e('Read the job description carefully to understand the role and requirements.', 'Đọc kỹ mô tả công việc để hiểu vai trò và yêu cầu.') ?></li>
                <li><?= t1_e('Prepare your resume and cover letter tailored to the specific job.', 'Chuẩn bị CV và thư xin việc phù hợp với công việc cụ thể.') ?></li>
                <li><?= t1_e('Highlight relevant skills, experiences, and achievements.', 'Làm nổi bật kỹ năng, kinh nghiệm và thành tích liên quan.') ?></li>
                <li><?= t1_e('Follow the application instructions provided in the job listing.', 'Thực hiện hướng dẫn ứng tuyển trong tin tuyển dụng.') ?></li>
                <li><?= t1_e('Submit your application before the deadline.', 'Nộp đơn trước thời hạn.') ?></li>
              </ol>
            </section>
            <section class="disclaimer-box">
              <h3><?= t1_e('Disclaimer', 'Tuyên bố miễn trừ') ?></h3>
              <p>
                <?= t1_e(
                    'All job listings are subject to change and may be filled or removed at any time. T1 Health & Wellness is not responsible for any discrepancies or inaccuracies in the job postings.',
                    'Tất cả tin tuyển dụng có thể thay đổi, được tuyển đủ hoặc gỡ bỏ bất cứ lúc nào. T1 Health & Wellness không chịu trách nhiệm về bất kỳ sai lệch hoặc thiếu chính xác nào trong tin tuyển dụng.'
                ) ?>
              </p>
            </section>
            <section class="ref-code-box">
              <h3><?= t1_e('Job Reference Code', 'Mã tham chiếu công việc') ?></h3>
              <p>
                <?= t1_text(
                    'Double-check your <strong>Job Reference Code</strong> before submitting the application form!',
                    'Hãy kiểm tra kỹ <strong>Mã tham chiếu công việc</strong> trước khi gửi đơn ứng tuyển!'
                ) ?>
              </p>
            </section>

            <section class="help-box">
              <h3><?= t1_e('Need Help?', 'Cần trợ giúp?') ?></h3>
              <p>
                <?= t1_text(
                    'Contact our HR team at <a href="mailto:106208127@student.swin.edu.au">106208127@student.swin.edu.au</a>',
                    'Liên hệ đội ngũ nhân sự qua <a href="mailto:106208127@student.swin.edu.au">106208127@student.swin.edu.au</a>'
                ) ?>
              </p>
            </section>
          </aside>
        </div>
      </section>
    </main>

    <!-- FOOTER: company information, project links, and website standards. -->

    <?php include 'footer.inc'; ?>
