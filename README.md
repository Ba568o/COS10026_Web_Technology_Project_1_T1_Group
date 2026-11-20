# T1 Health & Wellness — COS10026

Repository gồm `project1/` (HTML/CSS) và **`project2/` (PHP/MySQL)**.
Hướng dẫn chạy dưới đây dành cho **Project 2 đang phát triển**.

## 1. Chạy website bằng PHP

Đảm bảo MySQL local trên Mac đang chạy. Mở terminal tại thư mục gốc
`COS10026_Web_Technology_Project_1_T1_Group`, chạy:

```bash
php -S localhost:8000 -t project2
```

Mở **[http://localhost:8000](http://localhost:8000)**.
Giữ terminal mở khi dùng web; nhấn `Ctrl+C` để dừng.
Project 2 cần PHP để xử lý các file `.php`; không chạy bằng Python hoặc Live Server.

| Trang | Đường dẫn |
| --- | --- |
| Trang chủ | [index.php](http://localhost:8000/index.php) |
| Công việc | [jobs.php](http://localhost:8000/jobs.php) |
| Ứng tuyển, tải CV | [apply.php](http://localhost:8000/apply.php) |
| Đăng ký | [signup.php](http://localhost:8000/signup.php) |
| Đăng nhập | [login.php](http://localhost:8000/login.php) |
| Quản lý EOI | [manage.php](http://localhost:8000/manage.php) |

> Phạm vi hiện tại: yêu cầu 2, 3, 6. Phần xử lý gửi đơn (số 4) đã gỡ;
> Apply chỉ còn giao diện, chưa lưu đơn hoặc upload CV. Dữ liệu EOI cũ vẫn giữ nguyên.

## 2. Tài khoản đăng nhập website

- **HR / người chấm bài:** username `admin`, password `admin` tại trang Login.
  Sau đăng nhập sẽ vào `manage.php`.
- **Người tìm việc:** tạo tài khoản ở trang Sign up, sau đó đăng nhập bằng tài khoản
  vừa tạo. Đăng nhập thành công chuyển đến Jobs.

## 3. Mở phpMyAdmin để xem database

Mở **Docker Desktop**, chờ Docker chạy rồi dùng:

```bash
docker start cos10026-phpmyadmin
```

Mở **[http://localhost:8081](http://localhost:8081)**.
Tại màn hình đăng nhập phpMyAdmin:

- **Username:** `root`.
- **Password:** mật khẩu MySQL đã dùng trong lab, xem biến `$pwd` ở file
  `COS10026/lab9/settings.php` trên máy của bạn. Không đưa mật khẩu này vào Git.
- Sau đăng nhập chọn database **`t1_health`**.

`admin/admin` là tài khoản website, không phải tài khoản đăng nhập phpMyAdmin.

## 4. Cấu hình hiện tại

| Thành phần | Chạy ở đâu | Cổng | Vai trò |
| --- | --- | --- | --- |
| Website PHP | Trên Mac | 8000 | Hiển thị web và xử lý form |
| MySQL | Trên Mac | 3306 | Lưu tài khoản, EOI và dữ liệu |
| phpMyAdmin | Docker `cos10026-phpmyadmin` | 8081 | Giao diện quản lý MySQL |

PHP kết nối trực tiếp đến MySQL; không cần mở phpMyAdmin để website hoạt động.
Container phpMyAdmin kết nối tới MySQL trên Mac qua `host.docker.internal:3306`.

File [project2/settings.php](project2/settings.php) chứa cấu hình website:

```php
$host = 'localhost';
$user = 't1_local';
$password = '';
$database = 't1_health';
```

Database và user này đã được tạo trên máy hiện tại. Trên máy mới, quản trị viên
MySQL chạy [project2/database/local_setup.sql](project2/database/local_setup.sql)
để tạo database và user có quyền trong phạm vi `t1_health`. Mật khẩu kết nối của
project để trống theo đề; mật khẩu root hiện tại giữ nguyên.

PHP cần các extension `pdo_mysql`, `mbstring`, `fileinfo` và `zip`.
Giới hạn upload trên máy đã được đặt trong `/usr/local/etc/php/8.5/php.ini`:

```ini
upload_max_filesize = 5M
post_max_size = 8M
```

Không cần thêm tham số `-d` khi chạy. Nếu sửa cấu hình, dừng và chạy lại PHP server.
Trên máy khác, cấu hình tương tự trong `php.ini` của máy đó.

## 5. Lỗi thường gặp

- **Cổng 8000 đang được dùng:** dừng server cũ bằng `Ctrl+C`, hoặc chạy
  `php -S localhost:8002 -t project2` rồi mở `http://localhost:8002`.
- **Connection refused:** kiểm tra MySQL local đã chạy ở cổng 3306.
- **Access denied:** kiểm tra user `t1_local` và mật khẩu trống trong `settings.php`;
  chạy script setup bằng tài khoản quản trị nếu chưa tạo user.
- **Không mở được phpMyAdmin:** bật Docker Desktop và container, dùng địa chỉ cổng 8081.
- **CV bị từ chối:** dùng PDF/DOC/DOCX tối đa 5 MB; kiểm tra giới hạn trong `php.ini`.

## Tài liệu và Project 1

Chi tiết chức năng, cấu trúc PHP và quy ước phát triển nằm trong
[README Project 2](project2/README.md).

Project 1 là phiên bản HTML/CSS: mở `project1/index.html` trực tiếp trong trình duyệt.
[Website Project 1 đã triển khai](https://ba568o.github.io/COS10026_Web_Technology_Project_1_T1_Group/project1/).

## Team

| Name | Student ID | Main page |
| --- | --- | --- |
| Thai Bao Nguyen (Leader) | 104848806 | `index.html` |
| Minh Hoang Bui | 106208127 | `jobs.html` |
| Sy Tho Nguyen | 105705546 | `apply.html` |
| Anh Vu Duong | 106404734 | `about.html` |

## Links

- [GitHub repository](https://github.com/Ba568o/COS10026_Web_Technology_Project_1_T1_Group)
- [Jira board](https://104848806.atlassian.net/jira/software/projects/T123/summary)
- [MIT License](project1/LICENSE)
