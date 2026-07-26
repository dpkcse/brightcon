# Third-Party Asset Inventory

No repository presence alone establishes commercial redistribution rights. The release owner must retain provenance and license evidence.

| Asset | Location | Finding |
|---|---|---|
| Font Awesome icons/fonts | `node_modules/@fortawesome/fontawesome-free`, `public/build/assets/fa-*` | Font Awesome Free 6.7.2; icons CC BY 4.0, fonts SIL OFL 1.1, code MIT. Preserve notices/attribution required by the applicable licenses. |
| Bootstrap CSS/JS | npm source and compiled `public/build/assets/*` | Bootstrap 5.3.8, MIT; retain copyright/license notice. |
| Popper and Axios | npm source / compiled JS | MIT; retain notices in redistributed source/bundles as applicable. |
| Favicon | `public/favicon.ico`, uploaded favicon | **STATUS: UNVERIFIED — MUST NOT SHIP IN COMMERCIAL PACKAGE** |
| Gallery/placeholder images | `public/storage/uploads/gallery`, `storage/app/public/uploads/gallery` | **STATUS: UNVERIFIED — MUST NOT SHIP IN COMMERCIAL PACKAGE** |
| Client/organization logos | uploaded logo paths and seeded paths | **STATUS: UNVERIFIED — MUST NOT SHIP IN COMMERCIAL PACKAGE** |
| Project/service/home/slider images | uploaded paths and seeded paths | **STATUS: UNVERIFIED — MUST NOT SHIP IN COMMERCIAL PACKAGE** |
| Management/partner portraits | uploaded portrait paths | **STATUS: UNVERIFIED — MUST NOT SHIP IN COMMERCIAL PACKAGE** |
| Site logos/profile PDF | uploaded site paths | **STATUS: UNVERIFIED — MUST NOT SHIP IN COMMERCIAL PACKAGE** |
| Custom fonts | no separately documented custom-font source found | **STATUS: UNVERIFIED — MUST NOT SHIP if later found without provenance** |
| Copied frontend template/design | no license/provenance record found | **STATUS: UNVERIFIED — design provenance must be cleared before commercial release** |

Seeded paths and tracked upload trees must be reviewed together: a path reference is not proof that the referenced binary is licensed. Phase A does not replace or alter any asset.

## Phase C safe fallback decision

No new binary logo, favicon, or placeholder was introduced. Empty logo/favicon states use text or omit the icon, while existing buyer-uploaded paths retain precedence. This text-based fallback is original project implementation and is approved for commercial distribution; it carries no third-party asset provenance claim. Repository favicon and upload binaries remain unverified and excluded from an approved commercial candidate until individually cleared.

# Phase D seeded-asset status

No clean or demo seeder references a repository or uploaded binary asset. Sliders,
services, projects, organizations, and the fictional partner message store null image
fields and use text/code fallbacks. Gallery records are intentionally not seeded.
The per-record decision is documented in `documentation/demo-asset-provenance.md`.
