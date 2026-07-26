# CLI installation

Run `php artisan cms:requirements`, then `php artisan cms:install` for hidden interactive password prompts. Automation may provide all documented options plus `--force-non-interactive-confirmation`; environment-variable/secret-manager injection is safer than password options, which can appear in history and process lists. Output never prints passwords or the application key.

Use `--seed=clean` (default) or explicitly choose `--seed=demo`. `--no-storage-link` skips the optional link. Updating an existing environment file requires `--approve-env-update` and creates a timestamped backup. The CLI and web UI use the identical locked pipeline.
