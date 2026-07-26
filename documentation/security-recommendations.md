# Installation security recommendations

Use HTTPS, production mode, debug disabled, a dedicated least-privilege database user, and a unique strong administrator password. Do not pass passwords on shared command lines. The installer never persists passwords in session, echoes them in review/validation responses, logs request payloads, or puts secrets in URLs. `.env` is atomically written with a backup for approved updates and restrictive permissions where supported; an existing valid APP_KEY is retained.

Keep the marker and database completion fields intact, deny public access to the project root and `.env`, review file ownership after installation, and back up before adoption/recovery. Never attempt reinstall against an existing database.
# License recovery safety

Keep `APP_KEY` stable and backed up securely. An application-key change makes encrypted license metadata require re-entry but must not be “fixed” by deleting customer data or reopening installation. Keep offline signing private keys outside the application; only the public verification key belongs in deployment configuration. License tokens and provider payloads must not be logged or exposed in public notices.
