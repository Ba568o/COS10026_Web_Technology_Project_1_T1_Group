# T1 Health & Wellness

Website for **COS10026 — Web Technology** at Swinburne University of Technology.
The repository contains Project 1 (HTML/CSS) and Project 2 (PHP/MySQL) for a
fictional digital healthcare brand.

This is the shared README for both projects and the reusable PHP helpers.

## Project structure

```text
.
├── README.md
├── .gitignore
├── project1/
│   ├── index.html, jobs.html, apply.html, about.html
│   ├── styles.css
│   ├── images/
│   └── LICENSE
└── project2/
    ├── index.php, jobs.php, apply.php, about.php
    ├── header.inc, nav.inc, footer.inc
    ├── settings.php
    ├── process_eoi.php, eoi_helpers.php, eoi_confirmation.php
    ├── manage.php, search.php
    ├── components/listing.php
    ├── styles/
    └── images/
```

## Pages and shared features

| Page | Purpose | Project 1 | Project 2 |
| --- | --- | --- | --- |
| Home | Brand, services, careers and appointments | `index.html` | `index.php` |
| Jobs | Open roles and job details | `jobs.html` | `jobs.php` |
| Apply | Application form and CV upload | `apply.html` | `apply.php` |
| About | Team information and contributions | `about.html` | `about.php` |

Both projects use English/Vietnamese content, responsive layouts, Font Awesome
icons, dark mode and font-size controls. Accessibility patterns include skip
links, form labels, focus styles and ARIA attributes.

Project 1 uses HTML5 and CSS3 (Flexbox, Grid, `:has()` and media queries), with
external, embedded and inline CSS. Form validation uses HTML attributes.
Project 2 shares markup through PHP includes, separates styles into `styles/`,
and validates application submissions on the server.

## Run Project 1

From the repository root:

```bash
python3 -m http.server 8000 --directory project1
```

