# Clean and demo seed modes

Buildora CMS has two explicit, additive seed modes. Neither mode truncates or deletes
records. The default is **clean**. Demo mode must be deliberately requested.

All organizations, projects and business details in demo mode are fictional and are
provided only for product demonstration.

## Linux and macOS

```bash
# Clean installation (also the default when CMS_SEED_MODE is absent)
CMS_SEED_MODE=clean php artisan db:seed

# Fictional demonstration installation
CMS_SEED_MODE=demo php artisan db:seed

# Essential records only
php artisan db:seed --class=EssentialSystemSeeder

# Demo records only (essential rows should normally be seeded first)
php artisan db:seed --class="Database\\Seeders\\Demo\\DemoContentSeeder"

# Secure interactive administrator creation
php artisan cms:create-admin
```

## Windows PowerShell

```powershell
$env:CMS_SEED_MODE="demo"
php artisan db:seed
Remove-Item Env:CMS_SEED_MODE

php artisan cms:create-admin
```

Deployment automation may pass `--name`, `--email`, and `--password` to
`cms:create-admin`. Command-line passwords can be exposed to local process inspection
or shell history, so a protected secret-injection mechanism is required. Interactive
use prompts without echo and asks for confirmation. Passwords require at least 12
characters, mixed case, a number, and a symbol.

Alternatively, clean seeding creates an administrator only when
`CMS_ADMIN_NAME`, `CMS_ADMIN_EMAIL`, and `CMS_ADMIN_PASSWORD` are all present and
valid. A partial set fails. Existing emails are never updated, reset, or promoted.

## Production and existing-data guards

Production demo seeding requires the independent acknowledgement
`CMS_ALLOW_DEMO_SEED_IN_PRODUCTION=true`. If non-demo content already exists, demo
seeding also stops until the operator reviews the database and sets
`CMS_ALLOW_DEMO_SEED_WITH_EXISTING_DATA=true`. With acknowledgement, existing rows
remain unchanged and a warning is emitted. These controls are additive; neither
permits deletion.

Clean settings and theme records are inserted only when their table is empty. Required
homepage sections use unique `section_key` values and create-only semantics. Demo
services, project categories, and projects use `buildora-demo-*` slugs. Other records
use exact fictional headings/names. A collision is treated as pre-existing data and
is never overwritten. Rerunning a seed therefore preserves administrator edits.

Demo mode uses text, icons, and built-in no-image fallbacks. It intentionally creates
no gallery or social-link rows and references no binary asset.
