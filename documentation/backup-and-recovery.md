# Backup and Recovery

Before installation changes or upgrades, back up the database and buyer-controlled upload storage. Store encrypted backups outside the web root and test restoration. Recovery must preserve existing data: do not run `migrate:fresh`. For installer recovery, follow `installation-recovery.md`; for version updates, restore a known-good database/uploads pair before retrying.
