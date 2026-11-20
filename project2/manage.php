<?php
require_once __DIR__ . '/auth/session.php';
require_once __DIR__ . '/auth/users.php';
require_once __DIR__ . '/eoi_helpers.php';

/** Builds one English and one Vietnamese span for the language switch. */
function manage_line(string $en, string $vi): string
{
    // The stylesheet shows one span at a time from the language switch.
    return '<span class="language-en">' . auth_escape($en) . '</span><span class="language-vi">' . auth_escape($vi) . '</span>';
}

auth_require_hr();
if (($_SESSION['role'] ?? '') !== 'manager') {
    http_response_code(403);
    $pageTitle = 'Manage EOIs - T1 Health & Wellness';
    $pageDescription = 'HR manager tools for expression of interest records.';
    $pageStylesheet = 'manage.css';
    $bodyClass = 'manage-page';
    $currentPage = 'manage.php';
    include __DIR__ . '/header.inc';
    include __DIR__ . '/nav.inc';
    echo '<main id="main-content" class="manage"><p class="manage-error" role="alert">' . manage_line('You do not have access to the EOI manager.', 'Bạn không có quyền truy cập trang quản lý EOI.') . '</p></main>';
    include __DIR__ . '/footer.inc';
    exit;
}

$statuses = ['New', 'Current', 'Final'];
$sortFields = [
    'EOInumber' => ['EOI number', 'Số EOI'],
    'job_reference' => ['Job reference', 'Mã việc'],
    'first_name' => ['First name', 'Tên'],
    'last_name' => ['Last name', 'Họ'],
    'email' => ['Email', 'Email'],
    'status' => ['Status', 'Trạng thái'],
    'created_at' => ['Date received', 'Ngày nhận'],
];
$statusLabels = ['New' => 'Mới', 'Current' => 'Đang xử lý', 'Final' => 'Hoàn tất'];

/** Reads one trimmed text field from a request array. */
function manage_input(array $source, string $key, string $default = ''): string
{
    // Ignore missing keys and non-text values such as arrays.
    return isset($source[$key]) && is_string($source[$key]) ? trim($source[$key]) : $default;
}

$perPage = 15;
$sort = manage_input($_GET, 'sort');
$sort = isset($sortFields[$sort]) ? $sort : 'EOInumber';
$dir = ($_GET['dir'] ?? 'desc') === 'asc' ? 'asc' : 'desc';
$cvName = manage_input($_GET, 'cv');
$statusFilter = manage_input($_GET, 'status');
$jobReference = strtoupper(manage_input($_GET, 'job_reference'));
$firstName = manage_input($_GET, 'first_name');
$lastName = manage_input($_GET, 'last_name');
$page = filter_var($_GET['page'] ?? 1, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) ?: 1;

$notice = $_SESSION['manage_notice'] ?? '';
$error = $_SESSION['manage_error'] ?? '';
unset($_SESSION['manage_notice'], $_SESSION['manage_error']);

/** Redirects back to the EOI list and keeps the current search. */
function manage_back(array $query): void
{
    // 303 returns to the list with the same filters after a POST.
    header('Location: manage.php?' . http_build_query($query), true, 303);
    exit;
}

$query = ['cv' => $cvName, 'status' => $statusFilter, 'sort' => $sort, 'dir' => $dir, 'job_reference' => $jobReference, 'first_name' => $firstName, 'last_name' => $lastName, 'page' => $page];

