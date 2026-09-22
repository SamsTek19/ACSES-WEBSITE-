# Developer Workflow

## Start safely

1. Extract the package into a new private Git repository.
2. Read the root `README.md` and `docs/ARCHITECTURE.md`.
3. Create local `.env` files from the examples; never edit the examples with secrets.
4. Import the schema-only database and seed local-only accounts.
5. Confirm both applications run before changing code.
6. Create a feature branch for each coherent change.

## Engineering expectations

- Preserve role separation between students and administrators.
- Use prepared queries or Laravel's query builder; never concatenate user input.
- Keep CSRF validation on every state-changing browser request.
- Do not log OTPs, tokens, passwords, keys, complete mail addresses, or payment data.
- Add migrations for schema changes. Do not edit `database/schema.sql` manually;
  regenerate a schema snapshot only after migrations have been tested.
- Keep credentials and service keys in ignored environment files.
- Use sandbox accounts for payment, SMS, and email integrations.
- Validate uploaded files by MIME type, extension, size, and authorization.
- Add automated tests for authentication, authorization, payments, and voting.

## Required documentation for every return

Update `CHANGELOG-DEVELOPER.md` with:

- Summary of the problem and implemented behavior.
- Every changed file and why it changed.
- Database migrations and whether they are reversible.
- New or changed environment variables.
- Commands run and test results.
- Manual verification steps.
- Known limitations and follow-up work.
- Deployment and rollback instructions.

Do not return only compiled assets. Include the source files that generated them.

## Validation before packaging

```bash
cd student-portal
composer install
npm install
php artisan test
npm run build
php artisan route:list
```

Also exercise registration, email verification, login OTP, admin authorization,
dues/payment sandbox flows, election access links, voting, duplicate-vote prevention,
and result visibility using disposable local accounts.

## Return archive

Remove `.env`, `vendor`, `node_modules`, logs, caches, database data, uploaded files,
and IDE settings. Complete the return checklist, zip the whole handoff directory,
and send it to the platform owner with the commit hash and change summary.

