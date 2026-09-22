# ACSES Election Portal

Standalone PHP election application. It shares the database documented in
`../database/schema.sql` with the Laravel student portal.

## Local setup

1. Import `../database/schema.sql` into a local database named `acses_local`.
2. Copy `.env.example` to `.env`.
3. Copy `config.example.php` to `config.php`.
4. Start the app with `php -S 127.0.0.1:8080`.
5. Open <http://127.0.0.1:8080/access>.

The example configuration reads `.env`, uses PDO prepared statements, and sends
mail to a local Mailpit/MailHog SMTP listener on port 1025. Supply only disposable
local values. `config.php` and `.env` are intentionally excluded from source control.

## Important development notes

- Standardize administrator authorization on the shared `users` table and
  `role = 'admin'`; do not introduce or depend on an `admin_users` table.
- The dashboard references `get_results`, but that endpoint was absent from the
  production snapshot. Implement and test it before enabling live result refresh.
- Never log access tokens, link hashes, OTPs, CSRF tokens, SMTP credentials, or
  personally identifiable information.
- Keep TLS certificate verification enabled outside a disposable local mail catcher.
- Add schema changes as Laravel migrations so both applications remain aligned.

See the package root `README.md` and `docs/DEVELOPER-WORKFLOW.md` before editing.
