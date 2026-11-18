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
          <li>
            <a href="login.php" <?= $currentPage === 'login.php' ? 'aria-current="page"' : '' ?>>
              <span class="language-en">HR Login</span>
              <span class="language-vi">Đăng nhập HR</span>
            </a>
          </li>
        </ul>

        <!-- Display settings, site search, and the primary call to action. -->
        <div class="nav-actions">
          <input class="visually-hidden" type="checkbox" id="setting-toggle" />

          <!-- Display settings menu: language, theme, and text-size controls. -->
          <div id="setting-container">
            <input class="visually-hidden" type="checkbox" id="dark-toggle" aria-label="Toggle dark mode" />
            <label for="setting-toggle" class="setting-icon">
              <i class="fa-solid fa-gear" aria-hidden="true"></i>
            </label>

            <div class="settings-dropdown">
              <p class="settings-title">
                <span class="language-en">Display settings</span>
                <span class="language-vi">Cài đặt hiển thị</span>
              </p>

              <!-- Language selection. The shared radio name keeps the options mutually exclusive. -->
              <fieldset class="language-options">
                <legend>
                  <span class="language-en">Language</span>
                  <span class="language-vi">Ngôn ngữ</span>
                </legend>
                <label>
                  <input type="radio" name="language" id="language-en" checked />
                  English
                </label>
                <label>
                  <input type="radio" name="language" id="language-vi" />
                  Tiếng Việt
                </label>
              </fieldset>

              <!-- Theme selection. -->
              <label for="dark-toggle" class="darkmode-toggle">
                <span>
                  <span class="language-en">Dark mode</span>
                  <span class="language-vi">Chế độ tối</span>
                </span>
                <span class="theme-icons" aria-hidden="true">
                  <i class="fa-solid fa-sun sun"></i>
                  <i class="fa-solid fa-moon moon"></i>
                </span>
              </label>

              <!-- Text-size selection. -->
              <fieldset class="font-size-options">
                <legend>
                  <span class="language-en">Font size</span>
                  <span class="language-vi">Cỡ chữ</span>
                </legend>
                <label>
                  <input type="radio" name="font-size" id="font-super-small" />
                  <span class="language-en">Super small</span>
                  <span class="language-vi">Rất nhỏ</span>
                </label>
                <label>
                  <input type="radio" name="font-size" id="font-small" />
                  <span class="language-en">Small</span>
                  <span class="language-vi">Nhỏ</span>
                </label>
                <label>
                  <input type="radio" name="font-size" id="font-normal" checked />
                  <span class="language-en">Normal</span>
                  <span class="language-vi">Bình thường</span>
                </label>
                <label>
                  <input type="radio" name="font-size" id="font-large" />
                  <span class="language-en">Large</span>
                  <span class="language-vi">Lớn</span>
                </label>
                <label>
                  <input type="radio" name="font-size" id="font-super-large" />
                  <span class="language-en">Super large</span>
                  <span class="language-vi">Rất lớn</span>
                </label>
              </fieldset>
            </div>
          </div>

          <!-- English search form. The CSS language selector displays this form in English mode. -->
          <form class="nav-search language-en" action="https://www.google.com/search" method="GET" role="search" target="_blank">
            <label class="visually-hidden" for="site-search-en">Search</label>
            <button type="submit" class="nav-search-submit" aria-label="Submit search">
              <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
            </button>
            <input type="search" id="site-search-en" name="q" placeholder="SEARCH" required />
            <input type="hidden" name="as_sitesearch" value="ba568o.github.io/COS10026_Web_Technology_Project_1_T1_Group" />
          </form>

          <!-- Vietnamese search form. Only the visible form can be submitted. -->
          <form class="nav-search language-vi" action="https://www.google.com/search" method="GET" role="search" target="_blank">
            <label class="visually-hidden" for="site-search-vi">Tìm kiếm</label>
            <button type="submit" class="nav-search-submit" aria-label="Gửi tìm kiếm">
              <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
            </button>
            <input type="search" id="site-search-vi" name="q" placeholder="TÌM KIẾM" required />
            <input type="hidden" name="as_sitesearch" value="ba568o.github.io/COS10026_Web_Technology_Project_1_T1_Group" />
          </form>

          <a class="btn-pill" href="apply.php">
            <span class="language-en">Get started</span>
            <span class="language-vi">Bắt đầu</span>
          </a>
        </div>
      </nav>
</header>