Open [http://localhost:8000](http://localhost:8000). You can also open
`project1/index.html` directly or use the Live Server editor extension.

[Project 1 live site](https://ba568o.github.io/COS10026_Web_Technology_Project_1_T1_Group/project1/)

## Run Project 2

Requires PHP with `pdo_mysql`, `mbstring`, `fileinfo` and `zip`, plus MySQL/MariaDB.
A static server such as Live Server does not execute PHP.

1. Start MySQL and create the database using a MySQL client or phpMyAdmin:

   ```sql
   CREATE DATABASE IF NOT EXISTS t1_health CHARACTER SET utf8mb4;
   ```

2. Check `project2/settings.php`:

   ```php
   $host = 'localhost';
   $user = 'root';
   $password = '';
   $database = 't1_health';
   ```

   Keep the password empty as required by the assignment. If local MySQL refuses
   passwordless root access, configure an appropriate local database account with
   an empty password and update `$user`. The EOI flow requires CREATE and INSERT;
   listing queries also require SELECT permissions on `t1_health`.

3. From the repository root, start the PHP server:

   ```bash
   php -d upload_max_filesize=5M -d post_max_size=8M -S localhost:8000 -t project2
   ```

4. Open [Project 2](http://localhost:8000) or the
   [Apply page](http://localhost:8000/apply.php). Press `Ctrl+C` to stop the server.

Run one local server at a time on port 8000, or use a different port.
For XAMPP, place the repository under `htdocs`, start Apache and MySQL, then visit
`http://localhost/<repository-folder>/project2/`. Configure PHP upload limits to
at least `upload_max_filesize=5M` and `post_max_size=8M`.

### Shared database connection

Every Project 2 page that accesses the database must use:

```php
require_once __DIR__ . '/settings.php';
$db = database_connection();
```

The PDO connection uses `utf8mb4`, exception reporting and native prepared
statements, and is reused within a request. Use prepared statements for user
input. Keep `settings.php` tracked in Git: it is required for marking and contains
no password. `.gitignore` excludes local environment files, editor files and logs.

### Apply / Expression of Interest

- The form posts to `process_eoi.php`, with client-side validation disabled.
- The server validates fields, allowed selections and CV uploads (PDF/DOC/DOCX,
  up to 5 MB), and checks the session CSRF token.
- The first valid submission creates `eoi` if needed. The database itself must
  already exist.
- Each record includes all application fields, skills as a JSON array, CV content
  as a database BLOB, an auto-incremented `EOInumber`, and a status defaulting to
  `New`. Allowed statuses are `New`, `Current`, and `Final`.
- Errors preserve entered text and selections; the applicant must reselect the CV.
- Success redirects to a receipt containing the EOInumber, avoiding resubmission
  when the receipt is refreshed. Direct GET access to the processor redirects.

Jobs database rendering, HR management queries and About contributions in
the database are separate unfinished work items. The shared listing functions
below are not yet connected to any page.

## Shared search, filter and pagination functions

Logic only: no HTML, CSS or automatic output. Requires PDO and mbstring.

- `listing_state($query, $filters, $perPage)`: validate keyword, allowed filters and page.
- `listing_pagination($total, $page, $perPage)`: calculate page count and offset.
- `listing_query(...)`: run prepared search/filter queries, return rows and pagination.
- `listing_identifier($name)`: validate a configured SQL table/column identifier.

### Example from a Project 2 page

```php
require_once __DIR__ . '/settings.php';
require_once __DIR__ . '/components/listing.php';

$filters = [
    'status' => [
        'column' => 'status',
        'options' => ['New' => 'New', 'Current' => 'Current', 'Final' => 'Final'],
    ],
];
$state = listing_state($_GET, $filters, 10);
$result = listing_query(
    database_connection(),
    'eoi',
    ['EOInumber', 'first_name', 'last_name', 'job_reference', 'status'],
    ['first_name', 'last_name', 'job_reference'],
    $filters,
    $state,
    ['EOInumber' => 'DESC']
);

$rows = $result['rows'];
$pagination = $result['pagination'];
// pagination: total, page, pages, per_page, offset
```

Example input: `?q=Nguyen&status=New&page=2`.
Search matches a literal substring in any search column (OR); filters combine
with AND. Case/accent matching depends on database collation. Page size is
bounded to 1–100; invalid pages become 1 and oversized pages are clamped.

Use trusted PHP configuration for table names, columns and sort; include a
unique sort column for stable pagination. Tables must already exist. Callers
handle database exceptions, authenticate protected pages before querying and
escape data when displaying it. No pages are connected to these helpers yet.

## Team

| Name | Student ID | Main page |
| --- | --- | --- |
| Thai Bao Nguyen (Leader) | 104848806 | `index.html` |
| Minh Hoang Bui | 106208127 | `jobs.html` |
| Sy Tho Nguyen | 105705546 | `apply.html` |
| Anh Vu Duong | 106404734 | `about.html` |

---


## Working on Project 1 pages

1. Link the combined stylesheet in the page's `<head>`:

   ```html
   <link rel="stylesheet" href="styles.css">
   ```

2. Keep page-specific CSS inside its matching section in `styles.css`. Each page uses a class on its `<body>` element to keep its styles separate:
   - `index-page`
   - `jobs-page`
   - `apply-page`
   - `about-page`

3. Keep the shared header and footer consistent across all pages when making changes.
4. Move `aria-current="page"` to the nav link for your page.
5. Keep bilingual text with `.language-en` and `.language-vi` spans so the language switch works.
6. Use the existing CSS comments and author headings when editing a page's styles. Avoid changing another page's section unless the change is intended to be shared.
---


## Working on Project 2 pages

Reuse `header.inc`, `nav.inc` and `footer.inc`. Set page metadata and
`$pageStylesheet` before including the header; stylesheet names are relative to
`project2/styles/`. Keep database connections in `settings.php` and shared listing
logic in `components/listing.php`.

## Links and license

- [GitHub repository](https://github.com/Ba568o/COS10026_Web_Technology_Project_1_T1_Group)
- [Jira board](https://104848806.atlassian.net/jira/software/projects/T123/summary)
- [MIT License](project1/LICENSE)

## HR login

Open `http://localhost:8000/login.php` or choose **HR Login** in the navigation.
Use the assignment marker account: username `admin`, password `admin`.
The database connection password in `settings.php` remains empty; this is separate
from the HR account password.

On the first well-formed login attempt, the application creates the `users` table
and seeds the admin account if absent. Passwords are stored using `password_hash`
and verified using `password_verify`, with prepared SQL queries. The database
account needs CREATE, SELECT, INSERT and UPDATE permissions. Existing admin
passwords are not overwritten. The database itself must already exist.

Five failed attempts lock an account for five minutes. Successful login rotates
the session ID; inactive HR sessions expire after 30 minutes. Logout requires a
POST with a CSRF token and clears the session and cookie.

For now, successful login returns to a signed-in confirmation on `login.php`.
The job search page is unchanged. The manager dashboard is not implemented yet;
when adding it, put the following before any HTML or database access:

```php
require_once __DIR__ . '/auth/session.php';
auth_require_login();
```
