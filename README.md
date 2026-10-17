# T1 — Together as One

A recruitment website developed by **T1** for **COS10026 — Web Technology, Project 1** at Swinburne. The project covers semantic HTML5, CSS, responsive layouts, application forms, accessibility, and teamwork with Git/GitHub.

> This project is in development. The repository currently contains the application page and a draft jobs page. The Home page, About page, and shared images are still pending. Responsibilities and milestones below follow the team's *Group Agreement and Plan*; they do not indicate that every assessment requirement has been completed.

## 1. Team and Responsibilities

| Team member | Student ID | Assigned page | Additional responsibilities |
| --- | --- | --- | --- |
| Thai Bao Nguyen — Team Leader | 104848806 | `index.html` — Home | Jira, GitHub, and GitHub Pages deployment; company logo, navigation, overview, merged-cell table, and search |
| Minh Hoang Bui | 106208127 | `jobs.html` — Position Descriptions | Job references, salaries, responsibilities, requirements, and sidebar; HTML5 validation |
| Sy Tho Nguyen | 105705546 | `apply.html` — Job Application | Form layout, HTML5 validation, patterns, and POST configuration; accessibility reviews for forms and interactive elements |
| Anh Vu Duong | 106404734 | `about.html` — About Us | Group information, translated quotes, team photo under 300 KB, student cards, and fun facts; responsive testing |

All members contribute to the Home page and shared stylesheet, help resolve integration issues, and review quality before submission. Student email addresses follow the format `<Student ID>@student.swin.edu.au`.

## 2. Current Status

| Area | Repository status |
| --- | --- |
| Home — `index.html` | Not yet added; navigation links are prepared for integration |
| Jobs — `jobs.html` | Draft President position with CSS-controlled expandable details |
| Apply — `apply.html` | Application page with reusable HTML components loaded in the browser |
| About — `about.html` | Not yet added; navigation links are prepared for integration |
| Shared styles — `style.css` | Navigation, footer, dark mode, and some Jobs styles |
| Apply styles — `css/pages/apply/` | Organized by layout, form structure, inputs, buttons, responsive rules, and dark mode |
| Images — `images/` | Not yet added; existing image references currently point to missing assets |
| Backend/database | Not implemented; the form points to the configured form-testing endpoint |

## 3. Technology

- **HTML5:** semantic structure, fieldsets, legends, labels, input types, and native constraint validation.
- **CSS:** Flexbox, media queries, input states, CSS nesting, and checkbox-based dark mode using `:has()`.
- **Vanilla JavaScript:** `js/includes.js` fetches and inserts reusable HTML components, including nested form sections.
- **VS Code Live Server:** local preview through **Go Live**, with no build step or package installation for the project.
- **Git/GitHub:** version control and collaboration. GitHub Pages is the planned hosting target.

Use a browser that supports CSS nesting and `:has()`. JavaScript must be enabled to load the application components. The repository does not require Python, npm, a framework, or a backend server to preview the website.

## 4. Project Structure

```text
.
├── README.md
├── LICENSE
├── .gitignore
├── apply.html                     # Application page entry point; open with Live Server
├── jobs.html                      # Standalone draft jobs page
├── style.css                      # Existing shared stylesheet
├── js/
│   └── includes.js                # Browser-side HTML component loader
├── css/
│   └── pages/
│       └── apply/
│           ├── layout.css         # Container, headings, required-field indicators
│           ├── form.css           # Fieldsets, rows, columns, and labels
│           ├── inputs.css         # Inputs, selects, textareas, and validation states
│           ├── buttons.css        # Buttons, radios, checkboxes, acknowledgement
│           ├── responsive.css     # Breakpoints at 768px and 480px
│           └── darkmode.css       # Application page dark-mode colors
└── src/
    └── components/
        ├── shared/
        │   ├── navbar.html        # Reusable navigation
        │   └── footer.html        # Reusable footer
        └── apply/
            ├── header.html        # Page heading and instructions
            ├── form.html          # Form action, method, and section order
            └── form/
                ├── personal.html # Name, date of birth, gender, email, and phone
                ├── address.html  # Street, suburb/city, state, and postcode
                ├── job-details.html # Job reference, position, and skills
                └── submit.html   # Acknowledgement, confirmation, reset, submit
```

