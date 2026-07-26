# Phase B pre-change settings and authorization audit

Recorded on 2026-07-26 before Phase B implementation.

## Findings

1. `site_settings` was created in the CMS foundation migration as a singleton-style typed row. A later additive migration added contact-map fields. `SiteSetting` allowed the original and map columns, cast map visibility/coordinates/zoom, and retained trusted Google Maps helpers. `GeneralSettingsController` used `firstOrCreate`, three uploads, and one legacy cache eviction. `GeneralSettingsRequest` authorized every caller, accepted broad locale/timezone strings, and retained strong Google-host/HTTPS map validation.
2. `theme_settings` was created beside it with colors, typography dimensions, and raw custom CSS. `ThemeSetting` was fillable but had no casts. Its controller used `firstOrCreate` and evicted only `theme_settings`; its request accepted hex colors but unconstrained dimension strings and unrestricted CSS.
3. Frontend settings were loaded by `FrontendViewServiceProvider` for every `frontend.*` view. It directly cached the first site/theme row and active social/menu/footer collections forever. Blades accessed model properties directly. SEO and theme CSS supplied their own inline fallbacks. No helper or controller provided a centralized setting abstraction. Tests manually forgot or flushed legacy keys.
4. Legacy keys were `site_settings`, `theme_settings`, `social_links_active_ordered`, `menu_items_active_ordered`, and `footer_links_active_ordered`. Other feature caches are independent.
5. General/theme controllers forgot their corresponding legacy key. Social, menu, and footer CRUD controllers forgot their own collection key on writes. There was no shared frontend-context eviction and no global production flush; some tests used `Cache::flush()` for isolation.
6. Logo, favicon, and company profile uploads used `FileUploadService::replace`: Laravel-generated stored names on the public disk, then deletion of the old path after storage. Validation allowed raster logo/favicon and PDF profile. The service did not constrain old-path deletion to an owned directory. Theme had no uploaded assets.
7. Existing route names/URLs were `admin.settings.general.edit/update` at `/admin/settings/general` and `admin.settings.theme.edit/update` at `/admin/settings/theme`. They were inside the authenticated admin group and are compatibility-critical.
8. Authentication was the only authorization. Every authenticated user could reach settings and every CRUD route; Form Requests returned `true` from `authorize()`.
9. Raw CSS was rendered inside a style element after regex removal of literal script/style tags. It remained vulnerable to imports, URLs, browser-specific execution primitives, and style breakouts.
10. Map input accepted iframe markup only to extract its `src`, then validated HTTPS and trusted Google Maps hosts. The model re-validates before iframe rendering. This defense must remain.
11. Social/menu/footer public collections were forever-cached and their own controllers evicted the corresponding cache after create/update/delete. No combined context key existed.
12. Missing settings tables caused the frontend composer to throw a query exception. Database-backed cache could fail before those queries. Missing social/menu/footer tables likewise caused cascading failures.
13. Missing site/theme rows returned `null` publicly, while admin edit created a default row. Blade fallbacks prevented many null-property failures, but defaults were duplicated.
14. Compatibility risks included existing singleton rows, legacy cache entries, arbitrary legacy dimension/custom-CSS values, owner lockout after adding authorization, filesystem paths outside intended upload roots, visual drift from changed defaults, and deployment without freshly built assets.
15. Admin navigation contained General and Theme links, with single flat forms. General combined company/contact/map/files; Theme combined appearance and unrestricted CSS. Existing URLs and form actions must remain.

## Impact matrix

