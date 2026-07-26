# Phase E pre-change installation audit

Audited 2026-07-26 before implementation. The working tree was clean on branch `work`.

## Baseline findings

- Laravel 12 boots through `public/index.php` and `bootstrap/app.php`; the latter loads `routes/web.php`, then `routes/admin.php` under `web`, exposes `/up`, registers `FrontendViewServiceProvider`, and aliases only `admin` and `cms.maintenance`.
- `FrontendViewServiceProvider` composes only `frontend.*`. `SettingsService` returns defaults for expected missing-table/unavailable-database errors, but deliberately rethrows unexpected query failures. Installer views therefore must use a separate namespace.
- There is no installer middleware, route, state service, marker, complete-install command, or revisit lock. There are 96 application routes. Public content uses `cms.maintenance`; admin login is guest-only and admin CRUD uses `auth,admin`.
- The application has file framework maintenance. `SESSION_DRIVER`, `CACHE_STORE`, and `QUEUE_CONNECTION` default to database in `.env.example`; `config/session.php` also defaults to database. A missing cache/session migration can therefore break requests before installation. There is no project `config/cache.php` or `config/filesystems.php`; framework defaults currently fill that gap.
- `.env` is absent in this checkout. `.env.example` is readable and the repository root is writable by the current PHP user, so this process can create `.env`; hosting permissions remain deployment-specific. `APP_KEY` is consequently absent. Laravel encryption cannot be assumed until setup.
- The configured MySQL database is unavailable locally. `migrate:status` fails with connection refused. This confirms connection failures must not be inferred as a fresh install when durable/legacy signals exist.
- Runtime directories exist and are writable here (`storage`, its app/framework/log subtrees, and `bootstrap/cache`). `public/storage` is a real directory, not a symlink, so linking must be optional and must never overwrite it.
- Database fleet: users/password resets/sessions, database cache/locks, CMS foundation content/settings, partner messages, organizations/homepage additions, contact map settings, commercial settings including `installation_completed_at` and `installed_version`, and administrator flag compatibility. No new state migration is necessary.
- `DatabaseSeeder` validates `CMS_SEED_MODE`, always runs idempotent `EssentialSystemSeeder`, and explicitly adds fictional demo content only for `demo`. Seeders avoid truncation. `DefaultSettingsSeeder` creates missing settings only.
- `cms:create-admin` uses strong validation, a hidden interactive password, hashing, and an explicit promotion option. It contains reusable rules but no shared service yet. Installer behavior must prohibit promotion/collision.
- Existing shared-hosting documentation assumes manual `.env`, migrations, and storage linking. It supports uploaded `vendor/` and built assets, but does not yet describe a web installer, lock, partial recovery, or no-symlink fallback.
- Existing sites may lack both marker and completed fields. Compatibility must recognize core tables + settings + administrator (+ complete migrations/application data) as legacy-installed without writing during web requests.

## Failure and recovery baseline

| Scenario | Before Phase E | Required compatibility response |
|---|---|---|
| Database unavailable/credentials invalid | Artisan migration status throws; settings fallback can mask recognized connection failures | State diagnostics distinguish unavailable from fresh; durable installed signal denies reinstall |
| Partial migration | Laravel records completed migrations; no installer coordinator | Resume pending migrations only; never fresh/rollback automatically |
| Cache/session tables absent | database defaults can fail before controller | installer uses file cache/session-safe configuration |
| Storage unwritable | framework writes may fail; no preflight | blocking permission report before execution; no chmod/chown |
| Installer revisited | no routes exist | installed/legacy state rejects every installer URL and POST |
| Marker/database disagree | no marker exists | inconsistent state, no automatic install or data overwrite |

## Baseline commands

- `git status --short --branch`: clean, `## work`.
- `php artisan route:list --except-vendor`: passed; 96 routes.
- `php artisan migrate:status`: failed because local MySQL at `127.0.0.1:3306` refused the connection.
- `php artisan commercial:audit`: expected failure; sole blocker is missing final `LICENSE`, with existing non-blocking warnings.
- `php artisan test --compact`: passed, 54 tests / 315 assertions, with PHPUnit deprecation warnings.

## Impact matrix

| File/module | Current responsibility | Installation responsibility | Boot risk | DB risk | Session/cache risk | Security risk | Regression risk | Required test | Compatibility strategy |
|---|---|---|---|---|---|---|---|---|---|
| `bootstrap/app.php` | routing/providers/middleware | load isolated installer routes and aliases | High | Medium | Medium | High | High | route and middleware regression | preserve route names/order and `/up` |
| installation state service | none | layered marker/database/legacy classification | Medium | High | Low | High | High | full state matrix | never write on request; unavailable is not fresh |
| application/installer middleware | maintenance/admin only | redirect fresh HTML, JSON 409, lock installer | High | Medium | Medium | High | High | public/admin/install/JSON | exempt installer and health; legacy passes |
| installer routes/controller/views | none | guarded CSRF multi-step workflow | Low | Medium | High | High | Medium | step, CSRF, redaction | standalone views and file session |
| requirement/permission services | none | non-secret preflight | Low | None | Low | Medium | Low | pass/fail/creation | derive from Composer/runtime; never chmod |
| environment/key services | `.env.example`; Laravel config | atomic first-create/update, backup, valid key | High | High | High | Critical | Medium | injection, preservation, redaction | refuse silent overwrite; restrictive permissions |
| database tester | normal configured connection | temporary MySQL/MariaDB probe | Low | High | Low | Critical | Low | category/redaction | purge temporary connection; generic output |
| admin service/command | command-local creation | shared validation and collision-safe creation | Low | Medium | None | Critical | Medium | command + installer collision | preserve command behavior; prohibit installer promotion |
| installation manager | none | locked migration/seed/admin/completion pipeline | Medium | Critical | Medium | Critical | High | failure boundaries/idempotency | `migrate --force`, existing-data guard, marker last |
| seeders | clean/demo architecture | selected mode reuse | Low | High | Low | Medium | Medium | clean/demo/idempotency | call existing seeders; default clean |
| documentation/config | deployment defaults | safe shared-hosting defaults/recovery | Low | Low | Medium | Medium | Low | audit/docs checks | file cache/session, sync queue for installation |

Implementation may begin only after this audit; this file is the audit record required by Phase E.
