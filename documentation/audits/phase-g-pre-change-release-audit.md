# Phase G Pre-change Release Audit

Recorded 2026-07-26 UTC before Phase G release-tooling implementation. This is an internal engineering record and is not buyer-facing.

## Repository findings

- The working tree was clean on branch `work`; 8,932 paths were tracked and no relevant untracked files existed.
- `.gitignore` excludes environment files (except `.env.example`), runtime state, databases, archives, IDE files, `vendor/`, `node_modules/`, and `public/storage`, but it had no dedicated release-output rule.
- `config/commercial_release.php` provided audit requirements and exclusions, but no variants, builder, manifest, inventory, archive, checksum, approval-state, or acceptance-gate definitions.
- `commercial:audit` was inspection-only. It failed solely on absent `LICENSE`, warned about unverified favicon/upload trees, an unresolved vendor decision, missing offline verification key, and intentionally non-operational marketplace providers.
- Despite ignore rules, 8,574 `vendor/` files, 14 `public/build/` files, and 27 `storage/` paths were tracked. Vendor contains 76 production and 35 development packages. Source-package vendor exclusion therefore must be policy-driven rather than based on Git ignore state.
- `public/build/manifest.json`, compiled CSS/JS, and Font Awesome fonts are tracked. There are no source maps. Font Awesome, Bootstrap, Popper, and Axios metadata exists, but required notices must accompany redistributed builds.
- Uploaded media is reachable through `public/storage` and exists under `storage/app/public/uploads`; it includes images, logos, portraits, a PDF, and favicon. Provenance is unresolved and all such customer/runtime uploads must be excluded.
- `public/favicon.ico` is empty and unverified. Existing text/CSS fallbacks are the approved default; the favicon must be excluded.
- Runtime storage contains only tracked placeholder `.gitignore` files. No runtime log, cache, session, or compiled view was present in tracked placeholders.
- Composer scripts can create `.env` and SQLite during create-project flows; release building must not invoke those flows. npm provides `dev` and `build` only.
- `.env.example` uses production mode, disables debug and demo mode, selects file cache/session, and contains empty secret placeholders. Licensing defaults to unenforced with only offline verification operational.
- Existing installer, shared-hosting, recovery, licensing, security, and server-requirement documentation is available. Buyer/internal classification did not exist.
- Symlinks exist only under ignored `node_modules/.bin`; no tracked symlinks were found. No public source maps, nested archives, installer markers, activation data, private keys, CI secrets, or Git metadata are eligible for copying.
- The tracked sensitive-extension query found only Sail's development PostgreSQL test SQL inside tracked vendor. Explicit selection must prevent it entering source and must remove development dependencies from shared hosting.
- Dependency inventory (`composer licenses --format=json`) completed for 111 installed dependencies. Technical redistribution is possible from an isolated `composer install --no-dev`, but final owner/legal approval of dependency-included redistribution remains a commercial gate.
- Live MySQL/MariaDB and actual-final-ZIP installation acceptance reports are absent and must remain failed gates. The final owner-approved `LICENSE` is absent; `LICENSE-DRAFT.md` is non-operative.

## Commands recorded

`git status --short --branch`, `git ls-files`, `git ls-files vendor`, `git ls-files public/build`, `git ls-files storage`, the required sensitive-extension query, the required sensitive-term `git grep`, `composer licenses --format=json`, `composer validate --no-check-publish`, `npm ls --all`, `php artisan commercial:audit`, and `php artisan route:list --except-vendor` were run. Composer validation passed with the expected missing-license metadata warning; npm dependency resolution passed; 111 application routes were listed; commercial audit exited non-zero because final `LICENSE` is absent.

## Impact matrix

| Release area | Current behavior | Proposed behavior | Packaging impact | Security risk | License risk | Shared-hosting impact | Regression risk | Required validation | Gate |
|---|---|---|---|---|---|---|---|---|---|
| Selection | Git tree copied manually | Explicit variant allowlists and exclusions | Stable contents | Secret/runtime leakage | Low | Required files retained | Low | Selection tests | Pending |
| Source | No package | Source/tooling, no vendor; build included explicitly | Composer/Node required | Dependency omission | Notices | Build allows fallback | Low | Extract/build/test | Pending |
| Shared hosting | No package | Isolated production vendor plus public build | Larger archive | Dev dependency leakage | Redistribution approval | No first-boot build tools | Medium | Autoload/installer/assets | Blocked: owner decision |
| Documentation | Mixed buyer/internal docs | Buyer allowlist only | Standalone archive | Internal detail leakage | Final license absent | Clear upload/install help | Low | Link/content audit | Pending |
| Assets | Uploads and favicon unresolved | Exclude uploads/favicon; allow verified generated build | No customer media | Privacy/provenance | Font notices | Compiled assets retained | Medium | Provenance and reference scan | Pending |
| Integrity | None | JSON manifest/inventory and SHA-256 sidecar | Three metadata files | Tampering | Low | Buyer verification | Low | Hash/tamper tests | Pending |
| Approval | Audit only | Internal, RC, approved gates | Honest filenames/notices | False approval | Final terms absent | None | Low | State tests | Blocked: LICENSE/MySQL/ZIP |
| Scanner | Repository audit | Staging scanner with redacted findings | Blocks unsafe trees | High if incomplete | Branding/assets | Prevents unsafe upload | Low | Malicious fixtures | Pending |
| MySQL acceptance | Not run | Structured passed report required for approval | No automatic claim | Install failure | Low | Critical | None | Real server matrix | Blocked |
| Fresh ZIP | Not run | Structured passed report required for approval | Product archive tested | Missing/runtime data | Low | Critical | None | Separate extraction/install | Blocked |
