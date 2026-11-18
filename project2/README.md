# T1 Health & Wellness — Project 2

PHP and MySQL version of the T1 site for **COS10026 — Web Technology** at Swinburne University of Technology.

Project 1 (HTML and CSS only) stays in `project1/` and has its own README at the repository root. This file covers Project 2 only.

## Pages

| Page | File | Purpose |
| --- | --- | --- |
| Home | `index.php` | Brand, services, careers and appointments |
| Jobs | `jobs.php` | Open roles and job details |
| Apply | `apply.php` | Application form and CV upload |
| Confirmation | `eoi_confirmation.php` | Receipt with the new EOI number |
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
$user = 'root';
$password = '';
$database = 't1_health';
```

Every database page uses this file:

```php
require_once __DIR__ . '/settings.php';
$db = database_connection();
```

The connection uses `utf8mb4`, exceptions and native prepared statements. The MySQL account needs `CREATE`, `SELECT`, `INSERT`, `UPDATE` and `DELETE` on `t1_health`.

The `eoi` and `users` tables are created on first use. They are not created until a page that needs them runs.

## Run

From the repository root:

```bash
php -d upload_max_filesize=5M -d post_max_size=8M -S localhost:8000 -t project2
```

Open [http://localhost:8000](http://localhost:8000).

With XAMPP, put the repository under `htdocs`, start Apache and MySQL, then open `http://localhost/<repository-folder>/project2/`. Keep the same PHP upload limits.

## Expression of Interest

`apply.php` posts to `process_eoi.php`.

- Server-side checks cover every field, allowed selections, and the CV file.
- Accepted CV types are PDF, DOC and DOCX, up to 5 MB. The file type is checked, not only the extension.
- A session token blocks a forged repeat submit.
- A valid application is stored in `eoi`. Skills are saved as JSON. The CV is saved as a BLOB.
- `EOInumber` is the auto-increment primary key.
- `status` is `New`, `Current` or `Final`. New records start as `New`.
- Errors send the applicant back to the form with their text kept. They choose the CV file again.
- Success goes to `eoi_confirmation.php`, so refreshing the receipt does not insert another row.

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
├── process_eoi.php, eoi_helpers.php, eoi_confirmation.php
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
