# SKUUL Error Tracking Report

## Baseline

- Audit date: 2026-11-21.
- Laravel `12.73.0`, PHP `8.5.13`, MySQL `8.4`, Redis `7.4`, MailHog.
- 31 migrations: all `Ran`.
- 171 routes: no duplicate method/URI pairs.
- PHP syntax: 156 files checked, `0` failures.
- PHPUnit baseline: `29 tests, 140 assertions`, passing.
- Composer: manifest valid, `0` advisories.
- NPM: `4` advisories (`1 high`, `3 moderate`).
- Laravel log: 91 historical error entries, 21 grouped root causes, mostly fixed in current source.

The reported count of approximately 597 problems is not reproducible as 597 distinct runtime defects. It is dominated by root-cause cascades, historical log entries, generated Blade cache files under `storage/framework/views`, minified built assets under `public/build`, and IDE/static-analysis noise. The table below tracks verified root causes.

## Error Tracking

| ID | File | Error | Root Cause | Severity | Status |
|---|---|---|---|---|---|
| ERR-001 | `app/Http/Controllers/AttendanceController.php:26,32`; `GradeController.php:16,23,29,63,70`; `app/Http/Controllers/Admin/*.php` | 26–27 referenced Blade views missing | Scaffolded controllers reference views that were never created | Critical | Fixed |
| ERR-002 | `routes/web.php:73`; `app/Http/Controllers/Admin/AcademicYearController.php:36,41,46,60` | Implicit route binding fails for academic years | Resource uses `{academic_year}` while controller signatures use `$year` | High | Fixed |
| ERR-003 | `resources/views/grades/create.blade.php:7,60`; `resources/views/layouts/app.blade.php:76`; `GradeController.php:58,92` | Undefined `grades.*` route names | Routes are registered as `teacher.grades.*` | High | Fixed |
| ERR-004 | `resources/views/attendance/teacher-index.blade.php:7,51,64-84` | Attendance form cannot submit correct records | Form lacks method/action/CSRF, wrong fetch URL, generated rows lack `name` attributes, `innerHTML` XSS risk | Critical | Fixed |
| ERR-005 | `resources/js/app.js:7-16`; `resources/js/bootstrap.js:23-28` | Mobile sidebar toggles twice | Duplicate `DOMContentLoaded` handlers mutate the same class | High | Open |
| ERR-006 | `resources/views/layouts/app.blade.php:86` | Extra `@endif`; unclosed `@auth` | Copy/paste Blade directive error in unused legacy layout | Low | Open |
| ERR-007 | `resources/views/**/*.blade.php` | Dark mode inconsistent across many views | Dark classes applied only to newer views | Medium | Open |
| ERR-008 | `resources/views/auth/login.blade.php:19,31`; `auth/register.blade.php:21,25,32,36,41` | Invalid Tailwind class `lead-6` | Typo for `leading-6` | Low | Open |
| ERR-009 | `storage/framework/views/*.php` | IDE reports undefined `$component`, `$__env`, `$exception` | Generated Blade cache scanned by IDE | Low | Non-runtime / hygiene |
| ERR-010 | `public/build/**`; `.gitignore` | IDE scans minified assets and manifest | Generated build output not ignored | Low | Hygiene |
| ERR-011 | `tailwind.config.js:2,31-32`; `postcss.config.js:1`; `package.json` | ESM/CJS mismatch | `export default` and `require()` mixed without `"type": "module"` | Medium | Open |
| ERR-012 | `package.json:1`; `package-lock.json:2` | Lockfile root name drift | Lock root is `html`, package has no `name` | Low | Open |
| ERR-013 | `package-lock.json` | 4 npm advisories, including 1 high | Vite 5 toolchain vulnerability chain | Security | Open |
| ERR-014 | `.gitignore`; `composer.lock` | Lockfile hygiene warning | Reported as ignored, but currently present in working tree; verify tracking | Medium | Verify |
| ERR-015 | `composer.json`; `README.md:191` | `php artisan test` is not defined | Laravel 12 build lacks Collision test command | Medium | Open |
| ERR-016 | `config/app.php:63-64`; `bootstrap/cache/services.php` | Duplicate Sanctum/Spatie providers | Manual providers plus package auto-discovery plus stale cache | Medium | Fixed by cache clear / verify source |
| ERR-017 | `phpunit.xml:27`; `config/cache.php:5` | Test cache setting ignored | `CACHE_DRIVER` is legacy; config reads `CACHE_STORE` | Medium | Open |
| ERR-018 | `.env.example:35`; `config/broadcasting.php:5`; `config/session.php:13,19` | Env keys ignored | Legacy `BROADCAST_DRIVER`; hardcoded session path/same-site | Low | Open |
| ERR-019 | `test_db.php:2`; `test_db2.php:3` | Debug scripts expose hardcoded DB credentials in web-reachable root | Temporary debug files left behind | Security | Open |
| ERR-020 | `docker-compose.yml:46` | MySQL healthcheck passes credential in process args | Password visible via `docker inspect`/process list | Security | Open |
| ERR-021 | `docker-compose.yml:24-26` | Migrations can run before DB is ready | `depends_on` uses `service_started`, not `service_healthy` | High | Open |
| ERR-022 | `docker/start-container:19` | Migration failures swallowed | `migrate --force || true` hides real failures | High | Open |
| ERR-023 | `docker-compose.yml:11-17` | App env can fall back to defaults | DB user/password, Redis, and mail env not explicitly injected | Medium | Open |
| ERR-024 | `Dockerfile`; running container | Possible stale image versus current Dockerfile | Image was built before later Dockerfile edits | Medium | Verify |
| ERR-025 | `app/Http/Controllers/Auth/RegisterController.php:21-43` | Public registration can choose low-privilege role type | Caller-supplied `role_type` trusted | Critical / Security | Fixed |
| ERR-026 | `LoginController.php:32,63`; `Api/AuthController.php:20`; `Api/LoginController` path | Inactive users can log in | `Auth::attempt()` ignores `is_active` | High / Security | Fixed |
| ERR-027 | `app/Http/Controllers/Api/CatalogController.php:45-87` | Any authenticated user can read all catalog data | `index()`/`show()` lack admin scoping | High / Security | Fixed |
| ERR-028 | `AttendanceController.php:29-32`; `GradeController.php:26-29` | Parent can view another student's attendance/grades | Parent-child relationship not verified | High / Security | Fixed |
| ERR-029 | `Api/AcademicController.php:127-130` | Student with no enrollment sees all published exams | Empty subject list fails open | Medium / Security | Fixed |
| ERR-030 | `routes/api.php:14-22` | Login endpoints have no explicit throttle | No `throttle` middleware on login routes | Medium / Security | Fixed |
| ERR-031 | `Teacher/DashboardController.php:19` | `Unknown column grades.teacher_id` | Teacher dashboard queries nonexistent column | High | Fixed |
| ERR-032 | `Admin/TimetableController.php:99-102` | `Undefined property $student->classroom_id` | Students relate to classrooms via enrollments | High | Fixed |
| ERR-033 | `app/Models/Student.php:53-57` | Undefined `currentAcademicYearId` | Model uses nonexistent ambient auth attribute | Medium | Open |
| ERR-034 | `Admin/UserController.php:42,76` | Inactive users reactivate on update | Missing hidden checkbox `0` and `?? true` fallback | Medium | Open |
| ERR-035 | `Admin/FeeController.php:52-57` | Fee detail renders edit form | `show()` returns `admin.fees.edit` | Medium | Open |
| ERR-036 | `Api/FinanceController.php:74-85` | API allows inconsistent fee payment state | No `amount_paid <= amount` or status reconciliation | High | Fixed |
| ERR-037 | `Api/CatalogController.php:130-152` | Generic CRUD accepts invalid foreign keys | Reflective rules use `nullable|integer`, no `exists` validation | High | Fixed |
| ERR-038 | `database/migrations/0001_01_01_000013_create_jobs_table.php:11-21` | Non-standard queue table schema | Missing auto-increment `id` | High | Open |
| ERR-039 | `database/migrations/2024_01_01_000000_create_permission_tables.php:11-53` | Missing Spatie unique/index constraints | Slim custom migration omits standard indexes | Medium | Open |
| ERR-040 | `Admin/StudentController.php:80-103` | Duplicate enrollment causes 500 | No pre-check or graceful DB exception handling | Medium | Open |
| ERR-041 | `2026_11_20_000003_create_fee_records_table.php:20`; `Api/FinanceController.php:48-63` | Fee status is free-form | No DB/check or validation rule for allowed statuses | Low | Open |
| ERR-042 | `student|teacher|parent/dashboard.blade.php:6`; multiple admin views | Partial null-safe chains such as `$x?->user->name` | `?->` applied only to first link | Medium | Open |
| ERR-043 | `admin/library/issues.blade.php:35-44` | Student and teacher borrower IDs overlap in one select | Options not filtered by `borrower_type` | High | Fixed |
| ERR-044 | `admin/fees/records.blade.php:118` | Amount input prefilled with remaining balance | Balance confused with cumulative amount paid | High | Fixed |
| ERR-045 | `admin/fees/create.blade.php:55`; `admin/fees/edit.blade.php:56` | Checkbox can be unchecked incorrectly | Missing hidden `0` input | Low | Open |
| ERR-046 | `app/Http/Kernel.php:32-44` | Legacy Spatie namespace and Laravel 10-style aliases | Dead file left from Laravel 10 | Low | Open |
| ERR-047 | `vendor/.../Exceptions/Handler.php:1070` | Console exceptions masked by `renderThrowable()` cascade | Framework handler masks real console bootstrap errors | Low | Non-runtime cascade |
| ERR-048 | `storage/logs/laravel.log` | 21 historical root causes, 91 error entries | All verified fixed in current source; log retained | Info | Fixed / historic |
| ERR-049 | `tests/**` | Coverage gaps for routes, API, auth security, notifications, migrations | Tests cover 29 happy paths; many failure paths untested | High | Open |
| ERR-050 | `docs/API.md`; `README.md:178-179`; `routes/api.php` | Dual auth documentation and implementations | `/api/login` legacy alias plus `/api/auth/*` new endpoints | Low | Open |
| ERR-051 | `bootstrap/cache/*.php` | Duplicate service providers and stale manifests | Generated caches survived dependency changes | Medium | Fixed by `optimize:clear` |
| ERR-052 | `phpunit.xml:9` | Empty `tests/Unit` suite | Declared but absent | Low | Open |

## Severity Summary

| Severity | Count |
|---|---:|
| Critical | 4 |
| High | 21 |
| Medium | 16 |
| Low | 9 |
| Security | 7 |
| Info / non-runtime / hygiene | 7 |

## Verification Constraints

- No `migrate:fresh`, `db:wipe`, or record deletion.
- No `.env` overwrite and no secret values included in this report.
- No destructive Git operations.
- Fixes must be verified with actual commands: `php -l`, `route:list`, `migrate:status`, `npm run build`, PHPUnit, HTTP smoke tests, Docker status, and MailHog checks.
