<?php
// Jobs database array for the jobs.php with bilingual support for English and Vietnamese. This file is used to seed the MySQL `jobs` table and as a fallback.
return [
    [
        'reference' => 'DHS02',
        'title' => [
            'en' => 'Digital Health Support Specialist',
            'vi' => 'Chuyên viên hỗ trợ sức khỏe số',
        ],
        'company' => [
            'en' => 'T1 Health & Wellness',
            'vi' => 'T1 Sức khỏe & Wellness',
        ],
        'category_heading' => [
            'en' => 'Customer Support | Full-time',
            'vi' => 'Hỗ trợ khách hàng | Toàn thời gian',
        ],
        'description' => [
            'en' => 'Assist users with online patient platforms and appointment booking systems, resolving common technical or account-related issues.',
            'vi' => 'Hỗ trợ người dùng các nền tảng bệnh nhân trực tuyến và hệ thống đặt lịch, giải quyết các vấn đề kỹ thuật hoặc tài khoản thường gặp.',
        ],
        'reports_to' => [
            'en' => 'Customer Support Manager',
            'vi' => 'Quản lý hỗ trợ khách hàng',
        ],
        'salary_display' => [
            'en' => '$720 - $1,120 USD / month',
            'vi' => '$720 - $1,120 USD / tháng',
        ],
        'salary_min' => 720,
        'salary_max' => 1120,
        'location_key' => 'hcm',
        'location_display' => [
            'en' => 'Ho Chi Minh City, Vietnam',
            'vi' => 'Thành phố Hồ Chí Minh, Việt Nam',
        ],
        'job_type_key' => 'support',
        'experience_key' => 'none',
        'experience_years' => 0,
        'experience_display' => [
            'en' => 'No experience',
            'vi' => 'Không yêu cầu kinh nghiệm',
        ],
        'responsibilities' => [
            'en' => [
                'Assist users with accessing and navigating online platforms.',
                'Guide users through appointment booking and account management.',
                'Respond to enquiries and coordinate reported issues with technical teams.',
                'Maintain professional and helpful communication.',
            ],
            'vi' => [
                'Hỗ trợ người dùng truy cập và sử dụng các nền tảng trực tuyến.',
                'Hướng dẫn người dùng đặt lịch và quản lý tài khoản.',
                'Phản hồi thắc mắc và phối hợp vấn đề với đội ngũ kỹ thuật.',
                'Duy trì giao tiếp chuyên nghiệp và hữu ích.',
            ],
        ],
        'requirements' => [
            'en' => [
                'Strong communication and problem-solving skills.',
                'Basic computer and digital platform skills.',
                'Patience and a customer-focused attitude.',
                'Ability to explain technical steps clearly.',
                'Previous customer support experience is desirable.',
            ],
            'vi' => [
                'Kỹ năng giao tiếp và giải quyết vấn đề tốt.',
                'Kỹ năng máy tính và nền tảng số cơ bản.',
                'Kiên nhẫn và thái độ hướng đến khách hàng.',
                'Có khả năng giải thích rõ ràng các bước kỹ thuật.',
                'Ưu tiên có kinh nghiệm hỗ trợ khách hàng.',
            ],
        ],
        'benefits' => [
            'en' => [],
            'vi' => [],
        ],
        'apply_position' => 'support-specialist',
        'sort_order' => 1,
    ],
    [
        'reference' => 'UXD04',
        'title' => [
            'en' => 'Healthcare UX/UI Designer',
            'vi' => 'Nhà thiết kế UX/UI chăm sóc sức khỏe',
        ],
        'company' => [
            'en' => 'Vinmec Digital Health',
            'vi' => 'Vinmec Sức khỏe số',
        ],
        'category_heading' => [
            'en' => 'Product Design | Full-time',
            'vi' => 'Thiết kế sản phẩm | Toàn thời gian',
        ],
        'description' => [
            'en' => 'Create intuitive and accessible interfaces that help patients find health information and manage appointments with ease.',
            'vi' => 'Tạo giao diện trực quan và dễ tiếp cận giúp bệnh nhân tìm thông tin sức khỏe và quản lý lịch hẹn dễ dàng.',
        ],
        'reports_to' => [
            'en' => 'Head of Product Design',
            'vi' => 'Trưởng bộ phận thiết kế sản phẩm',
        ],
        'salary_display' => [
            'en' => '$1,200 - $2,000 USD / month',
            'vi' => '$1,200 - $2,000 USD / tháng',
        ],
        'salary_min' => 1200,
        'salary_max' => 2000,
        'location_key' => 'hanoi',
        'location_display' => [
            'en' => 'Hanoi, Vietnam',
            'vi' => 'Hà Nội, Việt Nam',
        ],
        'job_type_key' => 'design',
        'experience_key' => '2-years',
        'experience_years' => 2,
        'experience_display' => [
            'en' => '2 years',
            'vi' => '2 năm',
        ],
        'responsibilities' => [
            'en' => [
                'Design interfaces for patient platforms and booking systems.',
                'Create wireframes, prototypes and user journey maps.',
                'Conduct user research and collaborate with product teams.',
                'Ensure designs are consistent, accessible and user-friendly.',
            ],
            'vi' => [
                'Thiết kế giao diện cho nền tảng bệnh nhân và hệ thống đặt lịch.',
                'Tạo wireframe, nguyên mẫu và bản đồ hành trình người dùng.',
                'Nghiên cứu người dùng và phối hợp với các đội ngũ sản phẩm.',
                'Đảm bảo thiết kế nhất quán, dễ tiếp cận và thân thiện.',
            ],
        ],
        'requirements' => [
            'en' => [
                'Knowledge of UI/UX design principles.',
                'Familiarity with tools such as Figma.',
                'Understanding of responsive web design and accessibility.',
                'Strong visual communication and problem-solving skills.',
                'A portfolio is desirable.',
            ],
            'vi' => [
                'Kiến thức về nguyên tắc thiết kế UI/UX.',
                'Quen thuộc với các công cụ như Figma.',
                'Hiểu về thiết kế web đáp ứng và khả năng tiếp cận.',
                'Kỹ năng giao tiếp trực quan và giải quyết vấn đề tốt.',
                'Ưu tiên có hồ sơ năng lực.',
            ],
        ],
        'benefits' => [
            'en' => [],
            'vi' => [],
        ],
        'apply_position' => 'designer',
        'sort_order' => 2,
    ],
    [
        'reference' => 'DHD05',
        'title' => [
            'en' => 'Digital Health Software Developer',
            'vi' => 'Lập trình viên phần mềm sức khỏe số',
        ],
        'company' => [
            'en' => 'FPT Smart Healthcare',
            'vi' => 'FPT Chăm sóc sức khỏe thông minh',
        ],
        'category_heading' => [
            'en' => 'Software Development | Full-time',
            'vi' => 'Phát triển phần mềm | Toàn thời gian',
        ],
        'description' => [
            'en' => 'Build and maintain reliable, user-friendly software for patient platforms, appointment systems and health information websites.',
            'vi' => 'Xây dựng và duy trì phần mềm đáng tin cậy, thân thiện cho nền tảng bệnh nhân, hệ thống đặt lịch và website thông tin sức khỏe.',
        ],
        'reports_to' => [
            'en' => 'Engineering Manager',
            'vi' => 'Quản lý kỹ thuật',
        ],
        'salary_display' => [
            'en' => '$1,800 - $3,000 USD / month',
            'vi' => '$1,800 - $3,000 USD / tháng',
        ],
        'salary_min' => 1800,
        'salary_max' => 3000,
        'location_key' => 'danang',
        'location_display' => [
            'en' => 'Da Nang, Vietnam',
            'vi' => 'Đà Nẵng, Việt Nam',
        ],
        'job_type_key' => 'development',
        'experience_key' => '8-years',
        'experience_years' => 8,
        'experience_display' => [
            'en' => '8 years',
            'vi' => '8 năm',
        ],
        'responsibilities' => [
            'en' => [
                'Develop and maintain digital healthcare platform features.',
                'Write clean, maintainable and well-tested code.',
                'Collaborate with designers, analysts and developers.',
                'Resolve bugs and follow security and data protection practices.',
            ],
            'vi' => [
                'Phát triển và duy trì các tính năng nền tảng chăm sóc sức khỏe số.',
                'Viết mã sạch, dễ bảo trì và được kiểm thử kỹ.',
                'Phối hợp với nhà thiết kế, chuyên viên phân tích và lập trình viên.',
                'Khắc phục lỗi và tuân thủ các quy trình bảo mật, bảo vệ dữ liệu.',
            ],
        ],
        'requirements' => [
            'en' => [
                'Knowledge of JavaScript, Python or Java.',
                'Understanding of web development fundamentals.',
                'Familiarity with databases and APIs is an advantage.',
                'Strong logical thinking and problem-solving skills.',
                'Ability to learn new technologies.',
            ],
            'vi' => [
                'Kiến thức về JavaScript, Python hoặc Java.',
                'Hiểu các nền tảng phát triển web.',
                'Biết về cơ sở dữ liệu và API là một lợi thế.',
                'Tư duy logic và kỹ năng giải quyết vấn đề tốt.',
                'Có khả năng học công nghệ mới.',
            ],
        ],
        'benefits' => [
            'en' => [],
            'vi' => [],
        ],
        'apply_position' => 'developer',
        'sort_order' => 3,
    ],
    [
        'reference' => 'QAT06',
        'title' => [
            'en' => 'Healthcare Quality Assurance Tester',
            'vi' => 'Kiểm thử đảm bảo chất lượng chăm sóc sức khỏe',
        ],
        'company' => [
            'en' => 'Medlatec Digital',
            'vi' => 'Medlatec Số',
        ],
        'category_heading' => [
            'en' => 'Quality Assurance | Full-time',
            'vi' => 'Đảm bảo chất lượng | Toàn thời gian',
        ],
        'description' => [
            'en' => 'Test digital healthcare services and appointment workflows to identify issues before release and provide a reliable user experience.',
            'vi' => 'Kiểm thử dịch vụ chăm sóc sức khỏe số và quy trình đặt lịch để phát hiện lỗi trước khi phát hành.',
        ],
        'reports_to' => [
            'en' => 'QA Lead',
            'vi' => 'Trưởng nhóm đảm bảo chất lượng',
        ],
        'salary_display' => [
            'en' => '$880 - $1,520 USD / month',
            'vi' => '$880 - $1,520 USD / tháng',
        ],
        'salary_min' => 880,
        'salary_max' => 1520,
        'location_key' => 'hanoi',
        'location_display' => [
            'en' => 'Hanoi, Vietnam',
            'vi' => 'Hà Nội, Việt Nam',
        ],
        'job_type_key' => 'qa',
        'experience_key' => '1-year',
        'experience_years' => 1,
        'experience_display' => [
            'en' => '1 year',
            'vi' => '1 năm',
        ],
        'responsibilities' => [
            'en' => [
                'Test website features and healthcare workflows.',
                'Create and execute test cases from requirements.',
                'Document defects and perform regression testing.',
                'Work with developers to verify fixes.',
            ],
            'vi' => [
                'Kiểm thử tính năng website và quy trình chăm sóc sức khỏe.',
                'Tạo và thực hiện các trường hợp kiểm thử từ yêu cầu.',
                'Ghi nhận lỗi và thực hiện kiểm thử hồi quy.',
                'Phối hợp với lập trình viên để xác nhận bản sửa lỗi.',
            ],
        ],
        'requirements' => [
            'en' => [
                'Understanding of software testing fundamentals.',
                'Ability to follow procedures and document results.',
                'Strong attention to detail and analytical skills.',
                'Bug-tracking experience is an advantage.',
                'Basic knowledge of web applications.',
            ],
            'vi' => [
                'Hiểu các nền tảng kiểm thử phần mềm.',
                'Có khả năng tuân thủ quy trình và ghi nhận kết quả.',
                'Chú ý đến chi tiết và kỹ năng phân tích tốt.',
                'Có kinh nghiệm theo dõi lỗi là một lợi thế.',
                'Kiến thức cơ bản về ứng dụng web.',
            ],
        ],
        'benefits' => [
            'en' => [],
            'vi' => [],
        ],
        'apply_position' => 'qa-tester',
        'sort_order' => 4,
    ],
    [
        'reference' => 'HDA03',
        'title' => [
            'en' => 'Health Data Analyst',
            'vi' => 'Chuyên viên phân tích dữ liệu sức khỏe',
        ],
        'company' => [
            'en' => 'VNG HealthTech',
            'vi' => 'VNG HealthTech',
        ],
        'category_heading' => [
            'en' => 'Data Analytics | Full-time',
            'vi' => 'Phân tích dữ liệu | Toàn thời gian',
        ],
        'description' => [
            'en' => 'Analyse digital healthcare data to identify trends, evaluate service performance and support data-driven decisions responsibly.',
            'vi' => 'Phân tích dữ liệu chăm sóc sức khỏe số để nhận diện xu hướng, đánh giá hiệu quả dịch vụ và hỗ trợ quyết định dựa trên dữ liệu.',
        ],
        'reports_to' => [
            'en' => 'Data Analytics Manager',
            'vi' => 'Quản lý phân tích dữ liệu',
        ],
        'salary_display' => [
            'en' => '$1,400 - $2,400 USD / month',
            'vi' => '$1,400 - $2,400 USD / tháng',
        ],
        'salary_min' => 1400,
        'salary_max' => 2400,
        'location_key' => 'hcm',
        'location_display' => [
            'en' => 'Ho Chi Minh City, Vietnam',
            'vi' => 'Thành phố Hồ Chí Minh, Việt Nam',
        ],
        'job_type_key' => 'analytics',
        'experience_key' => '5-years',
        'experience_years' => 5,
        'experience_display' => [
            'en' => '5 years',
            'vi' => '5 năm',
        ],
        'responsibilities' => [
            'en' => [
                'Analyse platform usage and appointment booking records.',
                'Prepare reports, dashboards and visualisations.',
                'Monitor performance and identify opportunities for improvement.',
                'Check data quality and follow privacy requirements.',
            ],
            'vi' => [
                'Phân tích việc sử dụng nền tảng và hồ sơ đặt lịch.',
                'Chuẩn bị báo cáo, bảng điều khiển và hình ảnh trực quan.',
                'Theo dõi hiệu suất và xác định cơ hội cải thiện.',
                'Kiểm tra chất lượng dữ liệu và tuân thủ yêu cầu về quyền riêng tư.',
            ],
        ],
        'requirements' => [
            'en' => [
                'Basic knowledge of data analysis and statistics.',
                'Microsoft Excel experience; SQL or visualisation tools are an advantage.',
                'Strong analytical thinking and attention to detail.',
                'Ability to communicate findings clearly.',
                'Understanding of data privacy is desirable.',
            ],
            'vi' => [
                'Kiến thức cơ bản về phân tích dữ liệu và thống kê.',
                'Kinh nghiệm Microsoft Excel; biết SQL hoặc công cụ trực quan hóa là một lợi thế.',
                'Tư duy phân tích và chú ý đến chi tiết tốt.',
                'Có khả năng trình bày phát hiện rõ ràng.',
                'Ưu tiên hiểu về quyền riêng tư dữ liệu.',
            ],
        ],
        'benefits' => [
            'en' => [],
            'vi' => [],
        ],
        'apply_position' => 'data-analyst',
        'sort_order' => 5,
    ],
    [
        'reference' => 'HCW01',
        'title' => [
            'en' => 'Healthcare Content Writer',
            'vi' => 'Biên tập viên nội dung chăm sóc sức khỏe',
        ],
        'company' => [
            'en' => 'eDoctor Vietnam',
            'vi' => 'eDoctor Việt Nam',
        ],
        'category_heading' => [
            'en' => 'Content Writing | Full-time',
            'vi' => 'Viết nội dung | Toàn thời gian',
        ],
        'description' => [
            'en' => 'We are looking for a Healthcare Content Writer to create clear, engaging and reliable health-related content for our digital health information websites. The successful candidate will help users understand health topics through accessible and well-organised digital content.',
            'vi' => 'Chúng tôi tìm kiếm biên tập viên nội dung chăm sóc sức khỏe để tạo nội dung rõ ràng, hấp dẫn và đáng tin cậy cho các website thông tin sức khỏe số.',
        ],
        'reports_to' => [
            'en' => 'Content Manager',
            'vi' => 'Quản lý nội dung',
        ],
        'salary_display' => [
            'en' => 'Negotiable',
            'vi' => 'Thỏa thuận',
        ],
        'salary_min' => null,
        'salary_max' => null,
        'location_key' => 'cantho',
        'location_display' => [
            'en' => 'Can Tho, Vietnam',
            'vi' => 'Cần Thơ, Việt Nam',
        ],
        'job_type_key' => 'content',
        'experience_key' => '3-years',
        'experience_years' => 3,
        'experience_display' => [
            'en' => '3 years',
            'vi' => '3 năm',
        ],
        'responsibilities' => [
            'en' => [
                'Research and write health-related articles and educational content.',
                'Review content for clarity, accuracy and readability.',
                'Collaborate with healthcare professionals and the digital content team.',
                'Update existing website content when information needs revision.',
                'Follow content guidelines and maintain consistent writing standards.',
            ],
            'vi' => [
                'Nghiên cứu và viết bài báo sức khỏe cùng nội dung giáo dục.',
                'Kiểm tra nội dung về tính rõ ràng, chính xác và dễ đọc.',
                'Phối hợp với chuyên gia chăm sóc sức khỏe và đội ngũ nội dung số.',
                'Cập nhật nội dung website khi thông tin cần chỉnh sửa.',
                'Tuân thủ hướng dẫn nội dung và duy trì tiêu chuẩn viết nhất quán.',
            ],
        ],
        'requirements' => [
            'en' => [
                'Strong written communication and research skills.',
                'Excellent attention to detail.',
                'Ability to explain complex information in simple language.',
                'Understanding of health and wellness topics.',
                'Experience in content writing or health communication is an advantage.',
            ],
            'vi' => [
                'Kỹ năng viết và nghiên cứu tốt.',
                'Chú ý xuất sắc đến chi tiết.',
                'Có khả năng giải thích thông tin phức tạp bằng ngôn ngữ đơn giản.',
                'Hiểu về các chủ đề sức khỏe và thể chất.',
                'Có kinh nghiệm viết nội dung hoặc truyền thông sức khỏe là một lợi thế.',
            ],
        ],
        'benefits' => [
            'en' => [
                'Health, dental, and vision insurance',
                'Retirement savings plan',
                'Paid time off and holidays',
                'Professional development opportunities',
                'Flexible work arrangements',
            ],
            'vi' => [
                'Bảo hiểm sức khỏe, nha khoa và thị lực',
                'Kế hoạch tiết kiệm hưu trí',
                'Nghỉ phép và ngày lễ có lương',
                'Cơ hội phát triển chuyên môn',
                'Thỏa thuận làm việc linh hoạt',
            ],
        ],
        'apply_position' => 'content-writer',
        'sort_order' => 6,
    ],
];
