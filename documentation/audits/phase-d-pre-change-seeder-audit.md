# Phase D Pre-change Seeder Audit

Recorded: 2026-07-26

## Baseline commands

```text
$ git status --short --branch
## work
?? documentation/audits/
$ php artisan migrate:fresh --env=testing --force


In Connection.php line 838:
                                                                               
  SQLSTATE[HY000] [2002] Connection refused (Connection: mysql, Host: 127.0.0  
  .1, Port: 3306, Database: buildora_cms, SQL: select exists (select 1 from i  
  nformation_schema.tables where table_schema = schema() and table_name = 'mi  
  grations' and table_type in ('BASE TABLE', 'SYSTEM VERSIONED')) as `exists`  
  )                                                                            
                                                                               

In Connector.php line 67:
                                             
  SQLSTATE[HY000] [2002] Connection refused  
                                             

$ php artisan db:seed --env=testing --force

   INFO  Seeding database.  


In Connection.php line 838:
                                                                               
  SQLSTATE[HY000] [2002] Connection refused (Connection: mysql, Host: 127.0.0  
  .1, Port: 3306, Database: buildora_cms, SQL: select * from `site_settings`   
  where (`id` = 1) limit 1)                                                    
                                                                               

In Connector.php line 67:
                                             
  SQLSTATE[HY000] [2002] Connection refused  
                                             

$ php artisan test --compact

  !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

  Tests:    43 warnings (248 assertions)
  Duration: 9.75s

```

Exit codes: migrate=1, seed=1, tests=0.

The configured testing environment attempted MySQL at `127.0.0.1`; no local MySQL
server was available. The existing test suite uses its established in-memory SQLite
configuration and passed with 43 PHPUnit deprecation warnings and 248 assertions.

## Existing behavior and risks

Before implementation there was one seeder (`DatabaseSeeder`) and no factories.
It combined administrator, settings, navigation, homepage, and sample-content work.
An administrator was conditionally created with `firstOrCreate`, but the fallback
email was `admin@example.com`. All other records used `updateOrCreate`; rerunning the
seeder therefore overwrote matching customer settings and content. There was no
`truncate`, `delete`, or `forceDelete` call in seed code.

The seeder wrote a fixed site-settings row (`id = 1`) containing a fictional premium
engineering company, Bangladesh contact details, a Dhaka timezone, and developer
placeholders. It similarly rewrote theme row 1. It seeded three social profiles;
eight header links; eight footer links; two sliders; three feature items; five stable
homepage sections; four categories; four projects; four services; and three gallery
records referencing unverified `placeholders/gallery/*.jpg` files. Organizations and
partner messages existed in the schema but were not seeded. No contact submissions
were seeded.

Homepage sections are the only system-essential content: public composition looks
them up by the unique `section_key`; missing optional content is already supported.
The required keys are `about`, `partner_messages`, `project_highlights`, `gallery_cta`,
and `services`. Header/footer links are optional because templates have route-based
fallback navigation. Settings and theme rows are useful installation defaults, but
must only be inserted when their tables are empty. Services, categories, projects,
organizations, sliders, features, gallery, partner messages, and social links are
demo content.

Project and service route binding depends on unique slugs. Project foreign keys use
`project_category_id` with `nullOnDelete`; the seeder previously resolved category
models rather than relying on numeric IDs. Active content uses `status` (or
`is_active`), homepage selections use `is_featured`, ordering uses `sort_order` (or
`display_order`), and partner-message publication additionally respects
`published_at`. Slugs were generated with `Str::slug`; seeded categories, projects,
and services therefore collided with and overwrote customer records sharing a slug.

Settings are read through `SettingsService` under targeted keys configured in
`config/cms.php`. The old seeder performed no invalidation, so warmed settings could
remain stale. Existing tests expect the homepage to show active/featured ordered
services and projects and the public list/detail routes to preserve slug binding.
The principal production-upgrade risks were wholesale settings/theme rewrites,
content rewrites by human title or slug, accidental demo insertion, and possible
creation of an administrator using a fallback identity.

