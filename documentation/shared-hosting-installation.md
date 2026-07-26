# Shared-hosting installation

Point the domain document root to `public/`. The commercially approved shared-hosting package is currently disabled because dependency redistribution has not been approved. Do not claim a no-Composer installation. Use the source package and run Composer/Node through the host or a compatible build environment unless a later release explicitly includes approved `vendor/` and `public/build/`. Give PHP write access (not ownership changes or world-write permission) to `storage/`, its runtime subdirectories, `bootstrap/cache/`, and initially the project root if the web installer must create `.env`.

Create a MySQL/MariaDB database and user in cPanel/Plesk, visit `/install`, use an HTTPS `APP_URL`, and retain file cache/session, sync queue, and public filesystem defaults. Core pages need no queue worker or Supervisor and no cron. After setup, reduce `.env` write access.

If symlinks are allowed, run `php artisan storage:link`. Otherwise ask the host to map `public/storage` to `storage/app/public`, or copy uploaded public files with a controlled deployment process; never expose the repository root. A storage-link warning does not falsely fail an otherwise complete installation.
