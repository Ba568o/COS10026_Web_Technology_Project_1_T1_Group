-- Run as an administrator in phpMyAdmin's SQL tab or MySQL Workbench.
-- This leaves the existing root password unchanged.
CREATE DATABASE IF NOT EXISTS t1_health CHARACTER SET utf8mb4;
CREATE USER IF NOT EXISTS 't1_local'@'localhost' IDENTIFIED BY '';
GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, ALTER, REFERENCES
ON t1_health.* TO 't1_local'@'localhost';
-- If t1_local already exists, this script does not reset its password.

USE t1_health;
SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS about_contributions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    member_key VARCHAR(40) NOT NULL UNIQUE,
    full_name VARCHAR(100) NOT NULL,
    student_id VARCHAR(20) NOT NULL,
    role_en VARCHAR(120) NOT NULL,
    role_vi VARCHAR(120) NOT NULL,
    assigned_page_en VARCHAR(120) NOT NULL,
    assigned_page_vi VARCHAR(120) NOT NULL,
    p1_tasks_en TEXT NOT NULL,
    p1_tasks_vi TEXT NOT NULL,
    p2_tasks_en TEXT NOT NULL,
    p2_tasks_vi TEXT NOT NULL,
    quote_vi TEXT NOT NULL,
    quote_en TEXT NOT NULL,
    is_highlighted TINYINT(1) NOT NULL DEFAULT 0,
    sort_order TINYINT UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO about_contributions (
    member_key, full_name, student_id, role_en, role_vi,
    assigned_page_en, assigned_page_vi,
    p1_tasks_en, p1_tasks_vi, p2_tasks_en, p2_tasks_vi,
    quote_vi, quote_en, is_highlighted, sort_order
) VALUES
(
    'thai-bao',
    'Thai Bao Nguyen',
    '104848806',
    'Team Leader & Project Manager',
    'Trưởng nhóm & Quản lý dự án',
    'index.php — Home & Display Preferences',
    'index.php — Trang chủ & Cài đặt hiển thị',
    '["Designed and developed the Home page (index.html) — hero section, interactive components, and overall layout.","Managed project tracking, sprint milestones, and task assignments via Jira.","Configured and maintained the team GitHub repository, defining the branching model and PR review guidelines.","Deployed and verified the website on GitHub Pages, integrating pull requests into the develop branch."]',
    '["Thiết kế và phát triển trang chủ (index.html) — khu vực hero, các thành phần tương tác và bố cục tổng thể.","Quản lý tiến độ dự án, các mốc sprint và phân công nhiệm vụ cho thành viên thông qua Jira.","Thiết lập và quản lý kho lưu trữ GitHub của nhóm, xây dựng mô hình phân nhánh và quy chuẩn đánh giá PR.","Triển khai và kiểm thử website trên GitHub Pages, tích hợp các pull request vào nhánh develop."]',
    '["Modularized Project 1 into project2/ using reusable PHP templates (.php & .inc includes).","Built cookie-backed display settings persistence across all pages via preferences.php, header.inc, and nav.inc.","Refactored index.php and styles/index.css with the Job Pulse component and looping hero video assets."]',
    '["Mô-đun hóa Project 1 sang thư mục project2/ bằng các mẫu PHP tái sử dụng (.php và .inc).","Xây dựng cơ chế lưu cài đặt hiển thị bằng cookie xuyên suốt mọi trang qua preferences.php, header.inc và nav.inc.","Tái cấu trúc index.php và styles/index.css với thành phần Job Pulse cùng video nền hero lặp vòng."]',
    '“Làm leader không phải là đứng trước chỉ đường, mà là đứng sau đảm bảo không ai bị bỏ lại.”',
    '“Being a leader isn’t about standing in front pointing the way — it’s about standing behind to ensure no one is left behind.”',
    0,
    1
),
(
    'minh-hoang',
    'Minh Hoang Bui',
    '106208127',
    'Project Coordinator & Code Validator',
    'Điều phối viên dự án & Kiểm duyệt mã nguồn',
    'jobs.php — Job Listings & Database',
    'jobs.php — Danh sách việc làm & CSDL',
    '["Researched industry salary benchmarks and engineered the Jobs listing page (jobs.html) with accessible filter layouts.","Served as Project Coordinator, facilitating group communication, meeting schedules, and team agreements.","Conducted comprehensive W3C HTML5 and CSS3 code validation across all project files to guarantee compliance.","Implemented accessible expand/collapse job detail toggles using pure CSS."]',
    '["Nghiên cứu khung lương ngành và xây dựng trang danh sách việc làm (jobs.html) với bộ lọc tương tác dễ tiếp cận.","Đảm nhiệm vai trò Điều phối viên dự án, duy trì trao đổi nhóm, sắp xếp lịch họp và văn bản thỏa thuận nhóm.","Kiểm thử toàn diện tính hợp lệ W3C HTML5 và CSS3 trên mọi tập tin dự án để đảm bảo chuẩn tuân thủ.","Hiện thực hóa tính năng đóng/mở chi tiết công việc thân thiện tiếp cận hoàn toàn bằng CSS thuần."]',
    '["Created the bilingual job dataset (database/jobs_seed.php) with English and Vietnamese listings.","Built the job schema and query helper module (database/jobs_helpers.php) with salary-range matching and fallback seeding.","Integrated job table seeding into setup_portals.php and refined the search and filter UI in jobs.php."]',
    '["Tạo bộ dữ liệu tuyển dụng song ngữ Anh – Việt trong database/jobs_seed.php.","Xây dựng mô-đun khởi tạo bảng và truy vấn việc làm (database/jobs_helpers.php) hỗ trợ lọc mức lương và dữ liệu dự phòng.","Tích hợp khởi tạo dữ liệu việc làm vào setup_portals.php và tinh chỉnh giao diện tìm kiếm, bộ lọc tại jobs.php."]',
    '“Một trang web tốt không chỉ đẹp mắt — nó còn phải đúng và có ý nghĩa với người dùng.”',
    '“A great website isn’t merely beautiful — it must be accurate and meaningful to the people who use it.”',
    0,
    2
),
(
    'sy-tho',
    'Sy Tho Nguyen',
    '105705546',
    'Accessibility Specialist',
    'Chuyên viên tiếp cận Web (Accessibility)',
    'apply.php & manage.php — EOI & HR Portal',
    'apply.php & manage.php — Ứng tuyển & Cổng HR',
    '["Engineered the comprehensive job application form structure (apply.html) using semantic HTML5 form controls and fieldsets.","Served as Accessibility Specialist, auditing WCAG 2.1 AA compliance, keyboard navigation, and color contrast.","Configured thorough client-side form validations, including regex pattern matching for phone numbers and postcodes.","Ensured full accessibility support for assistive technologies and screen readers."]',
    '["Xây dựng biểu mẫu ứng tuyển toàn diện (apply.html) sử dụng các phần tử biểu mẫu ngữ nghĩa HTML5 và fieldset.","Đảm nhận vai trò Chuyên viên tiếp cận Web, kiểm định chuẩn WCAG 2.1 AA, điều hướng bàn phím và độ tương phản màu sắc.","Cấu hình kiểm tra tính hợp lệ biểu mẫu phía client chi tiết, bao gồm so khớp regex cho số điện thoại và mã bưu chính.","Đảm bảo khả năng tương thích toàn diện cho công nghệ hỗ trợ và phần mềm đọc màn hình."]',
    '["Built EOI submission and server-side validation in apply.php and eoi_helpers.php with CV upload storage.","Split applicant and HR authentication portals across auth/users.php, user_login.php, hr_login.php, and signup.php.","Implemented HR management and CV review tools in manage.php and hr_cv.php.","Configured database setup and seeding scripts via settings.php, database/local_setup.sql, and database/setup_portals.php."]',
    '["Xây dựng quy trình nộp hồ sơ EOI và kiểm tra dữ liệu phía máy chủ tại apply.php và eoi_helpers.php kèm lưu trữ CV.","Tách biệt cổng xác thực ứng viên và nhân sự (HR) qua auth/users.php, user_login.php, hr_login.php và signup.php.","Triển khai trang quản trị nhân sự và xem CV ứng viên tại manage.php và hr_cv.php.","Cấu hình kết nối cơ sở dữ liệu và kịch bản khởi tạo qua settings.php, database/local_setup.sql và database/setup_portals.php."]',
    '“Một biểu mẫu chuẩn xác và mượt mà chính là sự tôn trọng thiết thực nhất đối với thời gian của người dùng.”',
    '“A precise and frictionless form is the most tangible way to respect our users’ time.”',
    0,
    3
),
(
    'anh-vu',
    'Anh Vu Duong',
    '106404734',
    'Frontend Developer & Responsive Tester',
    'Lập trình viên Frontend & Kiểm thử giao diện',
    'about.php — About Us & Team Database',
    'about.php — Giới thiệu & CSDL Nhóm',
    '["Built the Group Information section, class schedule with valid nested lists, and full member roster with student IDs.","Conducted responsive design testing across multiple screen sizes and physical devices to ensure consistent presentation.","Sourced, formatted, and optimized team media, member quotes, and the Fun Facts summary data table.","Implemented sticky sidebar navigation, hover micro-interactions, and pure CSS layout refinements."]',
    '["Xây dựng phần Thông tin nhóm, lịch học với danh sách lồng hợp lệ và danh sách thành viên kèm mã sinh viên.","Thực hiện kiểm thử responsive đa màn hình và trên thiết bị vật lý để đảm bảo hiển thị đồng nhất.","Thu thập, định dạng và tối ưu hóa hình ảnh nhóm, trích dẫn thành viên và bảng tổng hợp Fun Facts.","Thiết lập thanh điều hướng sidebar cố định (sticky), các vi tương tác hover và tinh chỉnh layout bằng CSS thuần."]',
    '["Engineered the dynamic about.php page powered by MySQL and reusable PHP includes.","Designed the MySQL about_contributions schema and PDO auto-seeding helper in database/about_helpers.php.","Integrated bilingual Project 1 and Project 2 member contribution rendering from the database.","Maintained WCAG 2.1 AA accessibility compliance, dark mode support, and responsive CSS architecture."]',
    '["Xây dựng trang động about.php kết nối MySQL cùng các thành phần PHP tái sử dụng.","Thiết kế lược đồ bảng MySQL about_contributions và bộ hàm PDO tự động khởi tạo dữ liệu tại database/about_helpers.php.","Tích hợp hiển thị song ngữ đóng góp Project 1 và Project 2 của từng thành viên từ cơ sở dữ liệu.","Duy trì chuẩn tiếp cận WCAG 2.1 AA, hỗ trợ chế độ tối và kiến trúc CSS đáp ứng đa thiết bị."]',
    '“Không có lỗi nào không sửa được, chỉ có code chưa commit đủ nhiều.”',
    '“There is no unfixable bug — only code that hasn’t been committed enough.”',
    1,
    4
);
