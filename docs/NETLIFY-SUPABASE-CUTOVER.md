# Netlify and Supabase Cutover

This repository is being moved toward a static Netlify frontend backed by Supabase. The Laravel student portal and standalone PHP election portal remain in the repository for rollback until feature parity and database security have been verified.

## Current implementation

- Supabase schema and row-level security: `supabase/migrations/202610030001_initial_portal_schema.sql`. New tables use the `portal_*` prefix so they do not collide with existing Laravel/MySQL-style tables in the project.
- Seven idempotently seeded legacy events: `supabase/seed.sql`.
- Browser client uses only `VITE_SUPABASE_URL` and `VITE_SUPABASE_ANON_KEY`.
- Netlify routes `/portal/*` to the React app, and the public contact form is registered as a Netlify Form.
- React routes include Supabase sign-up/sign-in, student workspace, event administration, student approval, portal content, student messages, and election administration/voting.
- Election ballots are anonymous rows; one-vote-per-election enforcement and result access are database functions.

## Local database validation

Install and start Docker Desktop first. Then from the repository root:

```sh
supabase start
supabase db reset
supabase db lint --local
```

`db reset` resets only the local Supabase database and runs the configured seed. Do not run it against production.

## Deploy the Supabase schema

1. Confirm the target Supabase project is correct. The migration creates isolated `portal_*` tables and leaves the existing legacy tables untouched.
2. Link the Supabase CLI to that project using its project reference. Authenticate with the CLI locally; do not put an access token or database password in Git, Netlify, or chat.
3. Review the migrations, then run `supabase db push` to apply them. The second migration seeds the seven recovered events idempotently.
4. Confirm `select count(*) from public.portal_events;` returns seven before switching the public site to Supabase. `supabase/seed.sql` remains the local-reset seed and is safe to rerun.

## Supabase Auth and first admin

In Supabase Auth settings, set the site URL to the Netlify site origin and add both the Netlify origin and any custom domain to the redirect URL allow-list. Configure production email delivery and email confirmation before accepting student registrations.

To bootstrap the first administrator:

1. Register the administrator's intended email through `/portal/register` and confirm the email.
2. In the Supabase SQL Editor, promote only that verified account:

```sql
update public.portal_profiles as profile
set role = 'admin', account_status = 'approved', reviewed_at = now()
from auth.users as auth_user
where profile.id = auth_user.id
  and profile.email = 'ADMIN_EMAIL'
  and auth_user.email_confirmed_at is not null;
```

Replace `ADMIN_EMAIL` with the administrator's email. Verify exactly one profile was updated. Do not grant admin role from browser metadata or expose the Supabase service-role key to the frontend.

## Netlify settings

- Base directory: repository root.
- Build command: `npm run build` (or `npm ci && npm run build`).
- Publish directory: `dist`.
- Build environment variables:
  - `VITE_SUPABASE_URL`: the Supabase project URL.
  - `VITE_SUPABASE_ANON_KEY`: the project publishable/anon key only.
- Never set `SUPABASE_SERVICE_ROLE_KEY` or a database password as a `VITE_` variable.

Enable Netlify Forms for the `public-contact` form after the first deploy. The static form signature and SPA rewrite are emitted from `index.html` and `public/_redirects`.

## Cutover checks

- `/portal/register` creates a pending profile; an unapproved profile cannot read student data.
- `/portal/login` works after email confirmation.
- Only approved admins can manage events, student approvals, content, suggestions, and elections.
- Anonymous visitors can read events, but cannot mutate data.
- A ballot cannot be read by the voter, and a second vote for the same election is rejected atomically.
- Election results remain unavailable to students until the election ends.
- Netlify Forms receives a test contact submission.
- Refreshing `/portal/login`, `/portal/student`, and `/portal/admin/events` serves the SPA rather than a 404.

## Not ready to retire PHP yet

The migration SQL has not been applied to the hosted project or validated locally because Docker Desktop is unavailable in the current environment. The feature build passes, but database/RLS execution still needs Supabase CLI validation. Some legacy admin features also need parity work (including profile management and full edit/delete coverage for all content types). Keep both PHP applications available until the cutover checks pass. Disabled dues/payment workflows are intentionally not included.
