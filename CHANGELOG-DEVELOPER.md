# Developer Change Log

Complete this file before returning the platform for deployment.

## Summary

Added first-admin bootstrap, connected admin-managed Laravel events to the React
public site, removed course-registration and dues/payment workflows while
preserving existing data, and reduced local request latency.

## Changed files

| File | Reason |
|---|---|
| `student-portal/app/Console/Commands/CreateFirstAdmin.php` | Creates the first administrator through validated hidden password prompts and refuses if an admin exists. |
| `student-portal/tests/Feature/CreateFirstAdminTest.php` | Covers first-admin creation, password hashing, and the existing-admin guard. |
| `student-portal/app/Http/Controllers/Api/PublicEventController.php` | Serializes and briefly caches public event fields; event saves/deletes invalidate the cache. |
| `student-portal/config/cors.php` | Restricts public event API CORS to configured website origins. |
| `student-portal/routes/web.php` | Registers the throttled public events route and removes course-registration routes. |
| `student-portal/tests/Feature/PublicEventsApiTest.php` | Verifies API response fields, ordering, CORS, and cache invalidation. |
| `student-portal/.env.example` | Documents the allowed website origin and local file-backed session/cache defaults. |
| `.env.example` | Documents the Vite event endpoint setting. |
| `src/lib/useEvents.ts` | Fetches events once through a shared hook and tracks loading/error states. |
| `src/types.ts` | Extends the public event shape for API-provided links, image alt text, and event type. |
| `src/pages/Home.tsx` | Displays the latest three live events. |
| `src/pages/Events.tsx` | Displays and filters live events. |
| `src/data/mockData.ts` | Removes the no-longer-used hard-coded event list. |
| `docs/DEVELOPER-WORKFLOW.md` | Documents first-admin setup and public event integration configuration. |
| `student-portal/resources/views/components/admin/sidebar.blade.php` | Removes Finance, Student Applications, and Students navigation. |
| `student-portal/resources/views/components/dashboard/header.blade.php` | Removes desktop and mobile student dues links. |
| `student-portal/resources/views/components/layouts/dashboard.blade.php` | Removes the dues lock wrapper so old records cannot block student access. |
| `student-portal/bootstrap/app.php` | Removes the dues-gate middleware alias. |
| `student-portal/routes/web.php` | Removes student payment and admin dues routes and normalizes student routes to auth-only access. |
| `student-portal/app/Services/Admin/AdminDashboardService.php` | Removes finance and student-management metrics; reports content and feedback activity. |
| `student-portal/app/Services/Student/StudentDashboardService.php` | Removes dues/course-registration actions and copy. |
| `student-portal/app/Http/Controllers/Admin/AdminStudentAccountController.php` | Stops assigning dues when admins create or update students. |
| `student-portal/app/Http/Controllers/Auth/RegisterController.php` | Stops assigning dues during standard registration. |
| `student-portal/app/Services/Registration/PendingRegistrationService.php` | Stops assigning dues when approving registrations. |
| `student-portal/app/Services/Admin/AdminDueService.php` | Removes the unused method that could auto-create dues for a student. |
| `student-portal/app/Http/Controllers/Admin/AdminMaintenanceController.php` | Removes dues maintenance queries/actions while retaining account cleanup. |
| `student-portal/resources/views/dashboards/admin/index.blade.php` | Removes finance metrics, student overview cards, and enrollment chart. |
| `student-portal/resources/views/dashboards/admin/maint_portal/index.blade.php` | Removes dues cleanup/assignment/amount/merge panels. |
| `student-portal/resources/views/dashboards/admin/students/show.blade.php` | Removes the dues ledger and shortcut. |
| `student-portal/resources/views/dashboards/admin/dues/` and `student-portal/resources/views/dashboards/student/dues/` | Deletes unreachable dues and payment screens. |
| `student-portal/resources/views/dashboards/student/index.blade.php` | Removes the student dues summary/action. |
| `student-portal/app/Http/Controllers/Admin/AdminCourseRegistrationController.php` and `student-portal/app/Http/Controllers/Student/StudentCourseRegistrationController.php` | Removes course-enrollment review and upload workflows. |
| `student-portal/app/Models/CourseRegistration.php`, `student-portal/app/Services/CourseRegistration/`, `student-portal/app/Http/Requests/Admin/*CourseRegistration*`, and `student-portal/app/Http/Requests/Student/StoreCourseRegistrationRequest.php` | Removes unused course-registration model, service, and request validation. |
| `student-portal/resources/views/dashboards/admin/course-registrations/`, `student-portal/resources/views/dashboards/student/course-registration/`, and `student-portal/resources/views/emails/student/course-registration-status-updated.blade.php` | Removes course-registration screens and status email. |
| `student-portal/tests/Feature/CourseRegistrationDisabledTest.php` | Ensures course-enrollment routes stay disabled while student account applications remain available. |
| `student-portal/resources/views/emails/pending-registration/application-approved.blade.php` | Removes dues/payment from the new-student feature list. |
| `student-portal/resources/views/legal/privacy.blade.php` | Removes dues management from the stated data-use purpose. |
| `student-portal/resources/views/developers.blade.php` | Removes payments from the portal description. |
| `student-portal/tests/Feature/DuesDisabledTest.php` | Ensures dues and payment routes stay unavailable. |
| `docs/ARCHITECTURE.md` | Marks collection as disabled and legacy tables as retained. |
| `CHANGELOG-DEVELOPER.md` | Records implementation and handoff steps. |

