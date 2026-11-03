# Meals on Wheels — Technical Due Diligence & Reporting Audit

_Evidence-based implementation audit. Every conclusion below references actual files, functions,
or live database query results captured during analysis on a running instance of the application
(PHP 8.3 + SQL Server 2022, database attached and populated)._

Prepared: 2026-06-04

---

## EXECUTIVE SUMMARY (read first)

- **The reporting engine is NOT fundamentally broken.** All four implemented reports were
  generated live during this audit and produced **valid PDF files** (verified by `%PDF` signature).
- **The real reason "reports don't generate" is a front-end failure, not the backend.** The entire
  report UI is driven by jQuery/AJAX, and jQuery was loaded from an internet CDN
  (`code.jquery.com`) that does not load in the client's browser environment. With jQuery dead, the
  report list never populates and the **Generate** buttons silently do nothing. **This has been
  fixed** by serving jQuery locally (see Phase 1 §1.4).
- **14 of the 18 reports listed in the menu are not implemented** — they are menu rows with no
  handler and return `"No Report Found"`.
- The codebase is functional but carries **real security and integrity risks**: hardcoded `sa`
  credentials, a SQL-injection point, no database foreign keys, and a broken save handler.

---

# PHASE 1 — REPORTING INVESTIGATION

## 1.1 Files that make up the reporting subsystem

| Layer | File | Role |
|---|---|---|
| Menu (UI list) | `menu/menus/reports.php` | Renders the Reports panel; calls `fillReports()` + `openReport()` via AJAX |
| Data (list) | `menu/getData/fillReports.php` | `SELECT * FROM reports ORDER BY displayOrder` → clickable rows |
| Router | `menu/reports/getReport.php` | `switch($_POST['id'])` → includes a report **form** |
| Report forms | `menu/reports/individualRecipientReport.php`, `hotLabels.php`, `frozenLabels.php`, `insuranceBilling.php` | Build the parameter form; on submit `window.open('/viewReport?'+serialize)` |
| **Generation engine** | `viewReport/index.php` | The actual engine: builds HTML, invokes **Dompdf**, streams PDF |
| Report bodies | `viewReport/includes/individualReport.php`, `hotLabels.php`, `frozenLabels.php`, `insuranceBilling.php` | The per-report HTML + SQL |
| PDF library | `dompdf/` (vendored Composer package) | HTML→PDF (`Dompdf\Dompdf`) |
| Word library | `PhpWord/` (vendored) | **Only used by `wordTest/index.php`** — a prototype, not wired to any report |
| Styling | `css/docStyles.css`, `css/hotLabelStyles.css`, `css/frozenLabelStyles.css` | Loaded **remotely** from production domain (see 1.5) |

## 1.2 End-to-end trace (verified)

```
Reports menu (menus/reports.php)
  └─ $(document).ready → fillReports()                [jQuery AJAX]
       └─ POST menu/getData/fillReports.php
            └─ SELECT * FROM reports                   [18 rows]
            └─ renders <div onclick="openReport(this,ID)">
  └─ click → openReport(btn,id)                        [jQuery AJAX]
       └─ POST menu/reports/getReport.php  switch(id)
            └─ include the matching FORM
  └─ form submit → window.open('/viewReport?'+data)    [jQuery handler]
       └─ viewReport/index.php
            └─ require dompdf/vendor/autoload.php
            └─ branch on $_GET['reportType']
            └─ include viewReport/includes/<report>.php  → SQL → HTML
            └─ $dompdf->loadHtml($html); ->render(); ->stream()   → PDF
```

## 1.3 Per-report status (evidence-backed)

The `reports` table (live query) contains **18 rows**. `getReport.php` only has cases for
**id 1, 6, 14, 18**. Everything else hits `default: echo 'No Report Found';`
(`menu/reports/getReport.php` lines 7–24).

### IMPLEMENTED & WORKING (4) — live-generated valid PDFs this audit

