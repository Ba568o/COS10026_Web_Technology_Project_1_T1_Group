<?php
/**
 * index.php — home page of the T1 Health & Wellness careers site.
 * Trang chủ của website tuyển dụng T1 Health & Wellness.
 *
 * Sections, top to bottom (styles in styles/index.css use the same numbers):
 *   2. Hero          — looping video background (images/hero-loop.mp4, poster images/hero-poster.jpg)
 *   3. Role ticker   — open role titles sliding sideways
 *   4. Section 1     — What we build (bento grid of the three products)
 *   5. Section 2     — Why work at T1 (three values)
 *   6. Section 3     — Careers banner (kept from Project 1)
 *   7. Section 4     — Job pulse (salary chart of the open roles)
 * Header/navigation come from header.inc + nav.inc, the footer from footer.inc.
 * No JavaScript and no database: every animation is CSS, every figure matches jobs.php.
 */

// Values read by header.inc (title, meta description, stylesheet, <body> id/class) and nav.inc (current page).
$pageTitle = 'T1 Group - Health & Wellness';
$pageDescription = 'T1 Health & Wellness careers: open roles in digital health, what our teams build and why to work with us.';
$bodyClass = 'index-page';      // every rule in styles/index.css starts with body.index-page
$pageStylesheet = 'index.css';
$bodyId = 'top';                // target of the footer's "back to top" link
$currentPage = basename($_SERVER['PHP_SELF']);
include 'header.inc';
include 'nav.inc';
?>
<!-- MAIN CONTENT: home page of the T1 careers site — video hero, what T1 builds, why work here, careers, and the open roles at a glance. -->

    <main id="main-content">
      <!-- HERO: full-width looping video behind the headline.
           The video is muted, loops and plays inline, so browsers allow it to start on its own (no JavaScript).
           poster = still frame shown while the video loads or if it cannot play.
           aria-hidden: the video is decoration only; all the information is in the text. -->
      <section class="home-hero" aria-labelledby="hero-heading">
        <video class="home-hero-video" autoplay muted loop playsinline preload="metadata" poster="images/hero-poster.jpg" aria-hidden="true">
          <source src="images/hero-loop.mp4" type="video/mp4" />
        </video>
        <!-- Dark gradient over the video so the white text stays readable. -->
        <div class="home-hero-shade" aria-hidden="true"></div>

        <div class="home-hero-inner">
          <p class="home-hero-eyebrow">
            <span class="live-dot" aria-hidden="true"></span>
            <span class="language-en">NOW HIRING · 6 OPEN ROLES</span>
            <span class="language-vi">ĐANG TUYỂN · 6 VỊ TRÍ</span>
          </p>

          <h1 id="hero-heading">
            <span class="language-en">Build better health.</span>
            <span class="language-vi">Kiến tạo sức khỏe.</span>
            <br />
            <em>
              <span class="language-en">Shape smarter care.</span>
              <span class="language-vi">Định hình chăm sóc.</span>
            </em>
          </h1>

          <p class="home-hero-text">
            <span class="language-en">
              T1 builds online patient platforms, booking systems and health information websites. Join the designers, developers,
              analysts, testers and support specialists who make care easier to reach.
            </span>
            <span class="language-vi">
              T1 xây dựng nền tảng bệnh nhân trực tuyến, hệ thống đặt lịch và website thông tin sức khỏe. Hãy cùng các nhà thiết kế,
              lập trình viên, chuyên viên dữ liệu, kiểm thử và hỗ trợ giúp việc chăm sóc sức khỏe dễ tiếp cận hơn.
            </span>
          </p>

          <div class="home-hero-actions">
            <a class="hero-btn hero-btn-primary" href="jobs.php">
              <span class="language-en">Explore jobs</span>
              <span class="language-vi">Xem việc làm</span>
              <i class="fa-solid fa-arrow-right-long" aria-hidden="true"></i>
            </a>
            <a class="hero-btn hero-btn-ghost" href="about.php">
              <span class="language-en">Meet the team</span>
              <span class="language-vi">Gặp đội ngũ</span>
            </a>
          </div>

          <!-- Three quick facts, same figures as the Jobs page. -->
          <ul class="home-hero-facts">
            <li>
              <strong>6</strong>
              <span class="language-en">open roles</span>
              <span class="language-vi">vị trí đang tuyển</span>
            </li>
            <li>
              <strong>4</strong>
              <span class="language-en">cities in Vietnam</span>
              <span class="language-vi">thành phố tại Việt Nam</span>
            </li>
            <li>
              <strong>$720–$3,000</strong>
              <span class="language-en">monthly salary</span>
              <span class="language-vi">lương mỗi tháng</span>
            </li>
          </ul>
        </div>

        <!-- Jump to the next section. -->
        <a class="home-hero-scroll" href="#what-we-build">
          <span class="language-en">Scroll</span>
          <span class="language-vi">Cuộn xuống</span>
          <i class="fa-solid fa-chevron-down" aria-hidden="true"></i>
        </a>
      </section>

      <!-- ROLE TICKER: the open roles scroll sideways (CSS animation). The list is printed twice so the loop has no gap;
           the second copy is aria-hidden so screen readers read the roles once. Hover pauses it. -->
      <div class="role-ticker">
        <ul class="role-ticker-track">
          <?php
          // Role titles from the Jobs page. [English, Vietnamese]
          $tickerRoles = [
              ['Digital Health Software Developer', 'Lập trình viên phần mềm sức khỏe số'],
              ['Health Data Analyst', 'Chuyên viên phân tích dữ liệu sức khỏe'],
              ['Healthcare UX/UI Designer', 'Nhà thiết kế UX/UI chăm sóc sức khỏe'],
              ['Healthcare Quality Assurance Tester', 'Kiểm thử đảm bảo chất lượng'],
              ['Digital Health Support Specialist', 'Chuyên viên hỗ trợ sức khỏe số'],
              ['Healthcare Content Writer', 'Biên tập viên nội dung'],
          ];
          foreach ([false, true] as $isCopy):
              foreach ($tickerRoles as [$roleEn, $roleVi]): ?>
          <li<?= $isCopy ? ' aria-hidden="true"' : '' ?>>
            <i class="fa-solid fa-plus" aria-hidden="true"></i>
            <span class="language-en"><?= $roleEn ?></span>
            <span class="language-vi"><?= $roleVi ?></span>
          </li>
          <?php endforeach;
          endforeach; ?>
        </ul>
      </div>

      <!-- SECTION 1: WHAT WE BUILD — the three products as a bento grid.
           Each card says who uses the product and which T1 teams work on it. -->
      <section class="build" id="what-we-build" aria-labelledby="build-heading">
        <div class="home-container">
          <div class="build-heading">
            <div>
              <p class="section-label">
                <span class="language-en">WHAT WE BUILD</span>
                <span class="language-vi">CHÚNG TÔI XÂY DỰNG GÌ</span>
              </p>
              <h2 id="build-heading">
                <span class="language-en">Three products. One goal: care people can reach.</span>
                <span class="language-vi">Ba sản phẩm. Một mục tiêu: chăm sóc ai cũng tiếp cận được.</span>
              </h2>
            </div>
            <p class="build-intro">
              <span class="language-en">Every role at T1 works on at least one of these, from the first line of code to the last word on the page.</span>
              <span class="language-vi">Mỗi vị trí ở T1 đều góp phần vào ít nhất một sản phẩm, từ dòng code đầu tiên đến chữ cuối cùng trên trang.</span>
            </p>
          </div>

          <div class="build-grid">
            <!-- Card 01 (large): patient platforms, with a small CSS-drawn app preview. -->
            <article class="build-card build-card-feature">
              <span class="build-number" aria-hidden="true">01</span>
              <div class="build-icon" aria-hidden="true"><i class="fa-solid fa-laptop-medical"></i></div>
              <h3>
                <span class="language-en">Online Patient Platforms</span>
                <span class="language-vi">Nền tảng bệnh nhân trực tuyến</span>
              </h3>
              <p>
                <span class="language-en">We support digital platforms that help patients access healthcare services conveniently.</span>
                <span class="language-vi">Chúng tôi phát triển các nền tảng số giúp bệnh nhân tiếp cận dịch vụ chăm sóc sức khỏe một cách thuận tiện.</span>
              </p>
              <!-- Decorative app preview drawn with CSS boxes. -->
              <div class="build-preview" aria-hidden="true">
                <div class="preview-bar"><span></span><span></span><span></span></div>
                <div class="preview-row preview-row-strong"></div>
                <div class="preview-row"></div>
                <div class="preview-chart">
                  <span style="--h: 40%"></span><span style="--h: 65%"></span><span style="--h: 50%"></span><span style="--h: 85%"></span><span style="--h: 70%"></span><span style="--h: 95%"></span>
                </div>
              </div>
              <dl class="build-meta">
                <div>
                  <dt><span class="language-en">Used by</span><span class="language-vi">Người dùng</span></dt>
                  <dd><span class="language-en">Patients</span><span class="language-vi">Bệnh nhân</span></dd>
                </div>
                <div>
                  <dt><span class="language-en">Teams</span><span class="language-vi">Đội ngũ</span></dt>
                  <dd><span class="language-en">Support · Development · QA</span><span class="language-vi">Hỗ trợ · Phát triển · Kiểm thử</span></dd>
                </div>
              </dl>
            </article>

            <!-- Card 02 -->
            <article class="build-card">
              <span class="build-number" aria-hidden="true">02</span>
              <div class="build-icon" aria-hidden="true"><i class="fa-regular fa-calendar-check"></i></div>
              <h3>
                <span class="language-en">Appointment Booking Systems</span>
                <span class="language-vi">Hệ thống đặt lịch hẹn</span>
              </h3>
              <p>
                <span class="language-en">We make it easier for patients to book and manage healthcare appointments online.</span>
                <span class="language-vi">Chúng tôi giúp bệnh nhân dễ dàng đặt và quản lý lịch hẹn chăm sóc sức khỏe trực tuyến.</span>
              </p>
              <dl class="build-meta">
                <div>
                  <dt><span class="language-en">Used by</span><span class="language-vi">Người dùng</span></dt>
                  <dd><span class="language-en">Patients, Clinics</span><span class="language-vi">Bệnh nhân, phòng khám</span></dd>
                </div>
                <div>
                  <dt><span class="language-en">Teams</span><span class="language-vi">Đội ngũ</span></dt>
                  <dd><span class="language-en">Development · UX/UI Design</span><span class="language-vi">Phát triển · Thiết kế UX/UI</span></dd>
                </div>
              </dl>
            </article>

            <!-- Card 03 -->
            <article class="build-card">
              <span class="build-number" aria-hidden="true">03</span>
              <div class="build-icon" aria-hidden="true"><i class="fa-solid fa-book-open"></i></div>
              <h3>
                <span class="language-en">Health Information Websites</span>
                <span class="language-vi">Trang web thông tin sức khỏe</span>
              </h3>
              <p>
                <span class="language-en">We provide accessible digital resources to help people find useful health information.</span>
                <span class="language-vi">Chúng tôi cung cấp các tài nguyên số dễ tiếp cận để mọi người tìm kiếm thông tin sức khỏe hữu ích.</span>
              </p>
              <dl class="build-meta">
                <div>
                  <dt><span class="language-en">Used by</span><span class="language-vi">Người dùng</span></dt>
                  <dd><span class="language-en">General public</span><span class="language-vi">Công chúng</span></dd>
                </div>
                <div>
                  <dt><span class="language-en">Teams</span><span class="language-vi">Đội ngũ</span></dt>
                  <dd><span class="language-en">Content · Data Analytics</span><span class="language-vi">Nội dung · Phân tích dữ liệu</span></dd>
                </div>
              </dl>
            </article>

            <!-- Wide red card: the mission line and a link to the jobs. -->
            <a class="build-card build-card-cta" href="jobs.php">
              <span class="build-cta-text">
                <span class="language-en">Technology designed to improve digital healthcare access.</span>
                <span class="language-vi">Công nghệ nâng cao khả năng tiếp cận chăm sóc sức khỏe số.</span>
              </span>
              <span class="build-cta-link">
                <span class="language-en">See the teams that are hiring</span>
                <span class="language-vi">Xem các đội đang tuyển</span>
                <i class="fa-solid fa-arrow-right-long" aria-hidden="true"></i>
              </span>
            </a>
          </div>
        </div>
      </section>

      <!-- SECTION 2: WHY WORK AT T1 — dark band; heading on the left, three values on the right.
           Hovering a value slides a red bar across it. -->
      <section class="values" aria-labelledby="values-heading">
        <div class="home-container values-layout">
          <div class="values-intro">
            <p class="section-label">
              <span class="language-en">WHY WORK AT T1</span>
              <span class="language-vi">VÌ SAO LÀM VIỆC TẠI T1</span>
            </p>
            <h2 id="values-heading">
              <span class="language-en">Technology with people in mind.</span>
              <span class="language-vi">Công nghệ lấy con người làm trọng tâm.</span>
            </h2>
            <p>
              <span class="language-en">
                At T1 you build simple, accessible and reliable products that connect people with healthcare information and services.
              </span>
              <span class="language-vi">
                Tại T1, bạn tạo ra những sản phẩm đơn giản, dễ tiếp cận và đáng tin cậy, kết nối mọi người với thông tin và dịch vụ
                chăm sóc sức khỏe.
              </span>
            </p>
            <a class="values-link" href="about.php">
              <span class="language-en">How our team works</span>
              <span class="language-vi">Cách đội ngũ làm việc</span>
              <i class="fa-solid fa-arrow-right-long" aria-hidden="true"></i>
            </a>
          </div>

          <ol class="values-list">
            <li class="value-item">
              <span class="value-number" aria-hidden="true">01</span>
              <div class="value-icon" aria-hidden="true"><i class="fa-solid fa-users"></i></div>
              <div>
                <h3>
                  <span class="language-en">User-focused</span>
                  <span class="language-vi">Lấy người dùng làm trung tâm</span>
                </h3>
                <p>
                  <span class="language-en">We design with real people and their needs in mind.</span>
                  <span class="language-vi">Chúng tôi thiết kế dựa trên nhu cầu thực tế của người dùng.</span>
                </p>
              </div>
            </li>
            <li class="value-item">
              <span class="value-number" aria-hidden="true">02</span>
              <div class="value-icon" aria-hidden="true"><i class="fa-solid fa-shield-halved"></i></div>
              <div>
                <h3>
                  <span class="language-en">Reliable</span>
                  <span class="language-vi">Đáng tin cậy</span>
                </h3>
                <p>
                  <span class="language-en">We build secure and dependable technology you can trust.</span>
                  <span class="language-vi">Chúng tôi xây dựng công nghệ an toàn và ổn định để bạn có thể tin tưởng.</span>
                </p>
              </div>
            </li>
            <li class="value-item">
              <span class="value-number" aria-hidden="true">03</span>
              <div class="value-icon" aria-hidden="true"><i class="fa-regular fa-lightbulb"></i></div>
              <div>
                <h3>
                  <span class="language-en">Innovative</span>
                  <span class="language-vi">Đổi mới</span>
                </h3>
                <p>
                  <span class="language-en">We continuously improve for a healthier tomorrow.</span>
                  <span class="language-vi">Chúng tôi không ngừng cải tiến vì một tương lai khỏe mạnh hơn.</span>
                </p>
              </div>
            </li>
          </ol>
        </div>
      </section>

      <!-- SECTION 3: careers banner linking visitors to job opportunities. -->

      <section class="careers-banner" aria-labelledby="careers-heading">
        <div class="careers-card">
          <!-- Hero text, introduction, and call-to-action link. -->
          <div class="careers-content">
            <p class="section-label">
              <span class="language-en">CAREERS AT T1</span>
              <span class="language-vi">CƠ HỘI NGHỀ NGHIỆP TẠI T1</span>
            </p>

            <h2 id="careers-heading">
              <span class="language-en">We're building a healthier future!</span>
              <span class="language-vi">Chúng tôi đang xây dựng một tương lai khỏe mạnh hơn!</span>
            </h2>

            <p class="careers-description">
              <span class="language-en">
                Join T1 Health &amp; Wellness and help create better digital healthcare experiences for everyone.
              </span>
              <span class="language-vi">
                Hãy tham gia T1 Health &amp; Wellness và cùng tạo nên những trải nghiệm chăm sóc sức khỏe số tốt hơn cho mọi người.
              </span>
            </p>

            <!-- Career highlights -->
            <div class="careers-stats">
              <div class="career-stat">
                <strong>6</strong>
                <span>
                  <span class="language-en">Open positions</span>
                  <span class="language-vi">Vị trí đang tuyển</span>
                </span>
              </div>

              <div class="career-stat">
                <strong>4</strong>
                <span>
                  <span class="language-en">Cities</span>
                  <span class="language-vi">Thành phố</span>
                </span>
              </div>
            </div>

            <a href="jobs.php" class="careers-button">
              <span class="language-en">Explore Careers</span>
              <span class="language-vi">Khám phá cơ hội nghề nghiệp</span>
              <i class="fa-solid fa-arrow-right-long" aria-hidden="true"></i>
            </a>
          </div>

          <!-- Right image -->
          <div class="careers-image">
            <img src="images/hero_t1.png" alt="T1 team collaborating on digital healthcare solutions" loading="lazy" />
          </div>
        </div>
      </section>

      <!-- SECTION 4: JOB PULSE — the open roles at a glance, drawn with HTML and CSS only (no JavaScript, no images).
           The chart is a real <table>, so screen readers and visitors with CSS turned off still get every number.
           Figures match the job descriptions on jobs.php (monthly salary in USD). Update both places together. -->
      <section class="job-pulse" id="job-pulse" aria-labelledby="pulse-heading">
        <div class="home-container">
          <!-- Section heading with a decorative heartbeat line (CSS draws it in once; turned off for reduced motion). -->
          <div class="pulse-heading">
            <div>
              <p class="section-label">
                <span class="language-en">JOB PULSE</span>
                <span class="language-vi">NHỊP TUYỂN DỤNG</span>
              </p>
              <h2 id="pulse-heading">
                <span class="language-en">Where the demand is right now.</span>
                <span class="language-vi">Những vị trí đang được săn đón.</span>
              </h2>
              <p class="pulse-lead">
                <span class="language-en">Six open roles across four cities, ranked by how high the monthly salary can go.</span>
                <span class="language-vi">Sáu vị trí tại bốn thành phố, xếp theo mức lương tối đa mỗi tháng.</span>
              </p>
            </div>
            <svg class="pulse-beat" viewBox="0 0 320 60" aria-hidden="true" focusable="false">
              <polyline points="0,32 70,32 88,32 100,8 114,54 128,20 140,32 210,32 222,32 232,16 244,44 254,32 320,32" />
            </svg>
          </div>

          <div class="pulse-layout">
            <!-- MAIN CHART: one row per role. Each bar runs from the lowest to the highest monthly salary.
                 --min and --max (inline CSS custom properties) are the salary in USD; styles/index.css
                 turns them into the bar position on a $0–$3,000 scale. -->
            <div class="pulse-card">
              <table class="pulse-chart">
                <caption>
                  <span class="language-en">Monthly salary range by role (USD)</span>
                  <span class="language-vi">Khoảng lương theo vị trí (USD/tháng)</span>
                </caption>
                <thead>
                  <tr>
                    <th scope="col">
                      <span class="language-en">Role</span>
                      <span class="language-vi">Vị trí</span>
                    </th>
                    <th scope="col" class="pulse-axis-head">
                      <span class="visually-hidden">
                        <span class="language-en">Salary range</span>
                        <span class="language-vi">Khoảng lương</span>
                      </span>
                      <!-- Axis ticks: $0, $1k, $2k, $3k at 0%, 33%, 67%, 100% of the track. -->
                      <span class="pulse-axis" aria-hidden="true">
                        <span style="--at: 0">$0</span>
                        <span style="--at: 1000">$1k</span>
                        <span style="--at: 2000">$2k</span>
                        <span style="--at: 3000">$3k</span>
                      </span>
                    </th>
                    <th scope="col" class="pulse-exp-head">
                      <span class="language-en">Experience</span>
                      <span class="language-vi">Kinh nghiệm</span>
                    </th>
                  </tr>
                </thead>
                <tbody>
                  <tr class="is-hot">
                    <th scope="row">
                      <a href="jobs.php">
                        <span class="language-en">Digital Health Software Developer</span>
                        <span class="language-vi">Lập trình viên phần mềm sức khỏe số</span>
                      </a>
                      <span class="pulse-ref">DHD05</span>
                      <span class="pulse-hot"><i class="fa-solid fa-fire" aria-hidden="true"></i> HOT</span>
                    </th>
                    <td>
                      <span class="pulse-track"><span class="pulse-range" style="--min: 1800; --max: 3000"></span></span>
                      <span class="pulse-value">$1,800 – $3,000</span>
                      <!-- Shown when the row is hovered or focused. -->
                      <span class="pulse-tip">
                        <span class="language-en">Da Nang · Software Development</span>
                        <span class="language-vi">Đà Nẵng · Phát triển phần mềm</span>
                      </span>
                    </td>
                    <td class="pulse-exp">
                      <span class="language-en">8 years</span>
                      <span class="language-vi">8 năm</span>
                    </td>
                  </tr>
                  <tr class="is-hot">
                    <th scope="row">
                      <a href="jobs.php">
                        <span class="language-en">Health Data Analyst</span>
                        <span class="language-vi">Chuyên viên phân tích dữ liệu sức khỏe</span>
                      </a>
                      <span class="pulse-ref">HDA03</span>
                      <span class="pulse-hot"><i class="fa-solid fa-fire" aria-hidden="true"></i> HOT</span>
                    </th>
                    <td>
                      <span class="pulse-track"><span class="pulse-range" style="--min: 1400; --max: 2400"></span></span>
                      <span class="pulse-value">$1,400 – $2,400</span>
                      <span class="pulse-tip">
                        <span class="language-en">Ho Chi Minh City · Data Analytics</span>
                        <span class="language-vi">TP. Hồ Chí Minh · Phân tích dữ liệu</span>
                      </span>
                    </td>
                    <td class="pulse-exp">
                      <span class="language-en">5 years</span>
                      <span class="language-vi">5 năm</span>
                    </td>
                  </tr>
                  <tr>
                    <th scope="row">
                      <a href="jobs.php">
                        <span class="language-en">Healthcare UX/UI Designer</span>
                        <span class="language-vi">Nhà thiết kế UX/UI chăm sóc sức khỏe</span>
                      </a>
                      <span class="pulse-ref">UXD04</span>
                    </th>
                    <td>
                      <span class="pulse-track"><span class="pulse-range" style="--min: 1200; --max: 2000"></span></span>
                      <span class="pulse-value">$1,200 – $2,000</span>
                      <span class="pulse-tip">
                        <span class="language-en">Hanoi · Product Design</span>
                        <span class="language-vi">Hà Nội · Thiết kế sản phẩm</span>
                      </span>
                    </td>
                    <td class="pulse-exp">
                      <span class="language-en">2 years</span>
                      <span class="language-vi">2 năm</span>
                    </td>
                  </tr>
                  <tr>
                    <th scope="row">
                      <a href="jobs.php">
                        <span class="language-en">Healthcare Quality Assurance Tester</span>
                        <span class="language-vi">Kiểm thử đảm bảo chất lượng</span>
                      </a>
                      <span class="pulse-ref">QAT06</span>
                    </th>
                    <td>
                      <span class="pulse-track"><span class="pulse-range" style="--min: 880; --max: 1520"></span></span>
                      <span class="pulse-value">$880 – $1,520</span>
                      <span class="pulse-tip">
                        <span class="language-en">Hanoi · Quality Assurance</span>
                        <span class="language-vi">Hà Nội · Đảm bảo chất lượng</span>
                      </span>
                    </td>
                    <td class="pulse-exp">
                      <span class="language-en">1 year</span>
                      <span class="language-vi">1 năm</span>
                    </td>
                  </tr>
                  <tr>
                    <th scope="row">
                      <a href="jobs.php">
                        <span class="language-en">Digital Health Support Specialist</span>
                        <span class="language-vi">Chuyên viên hỗ trợ sức khỏe số</span>
                      </a>
                      <span class="pulse-ref">DHS02</span>
                    </th>
                    <td>
                      <span class="pulse-track"><span class="pulse-range" style="--min: 720; --max: 1120"></span></span>
                      <span class="pulse-value">$720 – $1,120</span>
                      <span class="pulse-tip">
                        <span class="language-en">Ho Chi Minh City · Customer Support</span>
                        <span class="language-vi">TP. Hồ Chí Minh · Hỗ trợ khách hàng</span>
                      </span>
                    </td>
                    <td class="pulse-exp">
                      <span class="language-en">No experience</span>
                      <span class="language-vi">Không yêu cầu</span>
                    </td>
                  </tr>
                  <!-- Negotiable salary: no bar, a dashed full-width track instead, so it is not read as $0. -->
                  <tr class="is-negotiable">
                    <th scope="row">
                      <a href="jobs.php">
                        <span class="language-en">Healthcare Content Writer</span>
                        <span class="language-vi">Biên tập viên nội dung</span>
                      </a>
                      <span class="pulse-ref">HCW01</span>
                    </th>
                    <td>
                      <span class="pulse-track"></span>
                      <span class="pulse-value">
                        <span class="language-en">Negotiable</span>
                        <span class="language-vi">Thỏa thuận</span>
                      </span>
                      <span class="pulse-tip">
                        <span class="language-en">Can Tho · Content Writing</span>
                        <span class="language-vi">Cần Thơ · Viết nội dung</span>
                      </span>
                    </td>
                    <td class="pulse-exp">
                      <span class="language-en">3 years</span>
                      <span class="language-vi">3 năm</span>
                    </td>
                  </tr>
                </tbody>
              </table>
              <p class="pulse-note">
                <i class="fa-solid fa-fire" aria-hidden="true"></i>
                <span class="language-en">HOT = the two roles with the highest salary ceiling. Figures from the job descriptions on the Jobs page.</span>
                <span class="language-vi">HOT = hai vị trí có mức lương tối đa cao nhất. Số liệu lấy từ mô tả công việc ở trang Tuyển dụng.</span>
              </p>
            </div>

            <!-- SIDE PANEL: headline figures and roles per city. -->
            <aside class="pulse-side" aria-label="Open roles summary">
              <dl class="pulse-stats">
                <div>
                  <dt>
                    <span class="language-en">Top salary</span>
                    <span class="language-vi">Lương cao nhất</span>
                  </dt>
                  <dd>$3,000</dd>
                </div>
                <div>
                  <dt>
                    <span class="language-en">Entry level</span>
                    <span class="language-vi">Không cần kinh nghiệm</span>
                  </dt>
                  <dd>
                    <span class="language-en">1 role</span>
                    <span class="language-vi">1 vị trí</span>
                  </dd>
                </div>
              </dl>

              <!-- Roles per city: bar width = roles / 2 (the largest count), set with --count. -->
              <h3>
                <span class="language-en">Roles by city</span>
                <span class="language-vi">Vị trí theo thành phố</span>
              </h3>
              <ul class="pulse-cities">
                <li style="--count: 2">
                  <span class="pulse-city-name">
                    <span class="language-en">Ho Chi Minh City</span>
                    <span class="language-vi">TP. Hồ Chí Minh</span>
                  </span>
                  <span class="pulse-city-bar" aria-hidden="true"></span>
                  <span class="pulse-city-count">2</span>
                </li>
                <li style="--count: 2">
                  <span class="pulse-city-name">
                    <span class="language-en">Hanoi</span>
                    <span class="language-vi">Hà Nội</span>
                  </span>
                  <span class="pulse-city-bar" aria-hidden="true"></span>
                  <span class="pulse-city-count">2</span>
                </li>
                <li style="--count: 1">
                  <span class="pulse-city-name">
                    <span class="language-en">Da Nang</span>
                    <span class="language-vi">Đà Nẵng</span>
                  </span>
                  <span class="pulse-city-bar" aria-hidden="true"></span>
                  <span class="pulse-city-count">1</span>
                </li>
                <li style="--count: 1">
                  <span class="pulse-city-name">
                    <span class="language-en">Can Tho</span>
                    <span class="language-vi">Cần Thơ</span>
                  </span>
                  <span class="pulse-city-bar" aria-hidden="true"></span>
                  <span class="pulse-city-count">1</span>
                </li>
              </ul>

              <a class="pulse-cta" href="jobs.php">
                <span class="language-en">See every role</span>
                <span class="language-vi">Xem tất cả vị trí</span>
                <i class="fa-solid fa-arrow-right-long" aria-hidden="true"></i>
              </a>
              <a class="pulse-cta pulse-cta-light" href="apply.php">
                <span class="language-en">Apply now</span>
                <span class="language-vi">Ứng tuyển ngay</span>
              </a>
            </aside>
          </div>
        </div>
      </section>
    </main>

    <!-- FOOTER: company information, project links, and website standards. -->

    <?php include 'footer.inc'; ?>