try {
    $db = auth_database();
    eoi_ensure_table($db);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? '';
        if ($action === 'delete') {
            $reference = strtoupper(manage_input($_POST, 'job_reference'));
            if (!preg_match('/^[A-Z0-9]{5}$/', $reference)) {
                $_SESSION['manage_error'] = ['Enter a job reference of exactly 5 letters or digits.', 'Mã việc phải gồm đúng 5 chữ hoặc số.'];
            } else {
                $delete = $db->prepare('DELETE FROM eoi WHERE UPPER(job_reference) = ?');
                $delete->execute([$reference]);
                $count = $delete->rowCount();
                $_SESSION['manage_notice'] = [$count . ' EOI record(s) deleted for ' . $reference . '.', 'Đã xóa ' . $count . ' hồ sơ EOI của mã ' . $reference . '.'];
            }
        } elseif ($action === 'status') {
            $number = filter_input(INPUT_POST, 'eoi_number', FILTER_VALIDATE_INT);
            $status = $_POST['status'] ?? '';
            if (!$number || $number < 1 || !in_array($status, $statuses, true)) {
                $_SESSION['manage_error'] = ['Choose a valid EOI and status.', 'Hãy chọn đúng số EOI và trạng thái.'];
            } else {
                $update = $db->prepare('UPDATE eoi SET status = ? WHERE EOInumber = ?');
                $update->execute([$status, $number]);
                if ($update->rowCount()) {
                    $_SESSION['manage_notice'] = ['EOI ' . $number . ' is now ' . $status . '.', 'EOI ' . $number . ' đã chuyển sang ' . $statusLabels[$status] . '.'];
                } else {
                    $exists = $db->prepare('SELECT EOInumber FROM eoi WHERE EOInumber = ?');
                    $exists->execute([$number]);
                    $_SESSION['manage_notice'] = $exists->fetch()
                        ? ['Status is already ' . $status . '.', 'Trạng thái đã là ' . $statusLabels[$status] . '.']
                        : ['That EOI number was not found.', 'Không tìm thấy số EOI này.'];
                }
            }
        }
        manage_back($query);
    }

    $clauses = [];
    $params = [];
    $invalid = false;
    // A CV name search matches part of the file name.
    if ($cvName !== '') {
        if (mb_strlen($cvName) > 255) {
            $error = ['CV name is too long.', 'Tên CV quá dài.'];
            $invalid = true;
        } else {
            $clauses[] = "cv_name LIKE ? ESCAPE '!'";
            $params[] = '%' . strtr($cvName, ['!' => '!!', '%' => '!%', '_' => '!_']) . '%';
        }
    }
    if ($statusFilter !== '') {
        if (!in_array($statusFilter, $statuses, true)) {
            $error = ['Choose a valid status filter.', 'Hãy chọn trạng thái lọc hợp lệ.'];
            $invalid = true;
        } else {
            $clauses[] = 'status = ?';
            $params[] = $statusFilter;
        }
    }
    if ($jobReference !== '') {
        if (!preg_match('/^[A-Z0-9]{5}$/', $jobReference)) {
            $error = ['Job reference must be exactly 5 letters or digits.', 'Mã việc phải gồm đúng 5 chữ hoặc số.'];
            $invalid = true;
        } else {
            $clauses[] = 'UPPER(job_reference) = ?';
            $params[] = $jobReference;
        }
    }
    if ($firstName !== '') {
        if (mb_strlen($firstName) > 50) {
            $error = ['First name is too long.', 'Tên quá dài.'];
            $invalid = true;
        } else {
            $clauses[] = 'LOWER(first_name) = LOWER(?)';
            $params[] = $firstName;
        }
    }
    if ($lastName !== '') {
        if (mb_strlen($lastName) > 50) {
            $error = ['Last name is too long.', 'Họ quá dài.'];
            $invalid = true;
        } else {
            $clauses[] = 'LOWER(last_name) = LOWER(?)';
            $params[] = $lastName;
        }
    }
    $where = $invalid ? 'WHERE 0' : ($clauses ? 'WHERE ' . implode(' AND ', $clauses) : '');
    $bound = $invalid ? [] : $params;
    $count = $db->prepare('SELECT COUNT(*) FROM eoi ' . $where);
    $count->execute($bound);
    $total = (int) $count->fetchColumn();
    $pages = max(1, (int) ceil($total / $perPage));
    $page = min($page, $pages);
    $query['page'] = $page;
    $sql = 'SELECT EOInumber, first_name, last_name, email, phone, job_reference, position, state, postcode, skills, status, created_at, cv_name
            FROM eoi ' . $where . ' ORDER BY ' . $sort . ' ' . ($dir === 'asc' ? 'ASC' : 'DESC') . ' LIMIT ' . $perPage . ' OFFSET ' . (($page - 1) * $perPage);
    $rows = $db->prepare($sql);
    $rows->execute($bound);
    $records = $rows->fetchAll();
} catch (PDOException $exception) {
    error_log('EOI manager database error: ' . $exception->getMessage());
    http_response_code(503);
    $records = [];
    $total = 0;
    $pages = 1;
    $error = ['The database is unavailable. Check that MySQL is running and the t1_health database exists.', 'Không kết nối được cơ sở dữ liệu. Hãy kiểm tra MySQL và database t1_health.'];
}

