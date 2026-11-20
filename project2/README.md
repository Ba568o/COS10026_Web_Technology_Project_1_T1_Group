# T1 Health & Wellness — Project 2

PHP and MySQL version of the T1 site for **COS10026 — Web Technology** at Swinburne University of Technology.

Hướng dẫn khởi động và tài khoản nằm trong [README gốc](../README.md). Tài liệu này mô tả chi tiết Project 2. Project 1 HTML/CSS nằm trong `project1/`.

## Pages

| Page | File | Purpose |
| --- | --- | --- |
| Home | `index.php` | Brand, services, careers and appointments |
| Jobs | `jobs.php` | Open roles and job details |
| Apply | `apply.php` | Application form and CV upload |
| About | `about.php` | Team information and contributions |
| Login | `login.php` | Applicant sign-in, and the HR manager sign-in |
| Sign up | `signup.php` | Create an applicant account |
| Manage | `manage.php` | HR queries for EOI records |
| Log out | `logout.php` | Ends the session |

Shared layout: `header.inc`, `nav.inc`, `footer.inc`.

## Requirements

- PHP with `pdo_mysql`, `mbstring`, `fileinfo` and `zip`
- MySQL or MariaDB
- A PHP server. Live Server and `python -m http.server` do not run PHP.

## Database setup

1. Start MySQL and create the database:

```sql
CREATE DATABASE IF NOT EXISTS t1_health CHARACTER SET utf8mb4;
```

2. Connection settings are in `settings.php`. The password stays empty:

```php
$host = 'localhost';
$user = 't1_local';
$password = '';
$database = 't1_health';
```

Every database page uses this file:

```php
require_once __DIR__ . '/settings.php';
$db = database_connection();
```

The connection uses `utf8mb4`, exceptions and native prepared statements. The MySQL account needs `CREATE`, `ALTER`, `SELECT`, `INSERT`, `UPDATE` and `DELETE` on `t1_health`.

The `eoi` and `users` tables are created on first use. They are not created until a page that needs them runs.

## Chạy Project 2 trên máy hiện tại

### 1. MySQL

Đảm bảo MySQL trên Mac đang chạy ở cổng **3306**. Database `t1_health` và
user `t1_local` đã được tạo trên máy này; mật khẩu kết nối để trống theo đề.
Không cần chạy lại script setup mỗi lần mở web.

### 2. Chạy website

Mở terminal tại thư mục gốc `COS10026_Web_Technology_Project_1_T1_Group` rồi chạy:

```bash
php -S localhost:8000 -t project2
```

Giữ terminal mở trong lúc dùng web. Nhấn `Ctrl+C` để dừng server.

