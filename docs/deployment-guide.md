# Production Deployment Guide

## Pre-launch requirements
- PHP 8.2 or newer with common Laravel extensions: BCMath, Ctype, cURL, DOM/XML, Fileinfo, JSON, Mbstring, OpenSSL, PDO MySQL, Tokenizer, and Zip.
- MySQL or MariaDB database.
- Composer 2.x.
- Node.js/npm for building Vite assets.
- Domain document root must point to the Laravel `public/` directory.
- Production `.env` must use `APP_ENV=production` and `APP_DEBUG=false`.

## Shared hosting deployment
1. Build locally first:
   ```bash
   composer install --no-dev --optimize-autoloader
   npm install
   npm run build
   ```
2. Upload the project files to the hosting account. Do not expose the repository root as the public web root.
3. Point the domain public root/document root to `public/`.
4. Create or update `.env` with production values: `APP_NAME`, `APP_URL`, `APP_ENV=production`, `APP_DEBUG=false`, database credentials, mail values if used, and cache/session drivers.
5. If SSH is available, run:
   ```bash
   composer install --no-dev --optimize-autoloader
   php artisan key:generate
   php artisan migrate --force
   php artisan storage:link
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   php artisan optimize
   ```
6. If SSH is not available, upload the locally built `vendor/` folder and `public/build/` assets. Ask the host to run migrations or import a tested database dump.
7. Ensure `storage/` and `bootstrap/cache/` are writable by PHP.
8. If `php artisan storage:link` cannot run, create a symlink from `public/storage` to `storage/app/public`, or configure the host's file manager equivalent.
9. Enable HTTPS and set `APP_URL` to the HTTPS domain.
10. After launch, visit `/`, `/admin/login`, `/sitemap.xml`, and `/robots.txt`.

## VPS deployment
1. Install PHP 8.2+, Composer, Node.js/npm, MySQL/MariaDB, and Nginx or Apache.
2. Create a database and database user.
3. Clone or upload the project to the server.
4. Configure Nginx/Apache document root to the project's `public/` directory.
5. Create `.env` and set production values.
6. Run production commands:
   ```bash
   composer install --no-dev --optimize-autoloader
   npm install
   npm run build
   php artisan key:generate
   php artisan migrate --force
   php artisan db:seed
   php artisan storage:link
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   php artisan optimize
   ```
   Run `php artisan db:seed` only for initial setup or when seed data is expected.
7. Set permissions so the web server can write to `storage/` and `bootstrap/cache/`.
8. Configure HTTPS certificates.
9. Restart PHP-FPM and the web server.
10. No queue worker is currently required unless future features add queued jobs.

## Production command reference
```bash
composer install --no-dev --optimize-autoloader
npm install
npm run build
php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

## Rollback notes
- Keep a database backup before running migrations.
- Keep a copy of the previous release files and `public/build/` assets.
- If a release fails, restore files, restore the database backup if migrations changed data, then clear caches.
