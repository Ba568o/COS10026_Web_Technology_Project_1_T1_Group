<?php
require_once __DIR__ . '/auth/session.php';
require_once __DIR__ . '/settings.php';
require_once __DIR__ . '/eoi_helpers.php';
auth_start();

/** Trims posted text and removes tags or extra slashes. */
function apply_clean($value): string
{
    // Checkboxes and missing fields are not strings, so they become empty.
    if (!is_string($value)) return '';
    // Remove added slashes, HTML tags and surrounding spaces.
    return trim(strip_tags(stripslashes($value)));
}

$applyPositions = ['president', 'vice-president', 'manager', 'developer', 'designer'];
$applyStates = ['VIC', 'NSW', 'QLD', 'SA', 'WA', 'TAS', 'NT', 'ACT'];
$applyGenders = ['male', 'female', 'other'];
$applySkills = ['html-css', 'javascript', 'php', 'sql', 'python', 'project-management'];
$values = [
    'first_name' => '', 'last_name' => '', 'date_of_birth' => '', 'gender' => '',
    'email' => '', 'phone' => '', 'street_address' => '', 'suburb' => '', 'state' => '',
    'postcode' => '', 'job_reference' => '', 'position' => '', 'other_skills' => '',
    'skills' => [], 'terms' => false,
];
if (is_string($_GET['job'] ?? null) && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    $values['job_reference'] = strtoupper(apply_clean($_GET['job']));
}
$errors = [];
$eoiNumber = null;
if (!empty($_SESSION['apply_eoi'])) {
    $eoiNumber = (int) $_SESSION['apply_eoi'];
    unset($_SESSION['apply_eoi']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Copy each text field into $values after cleaning.
    foreach (['first_name', 'last_name', 'date_of_birth', 'gender', 'email', 'phone', 'street_address', 'suburb', 'state', 'postcode', 'job_reference', 'position', 'other_skills'] as $key) {
        $values[$key] = apply_clean($_POST[$key] ?? null);
    }
    $values['job_reference'] = strtoupper($values['job_reference']);
    $values['state'] = strtoupper($values['state']);
    $values['skills'] = [];
    // Keep only skill values that appear on the form.
    foreach (is_array($_POST['skills'] ?? null) ? $_POST['skills'] : [] as $skill) {
        $skill = apply_clean($skill);
        if (in_array($skill, $applySkills, true)) $values['skills'][] = $skill;
    }
    // A missing checkbox means the terms were not accepted.
    $values['terms'] = isset($_POST['terms']);

    if ($values['first_name'] === '' || mb_strlen($values['first_name']) > 50 || !preg_match("/^[\\p{L}\\s.'-]+$/u", $values['first_name'])) $errors[] = 'First name: 1–50 letters. / Tên: 1–50 chữ.';
    if ($values['last_name'] === '' || mb_strlen($values['last_name']) > 50 || !preg_match("/^[\\p{L}\\s.'-]+$/u", $values['last_name'])) $errors[] = 'Last name: 1–50 letters. / Họ: 1–50 chữ.';
    $dob = DateTime::createFromFormat('Y-m-d', $values['date_of_birth']);
    // format() must match the input, so dates like 2024-02-31 are rejected.
    if (!$dob || $dob->format('Y-m-d') !== $values['date_of_birth']) {
        $errors[] = 'Enter a valid date of birth. / Nhập ngày sinh hợp lệ.';
    } else {
        $age = $dob->diff(new DateTime('today'))->y;
        if ($age < 15 || $age > 80) $errors[] = 'Age must be from 15 to 80. / Tuổi từ 15 đến 80.';
    }
    if (!in_array($values['gender'], $applyGenders, true)) $errors[] = 'Choose a gender. / Chọn giới tính.';
    if (!filter_var($values['email'], FILTER_VALIDATE_EMAIL) || strlen($values['email']) > 254) $errors[] = 'Enter a valid email. / Nhập email hợp lệ.';
    $phoneDigits = preg_replace('/\D/', '', $values['phone']);
    if ($values['phone'] === '' || strlen($values['phone']) > 15 || strlen($phoneDigits) < 8) $errors[] = 'Phone: 8–15 digits, maximum 15 characters. / Số điện thoại: 8–15 chữ số, tối đa 15 ký tự.';
    if ($values['street_address'] === '' || mb_strlen($values['street_address']) > 100) $errors[] = 'Street address is required, maximum 100 characters. / Địa chỉ tối đa 100 ký tự.';
    if ($values['suburb'] === '' || mb_strlen($values['suburb']) > 50) $errors[] = 'Suburb is required, maximum 50 characters. / Quận hoặc thành phố tối đa 50 ký tự.';
    if (!in_array($values['state'], $applyStates, true)) $errors[] = 'Choose a state. / Chọn bang.';
    if (!preg_match('/^\d{4}$/', $values['postcode'])) $errors[] = 'Postcode must be 4 digits. / Mã bưu chính gồm 4 số.';
    if (!preg_match('/^[A-Z0-9]{5}$/', $values['job_reference'])) $errors[] = 'Job reference must be 5 letters or digits. / Mã việc gồm 5 chữ hoặc số.';
    if (!in_array($values['position'], $applyPositions, true)) $errors[] = 'Choose a position. / Chọn vị trí.';
    if (mb_strlen($values['other_skills']) > 500) $errors[] = 'Other skills: maximum 500 characters. / Kỹ năng khác tối đa 500 ký tự.';
    if (!$values['terms']) $errors[] = 'Please accept the terms. / Vui lòng đồng ý điều khoản.';

    // Validate the uploaded CV before any database write.
    $cv = $_FILES['cv'] ?? null;
    $cvData = '';
    $cvName = '';
    $cvMime = '';
    if (!is_array($cv) || ($cv['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        $errors[] = 'Upload a CV. / Hãy tải CV lên.';
    } elseif (($cv['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
        $errors[] = 'The CV could not be uploaded. / Không tải được CV.';
    } else {
        $cvName = apply_clean(basename((string) $cv['name']));
        $extension = strtolower(pathinfo($cvName, PATHINFO_EXTENSION));
        if (!in_array($extension, ['pdf', 'doc', 'docx'], true)) $errors[] = 'CV must be PDF, DOC or DOCX. / CV phải là PDF, DOC hoặc DOCX.';
        if (($cv['size'] ?? 0) < 1 || ($cv['size'] ?? 0) > 5 * 1024 * 1024) $errors[] = 'CV must be 5 MB or smaller. / CV tối đa 5 MB.';
        if (!$errors) {
            // Read the temp file so the CV can be stored as a blob.
            $cvData = file_get_contents($cv['tmp_name']);
            $detected = mime_content_type($cv['tmp_name']);
            $cvMime = is_string($detected) ? $detected : 'application/octet-stream';
            if ($cvData === false || $cvData === '') $errors[] = 'The CV file is empty. / Tệp CV trống.';
            if (strlen($cvName) > 255) $cvName = substr($cvName, -255);
            if (strlen($cvMime) > 100) $cvMime = substr($cvMime, 0, 100);
        }
    }

    if (!$errors) {
        try {
            $db = database_connection();
            eoi_ensure_table($db);
            // Skills are stored as a JSON list. Status stays at the default New.
            $insert = $db->prepare('INSERT INTO eoi (first_name, last_name, date_of_birth, gender, email, phone, street_address, suburb, state, postcode, job_reference, position, skills, other_skills, cv_name, cv_mime, cv, terms) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
            $insert->execute([
                $values['first_name'], $values['last_name'], $values['date_of_birth'], $values['gender'],
                $values['email'], $values['phone'], $values['street_address'], $values['suburb'],
                $values['state'], $values['postcode'], $values['job_reference'], $values['position'],
                json_encode(array_values(array_unique($values['skills'])), JSON_UNESCAPED_UNICODE),
                $values['other_skills'], $cvName, $cvMime, $cvData, 1,
            ]);
            $_SESSION['apply_eoi'] = (int) $db->lastInsertId();
            // 303 sends the browser to GET so a refresh does not insert again.
            header('Location: apply.php#application', true, 303);
            exit;
        } catch (PDOException $exception) {
            error_log('Apply save error: ' . $exception->getMessage());
            $errors[] = 'The application could not be saved. / Không lưu được đơn.';
        }
    }
}

$pageTitle = 'Job Application - T1 Health & Wellness';
$pageDescription = 'Apply for a job position at T1 Health & Wellness. Fill out the application form with your details.';
$bodyClass = 'apply-page';
$pageStylesheet = 'apply.css';
$bodyId = 'top';
$currentPage = basename($_SERVER['PHP_SELF']);
include 'header.inc';
include 'nav.inc';
?>
<main id="main-content">
      <section class="hero" aria-labelledby="apply-heading">
        <div class="container hero-layout">
          <div class="hero-content">
            <p class="tag">
              <span class="language-en">YOUR FUTURE AT T1</span>
              <span class="language-vi">TƯƠNG LAI CỦA BẠN TẠI T1</span>
            </p>
            <h1 id="apply-heading">
              <span class="language-en">
                Great things start
                <br />
                with you.
              </span>
              <span class="language-vi">
                Những điều tuyệt vời
                <br />
                bắt đầu từ bạn.
              </span>
            </h1>
            <p class="hero-text">
              <span class="language-en">
                Bring your curiosity, your ideas, and your ambition. Let&rsquo;s take the next step, together.
              </span>
              <span class="language-vi">Hãy mang theo sự tò mò, ý tưởng và khát vọng của bạn. Cùng nhau bước tiếp.</span>
            </p>
            <a class="btn btn-submit" href="jobs.php">
              <span class="language-en">Explore open roles</span>
              <span class="language-vi">Xem vị trí đang tuyển</span>
              <i class="fa-solid fa-arrow-right-long" aria-hidden="true"></i>
            </a>
          </div>

          <div class="team-animation apply-team-animation">
            <div class="team-orbit team-orbit-one" aria-hidden="true"></div>
            <div class="team-orbit team-orbit-two" aria-hidden="true"></div>

            <div class="team-member team-member-one" aria-hidden="true">
              <span class="member-head"></span>
              <span class="member-body"></span>
            </div>
            <div class="team-member team-member-two" aria-hidden="true">
              <span class="member-head"></span>
              <span class="member-body"></span>
            </div>
            <div class="team-member team-member-three" aria-hidden="true">
              <span class="member-head"></span>
              <span class="member-body"></span>
            </div>

            <div class="team-message-card">
              <img src="images/logo.png" alt="" class="team-logo" />
              <p>
                <span class="language-en">
                  Different strengths.
                  <br />
                  <strong>One team.</strong>
                </span>
                <span class="language-vi">
                  Khác biệt về thế mạnh.
                  <br />
                  <strong>Một đội ngũ.</strong>
                </span>
              </p>
              <span class="team-caption">
                <span class="language-en">That&rsquo;s the T1 spirit.</span>
                <span class="language-vi">Đó chính là tinh thần T1.</span>
              </span>
            </div>

            <div class="team-floating-note team-note-one" aria-hidden="true">
              <i class="fa-solid fa-heart-pulse"></i>
            </div>
            <div class="team-floating-note team-note-two" aria-hidden="true">
              <i class="fa-solid fa-handshake"></i>
            </div>
            <span class="team-spark team-spark-one" aria-hidden="true"></span>
            <span class="team-spark team-spark-two" aria-hidden="true"></span>
            <span class="team-spark team-spark-three" aria-hidden="true"></span>
          </div>
        </div>
      </section>

      <section class="application-section" id="skip-to-application">
        <div class="container">
          <div class="application-layout">
            <aside class="application-sidebar" aria-label="Application guide">
              <div class="sidebar-content">
                <p class="eyebrow">
                  <span class="language-en">YOUR APPLICATION</span>
                  <span class="language-vi">HỒ SƠ ỨNG TUYỂN</span>
                </p>
                <h2>
                  <span class="language-en">
                    A little about you.
                    <br />
                    A world of possibility.
                  </span>
                  <span class="language-vi">
                    Một chút về bạn.
                    <br />
                    Một thế giới cơ hội.
                  </span>
                </h2>
                <p class="sidebar-description">
                  <span class="language-en">Have your job reference ready, then tell us what you can bring to the team.</span>
                  <span class="language-vi">Hãy chuẩn bị mã tham chiếu công việc, rồi cho chúng tôi biết bạn mang đến điều gì.</span>
                </p>
                <nav class="section-nav" aria-label="Jump to application section">
                  <ol>
                    <li>
                      <a href="#personal-info">
                        <span class="step-number">01</span>
                        <span class="step-text">
                          <span class="language-en">Personal information</span>
                          <span class="language-vi">Thông tin cá nhân</span>
                          <small>
                            <span class="language-en">Start with the basics</span>
                            <span class="language-vi">Bắt đầu từ thông tin cơ bản</span>
                          </small>
                        </span>
                      </a>
                    </li>
                    <li>
                      <a href="#address-info">
                        <span class="step-number">02</span>
                        <span class="step-text">
                          <span class="language-en">Your address</span>
                          <span class="language-vi">Địa chỉ của bạn</span>
                          <small>
                            <span class="language-en">Where you&rsquo;re based</span>
                            <span class="language-vi">Nơi bạn đang sinh sống</span>
                          </small>
                        </span>
                      </a>
                    </li>
                    <li>
                      <a href="#job-details">
                        <span class="step-number">03</span>
                        <span class="step-text">
                          <span class="language-en">The right fit</span>
                          <span class="language-vi">Vị trí phù hợp</span>
                          <small>
                            <span class="language-en">Your role and skills</span>
                            <span class="language-vi">Vai trò và kỹ năng của bạn</span>
                          </small>
                        </span>
                      </a>
                    </li>
                    <li>
                      <a href="#submit-section">
                        <span class="step-number">04</span>
                        <span class="step-text">
                          <span class="language-en">Review &amp; submit</span>
                          <span class="language-vi">Xem lại &amp; gửi</span>
                          <small>
                            <span class="language-en">Ready for what&rsquo;s next</span>
                            <span class="language-vi">Sẵn sàng cho bước tiếp theo</span>
                          </small>
                        </span>
                      </a>
                    </li>
                  </ol>
                </nav>
                <p class="sidebar-help">
                  <span class="language-en">
                    Still finding your fit?
                    <br />
                    <a href="jobs.php">Take a look at our open roles</a>
                  </span>
                  <span class="language-vi">
                    Vẫn đang tìm vị trí phù hợp?
                    <br />
                    <a href="jobs.php">Xem các vị trí đang tuyển</a>
                  </span>
                </p>
              </div>
            </aside>

            <div class="application-card" id="application">
              <div class="form-heading">
                <div>
                  <p class="eyebrow">
                    <span class="language-en">MAKE YOUR NEXT MOVE</span>
                    <span class="language-vi">BƯỚC TIẾP THEO CỦA BẠN</span>
                  </p>
                  <h2>
                    <span class="language-en">Job application</span>
                    <span class="language-vi">Đơn ứng tuyển</span>
                  </h2>
                </div>
                <p class="required-note">
                  <span class="required">*</span>
                  <span class="language-en">Required fields</span>
                  <span class="language-vi">Trường bắt buộc</span>
                </p>
              </div>

              <?php if ($eoiNumber): ?>
              <p class="apply-result ok" role="status"><span class="language-en">Application received. Your EOI number is <?= (int) $eoiNumber ?>.</span><span class="language-vi">Đã nhận đơn. Số EOI của bạn là <?= (int) $eoiNumber ?>.</span></p>
              <?php elseif ($errors): ?>
              <div class="apply-result bad" role="alert"><?php foreach ($errors as $error): ?><p><?= auth_language_pair($error) ?></p><?php endforeach; ?></div>
              <?php endif; ?>
              <form
                action="apply.php#application"
                method="POST"
                enctype="multipart/form-data"
                class="apply-form"
                novalidate
              >
                <fieldset class="form-section" id="personal-info">
                  <legend>
                    <span class="section-number" aria-hidden="true">01</span>
                    <span class="language-en">Personal information</span>
                    <span class="language-vi">Thông tin cá nhân</span>
                  </legend>
                  <p class="section-description">
                    <span class="language-en">Let&rsquo;s start with your details and how we can reach you.</span>
                    <span class="language-vi">Hãy bắt đầu với thông tin của bạn và cách chúng tôi có thể liên hệ.</span>
                  </p>

                  <div class="form-row">
                    <div class="form-group">
                      <label for="first-name">
                        <span class="language-en">First Name</span>
                        <span class="language-vi">Tên</span>
                        <span class="required">*</span>
                      </label>
                      <span class="placeholder-field">
                        <input
                          type="text"
                          id="first-name"
                          autocomplete="given-name"
                          name="first_name"
                          placeholder=" "
                        value="<?= auth_escape($values['first_name']) ?>" />
                        <span class="input-placeholder language-en" aria-hidden="true">e.g. Sy Tho</span>
                        <span class="input-placeholder language-vi" aria-hidden="true">Ví dụ: Sỹ Thọ</span>
                      </span>
                    </div>
                    <div class="form-group">
                      <label for="last-name">
                        <span class="language-en">Last Name</span>
                        <span class="language-vi">Họ</span>
                        <span class="required">*</span>
                      </label>
                      <span class="placeholder-field">
                        <input
                          type="text"
                          id="last-name"
                          autocomplete="family-name"
                          name="last_name"
                          placeholder=" "
                        value="<?= auth_escape($values['last_name']) ?>" />
                        <span class="input-placeholder language-en" aria-hidden="true">e.g. Nguyen</span>
                        <span class="input-placeholder language-vi" aria-hidden="true">Ví dụ: Nguyễn</span>
                      </span>
                    </div>
                  </div>

                  <div class="form-row">
                    <div class="form-group">
                      <label for="dob">
                        <span class="language-en">Date of Birth</span>
                        <span class="language-vi">Ngày sinh</span>
                        <span class="required">*</span>
                      </label>
                      <input type="date" id="dob" autocomplete="bday" name="date_of_birth" value="<?= auth_escape($values['date_of_birth']) ?>" />
                    </div>
                    <div class="form-group">
                      <span class="field-label" id="gender-label">
                        <span class="language-en">Gender</span>
                        <span class="language-vi">Giới tính</span>
                        <span class="required">*</span>
                      </span>
                      <div class="radio-group" role="radiogroup" aria-labelledby="gender-label">
                        <label class="radio-label">
                          <input type="radio" name="gender" value="male" <?= $values['gender'] === 'male' ? 'checked' : '' ?> />
                          <span class="language-en">Male</span>
                          <span class="language-vi">Nam</span>
                        </label>
                        <label class="radio-label">
                          <input type="radio" name="gender" value="female" <?= $values['gender'] === 'female' ? 'checked' : '' ?> />
                          <span class="language-en">Female</span>
                          <span class="language-vi">Nữ</span>
                        </label>
                        <label class="radio-label">
                          <input type="radio" name="gender" value="other" <?= $values['gender'] === 'other' ? 'checked' : '' ?> />
                          <span class="language-en">Other</span>
                          <span class="language-vi">Khác</span>
                        </label>
                      </div>
                    </div>
                  </div>

                  <div class="form-row">
                    <div class="form-group">
                      <label for="email">
                        <span class="language-en">Email Address</span>
                        <span class="language-vi">Địa chỉ email</span>
                        <span class="required">*</span>
                      </label>
                      <span class="placeholder-field">
                        <input type="text" id="email" autocomplete="email" name="email" placeholder=" " value="<?= auth_escape($values['email']) ?>" />
                        <span class="input-placeholder language-en" aria-hidden="true">e.g. example@email.com</span>
                        <span class="input-placeholder language-vi" aria-hidden="true">Ví dụ: tên_mẫu@email.com</span>
                      </span>
                    </div>
                    <div class="form-group">
                      <label for="phone">
                        <span class="language-en">Phone Number</span>
                        <span class="language-vi">Số điện thoại</span>
                        <span class="required">*</span>
                      </label>
                      <span class="placeholder-field">
                        <input
                          type="tel"
                          id="phone"
                          autocomplete="tel"
                          name="phone"
                          placeholder=" "
                        value="<?= auth_escape($values['phone']) ?>" />
                        <span class="input-placeholder language-en" aria-hidden="true">e.g. 0412 345 678</span>
                        <span class="input-placeholder language-vi" aria-hidden="true">Ví dụ: 0412 345 678</span>
                      </span>
                    </div>
                  </div>
                </fieldset>

                <fieldset class="form-section" id="address-info">
                  <legend>
                    <span class="section-number" aria-hidden="true">02</span>
                    <span class="language-en">Your address</span>
                    <span class="language-vi">Địa chỉ của bạn</span>
                  </legend>
                  <p class="section-description">
                    <span class="language-en">Tell us where you&rsquo;re currently based.</span>
                    <span class="language-vi">Cho chúng tôi biết bạn đang sinh sống ở đâu.</span>
                  </p>

                  <div class="form-group form-group-full">
                    <label for="street">
                      <span class="language-en">Street Address</span>
                      <span class="language-vi">Địa chỉ đường</span>
                      <span class="required">*</span>
                    </label>
                    <span class="placeholder-field">
                      <input type="text" id="street" name="street_address" placeholder=" " value="<?= auth_escape($values['street_address']) ?>" />
                      <span class="input-placeholder language-en" aria-hidden="true">e.g. 123 Burwood Road</span>
                      <span class="input-placeholder language-vi" aria-hidden="true">Ví dụ: 123 đường Burwood</span>
                    </span>
                  </div>

                  <div class="form-row">
                    <div class="form-group">
                      <label for="suburb">
                        <span class="language-en">Suburb/City</span>
                        <span class="language-vi">Quận/Thành phố</span>
                        <span class="required">*</span>
                      </label>
                      <span class="placeholder-field">
                        <input
                          type="text"
                          id="suburb"
                          autocomplete="address-level2"
                          name="suburb"
                          placeholder=" "
                        value="<?= auth_escape($values['suburb']) ?>" />
                        <span class="input-placeholder language-en" aria-hidden="true">e.g. Hawthorn</span>
                        <span class="input-placeholder language-vi" aria-hidden="true">Ví dụ: Thành phố Hawthorn</span>
                      </span>
                    </div>
                    <div class="form-group">
                      <label id="state-label" for="state-en">
                        <span class="language-en">State</span>
                        <span class="language-vi">Bang/Tỉnh</span>
                        <span class="required">*</span>
                      </label>
                      <select id="state-en" aria-labelledby="state-label" autocomplete="address-level1" name="state">
                        <option value="" disabled <?= $values['state'] === '' ? 'selected' : '' ?> class="language-en">-- Select State --</option>
                        <option value="" disabled <?= $values['state'] === '' ? 'selected' : '' ?> class="language-vi">-- Chọn bang --</option>
                        <?php foreach (['VIC', 'NSW', 'QLD', 'SA', 'WA', 'TAS', 'NT', 'ACT'] as $state): ?>
                        <option value="<?= $state ?>" <?= $values['state'] === $state ? 'selected' : '' ?>><?= $state ?></option>
                        <?php endforeach; ?>
                      </select>

                    </div>
                    <div class="form-group">
                      <label for="postcode">
                        <span class="language-en">Postcode</span>
                        <span class="language-vi">Mã bưu chính</span>
                        <span class="required">*</span>
                      </label>
                      <span class="placeholder-field">
                        <input
                          type="text"
                          id="postcode"
                          inputmode="numeric"
                          autocomplete="postal-code"
                          name="postcode"
                          placeholder=" "
                        value="<?= auth_escape($values['postcode']) ?>" />
                        <span class="input-placeholder language-en" aria-hidden="true">e.g. 3122</span>
                        <span class="input-placeholder language-vi" aria-hidden="true">Ví dụ: 3122</span>
                      </span>
                    </div>
                  </div>
                </fieldset>

                <fieldset class="form-section" id="job-details">
                  <legend>
                    <span class="section-number" aria-hidden="true">03</span>
                    <span class="language-en">Your role &amp; skills</span>
                    <span class="language-vi">Vai trò &amp; kỹ năng</span>
                  </legend>
                  <p class="section-description">
                    <span class="language-en">Choose your role, the skills you&rsquo;d bring to T1, and upload your CV.</span>
                    <span class="language-vi">Chọn vị trí, kỹ năng bạn mang đến T1, và tải lên sơ yếu lý lịch của bạn.</span>
                  </p>

                  <div class="form-row">
                    <div class="form-group">
                      <label for="job-ref">
                        <span class="language-en">Job Reference Number</span>
                        <span class="language-vi">Mã tham chiếu công việc</span>
                        <span class="required">*</span>
                      </label>
                      <span class="placeholder-field">
                        <input
                          type="text"
                          id="job-ref"
                          name="job_reference"
                          placeholder=" "
                        value="<?= auth_escape($values['job_reference']) ?>" />
                        <span class="input-placeholder language-en" aria-hidden="true">e.g. AB123</span>
                        <span class="input-placeholder language-vi" aria-hidden="true">Ví dụ: AB123</span>
                      </span>
                    </div>
                    <div class="form-group">
                      <label id="position-label" for="position-en">
                        <span class="language-en">Preferred Position</span>
                        <span class="language-vi">Vị trí mong muốn</span>
                        <span class="required">*</span>
                      </label>
                      <select id="position-en" aria-labelledby="position-label" name="position">
                        <option value="" disabled <?= $values['position'] === '' ? 'selected' : '' ?> class="language-en">-- Select Position --</option>
                        <option value="" disabled <?= $values['position'] === '' ? 'selected' : '' ?> class="language-vi">-- Chọn vị trí --</option>
                        <?php foreach (['president' => ['President', 'Chủ tịch'], 'vice-president' => ['Vice President', 'Phó chủ tịch'], 'manager' => ['Manager', 'Quản lý'], 'developer' => ['Developer', 'Lập trình viên'], 'designer' => ['Designer', 'Nhà thiết kế']] as $position => $label): ?>
                        <option value="<?= $position ?>" <?= $values['position'] === $position ? 'selected' : '' ?> class="language-en"><?= $label[0] ?></option>
                        <option value="<?= $position ?>" <?= $values['position'] === $position ? 'selected' : '' ?> class="language-vi"><?= $label[1] ?></option>
                        <?php endforeach; ?>
                      </select>

                    </div>
                  </div>

                  <div class="form-group form-group-full">
                    <span class="field-label" id="skills-label">
                      <span class="language-en">Key Skills</span>
                      <span class="language-vi">Kỹ năng chính</span>
                      <span class="optional">
                        <span class="language-en">(optional)</span>
                        <span class="language-vi">(không bắt buộc)</span>
                      </span>
                    </span>
                    <div class="checkbox-group" role="group" aria-labelledby="skills-label">
                      <label class="checkbox-label">
                        <input type="checkbox" name="skills[]" value="html-css" <?= in_array('html-css', $values['skills'], true) ? 'checked' : '' ?> />
                        HTML/CSS
                      </label>
                      <label class="checkbox-label">
                        <input type="checkbox" name="skills[]" value="javascript" <?= in_array('javascript', $values['skills'], true) ? 'checked' : '' ?> />
                        JavaScript
                      </label>
                      <label class="checkbox-label">
                        <input type="checkbox" name="skills[]" value="php" <?= in_array('php', $values['skills'], true) ? 'checked' : '' ?> />
                        PHP
                      </label>
                      <label class="checkbox-label">
                        <input type="checkbox" name="skills[]" value="sql" <?= in_array('sql', $values['skills'], true) ? 'checked' : '' ?> />
                        <span class="language-en">SQL/Database</span>
                        <span class="language-vi">SQL/Cơ sở dữ liệu</span>
                      </label>
                      <label class="checkbox-label">
                        <input type="checkbox" name="skills[]" value="python" <?= in_array('python', $values['skills'], true) ? 'checked' : '' ?> />
                        Python
                      </label>
                      <label class="checkbox-label">
                        <input type="checkbox" name="skills[]" value="project-management" <?= in_array('project-management', $values['skills'], true) ? 'checked' : '' ?> />
                        <span class="language-en">Project Management</span>
                        <span class="language-vi">Quản lý dự án</span>
                      </label>
                    </div>
                  </div>

                  <div class="form-group form-group-full">
                    <label for="other-skills">
                      <span class="language-en">Other Skills &amp; Qualifications</span>
                      <span class="language-vi">Kỹ năng &amp; bằng cấp khác</span>
                    </label>
                    <span class="placeholder-field">
                      <textarea
                        id="other-skills"
                        name="other_skills"
                        rows="5"
                        placeholder=" "
                        aria-describedby="other-skills-help"
                      ><?= auth_escape($values['other_skills']) ?></textarea>
                      <span class="input-placeholder language-en" aria-hidden="true">
                        Describe any additional skills, certifications, or relevant experience...
                      </span>
                      <span class="input-placeholder language-vi" aria-hidden="true">
                        Mô tả kỹ năng, chứng chỉ hoặc kinh nghiệm liên quan khác...
                      </span>
                    </span>
                    <small id="other-skills-help" class="form-help">
                      <span class="language-en">Maximum 500 characters</span>
                      <span class="language-vi">Tối đa 500 ký tự</span>
                    </small>
                  </div>

                  <div class="form-group form-group-full">
                    <label for="cv">
                      <span class="language-en">Upload CV</span>
                      <span class="language-vi">Tải lên sơ yếu lí lịch</span>
                      <span class="required">*</span>
                    </label>
                    <span class="file-input-wrap">
                      <input type="file" id="cv" name="cv" aria-describedby="cv-help" />
                      <label class="file-button-label" for="cv"><span class="language-en">Choose File</span><span class="language-vi">Chọn tệp</span></label>
                    </span>
                    <small id="cv-help" class="form-help file-note">
                      <span class="language-en">PDF, DOC or DOCX, maximum 5 MB</span>
                      <span class="language-vi">PDF, DOC hoặc DOCX, tối đa 5 MB</span>
                    </small>
                  </div>
                </fieldset>

                <fieldset class="form-section" id="submit-section">
                  <legend>
                    <span class="section-number" aria-hidden="true">04</span>
                    <span class="language-en">Review &amp; submit</span>
                    <span class="language-vi">Xem lại &amp; gửi</span>
                  </legend>
                  <p class="section-description">
                    <span class="language-en">One last look, then you&rsquo;re ready to take the next step.</span>
                    <span class="language-vi">Xem lại lần cuối, rồi bạn sẵn sàng cho bước tiếp theo.</span>
                  </p>

                  <div class="acknowledgement">
                    <h3>
                      <span class="language-en">Acknowledgement of Country</span>
                      <span class="language-vi">Lời tri ân tới Chủ nhân Truyền thống</span>
                    </h3>
                    <p>
                      <span class="language-en">
                        We acknowledge the Traditional Owners of the land on which we work and live, and pay our respects to Elders past,
                        present and emerging.
                      </span>
                      <span class="language-vi">
                        Chúng tôi tri ân các Chủ nhân Truyền thống của vùng đất nơi chúng tôi làm việc và sinh sống, và gửi lời kính trọng
                        tới các bậc trưởng lão trong quá khứ, hiện tại và tương lai.
                      </span>
                    </p>
                  </div>

                  <div class="form-group form-group-full">
                    <label class="checkbox-label terms-label">
                      <input type="checkbox" id="terms" name="terms" <?= $values['terms'] ? 'checked' : '' ?> />
                      <span>
                        <span class="language-en">
                          I confirm that all information provided is accurate and I agree to the
                          <a href="#">terms and conditions</a>
                          .
                        </span>
                        <span class="language-vi">
                          Tôi xác nhận mọi thông tin đã cung cấp là chính xác và đồng ý với
                          <a href="#">điều khoản và điều kiện</a>
                          .
                        </span>
                        <span class="required">*</span>
                      </span>
                    </label>
                  </div>

                  <div class="form-buttons">
                    <button type="reset" class="btn btn-reset"><span class="language-en">Reset Form</span><span class="language-vi">Đặt lại biểu mẫu</span></button>
                    <button type="submit" class="btn btn-submit"><span class="language-en">Submit Application</span><span class="language-vi">Gửi đơn ứng tuyển</span></button>
                  </div>
                </fieldset>
              </form>
            </div>
          </div>
        </div>
      </section>
    </main>

    <?php include 'footer.inc'; ?>
