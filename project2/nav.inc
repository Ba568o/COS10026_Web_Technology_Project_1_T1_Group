<?php
require_once __DIR__ . '/auth/session.php';
$navRole = auth_logged_in() ? (string) ($_SESSION['role'] ?? '') : '';
// Saved display settings from header.inc; the defaults apply if header.inc did not set them.
$prefsNav = (isset($prefs) && is_array($prefs) ? $prefs : []) + ['theme' => 'light', 'lang' => 'en', 'font' => 'normal'];
?>
<nav class="navbar" aria-label="Main navigation">
        <!-- Brand links to the home page. -->
        <a href="index.php" class="brand" aria-label="T1 Health &amp; Wellness - home">
          <img src="images/logo.png" alt="" class="brand-logo" />
          <span class="brand-text">
            <strong>T1</strong>
            <span>HEALTH &amp; WELLNESS</span>
          </span>
        </a>

        <!-- Primary navigation; the brand provides the link to Home. -->
        <ul class="nav-links">
          <li>
            <a href="index.php" <?php echo $currentPage === 'index.php' ? 'aria-current="page"' : ''; ?>>
              <span class="language-en">Home</span>
              <span class="language-vi">Trang chủ</span>
            </a>
          </li>
          <li>
            <a href="jobs.php" <?php echo $currentPage === 'jobs.php' ? 'aria-current="page"' : ''; ?>>
              <span class="language-en">Jobs</span>
              <span class="language-vi">Tuyển dụng</span>
            </a>
          </li>
          <li>
            <a href="apply.php" <?php echo $currentPage === 'apply.php' ? 'aria-current="page"' : ''; ?>>
              <span class="language-en">Apply</span>
              <span class="language-vi">Ứng tuyển</span>
            </a>
          </li>
          <li>
            <a href="about.php" <?php echo $currentPage === 'about.php' ? 'aria-current="page"' : ''; ?>>
              <span class="language-en">About</span>
              <span class="language-vi">Giới thiệu</span>
            </a>
          </li>
        </ul>

        <!-- Display settings, site search, and the primary call to action. -->
        <div class="nav-actions">
          <input class="visually-hidden" type="checkbox" id="setting-toggle" />

          <!-- Display settings menu: language, theme, and text size.
               Every option is a submit button: one click posts that single choice to preferences.php,
               which saves it in a cookie and reloads this page. The choice then stays on every page.
               No JavaScript and no "Save" button are needed. -->
          <div id="setting-container">
            <!-- The form sits INSIDE #setting-container: the CSS rule "#setting-toggle:checked + #setting-container"
                 needs the container to stay right after #setting-toggle. display: contents keeps the old layout. -->
            <form class="settings-form" action="preferences.php" method="post">
            <!-- Page to return to after saving (checked again in preferences.php). -->
            <input type="hidden" name="return" value="<?= htmlspecialchars(basename($_SERVER['SCRIPT_NAME'] ?? 'index.php') . (($_SERVER['QUERY_STRING'] ?? '') !== '' ? '?' . $_SERVER['QUERY_STRING'] : ''), ENT_QUOTES, 'UTF-8') ?>" />

            <!-- State inputs for the existing CSS. The stylesheets switch theme, language and text size with
                 :has(#dark-toggle:checked), #language-vi:checked and #font-large:checked, so these inputs keep
                 their ids and PHP ticks the saved ones. They have no name (never sent) and are disabled
                 (not clickable); the buttons below are what the visitor uses. -->
            <input class="visually-hidden" type="checkbox" id="dark-toggle" tabindex="-1" aria-hidden="true" disabled <?= $prefsNav['theme'] === 'dark' ? 'checked' : '' ?> />
            <input class="visually-hidden" type="radio" id="language-en" tabindex="-1" aria-hidden="true" disabled <?= $prefsNav['lang'] === 'en' ? 'checked' : '' ?> />
            <input class="visually-hidden" type="radio" id="language-vi" tabindex="-1" aria-hidden="true" disabled <?= $prefsNav['lang'] === 'vi' ? 'checked' : '' ?> />
            <?php foreach (['super-small', 'small', 'normal', 'large', 'super-large'] as $fontKey): ?>
            <input class="visually-hidden" type="radio" id="font-<?= $fontKey ?>" tabindex="-1" aria-hidden="true" disabled <?= $prefsNav['font'] === $fontKey ? 'checked' : '' ?> />
            <?php endforeach; ?>

            <label for="setting-toggle" class="setting-icon">
              <i class="fa-solid fa-gear" aria-hidden="true"></i>
            </label>

            <div class="settings-dropdown">
              <p class="settings-title">
                <span class="language-en">Display settings</span>
                <span class="language-vi">Cài đặt hiển thị</span>
              </p>

              <!-- Language: one button per language. aria-pressed tells screen readers which one is active. -->
              <fieldset class="language-options">
                <legend>
                  <span class="language-en">Language</span>
                  <span class="language-vi">Ngôn ngữ</span>
                </legend>
                <?php foreach (['en' => 'English', 'vi' => 'Tiếng Việt'] as $langKey => $langLabel): ?>
                <button type="submit" class="pref-option" name="language" value="<?= $langKey ?>" aria-pressed="<?= $prefsNav['lang'] === $langKey ? 'true' : 'false' ?>">
                  <span class="pref-dot" aria-hidden="true"></span>
                  <?= $langLabel ?>
                </button>
                <?php endforeach; ?>
              </fieldset>

              <!-- Theme: the button sends the opposite of the current theme, so each click switches it.
                   It keeps the class "darkmode-toggle" and the sun/moon icons, so it looks like the old switch. -->
              <button type="submit" class="darkmode-toggle" name="theme" value="<?= $prefsNav['theme'] === 'dark' ? 'light' : 'dark' ?>" aria-pressed="<?= $prefsNav['theme'] === 'dark' ? 'true' : 'false' ?>">
                <span>
                  <span class="language-en">Dark mode</span>
                  <span class="language-vi">Chế độ tối</span>
                </span>
                <span class="theme-icons" aria-hidden="true">
                  <i class="fa-solid fa-sun sun"></i>
                  <i class="fa-solid fa-moon moon"></i>
                </span>
              </button>

              <!-- Text size: one button per size. -->
              <fieldset class="font-size-options">
                <legend>
                  <span class="language-en">Font size</span>
                  <span class="language-vi">Cỡ chữ</span>
                </legend>
                <?php foreach (['super-small' => ['Super small', 'Rất nhỏ'], 'small' => ['Small', 'Nhỏ'], 'normal' => ['Normal', 'Bình thường'], 'large' => ['Large', 'Lớn'], 'super-large' => ['Super large', 'Rất lớn']] as $fontKey => $fontLabel): ?>
                <button type="submit" class="pref-option" name="font_size" value="<?= $fontKey ?>" aria-pressed="<?= $prefsNav['font'] === $fontKey ? 'true' : 'false' ?>">
                  <span class="pref-dot" aria-hidden="true"></span>
                  <span class="language-en"><?= $fontLabel[0] ?></span>
                  <span class="language-vi"><?= $fontLabel[1] ?></span>
                </button>
                <?php endforeach; ?>
              </fieldset>
            </div>
            </form>
          </div>

          <!-- Site search (both forms send GET search.php?q=..., which searches the jobs table in MySQL).
               The CSS language switch shows one form at a time; only the visible form can be submitted.
               No "required": every check is done on the server in search.php. -->
          <form class="nav-search language-en" action="search.php" method="get" role="search" novalidate>
            <label class="visually-hidden" for="site-search-en">Search jobs</label>
            <button type="submit" class="nav-search-submit" aria-label="Submit search">
              <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
            </button>
            <input type="search" id="site-search-en" name="q" placeholder="SEARCH" maxlength="100" />
          </form>

          <!-- Vietnamese search form. Same target as the English form. -->
          <form class="nav-search language-vi" action="search.php" method="get" role="search" novalidate>
            <label class="visually-hidden" for="site-search-vi">Tìm kiếm việc làm</label>
            <button type="submit" class="nav-search-submit" aria-label="Gửi tìm kiếm">
              <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
            </button>
            <input type="search" id="site-search-vi" name="q" placeholder="TÌM KIẾM" maxlength="100" />
          </form>

          <?php if ($navRole === 'manager'): ?>
          <a class="btn-pill" href="manage.php"><span class="language-en">Applications</span><span class="language-vi">Hồ sơ</span></a>
          <?php elseif ($navRole !== ''): ?>
          <a class="btn-pill nav-user" href="login.php"><?= auth_escape((string) $_SESSION['username']) ?></a>
          <?php else: ?>
          <a class="btn-pill" href="login.php"><span class="language-en">Login</span><span class="language-vi">Đăng nhập</span></a>
          <?php endif; ?>
        </div>
      </nav>
</header>
