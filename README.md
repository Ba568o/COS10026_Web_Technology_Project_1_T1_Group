# T1 Health & Wellness — Project 1

Static website for **COS10026 — Web Technology, Project 1** at Swinburne University of Technology.

**T1** stands for **Together as One**. The site is a fictional digital healthcare brand: home, jobs, an application form, and an about page.

Project 2 (PHP and MySQL): xem [hướng dẫn chạy website, phpMyAdmin và đăng nhập](project2/README.md#chạy-project-2-trên-máy-hiện-tại).

**Live site:** [ba568o.github.io/COS10026_Web_Technology_Project_1_T1_Group/project1/](https://ba568o.github.io/COS10026_Web_Technology_Project_1_T1_Group/project1/)

## Pages

| Page | File | Purpose |
| --- | --- | --- |
| Home | `index.html` | Brand, services, careers and appointments |
| Jobs | `jobs.html` | Open roles and job details |
| Apply | `apply.html` | Application form and CV upload |
| About | `about.html` | Team information and contributions |

## Features

- HTML5 and CSS3 only (no JavaScript and no database)
- English / Vietnamese language switch
- Dark mode and font-size controls
- Responsive layout
- Skip link, labels, focus styles and ARIA attributes
- HTML form checks (`required`, `pattern`, `maxlength`)
- External, embedded and inline CSS

## Run

From the repository root:

```bash
python3 -m http.server 8000 --directory project1
```

Open [http://localhost:8000](http://localhost:8000). You can also open `project1/index.html` directly, or use the Live Server extension.

## Structure

```text
project1/
├── index.html
├── jobs.html
├── apply.html
├── about.html
├── styles.css
├── images/
└── LICENSE
```

Page styles live in `styles.css`, separated by a class on `<body>`: `index-page`, `jobs-page`, `apply-page`, `about-page`.

## Editing a page

1. Link the stylesheet:

```html
<link rel="stylesheet" href="styles.css">
```

2. Keep a change inside that page's section of `styles.css`.
3. Keep the shared header and footer the same on every page.
4. Put `aria-current="page"` on the link for the page you are editing.
5. Put both languages in `.language-en` and `.language-vi` spans.

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
