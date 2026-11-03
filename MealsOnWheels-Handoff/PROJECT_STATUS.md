# Meals on Wheels — Current State Audit

_Prepared 2026-06-04. Snapshot of the application as received, plus the local environment set up to run it._

---

## 1. What this application is

A **PHP + Microsoft SQL Server** internal web application for running a Meals-on-Wheels
operation: managing meal recipients, their delivery routes, dietary needs, assessments,
contacts, billing/insurance, and printable reports/labels.

It is a **working, mature internal CRUD app**, not a prototype.

| Metric | Value |
|---|---|
| Application PHP files | 111 |
| Application PHP lines (excl. vendor libs) | ~11,000 |
| Database tables | 44 |
| Live data | 187 recipients, 383 contacts, 16 routes, 5 users |
| Vendored libraries | dompdf (PDF), PhpWord (Word export) |
| Front-end | jQuery + jQuery UI, CKEditor, custom JS |

## 2. Architecture

- **Single-page shell.** `index.php` renders the frame; all navigation and content load
  via **jQuery AJAX** against `menu/menuContent.php`, which includes one module from
  `menu/menus/` (dashboard, recipients, contacts, dietary, assessments, routes, reports,
  meal ledger, system data).
- **Data layer.** `classes/db.class.php` — a thin PDO wrapper over the `sqlsrv` driver
  (`select / insert / update / delete / create`). All queries are parameterized.
- **Auth.** Session-based login (`login/login.php`) using bcrypt (`password_verify`),
  with `isAdmin` / permission-level checks in `classes/security.php`.
- **CRUD pattern.** `addContentForms/` + `editContentForms/` (UI) →
  `handlers/` (write logic) → `getData/` (read queries).
- **Hosting (production).** IIS (21 KB `web.config`); database shipped as an attached
  `MealsOnWheels.mdf` / `.ldf`.

## 3. Feature areas (all implemented)

Dashboard (off-today/tomorrow, follow-ups due, due assessments, expiring certs),
Recipients, Contacts, Dietary (diets/allergies/accommodations/milks),
Assessments (dynamic question builder + scoring), Routes & route changes,
**Meal Ledger** (the most complex module — program/route ledgers with drill-down),
Billing/Insurance, and Reports (frozen/hot labels, individual recipient report,
insurance billing — exported via dompdf & PhpWord).

## 4. Issues found (state of the codebase)

| # | Severity | Issue | Location |
|---|---|---|---|
| 1 | 🔴 High | **Hardcoded DB credentials** in source (`sa` + plaintext password, tied to one machine name). Security risk + non-portable. | `classes/db.class.php` |
| 2 | 🔴 High | **Recipient-assessment save is broken** — a stray `exit($_POST['dateCreated']);` kills the handler; all insert code below it is dead. | `menu/handlers/addContentHandler.php:311` |
| 3 | 🟡 Med | **Leftover one-time data migration runs on every page load** for user id 1 (rewrites the contacts table each request). | `index.php:18-30` |
| 4 | 🟡 Med | **External CDN dependency** (jQuery, jQuery UI, Typekit, CKEditor). App degrades/breaks with no internet or on a locked-down network. | `includes/head.php` |
| 5 | 🟡 Med | **No CSRF protection** on POST handlers (mitigated by being internal + auth-gated). | handlers |
| 6 | 🟢 Low | **No project hygiene**: not a git repo, no README, no automated tests, no `composer.json` (libs vendored by hand). Empty `menu/setData/` and empty `includes/recipientContactTemplate.php` hint at abandoned refactors. | repo-wide |

## 5. Maintainability assessment (for effort estimation)

- **Readable, consistent structure** — the add/edit/handler/getData split is followed
  throughout, so locating where a change goes is predictable.
- **Low abstraction** — lots of repeated inline SQL and hand-built HTML strings in PHP.
  Small changes are quick; broad cross-cutting changes (e.g. a new field on recipients)
  touch several files (form + handler + getData + edit form).
- **No tests** — every change must be verified manually in the browser, so QA time is a
  real line item in any estimate.
- **Tight HTML/PHP/SQL coupling** — front-end and data logic live together; UI restyling
  and data changes are not cleanly separable.

**Rule of thumb for estimating client changes on this codebase:**
- Add/adjust a field on an existing entity: small (touches ~3–4 files + manual test).
- New report or label: small–medium (clear pattern to copy).
- New top-level module/menu: medium (copy an existing module end to end).
- Cross-cutting work (auth, security hardening, removing CDN deps, adding tests): medium–large.

---

## 6. Local run environment (set up on this machine)

Everything below was installed/configured to run the app locally for testing.

| Component | Detail |
|---|---|
| PHP | 8.3.31 (thread-safe x64), via winget → `%LOCALAPPDATA%\Microsoft\WinGet\Packages\PHP.PHP.8.3_*` |
| SQL drivers | Microsoft `sqlsrv` + `pdo_sqlsrv` 5.13.1 (in PHP `ext/`, enabled in that folder's `php.ini`) |
| ODBC | Microsoft ODBC Driver 18 for SQL Server |
| Database | SQL Server 2022 Express, instance `localhost\SQLEXPRESS`; `MealsOnWheels` attached from `C:\Users\Public\MowData\` |
| Web server | PHP built-in server on **http://localhost:8090** with `router.php` (emulates IIS clean URLs) |

**Changes made to the app for local running** (app logic untouched):
- `classes/db.class.php` — connection re-pointed to `localhost\SQLEXPRESS` using Windows
  authentication + `TrustServerCertificate=true`. Original production values are preserved
  in comments.
- `router.php` — added (dev-only) so PHP's built-in server resolves `/login`, `/logout`, etc.

**Test login** (password reset locally for access — other accounts untouched):
- Email: `admin@admin.com`
- Password: `Mow2026!`

**Browser note:** the app renders correctly in any clean browser. If a browser shows the
page source as text, that browser profile has an extension injecting source — use a clean
profile / disable extensions. Verified rendering correct in headless Chrome.

## 7. To restart the server later

Run `start-mow.bat` (in this folder), or manually:

```
& "<PHP path>\php.exe" -c "<PHP path>\php.ini" -S localhost:8090 -t "<repo>\MealsonWheels" "<repo>\MealsonWheels\router.php"
```

Then open http://localhost:8090/ in a clean browser profile.
