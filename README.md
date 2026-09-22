# ACSES Platform — Developer Handoff

Sanitized source snapshot prepared on 29 August 2026. This package contains no
production database records, passwords, API keys, mail credentials, logs, user
uploads, registration documents, or private SSH material.

## Package contents

- `student-portal/` — Laravel 12 student and administration portal.
- `election-portal/` — standalone PHP election application.
- `wordpress-plugin/acses-user-sync/` — optional WordPress identity-sync plugin.
- `database/schema.sql` — schema-only export of the shared 30-table MariaDB database.
- `docs/` — architecture, developer workflow, and return/deployment checklists.

The Laravel and election applications share the same `users` and election tables.
Run both against one local database named `acses_local`.

## Prerequisites

- PHP 8.2 or newer with PDO MySQL, OpenSSL, Mbstring, XML, Fileinfo, and GD.
- Composer 2.x.
- Node.js 20 or newer and npm.
- MySQL 8 or MariaDB 10.6 or newer.
- Optional: Mailpit or MailHog for safe local email testing.

Do not connect this package to the production database, SMTP service, payment
gateways, SMS service, Sentry project, or production domains.

## 1. Create the local database

```sql
CREATE DATABASE acses_local CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Import `database/schema.sql` with phpMyAdmin, Adminer, MySQL Workbench, or:

```bash
mysql -u root -p acses_local < database/schema.sql
```

Use the schema import for the initial setup. The production database predates
some Laravel migrations, so running every historical migration against an empty
database is not currently the authoritative bootstrap path.

## 2. Start the Laravel student portal

```bash
cd student-portal
cp .env.example .env
composer install
php artisan key:generate
npm install
npm run build
php artisan db:seed
php artisan serve --host=127.0.0.1 --port=8000
```

On Windows PowerShell, use `Copy-Item .env.example .env` instead of `cp`.
Adjust the `DB_*` values in `.env` if the local MySQL account differs.

Local seeded accounts:

| Role | Login | Password |
|---|---|---|
| Admin | `admin@example.test` | `ChangeMe!12345` |
| Student | `student@example.test` | `ChangeMe!12345` |

These credentials are local-only and must never be deployed.

Open <http://127.0.0.1:8000/login>. The admin dashboard is available at
<http://127.0.0.1:8000/admin/dashboard> after authentication.

## 3. Start the election portal

```bash
cd election-portal
cp .env.example .env
cp config.example.php config.php
php -S 127.0.0.1:8080
```

On Windows PowerShell, replace `cp` with `Copy-Item`. The provided configuration
loads its local `.env`, connects to `acses_local`, and uses Mailpit/MailHog on
port 1025 by default.

Open <http://127.0.0.1:8080/access>.

## 4. Optional local email capture

Run Mailpit or MailHog locally, expose SMTP on `127.0.0.1:1025`, and use its web
interface to view OTP and magic-link messages. Never send test mail through a
real student or production mailbox.

## 5. WordPress plugin

Copy `wordpress-plugin/acses-user-sync` to `wp-content/plugins/`, activate it,
then enter local database settings in WordPress admin. The plugin deliberately
contains no default database username or password.

## Before returning work

Follow `docs/DEVELOPER-WORKFLOW.md` and complete
`docs/RETURN-FOR-DEPLOYMENT-CHECKLIST.md`. Include documentation, tests, database
migrations, and a clear change log. Zip the entire updated handoff folder and
return it to the platform owner. Never include `.env`, credentials, dependencies,
logs, caches, database data, or user uploads.

