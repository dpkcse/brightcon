# Installation recovery

Run `php artisan cms:installation-status`. An unavailable database plus a marker, a marker without completed database state, or completed database state without a marker is **inconsistent** and fresh installation is denied.

A failed connection after `.env` creation can be retried with password re-entry. Laravel resumes only pending migrations. Idempotent essential/demo seeders can retry; administrator email collisions are never overwritten or promoted. No failure before the final marker reports success.

For a verified pre-installer website, first run `php artisan cms:adopt-existing-installation` for read-only preview. After backup and review, add `--confirm`; it updates only missing completion fields and creates the marker—no migration, seeding, password, user, or content changes. A database-complete/marker-write failure can be reconciled only after CLI review; never delete data or expose a public reset button.
