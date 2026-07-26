# Update Policy

Buyers may access updates published for their purchased Buildora CMS edition for as long as the licensor continues to develop and publish them. There is no minimum update frequency, guaranteed future feature, perpetual development obligation, or fixed compatibility period. Access may require a valid license. Packages may be delivered through the original marketplace, a seller portal, or another authorized distribution channel.

Buildora CMS currently has no automatic updater and performs no remote package download or remote code execution. Updates are installed from versioned packages using their documented migration steps.

Before updating, back up the database and uploads, test restoration, and review the changelog and version-specific instructions. Apply database changes with `php artisan migrate --force`; never use `migrate:fresh`. Buyer modifications may require manual merging. Existing data remains buyer-controlled, and access to future packages may require valid commercial entitlement.

License metadata: Naxas Limited is the licensor; the owner-approved license is effective 27 July 2026. Legal and support contact: info.naxasltd@gmail.com. `LICENSE` controls.
