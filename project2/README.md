# T1 Health & Wellness — Project 2

PHP and MySQL version of the T1 website for **COS10026 — Web Technology** at Swinburne University of Technology.

## Overview

T1 is the careers site for a digital health provider. Visitors can read the home page, browse jobs, open the about page and view the application form. Accounts are split into two tables:

- **Users** (`users`): job seekers register themselves. After signing in, the site looks the same as it does for a visitor.
- **HR** (`hr_users`): accounts are created in the database. There is no HR registration page. After signing in, HR can review applications and open stored CVs.

Applications are stored in the `eoi` table. Only an HR account can open a CV.

| Page | File | Who can use it |
| --- | --- | --- |
| Home | `index.php` | Everyone |
| Jobs | `jobs.php` | Everyone |
| Apply | `apply.php` | Everyone. The form saves an EOI in this page |
| About | `about.php` | Everyone |
| Choose a login | `login.php` | Everyone |
| User login and sign up | `user_login.php`, `signup.php` | Job seekers |
| HR login | `hr_login.php` | HR |
| Applications and CVs | `manage.php`, `hr_cv.php` | HR |

HR logins are rows in `hr_users`. The setup script creates **`t1healthhr1` / `t1healthhr1`** through **`t1healthhr5` / `t1healthhr5`**. The older **`admin` / `admin`** account remains when it is already in the table. These are website accounts, not the MySQL account.

Passwords are stored with `password_hash` and checked with `password_verify`. The hash cannot be turned back into the original password.

## How to run

PHP with `pdo_mysql`, and MySQL or MariaDB listening on port **3306**, are required. Live Server and `python -m http.server` do not run PHP.

### First time on a new machine

In phpMyAdmin or MySQL Workbench, run `database/local_setup.sql` with an administrator account. The script creates the `t1_health` database and the `t1_local` user with an empty password. The site reads those settings from `settings.php`.

Then, from the repository root:

```bash
php project2/database/setup_portals.php
```

This creates the `users`, `hr_users` and `eoi` tables, adds the five HR accounts when they are missing, and loads 16 sample applications. Run it once. Running it again does not insert those applications a second time or replace an HR password.

### Each time you open the site

From the repository root:

```bash
php -S localhost:8000 -t project2
```

Leave the terminal open. Press `Ctrl+C` to stop the server. Open [http://localhost:8000](http://localhost:8000).

| Task | Address |
| --- | --- |
| Home | http://localhost:8000 |
| Login | http://localhost:8000/login.php |
| Job seeker sign up | http://localhost:8000/signup.php |
| Applications, after an HR login | http://localhost:8000/manage.php |

If port 8000 is already in use, pick another port, for example `php -S localhost:8002 -t project2`.

With XAMPP, put the repository in `htdocs`, start Apache and MySQL, then open `http://localhost/<repository-folder>/project2/`.

phpMyAdmin is only the tool for viewing MySQL. The PHP pages connect to MySQL directly. In phpMyAdmin, select the `t1_health` database. The main tables for this part are `users`, `hr_users` and `eoi`.

## Team

| Name | Student ID | Main page |
| --- | --- | --- |
| Thai Bao Nguyen (Leader) | 104848806 | Home |
| Minh Hoang Bui | 106208127 | Jobs |
| Sy Tho Nguyen | 105705546 | Apply |
| Anh Vu Duong | 106404734 | About |
