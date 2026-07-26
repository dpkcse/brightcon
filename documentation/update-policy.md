# Update Policy

Buildora CMS has no automatic updater and performs no remote package download or remote code execution. Updates are delivered as versioned packages to entitled buyers under the approved commercial terms.

Before updating, back up the database and uploads, test restoration, and review the changelog and version-specific instructions. Apply database changes with `php artisan migrate --force`; never use `migrate:fresh`. Buyer modifications may require manual merging. Existing data remains buyer-controlled, and access to future packages may require valid commercial entitlement.
