# Buildora CMS installation guide

Use `/install` for the standalone, database-independent installer. It checks PHP and writable paths, then collects MySQL/MariaDB, application, administrator, and clean/demo choices. The review deliberately omits all passwords and the application key and requires passwords again. Installation runs only by CSRF-protected POST.

Create the database and user first. Clean mode is the recommended default; demo mode explicitly adds fictional sample content. The shared pipeline writes/backups `.env`, creates a key only when needed, tests the connection, runs pending migrations (never `migrate:fresh`), invokes existing seeders, creates a strong administrator, updates completion fields, and writes `storage/app/.installed` last. Existing `.env` updates require explicit approval.

After completion, `/install` remains locked by both marker and database state. Sign in at `/admin/login`. Never delete state signals to attempt reinstall; take a backup and use the recovery guide.
