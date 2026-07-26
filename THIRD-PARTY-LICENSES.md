# Third-Party Licenses and Redistribution Notes

Versions are lockfile/installed versions audited on 2026-07-26. This inventory is informational, not legal advice. Preserve upstream license and copyright notices; review all transitive packages with `composer licenses --format=json` and the npm lockfile before each release.

| Package | Installed version | Purpose | License | Source location | Attribution / redistribution note |
|---|---:|---|---|---|---|
| `laravel/framework` | 12.62.0 | Application framework | MIT | `vendor/laravel/framework` / Composer | Retain MIT copyright and permission notice; redistribution permitted under MIT. |
| `laravel/tinker` | 2.11.1 | Interactive console | MIT | `vendor/laravel/tinker` / Composer | Retain MIT notice; omit from a production package if not installed by the chosen Composer flags. |
| `bootstrap` | 5.3.8 | UI CSS/JavaScript | MIT | `node_modules/bootstrap` / npm | Retain MIT copyright/license notice in source distributions. |
| `@fortawesome/fontawesome-free` | 6.7.2 | Icons and compiled webfonts | Icons: CC BY 4.0; Fonts: SIL OFL 1.1; Code: MIT | `node_modules/@fortawesome/fontawesome-free`; `public/build/assets/fa-*` | Follow component-specific terms; provide attribution for CC BY icons and retain OFL/MIT notices. Do not separate compiled fonts from required notices. |
| `@popperjs/core` | 2.11.8 | Tooltip/dropdown positioning | MIT | `node_modules/@popperjs/core` / npm | Retain MIT notice. |
| `axios` | 1.18.1 | HTTP client | MIT | `node_modules/axios` / npm | Retain MIT notice. |
| `vite` | 6.4.3 | Frontend build tool | MIT | `node_modules/vite` / npm | Retain MIT notice if redistributed; normally a build-time dependency. |
| `laravel-vite-plugin` | 1.3.0 | Laravel/Vite integration | MIT | `node_modules/laravel-vite-plugin` / npm | Retain MIT notice if redistributed; normally a build-time dependency. |

## Asset notice

The compiled Font Awesome files are the only identified third-party font/icon binaries with a known package origin. Favicon, demo/upload images, client logos, project imagery, portraits, site logos/profile file, any unidentified custom fonts, and the frontend design/template origin remain **STATUS: UNVERIFIED — MUST NOT SHIP IN COMMERCIAL PACKAGE**. See `documentation/third-party-asset-inventory.md`.

## Complete dependency review

Direct-package listing does not replace the licenses of transitive dependencies. A dependency-included distribution must bundle the applicable license texts/notices from Composer and npm packages and receive legal approval. The source-only strategy should distribute lockfiles and let purchasers install dependencies from their upstream sources.
