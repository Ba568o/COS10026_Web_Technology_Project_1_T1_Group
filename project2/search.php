<?php
/**
 * search.php — Global local fuzzy search across Jobs (MySQL/seed) and Pages (site_index.php).
 *
 * Handles GET ?q=...&scope=all|jobs|pages submitted from nav.inc or the on-page search form.
 * Runs completely server-side (zero JS) with bilingual labels via includes/lang.php.
 */
require_once __DIR__ . '/auth/session.php';
require_once __DIR__ . '/includes/lang.php';
require_once __DIR__ . '/includes/search_engine.php';
require_once __DIR__ . '/database/jobs_helpers.php';

// Prevent stale bfcache/proxy pages when the user switches language or theme cookies.
if (!headers_sent()) {
    header('Vary: Cookie');
    header('Cache-Control: private, no-cache');
}

// Read theme cookie and set page metadata before header.inc starts HTML output.
$isDarkMode = (($_COOKIE['t1_theme'] ?? '') === 'dark');
$pageTitle = t1_text('T1 Health & Wellness - Search', 'T1 Health & Wellness - Tìm kiếm');
$pageDescription = t1_text(
    'Search open healthcare jobs and website pages at T1 Health & Wellness.',
    'Tìm kiếm vị trí tuyển dụng và các trang thông tin tại T1 Health & Wellness.'
);
$bodyClass = $isDarkMode ? 'search-page dark-mode' : 'search-page light-mode';
// Load search.css once it exists; skip safely if stylesheet is not created yet.
$pageStylesheet = is_file(__DIR__ . '/styles/search.css') ? 'search.css' : '';
$bodyId = 'top';
$currentPage = 'search.php';

// --- Validate GET parameters ---
$searchQuery = t1_search_clean_query($_GET['q'] ?? '');

$allowedScopes = ['all', 'jobs', 'pages'];
$rawScope = $_GET['scope'] ?? 'all';
$searchScope = is_string($rawScope) && in_array(trim($rawScope), $allowedScopes, true)
    ? trim($rawScope)
    : 'all';

$matchedJobs = [];
$matchedPages = [];
$visibleJobs = [];
$visiblePages = [];
$totalResults = 0;
$suggestedTerm = null;

if ($searchQuery !== '') {
    // 1. Score all job listings from MySQL (with automatic PHP seed fallback).
    $allJobs = jobs_fetch_all();
    foreach ($allJobs as $jobRecord) {
        $fieldPairs = [
            [$jobRecord['reference'] ?? '', 12],
            [$jobRecord['title']['en'] ?? '', 10],
            [$jobRecord['title']['vi'] ?? '', 10],
            [$jobRecord['category_heading']['en'] ?? '', 6],
            [$jobRecord['category_heading']['vi'] ?? '', 6],
            [$jobRecord['company']['en'] ?? '', 5],
            [$jobRecord['company']['vi'] ?? '', 5],
            [$jobRecord['location_display']['en'] ?? '', 5],
            [$jobRecord['location_display']['vi'] ?? '', 5],
            [$jobRecord['description']['en'] ?? '', 2],
            [$jobRecord['description']['vi'] ?? '', 2],
        ];
        $relevanceScore = t1_fuzzy_score($searchQuery, $fieldPairs);
        if ($relevanceScore > 0) {
            $matchedJobs[] = [
                'score' => $relevanceScore,
                'job' => $jobRecord,
            ];
        }
    }

    // 2. Score all static site pages from data/site_index.php.
    $sitePages = require __DIR__ . '/data/site_index.php';
    foreach ($sitePages as $pageEntry) {
        $fieldPairs = [
            [$pageEntry['title']['en'] ?? '', 10],
            [$pageEntry['title']['vi'] ?? '', 10],
            [$pageEntry['keywords']['en'] ?? '', 6],
            [$pageEntry['keywords']['vi'] ?? '', 6],
            [$pageEntry['description']['en'] ?? '', 3],
            [$pageEntry['description']['vi'] ?? '', 3],
        ];
        $relevanceScore = t1_fuzzy_score($searchQuery, $fieldPairs);
        if ($relevanceScore > 0) {
            $matchedPages[] = [
                'score' => $relevanceScore,
                'page' => $pageEntry,
            ];
        }
    }

    // Sort both groups from highest relevance score to lowest.
    usort($matchedJobs, static fn(array $first, array $second): int => $second['score'] <=> $first['score']);
    usort($matchedPages, static fn(array $first, array $second): int => $second['score'] <=> $first['score']);

    $visibleJobs = $searchScope === 'pages' ? [] : $matchedJobs;
    $visiblePages = $searchScope === 'jobs' ? [] : $matchedPages;
    $totalResults = count($visibleJobs) + count($visiblePages);

    // Build a small vocabulary from job codes/titles and page titles for "Did you mean...?" when empty.
    if ($totalResults === 0) {
        $suggestionVocabulary = [];
        foreach ($allJobs as $jobRecord) {
            if (!empty($jobRecord['reference'])) {
                $suggestionVocabulary[] = (string) $jobRecord['reference'];
            }
            $suggestionVocabulary[] = t1_pick($jobRecord['title'] ?? []);
        }
        foreach ($sitePages as $pageEntry) {
            $suggestionVocabulary[] = t1_pick($pageEntry['title'] ?? []);
        }
        $suggestedTerm = t1_search_suggest($searchQuery, array_filter($suggestionVocabulary));
    }
}