`images/`, `index.html`, and `about.html` will be added during integration. The current shared stylesheet is named **`style.css`**; the group agreement refers to the planned name `styles.css`.

## 5. Run with Go Live

1. Open the **repository root folder** in VS Code.
2. Install or enable the **Live Server** extension if it is not already available.
3. Open the root **`apply.html`** file.
4. Right-click and select **Open with Live Server**, or click **Go Live** in the status bar.
5. If the browser opens a directory listing, navigate to `/apply.html`.

The address will typically resemble:

```text
http://127.0.0.1:5500/apply.html
http://127.0.0.1:5500/jobs.html
```

Use the host and port shown by your Live Server instance. Save changes to HTML components, CSS, or JavaScript and let Live Server reload the page; refresh manually if needed. There is no build command to run.

**Use Live Server rather than opening the page through a `file://` URL.** Component loading uses `fetch()`, which needs an HTTP/HTTPS server. Component files are HTML fragments and should be viewed through `apply.html`, not opened as standalone pages.

There is currently no `index.html`, so the server root may display a directory listing instead of a Home page.

## 6. How Components Work

The root `apply.html` contains the document structure, metadata, stylesheets, and component placeholders:

```html
<div data-include="src/components/shared/navbar.html"></div>
```

It loads the component loader once:

```html
<script src="js/includes.js" defer></script>
```

The loader fetches each component, resolves nested placeholders, and replaces the placeholder with the resulting HTML. The application form is inserted only after all four sections have loaded, preventing submission of a partially loaded form.

```text
apply.html
  ├── src/components/shared/navbar.html
  ├── src/components/apply/header.html
  ├── src/components/apply/form.html
  │     ├── src/components/apply/form/personal.html
  │     ├── src/components/apply/form/address.html
  │     ├── src/components/apply/form/job-details.html
  │     └── src/components/apply/form/submit.html
  └── src/components/shared/footer.html
```

All `data-include` paths are relative to the **page URL**, including those inside nested components. Links, images, and stylesheets also resolve relative to the page. Use relative paths without a leading `/` so the Apply page can run under a repository subdirectory on GitHub Pages.

The loader accepts components from the page's `src/components/` directory, checks HTTP failures, and rejects circular includes. If loading fails, the affected section displays a retry message and the browser console records the error. If JavaScript is disabled, the page displays an instruction to enable it.

### Where to Make Changes

| Change | File to edit |
| --- | --- |
| Page title, metadata, stylesheets, or overall layout | `apply.html` |
| Navigation | `src/components/shared/navbar.html` |
| Footer | `src/components/shared/footer.html` |
| Application heading and instructions | `src/components/apply/header.html` |
| POST endpoint or section order | `src/components/apply/form.html` |
| Address fields | `src/components/apply/form/address.html` |
| Other field groups | Corresponding file in `src/components/apply/form/` |
| Application styling | Corresponding file in `css/pages/apply/` |
| Component loading behavior | `js/includes.js` |

**Edit `apply.html` directly for page-level changes.** It is the entry point, not a generated file. Edit the component files for section-level changes; saving and refreshing is sufficient.

Shared navigation and footer components are currently used by Apply. `jobs.html` still contains its own markup. To reuse components on another root-level page, add the appropriate `data-include` placeholders and load `js/includes.js` once. Keep scripts in page-level JavaScript files; component files are intended for markup.

## 7. Application Form

The form submits using **POST** to the existing testing endpoint:

```text
https://mercury.swin.edu.au/it000000/formtest.php
```

This is not an implemented recruitment storage system. Endpoint availability has not been verified; use fictional data when testing submissions.

Current HTML constraints:

| Field | Current validation |
| --- | --- |
| First Name, Last Name | Required; 2–50 characters consisting of A–Z/a–z or whitespace |
| Date of Birth | Required `date` input; no age limit configured |
| Gender | One radio option is required |
| Email | Required `email` input |
| Phone | Required; 8–15 characters consisting of digits, whitespace, `+`, or `-` |
| Street Address | Required; up to 100 characters |
| Suburb/City | Required; 2–50 characters consisting of A–Z/a–z, whitespace, or hyphens |
| State | Required selection from eight listed Australian states and territories |
| Postcode | Required; exactly four digits, without state matching |
| Job Reference | Required; exactly five alphanumeric characters |
| Preferred Position | Required selection |
| Other Skills | Optional; up to 500 characters |
| Confirmation checkbox | Required before submission |