| Report | id | Form file | Body file | Tables used | Export | Live test |
|---|---|---|---|---|---|---|
| **Hot Labels** | 6 | `menu/reports/hotLabels.php` | `viewReport/includes/hotLabels.php` | recipients, routes, meals, daysOff, diets, accommodations, allergies, milkChoices | PDF (Dompdf) | ✅ 38,759 B PDF |
| **Frozen Labels** | 18 | `menu/reports/frozenLabels.php` | `viewReport/includes/frozenLabels.php` | recipients, routes, meals, daysOff, diets, accommodations, allergies | PDF (Dompdf) | ✅ 22,569 B PDF |
| **Individual Recipient Report** | 1 | `menu/reports/individualRecipientReport.php` | `viewReport/includes/individualReport.php` | recipients, clientStatus, clientStatusLog, contact_link, contacts, diets, allergies, billings, billingRecipientTypes, genders, maritalStatus, ethnicGroups | PDF (Dompdf) | ✅ 2,918 B PDF |
| **Insurance Billing** | 14 | `menu/reports/insuranceBilling.php` | `viewReport/includes/insuranceBilling.php` | recipients, clientStatus, billings, **mealLedgers** | PDF (Dompdf) | ✅ 2,727 B PDF |

### LISTED IN MENU BUT NOT IMPLEMENTED (14) — return "No Report Found"

ids **2, 3, 4, 5, 7, 8, 9, 10, 11, 12, 13, 15, 16, 17**:
Assessment Detail Report, No Donation Report, Unduplicated Program Count, Interventions,
Follow Ups Needed, Physician Certification Due, Assessment Due, Closed Call List, Statements,
Status Change Report, Statement Listing, Route Totals, OAA Reporting, Under 60.
Status: **Incomplete / stub** — row exists in `reports` table, no `case` in `getReport.php`,
no form, no body, no `viewReport` branch.

### PROTOTYPE / DISABLED

- `wordTest/index.php` — a standalone PhpWord Avery-label prototype that only prints the literal
  text `"Text"`. Not linked to any menu or report. **Word export is effectively unused.**

## 1.4 ROOT CAUSE: why reports were "not generating"

**Not a backend problem.** The PDF engine works (4/4 live PDFs above). The failure is front-end:

- `menu/menus/reports.php` lines 17–46 — `fillReports()` and `openReport()` are **jQuery `$.ajax`**
  calls.
- Every report form (e.g. `frozenLabels.php` lines 42–50) binds **`$('#reportForm').on('submit')`**
  and calls `window.open('/viewReport?'+$('#reportForm').serialize())`.
- jQuery was loaded from a CDN: `includes/head.php` (original)
  `<script src="https://code.jquery.com/jquery-3.6.0.min.js">`.
- In the client's browser jQuery did not load (same failure that blocked login). With `$`
  undefined: the report list never renders, and clicking **Generate** throws/does nothing.

**Fix applied during audit:** downloaded jQuery 3.6.0 + jQuery UI 1.12.1 locally to
`js/lib/` + `css/lib/` and repointed `includes/head.php` to them (no CDN). Verified the page now
runs its JS offline. **This restores the report UI.**

## 1.5 Secondary report issues found (will affect output quality)

1. **Remote CSS dependency.** `includes/functionSetup.php:16` sets
   `$domain = 'http://mow.perspektivemg.com'`, and `viewReport/index.php` (lines 51, 65, 98, 128)
   loads report CSS from `$domain/css/...` with `$options->setIsRemoteEnabled(true)`. PDF styling is
   fetched from the **production website over the internet**. If that host is unreachable, reports
   still generate but **unstyled**. Should be a local file path.
2. **Malformed `<body` tag.** `viewReport/index.php` builds `...</style><body'` (no closing `>`) for
   every report type (lines 55, 69, 102, 132); the close `</body>` is added later. Dompdf tolerates
   it today, but it is fragile.