## Database changes

No migrations. The bootstrap command creates one admin user. The events endpoint
reads existing event records only. Existing course-registration and dues/payment
tables and records are not changed or deleted.

## Configuration changes

Set `PUBLIC_WEBSITE_URL` in the student portal to the public site's exact origin.
Set `VITE_EVENTS_API_URL` in the website build environment to the full Laravel
`/api/public/events` URL. Multiple allowed origins can be comma-separated. Local
student portal setup uses file-backed sessions/cache to avoid remote database
round-trips. Production deployments with multiple app instances should use a shared
low-latency session/cache store. No dues/payment configuration is used by active
portal routes.

## Verification performed

- `php artisan test --filter=CreateFirstAdminTest`: passed, 2 tests and 12 assertions.
- `php artisan test --filter=PublicEventsApiTest`: passed, 1 test and 15 assertions.
- `npm run build`: passed. Vite reports an existing CommonJS/ESM config warning.
- `php artisan route:list --path=api/public/events`: endpoint registered.
- `php artisan route:list --except-vendor` filtered for dues/payment routes: none registered.
- `php artisan test --filter=CourseRegistrationDisabledTest`: passed, 2 tests and 8 assertions.
- `php artisan route:list` filtered for course-registration routes: none registered.
- `php artisan view:cache`: passed after portal view removals.
- PHP syntax checks passed for registration and maintenance controllers.
- Local `SESSION_DRIVER=file` timing: admin login improved from ~3.4 s to ~0.37 s; a
	plain legal page improved from ~3.4 s to ~0.35 s. Warm event API improved from
	~7.9 s to ~0.3 s after cache fill (first cached request ~2.3 s).

The app-wide SQLite migration suite hits an existing MySQL-only `MODIFY` migration;
the focused tests create only the tables required by their respective features.

## Deployment steps

1. Set `PUBLIC_WEBSITE_URL` to the deployed public website origin in the Laravel environment.
2. Set `VITE_EVENTS_API_URL` to the deployed Laravel `/api/public/events` URL before building the public site.
3. Deploy Laravel and the built React website. No database migration is required.
4. If this is a new database, run `php artisan admin:create-first` from `student-portal`, then sign in at `/admin/login`.
5. Existing dues/payment routes are unavailable; no historical database records are removed.

## Rollback plan

Redeploy the previous Laravel and website versions. No schema rollback is required.
Any admin created by the bootstrap command remains in the database and should be
managed through the admin workflow.

## Known limitations and follow-up work

The existing admin profile form provisions additional accounts with a temporary
password; it does not send invitation email. The public events endpoint intentionally
exposes event details, banners, and links without authentication. The existing
app-wide SQLite migration suite remains blocked by a MySQL-specific migration. Legacy
payment controller/service source and database structures are retained but have no
registered routes or portal screens; existing records remain untouched.

