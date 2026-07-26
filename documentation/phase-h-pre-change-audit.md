# Phase H pre-change audit and impact matrix

Audit date: 2026-07-26. This document was recorded before Phase H implementation.

## Baseline inspected

- `routes/web.php` exposes 16 non-admin/non-installer framework routes (including health and storage routes); the buyer-facing CMS has 14 GET/POST routes. Existing named routes include `/competency`, `/equipment-list`, `/contact`, services, projects, gallery, sitemap, and robots.
- `routes/admin.php` places all content management behind `cms.installed`, `auth`, and `admin`. The access middleware verifies `User::isAdmin()`.
- The foundation migration owns flat `menu_items`, separate flat `footer_links`, and storage-only `contact_messages`. Existing rows use `label`, `url`, `target`, `sort_order`, and boolean `status`.
- Competency and equipment are hard-coded Blade content. Neither has a model, migration, controller, or admin UI.
- Contact submissions have validation, a prohibited honeypot, per-route throttling, database storage, unread/read controls, search, and deletion. They do not use configurable form switches/messages, notify by mail, record delivery, support workflow states, notes, or replies.
- Contact recipient/subject/message/form switches already exist in `site_settings`; Laravel mail configuration remains framework/environment based. No Mailable exists.
- `PartnerMessage` already provides name, designation, organization, two images, short/full/highlight text, LinkedIn URL, featured/active flags, publication date, ordering, admin CRUD, and public About rendering.
- `Organization` is the client/logo module. `FeatureItem` is a short homepage feature-card module, not attributable customer testimony.
- Homepage content is driven by `homepage_sections` for established sections plus dedicated featured queries. The schema does not yet provide record limits.
- SEO uses escaped Blade sections and site defaults. Sitemap contains static pages and active service/project detail URLs only.
- `FileUploadService` writes generated framework filenames to the public disk and deletes an old file only inside the supplied owned directory. Request validators currently enforce raster MIME/extensions for image-owning modules.
- Frontend menus are cached under `menu_items_active_ordered` and `footer_links_active_ordered`; navigation has a route-based fallback and responsive Bootstrap collapse. Footer links remain a separate legacy source.
- Admin navigation already groups homepage, content, appearance, settings, and contact modules.
- Demo is currently an explicit seed mode, not a runtime read-only mode. Demo seeding is guarded, text-only, idempotent, and refuses undisclosed customer-content mixing. Phase H must not conflate seed mode with runtime protection.
- Release building selects source roots broadly, selects buyer documentation explicitly, excludes runtime/database/log/upload data, and retains manual approval gates for LICENSE, MySQL/MariaDB, ZIP installation, vendor redistribution, and asset provenance.
- Existing tests cover settings, identity, seed architecture, installer foundations, licensing, partner messages, gallery, homepage, release packaging, and commercial audit. No Phase H module tests exist.

## Duplicate-module decisions

1. **Leadership/team:** PartnerMessage already satisfies the requested public leadership-message data and publication behavior. It will remain the sole leadership/management-message module. A competing Team or LeadershipMessage table is not justified in Phase H.
2. **Clients:** Organization remains the client/logo module and will not be duplicated.
3. **Testimonials:** FeatureItem cannot safely represent attributable testimony (no person/company/publication fields). Testimonials are optional rather than required for commercial completeness, and there is no approved attributable content or homepage design. Phase H therefore documents but intentionally defers a testimonial module instead of adding an unused table. Clean/demo modes contain no endorsements.
4. **Static settings:** Settings can provide page introductions, but cannot provide ordered, independently publishable equipment, competency, custom-page, or testimonial records. Dedicated additive schemas are justified.
5. **Footer links:** Existing `footer_links` must remain valid. MenuItem will be extended for professional header/footer hierarchy while legacy footer rows continue as a compatible fallback; it will not be destructively replaced.

## Impact matrix

