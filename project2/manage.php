<?php
require_once __DIR__ . '/auth/session.php';
require_once __DIR__ . '/auth/users.php';
require_once __DIR__ . '/eoi_helpers.php';

auth_require_login();
if (($_SESSION['role'] ?? '') !== 'manager') {
    http_response_code(403);
    exit('You do not have access to the EOI manager.');
}

$statuses = ['New', 'Current', 'Final'];
$sortFields = [
    'EOInumber' => 'EOI number',
    'job_reference' => 'Job reference',
    'first_name' => 'First name',
    'last_name' => 'Last name',
    'email' => 'Email',
    'status' => 'Status',
    'created_at' => 'Date received',
];

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
            $_SESSION['manage_error'] = 'The form expired. Please try again.';
            manage_back($query);
        }
        $action = $_POST['action'] ?? '';
        if ($action === 'delete') {
            $reference = strtoupper(trim((string) ($_POST['job_reference'] ?? '')));
            if (!preg_match('/^[A-Z0-9]{5}$/', $reference)) {
                $_SESSION['manage_error'] = 'Enter a job reference of exactly 5 letters or digits.';
            } else {
                $delete = $db->prepare('DELETE FROM eoi WHERE UPPER(job_reference) = ?');
                $delete->execute([$reference]);
                $_SESSION['manage_notice'] = $delete->rowCount() . ' EOI record(s) deleted for ' . $reference . '.';
            }
        } elseif ($action === 'status') {
            $number = filter_input(INPUT_POST, 'eoi_number', FILTER_VALIDATE_INT);
            $status = $_POST['status'] ?? '';
            if (!$number || $number < 1 || !in_array($status, $statuses, true)) {
                $_SESSION['manage_error'] = 'Choose a valid EOI and status.';
            } else {
                $update = $db->prepare('UPDATE eoi SET status = ? WHERE EOInumber = ?');
                $update->execute([$status, $number]);
                $_SESSION['manage_notice'] = $update->rowCount()
                    ? 'EOI ' . $number . ' is now ' . $status . '.'
                    : 'That EOI number was not found.';
            }
        }
        manage_back($query);
    }

    $where = '';
    $params = [];
    if ($list === 'job') {
        if ($jobReference === '') {
            $error = $error ?: 'Enter a job reference to search.';
            $where = 'WHERE 0';
        } elseif (!preg_match('/^[A-Z0-9]{5}$/', $jobReference)) {
            $error = 'Job reference must be exactly 5 letters or digits.';
            $where = 'WHERE 0';
        } else {
            $where = 'WHERE UPPER(job_reference) = ?';
            $params[] = $jobReference;
        }
    } elseif ($list === 'name') {
        $clauses = [];
        if ($firstName !== '') {
            if (mb_strlen($firstName) > 50) {
                $error = 'First name is too long.';
            } else {
                $clauses[] = 'LOWER(first_name) = LOWER(?)';
                $params[] = $firstName;
            }
        }
        if ($lastName !== '') {
            if (mb_strlen($lastName) > 50) {
                $error = 'Last name is too long.';
            } else {
                $clauses[] = 'LOWER(last_name) = LOWER(?)';
                $params[] = $lastName;
            }
        }
        if (!$clauses && $error === '') {
            $error = 'Enter a first name, a last name, or both.';
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
    $error = 'The database is unavailable. Check that MySQL is running and the t1_health database exists.';
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
    <p class="manage-eyebrow">HR MANAGER</p>
    <h1>Expression of interest records</h1>
    <p>Signed in as <?= auth_escape((string) $_SESSION['username']) ?>.</p>
    <form method="post" action="logout.php">
      <input type="hidden" name="csrf_token" value="<?= auth_escape(auth_token()) ?>">
      <button type="submit">Sign out</button>
    </form>
  </header>

  <?php if ($notice): ?><p class="manage-notice" role="status"><?= auth_escape($notice) ?></p><?php endif; ?>
  <?php if ($error): ?><p class="manage-error" role="alert"><?= auth_escape($error) ?></p><?php endif; ?>

  <section class="manage-panel" aria-labelledby="search-heading">
    <h2 id="search-heading">Find records</h2>
    <form method="get" action="manage.php" class="manage-search">
      <label for="list">Show</label>
      <select id="list" name="list">
        <option value="all" <?= $list === 'all' ? 'selected' : '' ?>>All EOIs</option>
        <option value="job" <?= $list === 'job' ? 'selected' : '' ?>>By job reference</option>
        <option value="name" <?= $list === 'name' ? 'selected' : '' ?>>By applicant name</option>
      </select>

      <label for="job_reference">Job reference</label>
      <input id="job_reference" name="job_reference" maxlength="5" value="<?= auth_escape($jobReference) ?>" placeholder="e.g. AB123">

      <label for="first_name">First name</label>
      <input id="first_name" name="first_name" maxlength="50" value="<?= auth_escape($firstName) ?>">

      <label for="last_name">Last name</label>
      <input id="last_name" name="last_name" maxlength="50" value="<?= auth_escape($lastName) ?>">

      <label for="sort">Sort by</label>
      <select id="sort" name="sort">
        <?php foreach ($sortFields as $field => $label): ?>
          <option value="<?= auth_escape($field) ?>" <?= $sort === $field ? 'selected' : '' ?>><?= auth_escape($label) ?></option>
        <?php endforeach; ?>
      </select>

      <label for="dir">Order</label>
      <select id="dir" name="dir">
        <option value="asc" <?= $dir === 'asc' ? 'selected' : '' ?>>Ascending</option>
        <option value="desc" <?= $dir === 'desc' ? 'selected' : '' ?>>Descending</option>
      </select>

      <button type="submit">Show results</button>
    </form>
    <p class="manage-hint">For a name search, fill in the first name, the last name, or both. Both together finds that exact applicant.</p>
  </section>

  <section class="manage-panel" aria-labelledby="delete-heading">
    <h2 id="delete-heading">Delete by job reference</h2>
    <form method="post" action="manage.php?<?= auth_escape(http_build_query($query)) ?>" class="manage-delete">
      <input type="hidden" name="csrf_token" value="<?= auth_escape(auth_token()) ?>">
      <input type="hidden" name="action" value="delete">
      <label for="delete_reference">Job reference</label>
      <input id="delete_reference" name="job_reference" maxlength="5" required pattern="[A-Za-z0-9]{5}">
      <button type="submit">Delete all matching EOIs</button>
    </form>
  </section>

  <section class="manage-panel" aria-labelledby="results-heading">
    <h2 id="results-heading">Results (<?= count($records) ?>)</h2>
    <?php if (!$records): ?>
      <p>No EOI records match this search.</p>
    <?php else: ?>
      <div class="manage-table-wrap">
        <table>
          <thead>
            <tr>
              <th>EOI</th>
              <th>Applicant</th>
              <th>Contact</th>
              <th>Job</th>
              <th>Skills</th>
              <th>CV</th>
              <th>Received</th>
              <th>Status</th>
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
                    <select name="status" aria-label="Status for EOI <?= (int) $record['EOInumber'] ?>">
                      <?php foreach ($statuses as $status): ?>
                        <option value="<?= auth_escape($status) ?>" <?= $record['status'] === $status ? 'selected' : '' ?>><?= auth_escape($status) ?></option>
                      <?php endforeach; ?>
                    </select>
                    <button type="submit">Update</button>
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