$pageTitle = 'Manage EOIs - T1 Health & Wellness';
$pageDescription = 'HR manager tools for expression of interest records.';
$pageStylesheet = 'manage.css';
$bodyClass = 'manage-page';
$currentPage = 'manage.php';
include __DIR__ . '/header.inc';
include __DIR__ . '/nav.inc';
?>
<main id="main-content" class="manage">
  <header class="manage-heading">
    <p class="manage-eyebrow"><?= manage_line('HR WORKSPACE', 'KHU VỰC NHÂN SỰ') ?></p>
    <div class="manage-heading-row">
      <div>
        <h1><?= manage_line('Applications and CVs', 'Hồ sơ và CV') ?></h1>
        <p><?= manage_line('Signed in as', 'Đang đăng nhập:') ?> <?= auth_escape((string) $_SESSION['username']) ?>.</p>
      </div>
      <nav class="manage-nav" aria-label="HR">
        <form method="post" action="logout.php">
          <button type="submit"><?= manage_line('Sign out', 'Đăng xuất') ?></button>
        </form>
      </nav>
    </div>
  </header>

  <?php if (is_array($notice)): ?><p class="manage-notice" role="status"><?= manage_line($notice[0], $notice[1]) ?></p><?php endif; ?>
  <?php if (is_array($error)): ?><p class="manage-error" role="alert"><?= manage_line($error[0], $error[1]) ?></p><?php endif; ?>

  <section class="manage-panel" aria-labelledby="search-heading">
    <h2 id="search-heading"><?= manage_line('Find records', 'Tìm hồ sơ') ?></h2>
    <?php foreach (['en' => ['Search CV name', 'CV file name', 'Filter', 'Status', 'Any status', 'Job reference', 'First name', 'Last name', 'Sort by', 'Order', 'Ascending', 'Descending', 'Show results'], 'vi' => ['Tìm tên CV', 'Tên tệp CV', 'Lọc', 'Trạng thái', 'Mọi trạng thái', 'Mã việc', 'Tên', 'Họ', 'Sắp xếp theo', 'Thứ tự', 'Tăng dần', 'Giảm dần', 'Xem kết quả']] as $lang => $copy): ?>
      <form method="get" action="manage.php" class="manage-search language-<?= $lang ?>">
        <h3><?= auth_escape($copy[0]) ?></h3>
        <label for="cv-<?= $lang ?>"><?= auth_escape($copy[1]) ?></label>
        <input id="cv-<?= $lang ?>" name="cv" maxlength="255" value="<?= auth_escape($cvName) ?>" placeholder="cv.pdf">
        <h3><?= auth_escape($copy[2]) ?></h3>
        <label for="status-filter-<?= $lang ?>"><?= auth_escape($copy[3]) ?></label>
        <select id="status-filter-<?= $lang ?>" name="status">
          <option value=""><?= auth_escape($copy[4]) ?></option>
          <?php foreach ($statuses as $status): ?>
            <option value="<?= auth_escape($status) ?>" <?= $statusFilter === $status ? 'selected' : '' ?>><?= auth_escape($lang === 'en' ? $status : $statusLabels[$status]) ?></option>
          <?php endforeach; ?>
        </select>
        <label for="job-reference-<?= $lang ?>"><?= auth_escape($copy[5]) ?></label>
        <input id="job-reference-<?= $lang ?>" name="job_reference" maxlength="5" value="<?= auth_escape($jobReference) ?>" placeholder="AB123">
        <label for="first-name-<?= $lang ?>"><?= auth_escape($copy[6]) ?></label>
        <input id="first-name-<?= $lang ?>" name="first_name" maxlength="50" value="<?= auth_escape($firstName) ?>">
        <label for="last-name-<?= $lang ?>"><?= auth_escape($copy[7]) ?></label>
        <input id="last-name-<?= $lang ?>" name="last_name" maxlength="50" value="<?= auth_escape($lastName) ?>">
        <label for="sort-<?= $lang ?>"><?= auth_escape($copy[8]) ?></label>
        <select id="sort-<?= $lang ?>" name="sort">
          <?php foreach ($sortFields as $field => $label): ?>
            <option value="<?= auth_escape($field) ?>" <?= $sort === $field ? 'selected' : '' ?>><?= auth_escape($label[$lang === 'en' ? 0 : 1]) ?></option>
          <?php endforeach; ?>
        </select>
        <label for="dir-<?= $lang ?>"><?= auth_escape($copy[9]) ?></label>
        <select id="dir-<?= $lang ?>" name="dir">
          <option value="asc" <?= $dir === 'asc' ? 'selected' : '' ?>><?= auth_escape($copy[10]) ?></option>
          <option value="desc" <?= $dir === 'desc' ? 'selected' : '' ?>><?= auth_escape($copy[11]) ?></option>
        </select>
        <button type="submit"><?= auth_escape($copy[12]) ?></button>
      </form>
    <?php endforeach; ?>
    <p class="manage-hint"><?= manage_line('Leave search and filters empty to see 15 CVs per page. A name filter matches the exact first name, last name, or both.', 'Để trống ô tìm và bộ lọc để xem 15 CV mỗi trang. Lọc tên khớp đúng tên, họ, hoặc cả hai.') ?></p>
  </section>

  <section class="manage-panel" aria-labelledby="delete-heading">
    <h2 id="delete-heading"><?= manage_line('Delete by job reference', 'Xóa theo mã việc') ?></h2>
    <form method="post" action="manage.php?<?= auth_escape(http_build_query($query)) ?>" class="manage-delete">
      <input type="hidden" name="action" value="delete">
      <label for="delete_reference"><?= manage_line('Job reference', 'Mã việc') ?></label>
      <input id="delete_reference" name="job_reference" maxlength="5" required pattern="[A-Za-z0-9]{5}">
      <button type="submit"><?= manage_line('Delete all matching EOIs', 'Xóa mọi EOI trùng mã') ?></button>
    </form>
  </section>

  <section class="manage-panel" aria-labelledby="results-heading">
    <?php $from = $total === 0 ? 0 : (($page - 1) * $perPage) + 1; $to = min($total, $page * $perPage); ?>
    <h2 id="results-heading"><?= manage_line('Results (' . $total . ')', 'Kết quả (' . $total . ')') ?></h2>
    <p class="manage-hint"><?= manage_line('Showing ' . $from . '–' . $to . ' of ' . $total . '.', 'Đang hiện ' . $from . '–' . $to . ' trong ' . $total . '.') ?></p>
    <?php if (!$records): ?>
      <p><?= manage_line('No EOI records match this search.', 'Không có hồ sơ EOI nào khớp.') ?></p>
    <?php else: ?>
      <div class="manage-table-wrap">
        <table>
          <thead>
            <tr>
              <th>EOI</th>
              <th><?= manage_line('Applicant', 'Ứng viên') ?></th>
              <th><?= manage_line('Contact', 'Liên hệ') ?></th>
              <th><?= manage_line('Job', 'Công việc') ?></th>
              <th><?= manage_line('Skills', 'Kỹ năng') ?></th>
              <th>CV</th>
              <th><?= manage_line('Received', 'Ngày nhận') ?></th>
              <th><?= manage_line('Status', 'Trạng thái') ?></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($records as $record): ?>
              <?php
                $skills = json_decode((string) $record['skills'], true);
                $skillText = is_array($skills) ? implode(', ', $skills) : '';
              ?>
              <tr>
                <td><?= (int) $record['EOInumber'] ?></td>
                <td><?= auth_escape($record['first_name'] . ' ' . $record['last_name']) ?><br><?= auth_escape($record['state'] . ' ' . $record['postcode']) ?></td>
                <td><?= auth_escape($record['email']) ?><br><?= auth_escape($record['phone']) ?></td>
                <td><?= auth_escape($record['job_reference']) ?><br><?= auth_escape($record['position']) ?></td>
                <td><?= auth_escape($skillText !== '' ? $skillText : '—') ?></td>
                <td><a href="hr_cv.php?eoi=<?= (int) $record['EOInumber'] ?>"><?= auth_escape($record['cv_name']) ?></a></td>
                <td><?= auth_escape((string) $record['created_at']) ?></td>
                <td>
                  <?php foreach (['en' => 'Update', 'vi' => 'Cập nhật'] as $lang => $updateLabel): ?>
                  <form method="post" action="manage.php?<?= auth_escape(http_build_query($query)) ?>" class="language-<?= $lang ?>">
                    <input type="hidden" name="action" value="status">
                    <input type="hidden" name="eoi_number" value="<?= (int) $record['EOInumber'] ?>">
                    <label class="visually-hidden" for="status-<?= $lang ?>-<?= (int) $record['EOInumber'] ?>"><?= $lang === 'en' ? 'Status for EOI ' . (int) $record['EOInumber'] : 'Trạng thái EOI ' . (int) $record['EOInumber'] ?></label>
                    <select id="status-<?= $lang ?>-<?= (int) $record['EOInumber'] ?>" name="status">
                      <?php foreach ($statuses as $status): ?>
                        <option value="<?= auth_escape($status) ?>" <?= $record['status'] === $status ? 'selected' : '' ?>><?= auth_escape($lang === 'en' ? $status : $statusLabels[$status]) ?></option>
                      <?php endforeach; ?>
                    </select>
                    <button type="submit"><?= auth_escape($updateLabel) ?></button>
                  </form>
                  <?php endforeach; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <?php if ($pages > 1): ?>
        <nav class="manage-pages" aria-label="Pages">
          <?php if ($page > 1): ?><a href="manage.php?<?= auth_escape(http_build_query(array_merge($query, ['page' => $page - 1]))) ?>"><?= manage_line('Previous', 'Trước') ?></a><?php endif; ?>
          <?php for ($number = max(1, $page - 2); $number <= min($pages, $page + 2); $number++): ?>
            <?php if ($number === $page): ?><span aria-current="page"><?= $number ?></span><?php else: ?><a href="manage.php?<?= auth_escape(http_build_query(array_merge($query, ['page' => $number]))) ?>"><?= $number ?></a><?php endif; ?>
          <?php endfor; ?>
          <?php if ($page < $pages): ?><a href="manage.php?<?= auth_escape(http_build_query(array_merge($query, ['page' => $page + 1]))) ?>"><?= manage_line('Next', 'Sau') ?></a><?php endif; ?>
        </nav>
      <?php endif; ?>
    <?php endif; ?>
  </section>
</main>
<?php include __DIR__ . '/footer.inc'; ?>
