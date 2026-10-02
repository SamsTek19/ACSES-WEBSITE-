# Platform Architecture

## Applications

### Student portal

Laravel 12 application serving students and administrators. It provides account
registration and verification, password and OTP login, trusted devices, events,
announcements, resources, suggestions, academic timelines, and administrative
management. Course-registration and dues/payment workflows are disabled.

The application uses separate `student` and `admin` authentication guards backed
by the shared `users` table. Public web routes are in `student-portal/routes/web.php`.

### Election portal

Standalone PHP application using PDO and PHPMailer. Students authenticate through
email access links and vote in elections stored in the shared database. Election
administrators use records from the same `users` table.

### WordPress sync plugin

Optional bridge between WordPress users and the portal database. Treat direct
cross-application database access as technical debt; new integrations should
prefer a scoped, authenticated API.

## Shared data

`database/schema.sql` is a schema-only snapshot. Key table groups include:

- Identity: `users`, `pending_registrations`, password and OTP tables.
- Sessions and queues: `sessions`, `cache`, `jobs`, `failed_jobs`.
- Student services: account applications, events, resources, and legacy course-registration and dues/payment tables.

Legacy course-registration and dues/payment tables remain for historical
compatibility; their feature routes and screens are disabled. Existing data is retained.
- Elections: elections, positions, candidates, votes, access links, and audit logs.

No production rows are included.

## External services

Production can use SMTP, SMS, Sentry, and local server storage.
All integrations are disabled or blank in the handoff configuration. Developers
must mock them or use provider sandbox credentials stored only in an ignored `.env`.

## Known technical debt

- Production schema history and Laravel migration history are not fully aligned.
- The election portal is framework-free and duplicates some authentication logic.
- Some election admin endpoints historically used an inconsistent `admin_users`
  table check; changes should standardize on `users.role = 'admin'`.
- The election dashboard references a `get_results` endpoint that was absent from
  the production snapshot.
- The WordPress plugin directly accesses the portal database.

