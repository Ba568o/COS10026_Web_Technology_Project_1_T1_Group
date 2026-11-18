<?php
session_start();
require_once __DIR__ . '/eoi_helpers.php';
$errors = $_SESSION['eoi_errors'] ?? [];
$old = $_SESSION['eoi_old'] ?? [];
unset($_SESSION['eoi_errors'], $_SESSION['eoi_old']);
$requestedJob = $_GET['job'] ?? '';
if (($old['job_reference'] ?? '') === '' && is_string($requestedJob) && preg_match('/^[A-Za-z0-9]{5}$/', $requestedJob)) {
    $old['job_reference'] = strtoupper($requestedJob);
}
if (empty($_SESSION['eoi_token'])) {
    $_SESSION['eoi_token'] = bin2hex(random_bytes(32));
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
              <span class="language-en">
                Explore open roles
                <i class="fa-solid fa-arrow-right-long"></i>
              </span>
              <span class="language-vi">
                Xem vị trí đang tuyển
                <i class="fa-solid fa-arrow-right-long"></i>
              </span>
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

              <?php if ($errors): ?>
              <div class="application-feedback" role="alert" tabindex="-1">
                <h3>Please correct your application / Vui lòng sửa thông tin</h3>
                <ul><?php foreach ($errors as $error): ?><li><?= eoi_escape($error) ?></li><?php endforeach; ?></ul>
                <p>Please select your CV again. / Vui lòng chọn lại CV.</p>
              </div>
              <?php endif; ?>
              <form
                action="process_eoi.php"
                novalidate
                method="POST"
                enctype="multipart/form-data"
                class="apply-form"
              >
                <input type="hidden" name="csrf_token" value="<?= eoi_escape($_SESSION['eoi_token']) ?>" />
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
                        value="<?= eoi_escape($old['first_name'] ?? '') ?>" />
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
                        value="<?= eoi_escape($old['last_name'] ?? '') ?>" />
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
                      <input type="date" id="dob" autocomplete="bday" name="date_of_birth" value="<?= eoi_escape($old['date_of_birth'] ?? '') ?>" />
                    </div>
                    <div class="form-group">
                      <span class="field-label" id="gender-label">
                        <span class="language-en">Gender</span>
                        <span class="language-vi">Giới tính</span>
                        <span class="required">*</span>
                      </span>
                      <div class="radio-group" role="radiogroup" aria-labelledby="gender-label">
                        <label class="radio-label">
                          <input type="radio" name="gender" value="male" <?= ($old['gender'] ?? '') === 'male' ? 'checked' : '' ?> />
                          <span class="language-en">Male</span>
                          <span class="language-vi">Nam</span>
                        </label>
                        <label class="radio-label">
                          <input type="radio" name="gender" value="female" <?= ($old['gender'] ?? '') === 'female' ? 'checked' : '' ?> />
                          <span class="language-en">Female</span>
                          <span class="language-vi">Nữ</span>
                        </label>
                        <label class="radio-label">
                          <input type="radio" name="gender" value="other" <?= ($old['gender'] ?? '') === 'other' ? 'checked' : '' ?> />
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
                        <input type="email" id="email" autocomplete="email" name="email" placeholder=" " value="<?= eoi_escape($old['email'] ?? '') ?>" />
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
                        value="<?= eoi_escape($old['phone'] ?? '') ?>" />
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
                      <input type="text" id="street" name="street_address" placeholder=" " value="<?= eoi_escape($old['street_address'] ?? '') ?>" />
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
                        value="<?= eoi_escape($old['suburb'] ?? '') ?>" />
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
                        <option value="" <?= ($old['state'] ?? '') === '' ? 'selected' : '' ?> disabled>-- Select State --</option>
                        <option value="VIC" <?= ($old['state'] ?? '') === 'VIC' ? 'selected' : '' ?>>VIC</option>
                        <option value="NSW" <?= ($old['state'] ?? '') === 'NSW' ? 'selected' : '' ?>>NSW</option>
                        <option value="QLD" <?= ($old['state'] ?? '') === 'QLD' ? 'selected' : '' ?>>QLD</option>
                        <option value="SA" <?= ($old['state'] ?? '') === 'SA' ? 'selected' : '' ?>>SA</option>
                        <option value="WA" <?= ($old['state'] ?? '') === 'WA' ? 'selected' : '' ?>>WA</option>
                        <option value="TAS" <?= ($old['state'] ?? '') === 'TAS' ? 'selected' : '' ?>>TAS</option>
                        <option value="NT" <?= ($old['state'] ?? '') === 'NT' ? 'selected' : '' ?>>NT</option>
                        <option value="ACT" <?= ($old['state'] ?? '') === 'ACT' ? 'selected' : '' ?>>ACT</option>
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
                        value="<?= eoi_escape($old['postcode'] ?? '') ?>" />
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
                        value="<?= eoi_escape($old['job_reference'] ?? '') ?>" />
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
                        <option value="" <?= ($old['position'] ?? '') === '' ? 'selected' : '' ?> disabled>-- Select Position --</option>
                        <option value="president" <?= ($old['position'] ?? '') === 'president' ? 'selected' : '' ?>>President</option>
                        <option value="vice-president" <?= ($old['position'] ?? '') === 'vice-president' ? 'selected' : '' ?>>Vice President</option>
                        <option value="manager" <?= ($old['position'] ?? '') === 'manager' ? 'selected' : '' ?>>Manager</option>
                        <option value="developer" <?= ($old['position'] ?? '') === 'developer' ? 'selected' : '' ?>>Developer</option>
                        <option value="designer" <?= ($old['position'] ?? '') === 'designer' ? 'selected' : '' ?>>Designer</option>
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
                        <input type="checkbox" name="skills[]" value="html-css" <?= in_array('html-css', $old['skills'] ?? [], true) ? 'checked' : '' ?> />
                        HTML/CSS
                      </label>
                      <label class="checkbox-label">
                        <input type="checkbox" name="skills[]" value="javascript" <?= in_array('javascript', $old['skills'] ?? [], true) ? 'checked' : '' ?> />
                        JavaScript
                      </label>
                      <label class="checkbox-label">
                        <input type="checkbox" name="skills[]" value="php" <?= in_array('php', $old['skills'] ?? [], true) ? 'checked' : '' ?> />
                        PHP
                      </label>
                      <label class="checkbox-label">
                        <input type="checkbox" name="skills[]" value="sql" <?= in_array('sql', $old['skills'] ?? [], true) ? 'checked' : '' ?> />
                        SQL/Database
                      </label>
                      <label class="checkbox-label">
                        <input type="checkbox" name="skills[]" value="python" <?= in_array('python', $old['skills'] ?? [], true) ? 'checked' : '' ?> />
                        Python
                      </label>
                      <label class="checkbox-label">
                        <input type="checkbox" name="skills[]" value="project-management" <?= in_array('project-management', $old['skills'] ?? [], true) ? 'checked' : '' ?> />
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
                      ><?= eoi_escape($old['other_skills'] ?? '') ?></textarea>
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
                      <label class="file-button-label" for="cv">
                        <span class="language-en">Choose File</span>
                        <span class="language-vi">Chọn tệp</span>
                      </label>
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
                      <input type="checkbox" id="terms" name="terms" <?= ($old['terms'] ?? '') === 'on' ? 'checked' : '' ?> />
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
                    <button type="reset" class="btn btn-reset">
                      <span class="language-en">Reset Form</span>
                      <span class="language-vi">Đặt lại biểu mẫu</span>
                    </button>
                    <button type="submit" class="btn btn-submit">
                      <span class="language-en">Submit Application</span>
                      <span class="language-vi">Gửi đơn ứng tuyển</span>
                    </button>
                  </div>
                </fieldset>
              </form>
            </div>
          </div>
        </div>
      </section>
    </main>

    <?php include 'footer.inc'; ?>