3. **Insurance Billing title bug.** Line 30 references `$_GET['label_date']` for the filename, but
   that form (`menu/reports/insuranceBilling.php`) submits `startDate`/`endDate`, not `label_date`
   → PHP notice + wrong filename (cosmetic).

## 1.6 Fix / rebuild / replace verdict

| Item | Verdict | Why |
|---|---|---|
| 4 working reports | **Fix (minor)** | Already generate PDFs; only need local CSS + the `<body>` cleanup |
| Report UI (jQuery) | **Fixed** | Localized jQuery; root cause resolved |
| 14 stub reports | **Build** | No code exists; build on the proven Dompdf pattern |
| Word export (PhpWord) | **Replace or remove** | Only a prototype; standardize on Dompdf/PDF unless Word is a hard requirement |

---

# PHASE 2 — DATABASE ANALYSIS

Live instance: `localhost\SQLEXPRESS`, database `MealsOnWheels` (44 user tables).

## 2.1 Table inventory with record counts (live `sys.partitions` query)

| Table | Rows | | Table | Rows |
|---|--:|---|---|--:|
| recipients | **187** | | mealLedgers | **396** |
| contacts | **383** | | meals | 83 |
| contact_link | 296 | | systemMeals | 3 |
| relationships | 12 | | mealTypes | 2 |
| recipient_links | 1 | | routes | 16 |
| routeChanges | 3 | | reports | 18 |
| daysOff | 437 | | users | 5 |
| billings | **172** | | physicians | 54 |
| billingRecipientTypes | 3 | | clinics | 13 |
| billingLogs | 0 | | certificateLogs | 108 |
| assessments | 2 | | clientStatus | 3 |
| assessmentQuestions | 15 | | clientStatusLog | 362 |
| assessmentQuestionsOptions | 77 | | noteLogs | 147 |
| assessmentScoreLinks | 11 | | noteLogContacts | 280 |
| assessmentScores | 26 | | diets | 12 |
| assessmentTypes | 4 | | allergies | 12 |
| assessmentStatus | 0 | | accommodations | 23 |
| scoreTypes | 0 | | milkChoices | 6 |
| genders | 3 | | ethnicGroups | 7 |
| maritalStatus | 6 | | economicStatuses | 0 |
| donations | 0 | | moreOptions | 0 |
| optionFields | 0 | | preferences | 0 |

## 2.2 Relationships — CRITICAL FINDING

**The database has ZERO foreign keys** (live `sys.foreign_keys` query returned 0 rows). All
relationships are **implicit, enforced only by application code** via naming convention
(`recipient_id`, `route_id`, `contact_id`, `link_id`, `assessment_id`, `billType_id`, etc.).
Implications: no referential integrity, orphan rows are possible, cascade deletes are manual,
and reporting JOINs rely entirely on convention.

## 2.3 Inferred relationship map (from column usage in code + data)

```
routes (16) ──< recipients (187) >── clientStatus (3)
                   │   │   │   │
                   │   │   │   └──< clientStatusLog (362)
                   │   │   └──< daysOff (437)
                   │   └──< certificateLogs (108) >── physicians(54), clinics(13)
                   │
   contacts(383) >─┴─ contact_link (296) ──< recipients     [M:N via contact_link]
                   │
   billings(172) ──┤  billType_id → billingRecipientTypes(3)
                   │
   assessmentScoreLinks(11) ──< assessmentScores(26)
        └─ assessment_id → assessments(2) ──< assessmentQuestions(15) ──< assessmentQuestionsOptions(77)
                   │
   mealLedgers(396)  ── recipMatrix (CSV of recipient_id-count-count)   [denormalized]
   meals(83) ── recipient_id, mealCounts (CSV day matrix)               [denormalized]
   noteLogs(147) ──< noteLogContacts(280)
   users(5)
```

## 2.4 Reporting-related tables (have direct reporting value)

