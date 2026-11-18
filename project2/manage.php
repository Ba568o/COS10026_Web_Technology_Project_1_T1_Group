<?php
require_once __DIR__ . '/auth/session.php';
require_once __DIR__ . '/auth/users.php';
require_once __DIR__ . '/eoi_helpers.php';

function manage_line(string $en, string $vi): string
{
    return '<span class="language-en">' . auth_escape($en) . '</span><span class="language-vi">' . auth_escape($vi) . '</span>';
}

auth_require_login();
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

$list = $_GET['list'] ?? 'all';
if (!in_array($list, ['all', 'job', 'name'], true)) $list = 'all';
$sort = isset($sortFields[$_GET['sort'] ?? '']) ? $_GET['sort'] : 'EOInumber';
$dir = ($_GET['dir'] ?? 'desc') === 'asc' ? 'asc' : 'desc';
$jobReference = strtoupper(trim((string) ($_GET['job_reference'] ?? '')));
$firstName = trim((string) ($_GET['first_name'] ?? ''));
$lastName = trim((string) ($_GET['last_name'] ?? ''));

$notice = $_SESSION['manage_notice'] ?? '';
$error = $_SESSION['manage_error'] ?? '';
unset($_SESSION['manage_notice'], $_SESSION['manage_error']);

function manage_back(array $query): void
{
    header('Location: manage.php?' . http_build_query($query), true, 303);
    exit;
}

$query = ['list' => $list, 'sort' => $sort, 'dir' => $dir, 'job_reference' => $jobReference, 'first_name' => $firstName, 'last_name' => $lastName];

