# Repository Hygiene Policy

## Source-control rules

Secrets, runtime state, local databases, logs, compiled server caches, dependency installs, IDE/OS metadata, coverage and temporary archives are ignored. Laravel's `storage` and `bootstrap/cache` directories remain through `.gitignore` placeholders. Source templates, configuration, migrations, seeders, documentation and `.env.example` remain tracked.

Generated logs, compiled Blade views and Composer-generated bootstrap cache manifests are reproducible and must not be committed. Cleanup must preserve required directories and must not sweep production uploads.

## Vendor decision

`/vendor/` is now ignored for normal source-control practice, but the already tracked vendor tree is **temporarily retained**. The deployment guide explicitly supports shared-hosting users without Composer uploading a locally built vendor tree. Evidence is insufficient to remove that workflow or confirm all legal/attribution obligations for a dependency-included commercial archive. This is an unresolved commercial-release blocker.

The future default should be a source-only package reconstructed from `composer.lock`. A separate dependency-included package may be offered only after legal/license review, reproducible build validation and approval. Supporting both packages is a product decision, not approved in Phase A.

## Public build decision

`public/build` remains tracked. The current shared-hosting workflow directs users to build locally and upload compiled assets where Node is unavailable. Removing it could break that confirmed deployment path. Builds must be reproducible with `npm ci && npm run build`, reviewed for stale/source-map output, and refreshed deliberately.

## Uploads

Tracked files under `public/storage/uploads` and `storage/app/public/uploads` were not deleted in this phase because uncontrolled removal could discard user/demo content and affect existing presentation. Their provenance is unverified; release staging must exclude them unless individually cleared and intentionally repackaged.

## Review cadence

Run the commercial audit before tags and release archives, review ignore rules when tooling changes, regenerate dependency inventories after lockfile changes, and never treat an audit PASS as a substitute for manual legal/security review.
