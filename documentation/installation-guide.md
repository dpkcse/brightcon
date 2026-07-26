# Buildora CMS installation guide

## Before installation

Use only server versions verified for the intended release. The application requires PHP 8.2 or later and the extensions listed in `server-requirements.md`; a real MySQL/MariaDB version is intentionally not advertised until external acceptance is complete. Point the site document root to `public/`. Copy `.env.example` to `.env` only for manual or CLI setup; release archives must not contain `.env`. For a source package, run `composer install --no-dev --optimize-autoloader` and `npm ci && npm run build`. A no-Composer shared-hosting package is unavailable unless vendor redistribution is approved.

Use `/install` for the standalone, database-independent installer. It checks PHP and writable paths, then collects MySQL/MariaDB, application, administrator, and clean/demo choices. The review deliberately omits all passwords and the application key and requires passwords again. Installation runs only by CSRF-protected POST.

Create the database and user first. Clean mode is the recommended default; demo mode explicitly adds fictional sample content. The shared pipeline writes/backups `.env`, creates a key only when needed, tests the connection, runs pending migrations (never `migrate:fresh`), invokes existing seeders, creates a strong administrator, updates completion fields, and writes `storage/app/.installed` last. Existing `.env` updates require explicit approval.

After completion, `/install` remains locked by both marker and database state. Sign in at `/admin/login`. Never delete state signals to attempt reinstall; take a backup and use the recovery guide.

## Post-installation, upgrades, and troubleshooting

Run `php artisan storage:link` where symlinks are supported. After confirming the environment, run `php artisan config:cache`, `php artisan route:cache`, and `php artisan view:cache`; clear and rebuild caches after configuration changes. Activate an offline license using `offline-license-activation.md`.

For a manual upgrade, back up files, database, uploads, `.env`, and license state; test the backup; extract the versioned package into staging; merge buyer changes; install/build dependencies as documented; and run `php artisan migrate --force`. Never use `migrate:fresh`. Run `php artisan cms:requirements` when troubleshooting. Correct connection or permission configuration without exposing credentials. For inconsistent markers, interrupted migrations, storage-link failures, or legacy adoption, use `installation-recovery.md` rather than deleting data or state signals. `php artisan optimize:clear` may clear stale caches before corrected production caches are rebuilt.

License metadata: Naxas Limited is the licensor; the owner-approved license is effective 27 July 2026. Legal and support contact: info.naxasltd@gmail.com. `LICENSE` controls.
