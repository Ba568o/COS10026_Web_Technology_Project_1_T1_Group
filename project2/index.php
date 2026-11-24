<?php
$pageTitle = 'T1 Group - Health & Wellness';
$pageDescription = '';
$bodyClass = 'index-page';
$pageStylesheet = 'index.css';
$bodyId = 'top';
$currentPage = basename($_SERVER['PHP_SELF']);
include 'header.inc';
include 'nav.inc';
?>
<!-- MAIN CONTENT: home page of the T1 careers site — what T1 builds, why work here, and the open roles at a glance. -->

    <main id="main-content">
      <!-- HERO SECTION: introduces T1 and its digital healthcare focus. -->
      <section class="hero" aria-labelledby="apply-heading">
        <!-- Background animation -->
        <div class="hero-background" aria-hidden="true">
          <div class="hero-glow"></div>
          <div class="hero-orb orb-one"></div>
          <div class="hero-orb orb-two"></div>
        </div>

        <div class="container hero-layout">
          <!-- Hero text, introduction, and call-to-action link. -->
          <div class="hero-content">
            <p class="tag">
              <span class="language-en">CAREERS IN DIGITAL HEALTH</span>
              <span class="language-vi">TUYỂN DỤNG NGÀNH SỨC KHỎE SỐ</span>
            </p>

            <h1 id="apply-heading">
              <span class="language-en">Build Better Health</span>
              <span class="language-vi">Kiến tạo sức khỏe</span>
              <br />
              <span class="language-en">Shape Smarter Care</span>
              <span class="language-vi">Định hình chăm sóc</span>
            </h1>

            <p class="hero-text">
              <span class="language-en">
                T1 builds online patient platforms, booking systems and health information websites. We are hiring designers, developers,
                analysts, testers and support specialists to make care easier to reach.
              </span>
              <span class="language-vi">
                T1 xây dựng nền tảng bệnh nhân trực tuyến, hệ thống đặt lịch và website thông tin sức khỏe. Chúng tôi đang tìm nhà thiết kế,
                lập trình viên, chuyên viên dữ liệu, kiểm thử và hỗ trợ để chăm sóc sức khỏe dễ tiếp cận hơn.
              </span>
            </p>

            <!-- Main links: open roles first, then the team behind them. -->
            <div class="hero-actions">
              <a class="btn btn-submit" href="jobs.php">
                <span class="language-en">EXPLORE JOBS</span>
                <span class="language-vi">XEM VIỆC LÀM</span>
                <i class="fa-solid fa-arrow-right-long" aria-hidden="true"></i>
              </a>
              <a class="btn btn-outline" href="about.php">
                <span class="language-en">MEET THE TEAM</span>
                <span class="language-vi">GẶP ĐỘI NGŨ</span>
              </a>
            </div>
          </div>

          <!-- Decorative online-consultation illustration. -->
          <div class="health-animation">
            <!-- Floating medical icons used as visual decoration. -->
            <div class="float-icon icon-heart" aria-hidden="true">
              <i class="fa-solid fa-heart-pulse"></i>
            </div>

            <div class="float-icon icon-cross" aria-hidden="true">
              <i class="fa-solid fa-stethoscope"></i>
            </div>

            <div class="float-icon icon-plus" aria-hidden="true">
              <i class="fa-solid fa-plus"></i>
            </div>

            <!-- Mock video-consultation interface. -->
            <div class="video-window">
              <div class="video-header">
                <span class="live-dot"></span>
                <span>
                  <span class="language-en">ONLINE CONSULTATION</span>
                  <span class="language-vi">TƯ VẤN TRỰC TUYẾN</span>
                </span>
                <i class="fa-solid fa-ellipsis"></i>
              </div>

              <div class="doctor-area">
                <div class="doctor-avatar" aria-hidden="true">
                  <div class="doctor-hair"></div>

                  <div class="doctor-face">
                    <div class="doctor-eyes"></div>
                    <div class="doctor-smile"></div>
                  </div>

                  <div class="doctor-neck"></div>

                  <div class="doctor-coat">
                    <div class="doctor-shirt"></div>
                    <div class="doctor-pocket">T1</div>
                  </div>

                  <div class="doctor-stethoscope"></div>
                </div>

                <div class="doctor-label">
                  <i class="fa-solid fa-circle-check"></i>
                  <span class="language-en">Your doctor is here</span>
                  <span class="language-vi">Bác sĩ của bạn đã sẵn sàng</span>
                </div>
              </div>

              <div class="call-controls">
                <span><i class="fa-solid fa-microphone"></i></span>
                <span><i class="fa-solid fa-video"></i></span>
                <span class="end-call">
                  <i class="fa-solid fa-phone"></i>
                </span>
              </div>
            </div>

            <!-- Floating health-information card. -->
            <div class="health-status">
              <div class="status-icon">
                <i class="fa-solid fa-heart-pulse"></i>
              </div>

              <div>
                <strong>
                  <span class="language-en">Health matters</span>
                  <span class="language-vi">Sức khỏe là quan trọng</span>
                </strong>
                <p>
                  <span class="language-en">Care from anywhere</span>
                  <span class="language-vi">Chăm sóc sức khỏe ở bất cứ đâu</span>
                </p>
              </div>

              <span class="status-check">
                <i class="fa-solid fa-check"></i>
              </span>
            </div>

            <!-- Decorative heartbeat line. -->
            <div class="pulse-line" aria-hidden="true">
              <svg viewBox="0 0 240 55">
                <polyline points="0,30 45,30 60,30 75,8 91,47 108,18 122,30 160,30 175,30 190,12 205,30 240,30" />
              </svg>
            </div>
          </div>
        </div>
      </section>

      <!-- SECTION 1: introduces T1 services and digital solutions. -->

      <section id="home-services" aria-labelledby="services-heading">
        <div class="home-container services-layout">
          <!-- Service cards describing the three main digital services. -->
          <div class="services-content">
            <p class="section-label">
              <span class="language-en">WHAT WE BUILD</span>
              <span class="language-vi">CHÚNG TÔI XÂY DỰNG GÌ</span>
            </p>

            <h2 id="services-heading">
              <span class="language-en">Products our teams work on every day.</span>
              <span class="language-vi">Những sản phẩm đội ngũ T1 làm mỗi ngày.</span>
            </h2>

            <div class="service-cards">
              <article class="service-card">
                <!-- INLINE CSS EXAMPLE -->
                <div class="service-icon" aria-hidden="true" style="background-color: #d92f44; color: #ffffff">
                  <i class="fa-solid fa-laptop-medical"></i>
                </div>

                <h3>
                  <span class="language-en">Online Patient Platforms</span>
                  <span class="language-vi">Nền tảng bệnh nhân trực tuyến</span>
                </h3>

                <p>
                  <span class="language-en">We support digital platforms that help patients access healthcare services conveniently.</span>
                  <span class="language-vi">
                    Chúng tôi phát triển các nền tảng số giúp bệnh nhân tiếp cận dịch vụ chăm sóc sức khỏe một cách thuận tiện.
                  </span>
                </p>
              </article>

              <article class="service-card">
                <div class="service-icon" aria-hidden="true">
                  <i class="fa-regular fa-calendar-check"></i>
                </div>

                <h3>
                  <span class="language-en">Appointment Booking Systems</span>
                  <span class="language-vi">Hệ thống đặt lịch hẹn</span>
                </h3>

                <p>
                  <span class="language-en">We make it easier for patients to book and manage healthcare appointments online.</span>
                  <span class="language-vi">Chúng tôi giúp bệnh nhân dễ dàng đặt và quản lý lịch hẹn chăm sóc sức khỏe trực tuyến.</span>
                </p>
              </article>

              <article class="service-card">
                <div class="service-icon" aria-hidden="true">
                  <i class="fa-solid fa-book-open"></i>
                </div>

                <h3>
                  <span class="language-en">Health Information Websites</span>
                  <span class="language-vi">Trang web thông tin sức khỏe</span>
                </h3>

                <p>
                  <span class="language-en">We provide accessible digital resources to help people find useful health information.</span>
                  <span class="language-vi">
                    Chúng tôi cung cấp các tài nguyên số dễ tiếp cận để mọi người tìm kiếm thông tin sức khỏe hữu ích.
                  </span>
                </p>
              </article>
            </div>
          </div>

          <!-- Summary table of digital services, purposes, and target users. -->
          <div class="solutions-content">
            <p class="section-label">
              <span class="language-en">OUR DIGITAL SOLUTIONS</span>
              <span class="language-vi">GIẢI PHÁP SỐ CỦA CHÚNG TÔI</span>
            </p>

            <div class="solutions-table-wrapper">
              <table class="solutions-table">
                <thead>
                  <tr>
                    <th scope="col">
                      <span class="language-en">Digital Service</span>
                      <span class="language-vi">Dịch vụ số</span>
                    </th>
                    <th scope="col">
                      <span class="language-en">Purpose</span>
                      <span class="language-vi">Mục đích</span>
                    </th>
                    <th scope="col">
                      <span class="language-en">Target Users</span>
                      <span class="language-vi">Đối tượng sử dụng</span>
                    </th>
                  </tr>
                </thead>

                <tbody>
                  <tr>
                    <td>
                      <span class="language-en">Online Patient Platforms</span>
                      <span class="language-vi">Nền tảng bệnh nhân trực tuyến</span>
                    </td>
                    <td>
                      <span class="language-en">Access healthcare services online</span>
                      <span class="language-vi">Tiếp cận dịch vụ chăm sóc sức khỏe trực tuyến</span>
                    </td>
                    <td>
                      <span class="language-en">Patients</span>
                      <span class="language-vi">Bệnh nhân</span>
                    </td>
                  </tr>

                  <tr>
                    <td>
                      <span class="language-en">Appointment Booking Systems</span>
                      <span class="language-vi">Hệ thống đặt lịch hẹn</span>
                    </td>
                    <td>
                      <span class="language-en">Book and manage appointments</span>
                      <span class="language-vi">Đặt và quản lý lịch hẹn</span>
                    </td>
                    <td>
                      <span class="language-en">Patients, Clinics</span>
                      <span class="language-vi">Bệnh nhân, phòng khám</span>
                    </td>
                  </tr>

                  <tr>
                    <td>
                      <span class="language-en">Health Information Websites</span>
                      <span class="language-vi">Trang web thông tin sức khỏe</span>
                    </td>
                    <td>
                      <span class="language-en">Access health information</span>
                      <span class="language-vi">Tiếp cận thông tin sức khỏe</span>
                    </td>
                    <td>
                      <span class="language-en">General Public</span>
                      <span class="language-vi">Công chúng</span>
                    </td>
                  </tr>
                </tbody>

                <tfoot>
                  <tr>
                    <td colspan="3">
                      <span class="language-en">Technology designed to improve digital healthcare access.</span>
                      <span class="language-vi">Công nghệ nhằm nâng cao khả năng tiếp cận dịch vụ y tế số</span>
                    </td>
                  </tr>
                </tfoot>
              </table>
            </div>
          </div>
        </div>
      </section>

      <!-- SECTION 2: explains the values and strengths of T1. -->

      <section class="why-t1" aria-labelledby="why-heading">
        <div class="home-container why-layout">
          <!-- Introduction -->
          <div class="why-intro">
            <p class="section-label">
              <span class="language-en">WHY WORK AT T1</span>
              <span class="language-vi">VÌ SAO LÀM VIỆC TẠI T1</span>
            </p>

            <h2 id="why-heading">
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
          </div>

          <!-- Feature 1 -->
          <article class="why-feature">
            <div class="feature-icon" aria-hidden="true">
              <i class="fa-solid fa-users"></i>
            </div>

            <h3>
              <span class="language-en">User-focused</span>
              <span class="language-vi">Lấy người dùng làm trung tâm</span>
            </h3>

            <p>
              <span class="language-en">We design with real people and their needs in mind.</span>
              <span class="language-vi">Chúng tôi thiết kế dựa trên nhu cầu thực tế của người dùng.</span>
            </p>
          </article>

          <!-- Feature 2 -->
          <article class="why-feature">
            <div class="feature-icon" aria-hidden="true">
              <i class="fa-solid fa-shield-halved"></i>
            </div>

            <h3>
              <span class="language-en">Reliable</span>
              <span class="language-vi">Đáng tin cậy</span>
            </h3>

            <p>
              <span class="language-en">We build secure and dependable technology you can trust.</span>
              <span class="language-vi">Chúng tôi xây dựng công nghệ an toàn và ổn định để bạn có thể tin tưởng.</span>
            </p>
          </article>

          <!-- Feature 3 -->
          <article class="why-feature">
            <div class="feature-icon" aria-hidden="true">
              <i class="fa-regular fa-lightbulb"></i>
            </div>

            <h3>
              <span class="language-en">Innovative</span>
              <span class="language-vi">Đổi mới</span>
            </h3>

            <p>
              <span class="language-en">We continuously improve for a healthier tomorrow.</span>
              <span class="language-vi">Chúng tôi không ngừng cải tiến vì một tương lai khỏe mạnh hơn.</span>
            </p>
          </article>
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
