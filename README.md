# T1 Health & Wellness

Website for **COS10026 — Web Technology, Project 1** at Swinburne University of Technology.

**T1** stands for **Together as one**. The site presents a fictional digital healthcare brand with job listings, an application form, and an about page for the group.

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
- One consolidated `styles.css` file for shared and page-specific styling
- Demonstrates external, embedded, and inline CSS
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

## Stylesheet organisation

The project uses a single external stylesheet, `styles.css`. It is organised into clearly labelled sections:

1. Global styles shared across the website
2. Index page styles — Nguyen Thai Bao
3. Jobs page styles — Minh Hoang Bui
4. Apply page styles — Sy Tho Nguyen
5. About page styles — Duong Anh Vu

The Index page also includes small examples of embedded CSS (inside a `<style>` element) and inline CSS (through an element's `style` attribute) as required for the project. These examples are intentionally limited to decorative details so they do not disrupt the page layout.

---

## Project structure

```text
.
├── index.html              # Home page
├── jobs.html               # Jobs page
├── apply.html              # Application page
├── about.html              # About page
├── styles.css              # Combined stylesheet (shared + page-specific styles)
├── images/                 # Logos and media
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

## Links

- [GitHub repository](https://github.com/Ba568o/COS10026_Web_Technology_Project_1_T1_Group)
- [Jira board](https://104848806.atlassian.net/jira/software/projects/T123/summary)

---

## License

This project is licensed under the [MIT License](LICENSE).
