# Release Exclusion Policy

## Must exclude from every Gumroad candidate

- `.env`, `.git`, GitHub/CI secrets and internal Git metadata.
- IDE configuration, OS metadata and internal deployment/absolute paths.
- Runtime logs, compiled Blade views, sessions, caches, test caches and debug files.
- Database backups/dumps, local databases and temporary archives.
- Production uploads and all unverified demo uploads.
- Contact messages, user accounts and any exported production records.
- Real SMTP credentials, API keys, access/bearer tokens, SSH/private keys and certificates.
- Machine-specific configuration and build scratch output.

Exclusions must be applied to a separate staging directory. Never delete files from a live installation to prepare a package.

## Must include

- Application source, public source assets, migrations and seeders.
- `.env.example` containing safe placeholders only.
- `composer.json`, `composer.lock`, `package.json` and `package-lock.json`.
- Required compiled `public/build` assets under the current shared-hosting strategy.
- Documentation, third-party notices, the final product license and changelog.
- A server requirement/deployment guide.

`vendor/` is excluded from the normal source-only package. It may appear only in a separately approved dependency-included package after the unresolved legal and workflow decision is closed.