try {
    $db = auth_database();
    eoi_ensure_table($db);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!auth_valid_token($_POST['csrf_token'] ?? null)) {
            $_SESSION['manage_error'] = ['The form expired. Please try again.', 'Biểu mẫu đã hết hạn. Vui lòng thử lại.'];
            manage_back($query);
        }
        $action = $_POST['action'] ?? '';
        if ($action === 'delete') {
            $reference = strtoupper(trim((string) ($_POST['job_reference'] ?? '')));
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
                $_SESSION['manage_notice'] = $update->rowCount()
                    ? ['EOI ' . $number . ' is now ' . $status . '.', 'EOI ' . $number . ' đã chuyển sang ' . $statusLabels[$status] . '.']
                    : ['That EOI number was not found.', 'Không tìm thấy số EOI này.'];
            }
        }
        manage_back($query);
    }

    $where = '';
    $params = [];
    if ($list === 'job') {
        if ($jobReference === '') {
            $error = $error ?: ['Enter a job reference to search.', 'Nhập mã việc để tìm.'];
            $where = 'WHERE 0';
        } elseif (!preg_match('/^[A-Z0-9]{5}$/', $jobReference)) {
            $error = ['Job reference must be exactly 5 letters or digits.', 'Mã việc phải gồm đúng 5 chữ hoặc số.'];
            $where = 'WHERE 0';
        } else {
            $where = 'WHERE UPPER(job_reference) = ?';
            $params[] = $jobReference;
        }
    } elseif ($list === 'name') {
        $clauses = [];
        if ($firstName !== '') {
            if (mb_strlen($firstName) > 50) {
                $error = ['First name is too long.', 'Tên quá dài.'];
            } else {
                $clauses[] = 'LOWER(first_name) = LOWER(?)';
                $params[] = $firstName;
            }
        }
        if ($lastName !== '') {
            if (mb_strlen($lastName) > 50) {
                $error = ['Last name is too long.', 'Họ quá dài.'];
            } else {
                $clauses[] = 'LOWER(last_name) = LOWER(?)';
                $params[] = $lastName;
            }
        }
        if (!$clauses && $error === '') {
            $error = ['Enter a first name, a last name, or both.', 'Nhập tên, họ, hoặc cả hai.'];
            $where = 'WHERE 0';
        } elseif ($error !== '') {
            $where = 'WHERE 0';
        } else {
            $where = 'WHERE ' . implode(' AND ', $clauses);
        }
    }

    $sql = 'SELECT EOInumber, first_name, last_name, email, phone, job_reference, position, state, postcode, skills, status, created_at, cv_name
            FROM eoi ' . $where . ' ORDER BY ' . $sort . ' ' . ($dir === 'asc' ? 'ASC' : 'DESC');
    $rows = $db->prepare($sql);
    $rows->execute($params);
    $records = $rows->fetchAll();
} catch (PDOException $exception) {
    error_log('EOI manager database error: ' . $exception->getMessage());
    http_response_code(503);
    $records = [];
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
    <p class="manage-eyebrow"><?= manage_line('HR MANAGER', 'QUẢN LÝ NHÂN SỰ') ?></p>
    <h1><?= manage_line('Expression of interest records', 'Hồ sơ biểu hiện quan tâm') ?></h1>
    <p><?= manage_line('Signed in as', 'Đang đăng nhập:') ?> <?= auth_escape((string) $_SESSION['username']) ?>.</p>
    <form method="post" action="logout.php">
      <input type="hidden" name="csrf_token" value="<?= auth_escape(auth_token()) ?>">
      <button type="submit"><?= manage_line('Sign out', 'Đăng xuất') ?></button>
    </form>
  </header>

  <?php if (is_array($notice)): ?><p class="manage-notice" role="status"><?= manage_line($notice[0], $notice[1]) ?></p><?php endif; ?>
  <?php if (is_array($error)): ?><p class="manage-error" role="alert"><?= manage_line($error[0], $error[1]) ?></p><?php endif; ?>

  <section class="manage-panel" aria-labelledby="search-heading">
    <h2 id="search-heading"><?= manage_line('Find records', 'Tìm hồ sơ') ?></h2>
    <?php foreach (['en' => ['Show', 'All EOIs', 'By job reference', 'By applicant name', 'Job reference', 'First name', 'Last name', 'Sort by', 'Order', 'Ascending', 'Descending', 'Show results'], 'vi' => ['Hiển thị', 'Tất cả EOI', 'Theo mã việc', 'Theo tên ứng viên', 'Mã việc', 'Tên', 'Họ', 'Sắp xếp theo', 'Thứ tự', 'Tăng dần', 'Giảm dần', 'Xem kết quả']] as $lang => $copy): ?>
      <form method="get" action="manage.php" class="manage-search language-<?= $lang ?>">
        <label for="list-<?= $lang ?>"><?= auth_escape($copy[0]) ?></label>
        <select id="list-<?= $lang ?>" name="list">
          <option value="all" <?= $list === 'all' ? 'selected' : '' ?>><?= auth_escape($copy[1]) ?></option>
          <option value="job" <?= $list === 'job' ? 'selected' : '' ?>><?= auth_escape($copy[2]) ?></option>
          <option value="name" <?= $list === 'name' ? 'selected' : '' ?>><?= auth_escape($copy[3]) ?></option>
        </select>
        <label for="job-reference-<?= $lang ?>"><?= auth_escape($copy[4]) ?></label>
        <input id="job-reference-<?= $lang ?>" name="job_reference" maxlength="5" value="<?= auth_escape($jobReference) ?>" placeholder="AB123">
        <label for="first-name-<?= $lang ?>"><?= auth_escape($copy[5]) ?></label>
        <input id="first-name-<?= $lang ?>" name="first_name" maxlength="50" value="<?= auth_escape($firstName) ?>">
        <label for="last-name-<?= $lang ?>"><?= auth_escape($copy[6]) ?></label>
        <input id="last-name-<?= $lang ?>" name="last_name" maxlength="50" value="<?= auth_escape($lastName) ?>">
        <label for="sort-<?= $lang ?>"><?= auth_escape($copy[7]) ?></label>
        <select id="sort-<?= $lang ?>" name="sort">
          <?php foreach ($sortFields as $field => $label): ?>
            <option value="<?= auth_escape($field) ?>" <?= $sort === $field ? 'selected' : '' ?>><?= auth_escape($label[$lang === 'en' ? 0 : 1]) ?></option>
          <?php endforeach; ?>
        </select>
        <label for="dir-<?= $lang ?>"><?= auth_escape($copy[8]) ?></label>
        <select id="dir-<?= $lang ?>" name="dir">
          <option value="asc" <?= $dir === 'asc' ? 'selected' : '' ?>><?= auth_escape($copy[9]) ?></option>
          <option value="desc" <?= $dir === 'desc' ? 'selected' : '' ?>><?= auth_escape($copy[10]) ?></option>
        </select>
        <button type="submit"><?= auth_escape($copy[11]) ?></button>
      </form>
    <?php endforeach; ?>
    <p class="manage-hint"><?= manage_line('For a name search, fill in the first name, the last name, or both. Both together finds that exact applicant.', 'Khi tìm theo tên, hãy nhập tên, họ, hoặc cả hai. Nhập cả hai sẽ tìm đúng người đó.') ?></p>
  </section>

  <section class="manage-panel" aria-labelledby="delete-heading">
    <h2 id="delete-heading"><?= manage_line('Delete by job reference', 'Xóa theo mã việc') ?></h2>
    <form method="post" action="manage.php?<?= auth_escape(http_build_query($query)) ?>" class="manage-delete">
      <input type="hidden" name="csrf_token" value="<?= auth_escape(auth_token()) ?>">
      <input type="hidden" name="action" value="delete">
      <label for="delete_reference"><?= manage_line('Job reference', 'Mã việc') ?></label>
      <input id="delete_reference" name="job_reference" maxlength="5" required pattern="[A-Za-z0-9]{5}">
      <button type="submit"><?= manage_line('Delete all matching EOIs', 'Xóa mọi EOI trùng mã') ?></button>
    </form>
  </section>

  <section class="manage-panel" aria-labelledby="results-heading">
    <h2 id="results-heading"><?= manage_line('Results (' . count($records) . ')', 'Kết quả (' . count($records) . ')') ?></h2>
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
                <td><?= auth_escape($record['cv_name']) ?></td>
                <td><?= auth_escape((string) $record['created_at']) ?></td>
                <td>
                  <form method="post" action="manage.php?<?= auth_escape(http_build_query($query)) ?>">
                    <input type="hidden" name="csrf_token" value="<?= auth_escape(auth_token()) ?>">
                    <input type="hidden" name="action" value="status">
                    <input type="hidden" name="eoi_number" value="<?= (int) $record['EOInumber'] ?>">
                    <label class="visually-hidden" for="status-<?= (int) $record['EOInumber'] ?>"><?= manage_line('Status for EOI ' . (int) $record['EOInumber'], 'Trạng thái EOI ' . (int) $record['EOInumber']) ?></label>
                    <select id="status-<?= (int) $record['EOInumber'] ?>" name="status">
                      <?php foreach ($statuses as $status): ?>
                        <option value="<?= auth_escape($status) ?>" <?= $record['status'] === $status ? 'selected' : '' ?>><?= auth_escape($status . ' — ' . $statusLabels[$status]) ?></option>
                      <?php endforeach; ?>
                    </select>
                    <button type="submit"><?= manage_line('Update', 'Cập nhật') ?></button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </section>
</main>
<?php include __DIR__ . '/footer.inc'; ?>
