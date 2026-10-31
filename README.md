# T1 Health & Wellness

Website for **COS10026 — Web Technology, Project 1** at Swinburne University of Technology.

**T1** stands for **TeamOne**. The site presents a fictional digital healthcare brand with job listings, an application form, and an about page for the group.

**Live site:** [ba568o.github.io/COS10026_Web_Technology_Project_1_T1_Group](https://ba568o.github.io/COS10026_Web_Technology_Project_1_T1_Group/)

---

## Pages

| Page | File | Description |
| --- | --- | --- |
| Home | `index.html` | Brand intro, services, careers highlight, appointment info |
| Jobs | `jobs.html` | Open roles with filters and expandable job cards |
| Apply | `apply.html` | Job application form with CV upload |
| About | `about.html` | Group info, member contributions, team photo |

---

## Features

- HTML5 and CSS3 only (no JavaScript)
- Shared header and footer across pages
- English / Vietnamese language switch
- Dark mode and font-size controls (CSS-only settings menu)
- Responsive layout for desktop, tablet, and mobile
- Accessible patterns: skip link, labels, focus styles, `aria-*` attributes
- Apply form validation with HTML attributes (`required`, `pattern`, `maxlength`)
- CV upload (`enctype="multipart/form-data"`, accepts PDF / DOC / DOCX)

---

## Tech stack

- HTML5
- CSS3 (Flexbox, Grid, `:has()`, media queries)
- [Font Awesome](https://fontawesome.com/) icons via CDN

---

## Project structure

```text
.
├── index.html              # Home page
├── jobs.html               # Jobs page
├── apply.html              # Application page
├── about.html              # About page
├── styles.css              # Shared styles (header, settings, language)
├── index.css               # Home page styles
├── jobs.css                # Jobs page styles
├── apply.css               # Apply page styles (+ shared footer styles)
├── css/pages/about/        # About page styles
├── images/                 # Logos and media
├── templates/              # Header and footer copy templates for the team
│   ├── header.html
│   └── footer.html
├── LICENSE
└── README.md
```

---

## How to run locally

1. Clone the repository:

```bash
git clone https://github.com/Ba568o/COS10026_Web_Technology_Project_1_T1_Group.git
cd COS10026_Web_Technology_Project_1_T1_Group
```

2. Open the site in a browser. Either:
   - open `index.html` directly, or
   - use a local server (recommended):

```bash
python3 -m http.server 8000
```

Then visit `http://localhost:8000`.

In VS Code / Cursor you can also use the **Live Server** extension.

---

## Team

| Name | Student ID | Main page |
| --- | --- | --- |
| Thai Bao Nguyen (Leader) | 104848806 | `index.html` |
| Minh Hoang Bui | 106208127 | `jobs.html` |
| Sy Tho Nguyen | 105705546 | `apply.html` |
| Anh Vu Duong | 106404734 | `about.html` |

---

## Working on a page

1. Link shared styles first, then your page stylesheet:

```html
<link rel="stylesheet" href="styles.css">
<link rel="stylesheet" href="your-page.css">
```

2. Copy `templates/header.html` after `<body>` and `templates/footer.html` before `</body>`.
3. Move `aria-current="page"` to the nav link for your page.
4. Keep bilingual text with `.language-en` and `.language-vi` spans so the language switch works.

---

## Links

- [GitHub repository](https://github.com/Ba568o/COS10026_Web_Technology_Project_1_T1_Group)
- [Jira board](https://104848806.atlassian.net/jira/software/projects/T123/summary)

---

## License

This project is licensed under the [MIT License](LICENSE).