| Module | Current implementation | Proposed implementation | Database impact | Route impact | Frontend impact | Admin impact | SEO impact | Demo-mode impact | Regression risk | Required tests |
|---|---|---|---|---|---|---|---|---|---|---|
| Custom pages | Missing | Published page, admin preview/CRUD, centralized reserved slugs, restricted HTML | New additive `pages` table | Add `/pages/{page:slug}` and admin resource/preview | Page template and optional featured image | Search/filter/pagination CRUD; dependency-aware archive/delete | Per-page metadata/canonical; sitemap published only | Destructive actions/uploads use existing runtime policy when available; demo seed remains separate | Medium: route collisions and unsafe HTML | Auth, CRUD, reserved/unique slug, visibility, sanitization, SEO, sitemap, menu dependency |
| Rich content | Missing | Conservative DOM allowlist; no executable/template content | Stored sanitized HTML only | None | Safe formatted content | Sanitize on write and preview sanitized result | Escaped metadata | No special mutation | High if URL/attribute handling is wrong | Dangerous tags, attributes, schemes, external rel/target |
| Menus | Flat header plus separate flat footer | Extend MenuItem with location, two levels, typed destinations, custom pages, legacy URL fallback | Nullable/defaulted columns and self/page FKs without destructive updates | Existing routes retained | Accessible dropdown hierarchy and existing fallback; legacy footer fallback | Parent/destination controls and dependency checks | No direct metadata | Destructive structure changes guarded where runtime demo policy exists | High: legacy cache/render behavior | Legacy rows, location, hierarchy/cycles, URL safety, page links, cache, fallback/rendering |
| Equipment | Static Blade array | Managed published ordered list; static fallback only while no records | New `equipment` table | `/equipment-list` unchanged; no detail route | Responsive cards/table, images optional, empty/fallback state | CRUD/search/filter/pagination | List metadata only; no new sitemap URL | Text-only fictional demo records; no unverified media | Medium | CRUD, publication/order, route, empty/image replacement |
| Competencies | Static Blade cards | Managed published ordered list; static fallback only while no records; no meaningless percentage | New `competencies` table | `/competency` unchanged; no detail route | Managed cards with optional icon/image | CRUD/search/filter/pagination | List metadata only | Text-only fictional demo records | Medium | CRUD, publication/order, route, empty/image replacement |
| Contact notification | Storage only | Store first, synchronous notification, safe Reply-To, sanitized failure code | Add delivery fields | `/contact` unchanged | Configured success/failure-safe feedback | Delivery badge/detail | None | Suppress external delivery if runtime demo mode is enabled | High: mail failure/data loss/privacy | recipient/from/reply-to, injection, failure persistence/status, demo suppression |
| Contact workflow | Boolean read/unread and delete | unread/read/replied/archived, internal note, admin reply | Add workflow/reply fields | Add admin status/reply routes | None | Detail actions and safe reply form | None | Suppress external reply and destructive delete in runtime demo | High: visitor privacy and mail errors | admin-only, transitions, recipient, timestamp, failure/no leakage |
| Leadership/team | PartnerMessage complete | Reuse unchanged; document decision (image alt may be added only if needed) | None initially | None | Existing About section retained | Existing CRUD retained | Existing page metadata | Existing demo record explicitly fictional | Low | active/publication filtering and no duplicate model/table |
| Testimonials | Not represented; FeatureItem is not equivalent | Intentionally deferred pending an approved public design and attributable content; no fabricated endorsements | None | None | None | None | None | Clean/demo remain empty | Low | Assert no testimonial demo data or duplicate misuse |
| Homepage integration | Existing keyed sections; fixed limits in code | Add optional competency/equipment/testimonial section keys and nullable record limit | Add nullable `record_limit` | No route changes | Sections render only when configured/active | Existing section editor extended | No duplicate URLs | Seeds do not force runtime demo | Medium | established sections unchanged, visibility/limit/featured behavior |
| Media | Safe owned-directory replacement service | Reuse service with raster-only validation and module-owned directories | Path/alt columns above | None | Optional images/no broken placeholders | Upload/remove controls | Alt text | Demo seeds have no images | Medium | MIME/size, optional, replacement and owned deletion |
| Packaging/docs | Broad source roots; explicit buyer docs | Add Phase H guides to buyer docs; verify exclusion rules | None | None | None | Buyer guidance | None | Exclude uploads/contact runtime data/logs | Low | dry-run inventory, exclusions, gates remain blocking |

## Schema safety conclusion

All requested core work is safe only with additive tables and nullable/defaulted columns. Existing menu, footer, contact, route, cache, installer, licensing, and customer-content records must remain untouched. Foreign keys will use restrictive/nulling behavior rather than cascades that remove buyer content. No implementation will clear any commercial gate.
