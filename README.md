# Mini Issue Tracker

A small team issue tracker built with **Laravel 13**. Manage projects, issues,
tags, and comments, with tag management, comments, member assignment, and search
all handled over **AJAX** (no full page reloads).

Built as the PRITECH Laravel technical task.

---

## Tech stack

- **Laravel 13** (PHP 8.3+)
- **SQLite** by default (zero external DB setup; works with MySQL/Postgres too)
- **Blade** templates with layouts and partials
- **Bootstrap 5** (CDN) + **vanilla JavaScript** (Fetch API) for AJAX — no build step required
- **PHPUnit** feature tests

---

## Features

### Core

| Area | What it does |
|------|--------------|
| **Projects** | List, create, edit, delete. Project page shows all of its issues. |
| **Issues** | List with **filters** (status, priority, tag), create, edit, delete, detail page. |
| **Tags** | Create tags with a unique name + optional color, list them. **Attach/detach to an issue via AJAX.** |
| **Comments** | On the issue page, comments **load via AJAX (paginated)** and are **added via AJAX** with inline validation; new comments are prepended and the form is cleared. |

### Technical implementation

- **Resource controllers** for projects and issues.
- **Form Request** classes for every write operation (`StoreProjectRequest`, `StoreIssueRequest`, `StoreCommentRequest`, …).
- **Migrations, factories, and seeders** generate realistic demo data.
- **Eloquent relationships** with **eager loading** (`with`, `withCount`) to avoid N+1 queries.
- The two extra `projects` columns (`start_date`, `deadline`) are added in a **dedicated migration** (`..._add_start_date_and_deadline_to_projects_table`).
- Status/priority allowed values live on the model (`Issue::STATUSES`, `Issue::PRIORITIES`) and are enforced in the Form Requests.

### Bonus (all implemented)

- **Members (many-to-many with users)** — assign/remove members on an issue via AJAX (`issue_user` pivot).
- **Authorization** — a `ProjectPolicy` so only a project's **owner** can edit/delete it. Simple login/register included; demo users are seeded.
- **Search** — text search on issue title/description with **debounce**, combined with the filters in a single AJAX request.

---

## Getting started

> Requires PHP 8.3+, Composer, and the SQLite PHP extension (bundled with most PHP installs).

```bash
# 1. Install dependencies
composer install

# 2. Environment
cp .env.example .env
php artisan key:generate

# 3. Database (SQLite) — create the file, then migrate + seed
touch database/database.sqlite
php artisan migrate --seed

# 4. Run it
php artisan serve
```

Then open <http://127.0.0.1:8000>.

### Seeded demo accounts

Password for every seeded user is **`password`**.

| Email | Role |
|-------|------|
| `alice@example.com` | Owns a couple of projects |
| `bob@example.com`   | Owns a couple of projects |

Log in as `alice@example.com` to edit/delete her projects; logging in as `bob`
and trying to edit Alice's project returns a 403 (the policy in action).

---

## Running the tests

```bash
php artisan test
```

Feature tests cover the project ownership policy, issue filtering, the AJAX
comment flow (create + validation + pagination), and AJAX tag attach/detach.

---

## How the AJAX pieces work

- `public/js/app.js` — a tiny Fetch wrapper that attaches the CSRF token and the
  `X-Requested-With` header, and parses JSON responses (including 422 validation errors).
- `public/js/issues-index.js` — filters reload instantly, search is debounced (300 ms);
  the server returns just the rendered list partial, which is swapped into the page.
  Pagination links stay inside the AJAX boundary.
- `public/js/issue-show.js` — comments (load more + add), tag attach/detach, and
  member assign/remove. The server renders the relevant Blade partial and returns its
  HTML, so the markup stays consistent with the initial page render.

---

## Project structure (the parts that matter)

```
app/
├── Http/
│   ├── Controllers/        ProjectController, IssueController, TagController,
│   │                       CommentController, IssueTagController,
│   │                       IssueMemberController, Auth/AuthController
│   └── Requests/           Form Request validation classes
├── Models/                 Project, Issue, Tag, Comment, User
└── Policies/               ProjectPolicy
database/
├── migrations/             schema, incl. the standalone start_date/deadline migration
├── factories/              Project/Issue/Tag/Comment factories
└── seeders/                DatabaseSeeder (users, tags, projects, issues, comments)
resources/views/            Blade layouts, partials, and pages
public/{css,js}/            styles + vanilla-JS AJAX
routes/web.php              routes (read public, writes behind auth)
```

---

## Notes & decisions

- **SQLite by default** keeps setup to a single command and matches the in-memory
  test database. Switch `DB_CONNECTION` in `.env` to use MySQL/Postgres.
- **No front-end build step.** Bootstrap is loaded from a CDN and the JavaScript is
  plain ES — the app runs immediately after `composer install`, with nothing to compile.
- **Reads are public, writes require auth.** Adding a comment is intentionally open
  (it captures an `author_name`), matching the task's data model.
