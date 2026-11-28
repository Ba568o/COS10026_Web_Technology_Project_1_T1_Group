<?php
/**
 * site_index.php — Bilingual metadata index of public frontend pages and sections.
 *
 * Used by search.php together with includes/search_engine.php so users can search
 * static pages (e.g. About, Apply, Job Pulse) without scanning PHP files on disk.
 * Keep all text as plain UTF-8; search.php handles HTML escaping on output.
 */
return [
    [
        'url' => 'index.php',
        'title' => [
            'en' => 'Home — T1 Health & Wellness',
            'vi' => 'Trang chủ — T1 Health & Wellness',
        ],
        'description' => [
            'en' => 'Explore T1 Health & Wellness digital healthcare services, mission, core values, and career highlights.',
            'vi' => 'Khám phá dịch vụ chăm sóc sức khỏe số, sứ mệnh, giá trị cốt lõi và cơ hội nghề nghiệp tại T1 Health & Wellness.',
        ],
        'keywords' => [
            'en' => 'home digital healthcare telehealth mission values careers t1 health wellness',
            'vi' => 'trang chu cham soc suc khoe so y te truc tuyen su menh gia tri nghe nghiep',
        ],
    ],
    [
        'url' => 'index.php#what-we-build',
        'title' => [
            'en' => 'What We Build — Digital Health Platform',
            'vi' => 'Những gì chúng tôi xây dựng — Nền tảng Y tế Số',
        ],
        'description' => [
            'en' => 'Overview of our online consultation, patient care workflows, and digital health infrastructure.',
            'vi' => 'Tổng quan về tư vấn trực tuyến, quy trình chăm sóc bệnh nhân và hạ tầng y tế số.',
        ],
        'keywords' => [
            'en' => 'what we build platform consultation appointment patient services technology',
            'vi' => 'xay dung nen tang tu van dat lich benh nhan cong nghe y te',
        ],
    ],
    [
        'url' => 'index.php#job-pulse',
        'title' => [
            'en' => 'Job Pulse — Monthly USD Salary Overview',
            'vi' => 'Nhịp tuyển dụng — Tổng quan mức lương USD hàng tháng',
        ],
        'description' => [
            'en' => 'Visual comparison of monthly USD compensation across all open digital healthcare roles.',
            'vi' => 'Biểu đồ so sánh mức lương USD hàng tháng cho tất cả các vị trí chăm sóc sức khỏe số đang tuyển.',
        ],
        'keywords' => [
            'en' => 'job pulse salary chart usd compensation overview roles statistics',
            'vi' => 'nhip tuyen dung bieu do luong usd thu nhap thong ke vi tri',
        ],
    ],
    [
        'url' => 'about.php',
        'title' => [
            'en' => 'About Us & Team Members',
            'vi' => 'Về chúng tôi & Thành viên nhóm',
        ],
        'description' => [
            'en' => 'Meet the T1 Group members, project contributions, university details, and fun facts behind T1 Health & Wellness.',
            'vi' => 'Gặp gỡ các thành viên nhóm T1, đóng góp dự án, thông tin môn học và những điều thú vị về T1 Health & Wellness.',
        ],
        'keywords' => [
            'en' => 'about us team members group info contributions fun facts cos10026 swinburne',
            'vi' => 'gioi thieu ve chung toi thanh vien nhom dong gop thong tin thu vi',
        ],
    ],
    [
        'url' => 'jobs.php',
        'title' => [
            'en' => 'Job Opportunities & Filters',
            'vi' => 'Cơ hội tuyển dụng & Bộ lọc việc làm',
        ],
        'description' => [
            'en' => 'Browse open roles and filter by USD salary range, experience, location, job type, or keyword.',
            'vi' => 'Xem các vị trí đang tuyển và lọc theo mức lương USD, kinh nghiệm, địa điểm, loại công việc hoặc từ khóa.',
        ],
        'keywords' => [
            'en' => 'jobs careers openings salary filter location experience hiring recruit',
            'vi' => 'viec lam tuyen dung co hoi loc luong kinh nghiem dia diem ung tuyen',
        ],
    ],
    [
        'url' => 'apply.php#skip-to-application',
        'title' => [
            'en' => 'Apply for a Job — Expression of Interest',
            'vi' => 'Nộp đơn ứng tuyển — Đăng ký quan tâm việc làm',
        ],
        'description' => [
            'en' => 'Submit your application form, preferred role, skills, and CV to join T1 Health & Wellness.',
            'vi' => 'Điền đơn ứng tuyển, chọn vị trí mong muốn, kỹ năng và tải lên CV để gia nhập T1 Health & Wellness.',
        ],
        'keywords' => [
            'en' => 'apply application form eoi submit resume cv candidate',
            'vi' => 'nop don ung tuyen bieu mau ho so cv ung vien',
        ],
    ],
    [
        'url' => 'login.php',
        'title' => [
            'en' => 'Account Login (Applicant & HR)',
            'vi' => 'Đăng nhập tài khoản (Ứng viên & Nhân sự)',
        ],
        'description' => [
            'en' => 'Sign in to check your application portal or access the HR management dashboard.',
            'vi' => 'Đăng nhập để xem cổng ứng viên hoặc truy cập trang quản lý dành cho nhân sự.',
        ],
        'keywords' => [
            'en' => 'login sign in account applicant hr manager portal',
            'vi' => 'dang nhap tai khoan ung vien nhan su quan ly',
        ],
    ],
    [
        'url' => 'signup.php',
        'title' => [
            'en' => 'Create an Applicant Account',
            'vi' => 'Đăng ký tài khoản ứng viên',
        ],
        'description' => [
            'en' => 'Register a new T1 Health & Wellness applicant account to track your applications.',
            'vi' => 'Tạo tài khoản ứng viên mới tại T1 Health & Wellness để theo dõi đơn ứng tuyển.',
        ],
        'keywords' => [
            'en' => 'signup register create account new user applicant',
            'vi' => 'dang ky tao tai khoan nguoi dung moi ung vien',
        ],
    ],
];