## Impact matrix

| Table/model | Existing purpose | Class | Proposed seeder | Natural key | Dependencies | Overwrite risk before Phase D | Required coverage | Compatibility strategy |
|---|---|---|---|---|---|---|---|---|
| `site_settings` / `SiteSetting` | Company, contact, SEO, install metadata | Essential row; demo values optional | `DefaultSettingsSeeder`, `DemoSettingsSeeder` | singleton only when table empty | settings cache | Critical (`id=1` rewritten) | absent/existing/cache tests | insert only when empty; demo may fill the clean seed's still-neutral row only when demonstrably untouched |
| `theme_settings` / `ThemeSetting` | Visual defaults | Essential | `DefaultSettingsSeeder` | singleton only when table empty | settings cache | Critical (`id=1` rewritten) | absent/existing/cache tests | insert config defaults only when empty |
| `homepage_sections` / `HomepageSection` | Stable homepage slots | Essential | `HomepageSectionSeeder` | unique `section_key` | homepage composer | High | key/idempotency/edit tests | `firstOrCreate`; never update existing keys |
| `menu_items`, `footer_links` | Optional navigation | Neither required nor demo in Phase D | none | URL would be safest | named public routes | High by labels | clean route/empty tests | retain template fallbacks; seed nothing |
| `social_links` | Social navigation | Demo-optional | none | platform insufficient | frontend cache | High by platform | zero-row clean/demo tests | seed nothing; avoid impersonation |
| `sliders` / `Slider` | Homepage hero | Demo | `DemoSliderSeeder` | reserved demo heading | homepage | High by heading | demo/idempotency/edit tests | create-only reserved headings, no images |
| `feature_items` / `FeatureItem` | Homepage benefit cards | Demo | `DemoFeatureItemSeeder` | reserved demo title | homepage | High by title | demo/idempotency/edit tests | create-only exact demo titles, icon-only |
| `project_categories` / `ProjectCategory` | Project grouping | Demo | `DemoProjectCategorySeeder` | unique reserved slug | projects | High by slug | relationships/collision tests | create-only demo slugs; abort a dependent demo project if its slug belongs to conflicting data |
| `projects` / `Project` | Portfolio | Demo | `DemoProjectSeeder` | unique reserved slug | category FK | High by slug | links/idempotency/edit tests | create-only demo slugs; no image paths or fixed IDs |
| `services` / `Service` | Service catalogue | Demo | `DemoServiceSeeder` | unique reserved slug | homepage/routes | High by slug | scopes/idempotency/edit tests | create-only demo slugs; no image paths |
| `organizations` / `Organization` | Client/partner list | Demo | `DemoOrganizationSeeder` | exact reserved demo name (schema has no slug) | homepage | None previously | demo/idempotency tests | create-only names with no logos/real URLs |
| `partner_messages` / `PartnerMessage` | About leadership message | Demo | `DemoPartnerMessageSeeder` | fictional name + organization | about page | None previously | publication/idempotency tests | create-only compound match; no portrait/logo |
| `gallery_images` / `GalleryImage` | Gallery assets | Demo but intentionally empty | none | image path | storage | High; broken/unverified paths | zero assets test | seed no records and rely on empty state |
| `users` / `User` | Authentication/admin | Explicit operator action | `AdminUserSeeder`, `cms:create-admin` | unique email | auth | Medium fallback identity | validation/hash/no-reset tests | require all credentials; never update or promote |
| `contact_messages` | Buyer submissions | Never seeded | none | n/a | none | None | zero-row test | do not seed |

## Ownership decision

No migration or `is_demo` column is justified in this phase. Unique route slugs are
sufficient for services, categories, and projects. Other demo records use deliberately
reserved exact compound keys. This avoids changing public queries and leaves every
existing row non-demo. Collision behavior is create-only: a matching record is left
unchanged, and relationships use the existing matching category only when its expected
demo name also matches. Future reset support may add ownership metadata in a separate
additive phase; Phase D never deletes content.
