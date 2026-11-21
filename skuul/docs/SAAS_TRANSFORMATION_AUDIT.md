# SKUUL SaaS Multi-Tenant Transformation Audit

Date: 2026-11-21  
Repo: `skuul/` (Laravel app root)  
Baseline: Laravel 12.73.0, PHP 8.5 target, MySQL default, Spatie Permission 6, Sanctum 4, Breeze deps present, Tailwind/Vite frontend.

## Existing Implementation

- **Modules present**: admin users/students/teachers/classrooms/subjects/academic years/announcements, attendance, grades, exams/results, timetables, fee structures/records, library/books/issues, transport routes/vehicles/assignments, notifications, role dashboards, web+API auth, Sanctum API.
- **Auth/Roles**: `users.role_type` enum (`admin,teacher,student,parent`) + Spatie roles; permissions seeded but not enforced. Web routes grouped by `role:*`; API mostly `auth:sanctum` with controller checks.
- **Data model**: 22 business tables, 11 system tables. No `schools`/tenant tables, no `school_id`, no tenant scopes. Global unique constraints on `users.email`, `students.student_id`, `teachers.employee_id`, `parents.parent_id_code`, `subjects.code`, `books.isbn`, transport names/registrations, and several composites.
- **Frontend**: Blade component layouts (`x-app-layout`, `x-guest-layout`), role sidebar in `resources/views/components/layout.blade.php`, Tailwind dark mode, Vite inputs `resources/css/app.css`, `resources/js/app.js`. Root `/` redirects to login; no public marketing/registration site.
- **Storage**: `profile_photo` field exists but no upload/download implementation; local/public/s3 disks configured.
- **Tests**: PHPUnit feature tests for auth, modules, API, roles; no tenant/subscription/payment tests.

## Required Changes

### Multi-tenancy (shared DB, tenant isolation)
- Add `schools`, `school_users`, `school_settings`, `school_domains`, subscription/billing tables.
- Add nullable then backfilled `school_id` to tenant-owned business tables and pivot/spatie team-aware strategy where needed; convert global uniques to tenant-scoped uniques.
- Add central tenant resolution (authenticated membership + validated host/subdomain), `BelongsToSchool` trait/global scope, tenant-aware route middleware, and model/policy/query guards.
- Prevent trusting client `school_id`; scope admin/API/dashboard/notification/impersonation/file/export queries by active school.

### Platform Super Admin
- Separate `platform_admin`/super-admin role outside school tenant roles; dedicated `/platform` routes, middleware, dashboard, school management, subscription/revenue/audit views.
- Platform audit log for sensitive actions.

### School Registration & Onboarding
- Public marketing site and registration flow with validation, email verification, approval workflow, school/admin creation, default settings, academic config, trial/subscription assignment, onboarding progress.

### Subscriptions & Billing
- Plans, plan features, subscriptions, invoices, payment transactions, history, trials, configurable limits/features, status middleware with billing-access exception, grace/suspension config.
- M-Pesa Daraja service: STK push, callback, verification, idempotent activation/renewal; sandbox-ready when credentials absent; extensible gateway interface.

### School Customization
- School branding/settings: logo, name, colors, contact, address, academic config, feature toggles; tenant-aware file storage paths.

## Implementation Strategy

1. Create core tenant/subscription schema and Eloquent models.
2. Add tenant resolution middleware + global scope trait; register in `bootstrap/app.php`.
3. Add platform admin guardrails, routes, controllers, views.
4. Add public registration/onboarding and subscription/payment services.
5. Retrofit existing controllers/queries/APIs/jobs/storage to tenant-aware patterns.
6. Add/adjust tests and run verification commands.
