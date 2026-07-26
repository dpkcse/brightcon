# Installation security recommendations

Use HTTPS, production mode, debug disabled, a dedicated least-privilege database user, and a unique strong administrator password. Do not pass passwords on shared command lines. The installer never persists passwords in session, echoes them in review/validation responses, logs request payloads, or puts secrets in URLs. `.env` is atomically written with a backup for approved updates and restrictive permissions where supported; an existing valid APP_KEY is retained.

Keep the marker and database completion fields intact, deny public access to the project root and `.env`, review file ownership after installation, and back up before adoption/recovery. Never attempt reinstall against an existing database.