| Trang | Địa chỉ |
| --- | --- |
| Trang chủ | [localhost:8000](http://localhost:8000) |
| Tìm việc | [jobs.php](http://localhost:8000/jobs.php) |
| Ứng tuyển và upload CV | [apply.php](http://localhost:8000/apply.php) |
| Đăng ký người dùng | [signup.php](http://localhost:8000/signup.php) |
| Đăng nhập | [login.php](http://localhost:8000/login.php) |
| Quản lý EOI | [manage.php](http://localhost:8000/manage.php) |

Đăng nhập **`admin` / `admin`** để vào trang quản lý. Đây là tài khoản website,
không phải tài khoản MySQL/phpMyAdmin. Người dùng thường đăng ký ở `signup.php`;
đăng nhập thành công sẽ chuyển đến Jobs. Apply hiện chỉ có giao diện.

### 3. Mở phpMyAdmin khi cần xem database

Mở **Docker Desktop**, chờ Docker khởi động rồi chạy:

```bash
docker start cos10026-phpmyadmin
```

Mở [phpMyAdmin tại localhost:8081](http://localhost:8081), đăng nhập bằng tài khoản
MySQL quản trị đang dùng trong lab, rồi chọn database **`t1_health`**.

Docker ở cấu hình này chỉ chạy phpMyAdmin. MySQL chạy trên Mac; website PHP kết nối
trực tiếp tới MySQL và không cần mở phpMyAdmin để hoạt động.

### Giới hạn upload và lỗi thường gặp

PHP trên máy đã được cấu hình trong `/usr/local/etc/php/8.5/php.ini`:

```ini
upload_max_filesize = 5M
post_max_size = 8M
```

Không cần thêm `-d` vào lệnh chạy. Nếu sửa `php.ini`, dừng và chạy lại PHP server.
Trên máy khác, đặt hai giá trị này trong file `php.ini` đang được PHP sử dụng.

- **Address already in use:** cổng 8000 đang có server khác. Dừng server đó hoặc
  chạy `php -S localhost:8002 -t project2` và mở `http://localhost:8002`.
- **Access denied:** kiểm tra tài khoản `t1_local` và mật khẩu trống trong
  `settings.php`; xem phần thiết lập database bên dưới nếu chạy trên máy mới.
- **Connection refused:** kiểm tra MySQL trên Mac đang chạy ở cổng 3306.
- **Không mở được phpMyAdmin:** kiểm tra Docker Desktop và container
  `cos10026-phpmyadmin` đã chạy; dùng cổng 8081, không phải 8000.

Với XAMPP trên máy khác, đặt repository vào `htdocs`, bật Apache và MySQL rồi mở
`http://localhost/<repository-folder>/project2/`. Cấu hình tài khoản database và
hai giới hạn upload trong PHP của XAMPP tương ứng.

## Phạm vi hiện tại: yêu cầu 2, 3 và 6

- `settings.php`: kết nối MySQL dùng chung, mật khẩu trống.
- `eoi_helpers.php`: định nghĩa bảng `eoi`, đầy đủ trường và status New/Current/Final.
- Login và `manage.php`: xác thực manager và quản lý các bản ghi EOI.

Phần 4 đã được gỡ: không còn `process_eoi.php`, validation đơn phía server hay
trang xác nhận EOInumber. Apply chỉ giữ giao diện; nút gửi bị vô hiệu hóa để tránh
submit đến endpoint không tồn tại. Bảng và dữ liệu EOI hiện có được giữ nguyên.
Manager vẫn quản lý các bản ghi đã có hoặc dữ liệu được nhập bằng phpMyAdmin.

## HR manager

Open `login.php`, or use **HR Login** in the navigation.

| Username | Password |
| --- | --- |
| `admin` | `admin` |

This account is created automatically if it is missing. The password in the `users` table is a hash, not the plain text. The empty value in `settings.php` is the MySQL password, not this login.

After signing in, a manager is sent to `manage.php`. An applicant account cannot open that page.

The manager can:

- list every EOI
- list by job reference
- list by first name, last name, or both
- choose the sort field and ascending or descending order
- change one EOI status to `New`, `Current` or `Final`
- delete every EOI for one job reference

## Accounts and security

- Applicant passwords are hashed with `password_hash` and checked with `password_verify`.
- Queries that use visitor input use prepared statements.
- Output is escaped before it is printed.
- Login rotates the session id. The session cookie is `HttpOnly`.
- An inactive session ends after 30 minutes.
- Five failed sign-in attempts lock that account for five minutes.
- Logout is a POST with a token, then the session and cookie are cleared.
- The username `admin` cannot be registered from the sign-up page.

## Project structure

```text
project2/
├── index.php, jobs.php, apply.php, about.php
├── eoi_helpers.php
├── login.php, signup.php, logout.php, manage.php
├── settings.php
├── header.inc, nav.inc, footer.inc
├── auth/
│   ├── session.php
│   ├── users.php
│   └── registration.php
├── components/listing.php
├── styles/
└── images/
```

`components/listing.php` is a shared search and pagination helper. No page calls it yet.

## Adding a page

1. Set `$pageTitle`, `$pageDescription`, `$pageStylesheet`, `$bodyClass` and `$currentPage`.
2. Include `header.inc`, then `nav.inc`. Stylesheet names are relative to `styles/`.
3. Include `footer.inc` at the end.
4. For a page that only a signed-in user may open, call `auth_require_login()` before any HTML.
5. Keep new database access on `database_connection()` from `settings.php`.

## Team

| Name | Student ID | Main page |
| --- | --- | --- |
| Thai Bao Nguyen (Leader) | 104848806 | Home |
| Minh Hoang Bui | 106208127 | Jobs |
| Sy Tho Nguyen | 105705546 | Apply |
| Anh Vu Duong | 106404734 | About |

## phpMyAdmin / existing local MySQL

phpMyAdmin manages MySQL; PHP connects directly to MySQL, not to the phpMyAdmin URL.
If root requires a password, keep it unchanged. Sign into phpMyAdmin or Workbench
with your existing administrator account and execute `database/local_setup.sql`.
Then set `$user = 't1_local'` in `settings.php`, leaving `$password = ''`.
This account is scoped to `t1_health` on localhost. If MySQL rejects empty passwords
under its password policy, use the course's configured local server instead of
disabling security on your existing installation.

The first login creates the users table and seeds admin/admin only if absent.
An existing admin password or role is never overwritten. Applicants are sent to
jobs.php; managers to manage.php. Application submission and CV processing have been removed from the current scope.
