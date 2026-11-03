# Meals on Wheels — Setup & Handoff Guide

This is the Meals on Wheels app (PHP + Microsoft SQL Server) with the **10 new operational
reports** added. This guide gets it running on a fresh Windows laptop for testing.

> Tip: the reporting UI is jQuery-driven and renders normally in any standard browser. If a
> machine has aggressive endpoint-security software (e.g. CrowdStrike Falcon) that hooks
> browsers, pages may display as raw source text — that is a machine/security issue, not the
> app. On a normal laptop it renders fine.

---

## 1. Prerequisites (install once)

1. **PHP 8.3 (x64, Thread-Safe)** — easiest via winget:
   ```
   winget install --id PHP.PHP.8.3 --scope user
   ```
2. **Microsoft ODBC Driver 18 for SQL Server** + **SQL Server 2022 Express**:
   ```
   winget install --id Microsoft.msodbcsql.18
   winget install --id Microsoft.SQLServer.2022.Express
   ```
3. **PHP `sqlsrv` + `pdo_sqlsrv` extensions** (match PHP 8.3 TS x64):
   - Download from Microsoft's "Drivers for PHP for SQL Server" (5.13+).
   - Copy `php_sqlsrv_83_ts_x64.dll` and `php_pdo_sqlsrv_83_ts_x64.dll` into PHP's `ext\` folder.
   - In `php.ini` add:
     ```
     extension=php_sqlsrv_83_ts_x64.dll
     extension=php_pdo_sqlsrv_83_ts_x64.dll
     ```
   - Verify: `php -m` lists `sqlsrv` and `pdo_sqlsrv`.

## 2. Attach the database

The database files ship with this project: `MealsOnWheels.mdf` and `MealsOnWheels_log.ldf`.

1. Copy both files somewhere SQL Server can read, e.g. `C:\Users\Public\MowData\`.
2. Attach them (Windows auth, run as a user that is sysadmin on the instance):
   ```
   sqlcmd -S localhost\SQLEXPRESS -E -C -N -Q "CREATE DATABASE [MealsOnWheels] ON (FILENAME='C:\Users\Public\MowData\MealsOnWheels.mdf'),(FILENAME='C:\Users\Public\MowData\MealsOnWheels_log.ldf') FOR ATTACH;"
   ```
   (SQL Server will auto-upgrade the file format if needed.)

## 3. Point the app at the database

Edit **`MealsonWheels/classes/db.class.php`** → `connect()`:

```php
$serverName = "localhost\\SQLEXPRESS";   // <-- your SQL instance
$database   = "MealsOnWheels";
$this->link = new PDO("sqlsrv:Server=$serverName;Database=$database;TrustServerCertificate=true;LoginTimeout=15");
```

- This uses **Windows authentication** (the user running PHP must have access to the DB).
- The original production server/SQL-login values are preserved in comments in that file for
  when deploying to the live server.

## 4. Run the app

From the project root:
```
"<PHP path>\php.exe" -c "<PHP path>\php.ini" -S localhost:8090 -t "<repo>\MealsonWheels" "<repo>\MealsonWheels\router.php"
```
`router.php` emulates the IIS clean-URLs the app expects. Then open **http://localhost:8090**.

(`start-mow.bat` in this folder does this — edit the PHP path inside it to match your install.)

## 5. Create a test login, then log in

The shipped database contains the original user accounts (whose passwords are unknown). Create a
known test login by running this once from a terminal (after step 3 is configured):

```
"<PHP path>\php.exe" MealsonWheels\tools\create-test-user.php
```

It creates / resets:

| Email | Password |
|---|---|
| `staff@mow.com` | `Mow12345` |

Then open http://localhost:8090 and log in. (Local test account — delete
`tools\create-test-user.php` and change the password before any production use.)

---

## 6. The 10 reports (Reports menu)

All built on the app's existing PDF engine (Dompdf). Click **Reports** in the sidebar, pick a
report, set parameters, click **Generate** → the PDF opens in a new tab. Each report queries the
database live at generation time, so it always reflects the latest data.

| Report | Parameters | Source tables |
|---|---|---|
| Follow Ups Needed | due-on-or-before date | noteLogs, recipients |
| Physician Certification Due | days ahead | certificateLogs, recipients, physicians, clinics |
| Assessment Due | due-on-or-before date | assessmentScores/Links, recipients |
| Status Change Report | date range + status | clientStatusLog, recipients |
| Closed Call List | — | recipients (Non Active), clientStatusLog |
| Under 60 | as-of date | recipients |
| Route Totals | — | routes, recipients |
| No Donation | date range | recipients, donations |
| Unduplicated Program Count | date range | mealLedgers, recipients |
| Interventions | date range | noteLogs, recipients |

**Note on "Closed Call List":** the system has no literal "Closed" status (statuses are Active /
Non Active / Applicant), so this report uses **Non Active** as "closed." Confirm this maps to what
the client means.

**Reports with no data yet:** Follow Ups Needed and Interventions will show an empty-state message
until staff enter follow-ups / intervention flags — the logic is correct; there is simply no such
data in the current database.

## 7. What changed in this build (for review)

- Added 10 reports: forms in `menu/reports/forms/`, bodies in `viewReport/includes/reports/`,
  wired in `menu/reports/getReport.php`, dispatched in `viewReport/index.php`.
- `css/reportStyles.css` — self-contained report styling (no remote CSS dependency).
- jQuery / jQuery UI served locally (`js/lib/`, `css/lib/`) instead of from a CDN.
- Fixed a missing `appendForms()` in `js/formControl.js` that halted page JS on load.
- `db.class.php` — local connection + `LoginTimeout` (prod values kept in comments).
- Full audit: see `DUE_DILIGENCE_AUDIT.md`.
