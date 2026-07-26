# Buildora CMS

**Construction, Engineering and Corporate Website Management System**

Buildora CMS is a Laravel-based commercial CMS foundation that keeps protected product identity separate from buyer-configurable company identity. Company names, logos, contact information, SEO fields, social links, and theme settings remain editable in the administration panel; product defaults are centralized in `config/cms.php`.

## Development

```bash
composer install
npm ci
php artisan migrate
npm run build
php artisan test --compact
```

Copy `.env.example` to `.env`, generate an application key, and supply environment-specific database values. Database seeding defaults to a clean, idempotent installation and never creates an administrator unless all three explicit `CMS_ADMIN_*` values are supplied. The preferred operator flow is the secure `php artisan cms:create-admin` command.

See `documentation/seed-modes.md` for clean and fictional demo seed commands, production acknowledgements, collision behavior, and administrator automation.

See `docs/deployment-guide.md`, `docs/admin-user-manual.md`, and `docs/developer-notes.md`. Commercial distribution remains blocked pending an owner-approved final `LICENSE` and resolution of the release findings documented in `documentation/commercial-readiness-audit.md`.

## Secure installation

Buildora CMS supports a guarded web installer at `/install` and the shared CLI pipeline `php artisan cms:install`. Before setup, see [the installation guide](documentation/installation-guide.md), [shared-hosting guide](documentation/shared-hosting-installation.md), and [recovery guide](documentation/installation-recovery.md). Clean data is the default; demo data requires an explicit choice. The installer uses layered marker/database locking and never treats deleting a browser session as permission to reinstall.

## Non-destructive licensing

Licensing is independent of installation and demo seed state. Public pages, core administration and data recovery remain available without activation; only future update downloads and explicitly defined premium activation actions require a valid entitlement by default. The manual/offline signed provider is the only operational provider; marketplace adapters are extension points and never silently fall back. See the [enforcement policy](documentation/license-enforcement-policy.md), [offline activation](documentation/offline-license-activation.md), and [recovery guide](documentation/license-recovery.md). No updater is included, and licensing is not presented as an anti-piracy guarantee. Redistribution remains governed by the final commercial `LICENSE`.
