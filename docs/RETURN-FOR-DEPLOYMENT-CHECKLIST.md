# Return for Deployment Checklist

Developer name:

Repository and branch:

Commit hash:

Date:

## Scope

- [ ] `CHANGELOG-DEVELOPER.md` describes all changes.
- [ ] Changed files and business behavior are listed.
- [ ] Known limitations and unfinished items are listed.
- [ ] Screenshots are included for material UI changes.

## Security and privacy

- [ ] No `.env`, credentials, API keys, tokens, cookies, or private keys are included.
- [ ] No production database records, logs, uploads, or student documents are included.
- [ ] Authorization was tested separately for student and admin roles.
- [ ] State-changing routes retain CSRF protection.
- [ ] Sensitive values are not written to logs.

## Database

- [ ] Every schema change has a tested migration.
- [ ] Migration order and rollback commands are documented.
- [ ] No production data is embedded in seeders or fixtures.

## Verification

- [ ] `php artisan test` passes, or failures are documented.
- [ ] `npm run build` passes.
- [ ] Registration and verification were tested locally.
- [ ] Student and admin login/OTP flows were tested locally.
- [ ] Election access and voting were tested locally.
- [ ] Payment integrations use sandbox or mocks only.

## Deployment handoff

- [ ] New environment variables are documented without real values.
- [ ] Deployment commands are listed in order.
- [ ] Cache-clear/rebuild commands are listed.
- [ ] Rollback procedure is included.
- [ ] The final archive excludes `vendor`, `node_modules`, `.git`, caches, and logs.