recipients, routes, billings, mealLedgers, meals, daysOff, assessments + assessment* family,
certificateLogs, clientStatus + clientStatusLog, contacts + contact_link, diets/allergies/
accommodations/milkChoices, noteLogs.

## 2.5 Empty / unused / low-use tables

- **Empty (0 rows):** `assessmentStatus`, `scoreTypes`, `billingLogs`, `donations`,
  `economicStatuses`, `moreOptions`, `optionFields`, `preferences`.
  Several of these back **stub reports** (e.g. `donations` ⇄ "No Donation Report",
  `economicStatuses` ⇄ OAA/Under-60 demographic reports) — i.e. the feature was scaffolded in the
  schema but never populated or wired up.
- **Barely used:** `recipient_links` (1 row), `routeChanges` (3).

## 2.6 Data quality risks for reporting

- **Denormalized CSV columns**: `meals.mealCounts`, `mealLedgers.recipMatrix`, and
  `recipients.diets/allergies/accommodations` store comma-joined values parsed in PHP
  (`explode`), and are even injected into SQL `IN (...)` (see Phase 4). This makes set-based
  reporting SQL hard and is a correctness/performance risk.

---

# PHASE 3 — REPORT REQUIREMENTS READINESS

Baseline: there is a **proven, reusable PDF pattern** (form → `getReport.php` case →
`viewReport` branch → include body → Dompdf). Building a new report = 5 small, well-understood
edits. Difficulty below assumes reuse of that pattern.

| Requested report | Reusable code? | Existing template? | New SQL? | Complexity | Notes / evidence |
|---|---|---|---|---|---|
| **Recipient Reports** | ✅ `individualReport.php` | ✅ working | Minor | **Low** | Extend the existing working report; data already mapped |
| **Route Reports** | ✅ label reports are route-based | Partial | Moderate | **Low–Med** | "Route Totals" (id 15) is a stub; routes+recipients+mealLedgers available |
| **Daily Meal Reports** | ✅ ledger module (`menu/functions/ledger/*`, 638 lines) | Partial | Moderate | **Medium** | Must parse denormalized `mealLedgers.recipMatrix` / `meals.mealCounts` CSV |
| **Assessment Reports** | Partial (data layer only) | ❌ (id 2, 9 stubs) | Yes | **Med–High** | Dynamic EAV model (assessments→questions→options→scores); flexible but complex |
| **Billing Reports / Statements** | ✅ insurance billing body | ❌ (id 11, 13 stubs) | Yes | **Medium** | `billings` (172) well populated; statement layout is new |
| **Insurance Reports** | ✅ working (id 14) | ✅ | Minor | **Low** | Extend existing Insurance Billing |
| **KPI Dashboard** | Partial (`menu/menus/dashboard.php` widgets) | ❌ for export | Yes (aggregations) | **Medium** | On-screen dashboard exists; PDF/printable KPI export is new |
| **Executive Summary** | ❌ | ❌ | Yes (cross-table aggregation) | **Med–High** | New roll-ups; relates to stubbed OAA / Unduplicated Program Count |

**Overall readiness: GOOD.** The hard part (PDF pipeline, auth, DB access, data) exists and is
proven. New reports are mostly "new SQL + new HTML body in the existing pattern." The two
constraints that add time everywhere: (a) **no FKs / denormalized CSV columns** make aggregate SQL
fiddly, and (b) **no test harness** means every report is QA'd by hand.

---

# PHASE 4 — CODEBASE HEALTH

### 🔴 CRITICAL

1. **Hardcoded DB credentials in source.** `classes/db.class.php` (original) embeds
   `username="sa"` + plaintext password tied to a machine name. Full-privilege DB account in code.
   _(Re-pointed to local Windows-auth during this audit; production code still carries the secret.)_
2. **SQL injection.** `menu/getData/fillRecipientData.php:10`:
   `$sql .= ' TOP ' . $_POST['amount'];` — unvalidated `$_POST` concatenated into SQL.
   (The text search on lines 35–41 **is** correctly parameterized — so the team knows the pattern;
   this one was missed.)
