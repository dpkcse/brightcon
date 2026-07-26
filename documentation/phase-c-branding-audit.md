# Phase C Branding and Identity Audit

Audit recorded on 2026-07-26 before Phase C implementation. The working tree was clean on branch `work`. The required branding scan found project-specific identity in configuration, package metadata, settings creation fallbacks, admin and legacy public views, tests, and historical documentation. The commercial audit reported two blockers (`LICENSE` and `CHANGELOG.md`), warning-level project branding, production-domain heuristics, and unverified repository/customer assets. The route inventory contained 96 routes and no product-branded route names or URLs.

## Focused inspection findings

- Product/package identity: `composer.json`, `config/app.php`, `.env.example`, the package lock root name, and admin views used the former project name.
- Customer identity: `site_settings.company_name` is already buyer-configurable. Existing rows, uploads, contact details, maps, social links, copyright, and colors must remain untouched.
- Environment identity: `APP_NAME`, `APP_URL`, and database values are deployment concerns and must not become the only product/company identity source.
- Runtime fallbacks: `config/cms.php`, `SettingsService`, header/footer/SEO, and settings-row creation require neutral company fallbacks and separate product fallbacks.
- Legacy views: `frontend/pages/services.blade.php`, `projects.blade.php`, and `gallery.blade.php` retain old titles, while routes resolve to their nested `index.blade.php` counterparts. They are not deleted because view resolution may be used externally; fallback titles should be updated.
- Assets: `public/favicon.ico` and tracked upload samples have unverified provenance. Dynamic customer uploads take precedence already. Phase C will use text fallback and will not modify/delete uploaded files or claim binary provenance.
- Seeders: the active seeder contains a predictable administrator password and overwrites row `id=1` through `updateOrCreate`. Phase C must remove predictable credential creation without changing existing users; full clean/demo separation remains Phase D.
- Compiled assets: generated Vite files contain framework/source-map URL strings caught by the broad production-domain heuristic, but no former brand in source comments or maps. Rebuilding is required after source changes.
- Namespace/URL compatibility: no PHP namespace, route name, slug, repository remote, or public URL requires renaming.

## Impact matrix

| File/module | Existing identity value | Classification | Proposed replacement | Runtime impact | Database impact | URL impact | Test requirement | Backward compatibility |
|---|---|---|---|---|---|---|---|---|
| `config/cms.php` / settings service | Former company fallback and flat version | Product + customer fallback | Central product structure; `Your Company` company fallback; generic tagline | Canonical fallback behavior | None; nulls remain null | None | Product/company precedence and null fallback | Existing non-null settings win |
| `config/app.php`, `config/database.php`, `.env.example` | Former app/database defaults | Environment | Buildora-safe placeholders | Fresh-install defaults only | No current `.env` or DB touched | Example `APP_URL` remains localhost | Static metadata assertions | Environment overrides remain authoritative |
| Composer/npm metadata | Former package/root name | Product/package | `buildora/construction-cms` and `buildora-cms` | Dependency identity only | None | No remote/repository rename | Composer validation/static assertions | PHP namespaces unchanged |
| General settings controller | Former company name on row creation | Customer | Neutral configured default | New empty installations only | Creates only when absent; no mass update | None | Existing row preservation | Existing first row remains unchanged |
| Admin login/layout | Hard-coded former admin title | Product + customer | Dynamic company plus protected product/version | Visible admin branding | Read-only | Routes/forms unchanged | Login, layout, powered-by, auth regression | Existing auth/session behavior retained |
| Frontend header/footer/copyright/SEO | `APP_NAME` used for company fallback | Customer + product final fallback | Company-first helper values, product only final fallback | Titles, metadata, text fallback | Read-only | Canonical logic unchanged | Homepage/footer/SEO/maintenance/error routes | Existing configured company/assets win |
| Legacy top-level page views | Former title suffix | Customer | Dynamic company-first title | Only if directly resolved externally | None | No route rename | Route/view regression | Update rather than delete |
| System information | Flat product version only | Product | Central name, description, edition, version | Read-only diagnostics | Read-only | None | Admin-only/no-secret assertions | Existing access control retained |
| Default/upload assets | Unverified favicon and uploads | Product/customer assets | Text fallback; mark binaries pending | Safe empty state | No file/database deletion | Existing storage paths unchanged | Upload precedence/no required binary | Existing uploaded logo/favicon preserved |
| Active seeder | Predictable local admin credential | Security/customer data | Require explicit environment password before creating admin | Safer opt-in seeding | Existing users not updated | None | Seeder security assertion | Content seed boundary otherwise retained |
| Documentation | Former name as current target and history | Product/history | Buildora current headings; preserve clearly scoped history | Release/readme accuracy | None | Historical paths retained | Audit exclusion-path test | Prior findings remain truthful |
| Commercial audit | Branding findings warning-level globally | Release policy | Blocking in active files; exact scoped historical/test fixture exclusions | Release gate hardening | None | None | Runtime/domain fail; exclusions; license blocker | Historical evidence remains allowed only by path |
| Changelog/license | Missing | Release/legal | Unreleased changelog and clearly non-operative license draft | Changelog blocker clears; license remains blocker | None | None | Required file checks | No invented legal grant |

## Commands recorded

The pre-change record was produced with `git status --short --branch`, `git grep -n -i -E 'brightcon|bright[[:space:]_-]*construction|brightconeng\.com'`, `php artisan commercial:audit`, `php artisan route:list --except-vendor`, targeted `find`/`rg` inventories, and direct inspection of configuration, models, services, controllers, views, seeders, documentation, tests, public assets, and the Vite manifest.