The form does not use `novalidate`, so the browser can block submissions with missing or invalid required values. Client-side validation does not replace server-side validation if a backend is added later.

**Outstanding items:** Key Skills displays a required marker but does not enforce selecting at least one checkbox. Adding `required` to every checkbox would require every skill, which is not the intended behavior. The terms and conditions link is currently `#`, and name patterns do not support accented names. Confirm these rules against the assessment brief before final submission.

## 8. Team Workflow

1. Create a task branch, such as `feat/apply-address` or `docs/project-readme`.
2. Edit the assigned page or component. Coordinate changes to shared CSS with the team.
3. Open the affected page with Live Server and save your changes.
4. Check the rendered page, form behavior, links, browser console, and network requests.
5. Commit the changes and open a pull request describing the result, validation performed, and outstanding issues.
6. Ask the relevant team member to review before merging.

Suggested conventions: use `kebab-case` filenames and two-space indentation for HTML, CSS, and JavaScript. Split components by responsibility, such as a complete form section, instead of creating a file for every input. Keep IDs unique and match each `label for` to its control. Coordinate changes to form field `name` values because they affect POST data.

## 9. Integration Checklist

- [ ] Open `apply.html` through Live Server and confirm all components load successfully.
- [ ] Confirm all four form sections appear exactly once inside the application form.
- [ ] Check the assembled DOM as well as individual source files when validating HTML; the page source contains placeholders for dynamically inserted content.
- [ ] Confirm empty submissions are blocked and invalid email, postcode, and job reference values are rejected.
- [ ] Confirm Reset restores default values; use fictional data for POST tests.
- [ ] Navigate through inputs, selects, radios, checkboxes, and buttons with the keyboard; inspect labels and focus states.
- [ ] Check 375px, 768px, and desktop layouts for horizontal overflow or content hidden by the footer.
- [ ] Check text, inputs, and buttons in light and dark modes.
- [ ] Check links, images, navigation, and component loading under a repository subdirectory.
- [ ] Complete Home, About, Jobs content, images, terms, and Jira/GitHub footer links.

This checklist describes pre-submission work, not a claim that every check has passed. The repository does not currently include an automated browser test suite.

## 10. Deployment and Submission

Deploy the HTML pages together with **`style.css`, `css/`, `js/`, `src/components/`, and any image assets**. Components must remain available on the server because the browser requests them when the page opens. No build step is required.

There is no automated deployment workflow in the repository yet. Under the group plan, the Team Leader will configure GitHub Pages and share the deployed URL after integration. Root-level page filenames support consistent navigation and submission packaging.

Remaining integration work:

- Add `index.html`, `about.html`, and referenced images: `logo.png`, `half-moon.png`, `sun.png`, `down.png`, and `up.png`.
- Complete Jobs navigation and review its existing `/images/...` references for repository-subdirectory hosting.
- Review the Jobs document structure; its footer currently appears after the closing `body` tag.
- Check Jobs content against the requirements for five-character job references, salaries, responsibilities, requirements, and an `aside`.
- Add Jira/GitHub footer links, validate the integrated website, and package the submission according to Canvas instructions.

## 11. Timeline and Communication

| Milestone | Work defined in the Group Agreement |
| --- | --- |
| Week 4 | Finalize team/topic, submit the agreement, set up Jira/GitHub and project structure, draft individual pages |
| Week 5 | Complete page HTML/CSS, responsive layouts, accessibility reviews, and Acknowledgement of Country |
| Week 6 | Integrate pages, validate HTML5, deploy to GitHub Pages, and complete individual tutorial interviews |
| Week 7 | Buffer, final link checks, ZIP packaging, and Canvas submission before the deadline |

The team uses Zalo/Discord for daily discussion, Google Meet/Discord for agreed meetings, and MS Word for shared documents. Members should report progress, give early notice of delays or absences, support one another, and respond within 6–12 hours as agreed. Issues unresolved after 24 hours are escalated by the Team Leader to the lecturer for support.

## 12. License

The repository uses the [MIT License](LICENSE). Add appropriate acknowledgements for external images, content, and other resources as they are introduced.