3. **Broken write handler — assessment save is dead code.**
   `menu/handlers/addContentHandler.php:311` begins the `recip-assessment` branch with
   `exit($_POST['dateCreated']);`, so the insert into `assessmentScoreLinks` / `assessmentScores`
   (lines 312–355) **never executes**. Saving a recipient assessment silently fails.

### 🟠 HIGH

4. **No foreign keys / no referential integrity** (Phase 2.2). Orphan and inconsistent data are
   possible and cannot be caught by the DB.
5. **Reflected output without escaping (XSS).** e.g. `menu/getData/getOptionDisplay.php:23,28` and
   `menu/getData/fillDietaryData.php` echo `$_POST['type']` straight into HTML.
6. **No CSRF protection** on any state-changing handler (`add/edit/deleteContentHandler.php`).
7. **Reports depend on the live production website for CSS** (Phase 1.5 #1) — external runtime
   dependency for an internal tool.

### 🟡 MEDIUM

8. **Migration code on the hot path.** `index.php:18–30` rewrites the entire `contacts` table on
   **every page load** for user id 1, with commented-out CSV import beside it.
9. **Denormalized CSV columns** parsed in PHP and concatenated into `IN (...)` clauses
   (`viewReport/includes/hotLabels.php:35,44,47`, `frozenLabels.php:45,50,53`) — performance + a
   second injection surface (from stored data).
10. **CDN runtime dependency** for jQuery/jQuery UI/Typekit/CKEditor (`includes/head.php`) — breaks
    offline/locked-down networks. _(jQuery/jQuery UI localized during audit; Typekit/CKEditor
    remain.)_

### 🟢 LOW

11. **No version control** (no `.git`), **no dependency manifest** (no `composer.json`; dompdf &
    PhpWord vendored by hand), **no automated tests**.
12. **Dead/abandoned code**: empty `menu/setData/` directory; empty
    `menu/includes/recipientContactTemplate.php`; `wordTest/` prototype; commented blocks.
13. **Monolithic handlers**: `addContentHandler.php` (397 lines) and `editContentHandler.php`
    (391 lines) are single large `if/else` chains — maintainable but brittle.

---

# PHASE 5 — IMPLEMENTATION ESTIMATE

Estimates assume one engineer familiar with PHP + SQL Server, reusing the existing report pattern,
and **building the 14 stub reports + hardening**. "New report" average derived from the proven
5-edit pattern (form + case + branch + body + SQL), scaled by the complexity ratings in Phase 3.

| Work area | Optimistic | Realistic | Safe | Key assumptions |
|---|--:|--:|--:|---|
| **A. Audit & Discovery** | 6 | 12 | 20 | Largely complete (this document + running env). Remaining: confirm client's exact report specs |
| **B. Report Repairs** | 8 | 16 | 28 | jQuery (done), local CSS, `<body>` fix, assessment-save bug, make 14 stub menu rows honest |
| **C. New Report Development** | 60 | 110 | 170 | 14 stubs + the 8 requested types; avg ~8–12 h each, KPI/Exec/Assessment at the high end |
| **D. Testing & QA** | 20 | 30 | 45 | Manual (no test harness); ~25–30% of dev |
| **E. Deployment & Hardening** | 6 | 12 | 20 | Externalize credentials, fix SQL injection/XSS/CSRF, IIS config, smoke test |
| **TOTAL** | **100** | **180** | **283** | ≈ 2.5 / 4.5 / 7 engineer-weeks |

**Per-report quick reference (realistic, using existing pattern):**
Low complexity 4–6 h · Medium 8–12 h · Med–High 14–20 h.

**Biggest estimate risks (what pushes toward "Safe"):**
- Vague report specs from the client (acceptance criteria churn).
- Denormalized CSV data forcing PHP-side aggregation instead of clean SQL.
- No tests → regression risk as reports are added.
- Production deployment access/credentials not yet confirmed.

---

# PHASE 6 — CLIENT PROPOSAL

## 1. Current State Summary
A working PHP + SQL Server case-management system (187 recipients, 383 contacts, 44 tables) with a
**functional PDF reporting engine** (Dompdf). Four reports are fully built and were generated live
during this audit. The reason staff "can't generate reports" is a **front-end dependency failure**
(jQuery loaded from an internet CDN that fails in their environment) — already fixed. Of 18 reports
in the menu, **4 work and 14 are unimplemented stubs**.

## 2. Risks
- Hardcoded `sa` credentials and a SQL-injection point (security).
- No database foreign keys (data integrity).
- Broken assessment-save handler (silent data loss).
- Reports pull CSS from the live production site (external runtime dependency).
- No source control, dependency manifest, or tests (delivery + maintenance risk).

## 3. Recommended Approach
1. **Stabilize** (low effort, high value): keep jQuery local, move report CSS local, externalize DB
   credentials, fix the SQL-injection and assessment-save bugs, put the project under git.
2. **Make the menu honest**: hide/disable the 14 stub reports until built (prevents
   "No Report Found").
3. **Build reports in priority order** on the proven Dompdf pattern, starting with the
   lowest-complexity, highest-value items (Recipient, Insurance, Route).
4. **Harden before/with deployment**: parameterize all queries, add CSRF tokens, escape output.

## 4. Phase-by-Phase Roadmap
- **Phase A – Stabilize & Secure (≈1 week):** credentials, SQL-injection, assessment bug, jQuery/CSS
  local, git init, hide stub reports.
- **Phase B – Repair & Quick Wins (≈1 week):** polish the 4 working reports; build the 2–3
  low-complexity requested reports (Recipient, Insurance, Route).
- **Phase C – Core Report Build-out (≈2–3 weeks):** Daily Meal, Billing/Statements, Assessment
  reports; address denormalized-data handling.
- **Phase D – Analytics (≈1–2 weeks):** KPI dashboard export + Executive Summary.
- **Phase E – QA & Deploy (≈1 week):** manual QA pass, hardening, IIS deployment.

## 5. Deliverables
- Fixed, working report UI + the 4 existing reports cleaned up.
- The requested report set (Recipient, Route, Daily Meal, Assessment, Billing, Insurance, KPI,
  Executive Summary) as PDF.
- Security fixes (credentials externalized, injection/XSS/CSRF closed).
- Database FK/integrity recommendations (or applied constraints).
- Source control + a short run/deploy README.

## 6. Timeline
**≈6–8 weeks** for one engineer at the realistic estimate (180 h), front-loaded with the
stabilize/secure work so staff regain working reports in week 1.

## 7. Estimated Effort
**Optimistic 100 h · Realistic 180 h · Safe 283 h** (Phase 5). Recommend contracting against the
**realistic** figure with the safe figure as the not-to-exceed.

---

## APPENDIX — Evidence index (files cited)
`classes/db.class.php` · `index.php` · `includes/head.php` · `includes/functionSetup.php` ·
`menu/menus/reports.php` · `menu/getData/fillReports.php` · `menu/getData/fillRecipientData.php` ·
`menu/getData/getOptionDisplay.php` · `menu/getData/fillDietaryData.php` ·
`menu/reports/getReport.php` · `menu/reports/{individualRecipientReport,hotLabels,frozenLabels,insuranceBilling}.php` ·
`menu/handlers/addContentHandler.php` · `viewReport/index.php` ·
`viewReport/includes/{individualReport,hotLabels,frozenLabels,insuranceBilling}.php` ·
`wordTest/index.php` · `menu/functions/ledger/*` ·
Live DB: `sys.tables`/`sys.partitions` (counts), `sys.foreign_keys` (0 rows), `reports` table (18 rows),
live PDF generation of report types 1/6/14/18.