$scopeTabs = [
    'all' => ['en' => 'All', 'vi' => 'Tất cả', 'count' => count($matchedJobs) + count($matchedPages)],
    'jobs' => ['en' => 'Jobs', 'vi' => 'Việc làm', 'count' => count($matchedJobs)],
    'pages' => ['en' => 'Pages', 'vi' => 'Trang web', 'count' => count($matchedPages)],
];

$quickSuggestions = ['DHS02', 'Developer', 'Designer', 'About', 'Apply'];

include __DIR__ . '/header.inc';
include __DIR__ . '/nav.inc';
?>
<main id="main-content" class="search-main">
  <section class="search-hero" aria-labelledby="search-page-heading">
    <div class="container search-container">
      <h1 id="search-page-heading"><?= t1_e('Search T1 Health & Wellness', 'Tìm kiếm tại T1 Health & Wellness') ?></h1>
      <p class="search-subtitle">
        <?= t1_e(
            'Find open roles, job reference codes, or website sections in English and Vietnamese.',
            'Tìm vị trí tuyển dụng, mã việc làm hoặc các mục trên website bằng tiếng Anh và tiếng Việt.'
        ) ?>
      </p>

      <!-- On-page search form (submits GET search.php?q=...&scope=...). -->
      <search aria-label="<?= t1_e('Site-wide search form', 'Biểu mẫu tìm kiếm toàn trang') ?>">
        <form class="search-page-form" action="search.php" method="get" novalidate>
          <?php if ($searchScope !== 'all'): ?>
          <input type="hidden" name="scope" value="<?= auth_escape($searchScope) ?>" />
          <?php endif; ?>
          <label class="visually-hidden" for="page-search-input">
            <?= t1_e('Search jobs and pages', 'Tìm kiếm việc làm và trang web') ?>
          </label>
          <input
            type="search"
            id="page-search-input"
            name="q"
            value="<?= auth_escape($searchQuery) ?>"
            maxlength="100"
            autocorrect="off"
            autocapitalize="none"
            spellcheck="false"
            enterkeyhint="search"
            placeholder="<?= t1_e('Search by role, code (e.g. DHS02), or page...', 'Tìm theo vị trí, mã (vd: DHS02) hoặc trang...') ?>"
          />
          <button type="submit" class="search-submit-button">
            <?= t1_e('Search', 'Tìm kiếm') ?>
          </button>
        </form>
      </search>

      <?php if ($searchQuery !== ''): ?>
      <!-- Scope filter tabs preserve the active query while switching between All, Jobs, and Pages. -->
      <nav class="search-scope-tabs" aria-label="<?= t1_e('Filter search results by category', 'Lọc kết quả theo danh mục') ?>">
        <?php foreach ($scopeTabs as $scopeKey => $tabMeta): ?>
          <?php
          $tabHref = 'search.php?q=' . rawurlencode($searchQuery) . ($scopeKey === 'all' ? '' : '&scope=' . rawurlencode($scopeKey));
          $isActiveTab = ($searchScope === $scopeKey);
          ?>
        <a
          href="<?= auth_escape($tabHref) ?>"
          class="search-scope-tab<?= $isActiveTab ? ' is-active' : '' ?>"
          <?= $isActiveTab ? 'aria-current="page"' : '' ?>
        >
          <?= t1_e($tabMeta['en'], $tabMeta['vi']) ?>
          <span class="search-tab-count">(<?= (int) $tabMeta['count'] ?>)</span>
        </a>
        <?php endforeach; ?>
      </nav>
      <?php endif; ?>
    </div>
  </section>

  <section class="search-results-section" aria-label="<?= t1_e('Search results', 'Kết quả tìm kiếm') ?>">
    <div class="container search-container">
      <?php if ($searchQuery === ''): ?>
      <!-- Initial landing state when no query has been entered yet. -->
      <div class="search-guide-box">
        <h2><?= t1_e('Try a quick search', 'Thử tìm kiếm nhanh') ?></h2>
        <p>
          <?= t1_e(
              'Enter a job title, reference code, city, or page name above, or pick a popular keyword below:',
              'Nhập tên vị trí, mã việc làm, thành phố hoặc tên trang ở trên, hoặc chọn nhanh một từ khóa bên dưới:'
          ) ?>
        </p>
        <ul class="search-quick-links">
          <?php foreach ($quickSuggestions as $sampleKeyword): ?>
          <li>
            <a href="search.php?q=<?= auth_escape(rawurlencode($sampleKeyword)) ?>">
              <?= auth_escape($sampleKeyword) ?>
            </a>
          </li>
          <?php endforeach; ?>
        </ul>
      </div>
      <?php else: ?>
      <!-- WCAG polite live region announcing result count for screen readers. -->
      <p class="search-status-bar" role="status" aria-live="polite">
        <?= t1_e(
            'Found ' . $totalResults . ' result(s) for "' . $searchQuery . '".',
            'Tìm thấy ' . $totalResults . ' kết quả cho "' . $searchQuery . '".'
        ) ?>
      </p>

        <?php if ($totalResults === 0): ?>
        <div class="search-empty-state">
          <h2><?= t1_e('No matching results found', 'Không tìm thấy kết quả phù hợp') ?></h2>
          <?php if ($suggestedTerm !== null): ?>
          <p class="search-suggestion">
            <?= t1_e('Did you mean:', 'Có phải bạn muốn tìm:') ?>
            <a href="search.php?q=<?= auth_escape(rawurlencode($suggestedTerm)) ?>">
              <?= auth_escape($suggestedTerm) ?>
            </a>?
          </p>
          <?php else: ?>
          <p>
            <?= t1_e(
                'Try another keyword, check your spelling, or browse all open jobs.',
                'Hãy thử từ khóa khác, kiểm tra lại chính tả hoặc xem toàn bộ vị trí đang tuyển.'
            ) ?>
          </p>
          <?php endif; ?>
          <p class="search-fallback-links">
            <a href="jobs.php"><?= t1_e('Browse all jobs', 'Xem tất cả việc làm') ?></a>
            <a href="search.php"><?= t1_e('Clear search', 'Xóa tìm kiếm') ?></a>
          </p>
        </div>
        <?php else: ?>
          <?php if (!empty($visibleJobs)): ?>
          <div class="search-group search-group-jobs">
            <h2><?= t1_e('Job Opportunities', 'Cơ hội việc làm') ?></h2>
            <ul class="search-result-list">
              <?php foreach ($visibleJobs as $jobMatch): ?>
                <?php
                $jobRecord = $jobMatch['job'];
                $jobReference = (string) ($jobRecord['reference'] ?? '');
                $jobTitle = t1_pick($jobRecord['title'] ?? []);
                $jobSalary = t1_pick($jobRecord['salary_display'] ?? []);
                $jobLocation = t1_pick($jobRecord['location_display'] ?? []);
                $jobDescription = t1_pick($jobRecord['description'] ?? []);
                ?>
              <li class="search-result-card search-job-card">
                <span class="search-result-badge"><?= auth_escape($jobReference) ?></span>
                <h3>
                  <a href="jobs.php#job-<?= auth_escape(rawurlencode($jobReference)) ?>">
                    <?= auth_escape($jobTitle) ?>
                  </a>
                </h3>
                <p class="search-job-meta">
                  <?= auth_escape($jobSalary) ?> &middot; <?= auth_escape($jobLocation) ?>
                </p>
                <p class="search-result-excerpt"><?= t1_search_excerpt($jobDescription) ?></p>
                <p class="search-result-actions">
                  <a href="jobs.php#job-<?= auth_escape(rawurlencode($jobReference)) ?>">
                    <?= t1_e('View job details', 'Xem chi tiết') ?>
                  </a>
                  <a href="apply.php?job=<?= auth_escape(rawurlencode($jobReference)) ?>#skip-to-application">
                    <?= t1_e('Apply for this job', 'Ứng tuyển ngay') ?>
                  </a>
                </p>
              </li>
              <?php endforeach; ?>
            </ul>
          </div>
          <?php endif; ?>

          <?php if (!empty($visiblePages)): ?>
          <div class="search-group search-group-pages">
            <h2><?= t1_e('Website Pages', 'Trang thông tin') ?></h2>
            <ul class="search-result-list">
              <?php foreach ($visiblePages as $pageMatch): ?>
                <?php
                $pageEntry = $pageMatch['page'];
                $pageEntryUrl = (string) ($pageEntry['url'] ?? 'index.php');
                $pageEntryTitle = t1_pick($pageEntry['title'] ?? []);
                $pageEntryDescription = t1_pick($pageEntry['description'] ?? []);
                ?>
              <li class="search-result-card search-page-card">
                <span class="search-result-badge"><?= t1_e('Page', 'Trang') ?></span>
                <h3>
                  <a href="<?= auth_escape($pageEntryUrl) ?>">
                    <?= auth_escape($pageEntryTitle) ?>
                  </a>
                </h3>
                <p class="search-result-excerpt"><?= t1_search_excerpt($pageEntryDescription) ?></p>
              </li>
              <?php endforeach; ?>
            </ul>
          </div>
          <?php endif; ?>
        <?php endif; ?>
      <?php endif; ?>
    </div>
  </section>
</main>
<?php include __DIR__ . '/footer.inc'; ?>