| File/module | Current responsibility | Proposed change | Database impact | Cache impact | Authorization impact | Regression risk | Required test | Compatibility strategy |
|---|---|---|---|---|---|---|---|---|
| Settings migrations/models | Two typed singleton tables | Add nullable/safe columns and casts | Additive only | None directly | Add `users.is_admin` | Existing rows/casts | Migration/model tests | Preserve columns and values; promote only users existing at migration time |
| Defaults/config | Scattered Blade/controller fallbacks | Central CMS defaults/version/cache keys | None | Defines canonical and legacy keys | None | Visual drift | Defaults/render tests | Match current BrightCon output |
| Settings service | None | Request-local typed facade over both models | Read existing rows | Canonical keys plus legacy eviction | None | Missing DB masking | Typed/query/missing-table tests | Catch only recognized installation failures |
| Frontend composer | Direct queries for five tables | Service-backed installation-safe context | Read only | Reuse canonical/legacy collection keys | None | Public route failures | Public regression/missing-table tests | Preserve all view variable names |
| General/theme controllers | Singleton update/upload | Service invalidation and expanded safe fields/assets | Update existing row | Targeted eviction | Admin-only | Stale output/upload loss | Update/cache/upload tests | Keep routes and first-row behavior |
| Form Requests | Broad general/theme validation | Admin authorization and constrained validation | None | None | Gate-backed | Rejecting legacy writes | Validation/security tests | Preserve stored legacy values until explicitly resaved |
| Custom CSS partial | Weak tag stripping/raw render | Disabled-by-default policy and validator | New enable flag | Theme/context eviction | Admin-only editing | Existing CSS disappearance | Safe/unsafe/render tests | Preserve value; warn and suppress unsafe CSS |
| Upload service | Store then delete any old public path | Owned-directory replacement checks | Paths only | Site/context eviction | Admin-only setting upload | Orphans/path deletion | Replacement/path-boundary tests | Keep existing public disk and directories |
| Admin routes/middleware | Authenticated users have all access | Minimal `admin` middleware/gate | `is_admin` | None | 403 non-admin | Owner lockout | guest/non-admin/admin tests | Existing users promoted once in additive migration |
| Login controller | Unlimited attempts | Email+IP limiter, clear on success | None | Rate-limiter keys | None | Login/session regression | Throttle/success/session tests | Same route, generic error, five/minute |
| SEO partial | Local metadata fallbacks | Validated defaults, analytics/token/JSON-LD | New site fields | Site/context | Settings admin-only | Override precedence/XSS | SEO tests | Preserve page yields and current defaults |
| Maintenance middleware/view | None | CMS status public guard | New safe default `active` | Site cache | Admin routes excluded | Accidental outage | active/maintenance/admin tests | Existing and missing rows are active |
| System information | None | Read-only safe admin page | Read only | Read settings service | Admin-only | Secret disclosure | access/content tests | No environment values or private paths |
| Social/menu/footer controllers | Own cache eviction | Also evict frontend context | None | Targeted old/new keys | Existing admin middleware | Stale context | CRUD/cache tests | Retain legacy keys and routes |

## Implementation constraints established by the audit

- No third settings table, destructive migration, branding conversion, seed rewrite, installer, licensing, mail delivery, or public redesign.
- Existing unsafe CSS remains stored but is not rendered until it passes policy and is explicitly enabled.
- Installation fallback is limited to recognized connectivity, missing-table, and missing-cache-table conditions; normal application exceptions must still surface.
- Existing settings route names, public composer variable names, Google Maps trust rules, and public-disk upload locations remain compatible.

## Phase B completion report

- **Schema and compatibility:** Two additive settings migrations introduce the requested site/theme fields and `users.is_admin`. Existing users are deliberately promoted at migration time; accounts created afterward default to non-admin. No existing column or row is renamed, deleted, or overwritten. Settings defaults preserve the current visual identity, website-active state, map behavior, and contact switches.
- **Settings design:** A scoped `SettingsRepositoryInterface`/`SettingsService` provides site/theme models and typed string, boolean, integer, decimal, URL, and color access. It memoizes per request, retains legacy cache keys for public compatibility, defines canonical `cms.*` aliases, and performs targeted invalidation.
- **Installation safety:** Recognized missing-table, missing-cache-table, unavailable-database, and absent-row states receive unsaved model defaults. Unrelated query exceptions are rethrown. Social/menu/footer queries first check table availability and preserve their old view variables.
- **Authorization and login:** All existing authenticated admin routes now require the minimal admin middleware. Form Requests independently verify administrator status. Login permits five failed attempts per normalized email/IP per minute, uses a generic failure, clears the limiter on success, and retains session regeneration/invalidation.
- **Validation and CSS:** General and theme requests constrain locale, timezone, date format, semantic versions, colors, CSS dimensions, URLs, contact text, analytics identifiers, robots, maintenance status, and files. Custom CSS defaults off, is editable only by admins, is length-limited, rejects imports/URLs/execution primitives/data/HTML, and renders only when enabled and safe. Existing unsafe text is preserved with an admin warning.
- **Uploads:** Dark/light logos and Open Graph/Twitter images use generated public-disk filenames and raster-only validation. Replacement deletes an old file only after storing its replacement and only when the old path belongs to the destination directory; original logo/favicon/profile behavior remains.
- **SEO, maintenance, and product version:** Escaped metadata keeps page overrides ahead of defaults. Valid analytics IDs generate fixed script structures, verification is a token, JSON-LD is JSON encoded, and separate OG/Twitter assets are supported. CMS maintenance affects only public content routes and escapes its message; admin login/routes stay available. Installed-version settings fall back to configured product version.
- **System information:** The admin-only read-only page exposes framework/runtime driver names, safe status booleans, version and audit summary; it does not read or display credentials, environment-file content, keys, tokens, or database connection details.
- **Verification:** Automated coverage includes typed/cached/default/missing-table service behavior, authorization, validation, CSS, cache visibility, login limiting, maintenance escaping, owned upload replacement, and system-information secrecy. Existing route/contact/map/homepage/gallery/partner regression tests remain in the full suite.
- **Known limitations/blockers:** This is a minimal admin/non-admin boundary, not granular RBAC. Existing unsafe CSS requires manual review. CMS maintenance is intentionally separate from Laravel native maintenance. The installer, mail delivery, license verification, updater, and all Phase C work remain unimplemented. The Phase A commercial audit continues to report the pre-existing missing `LICENSE` and `CHANGELOG.md` release blockers; Phase B did not manufacture those legal/release documents.
