# Developer Notes

## Architecture summary
This is a Laravel CMS website with public frontend controllers, protected admin controllers, Eloquent CMS models, Blade views, Vite assets, and filesystem uploads stored on the public disk.

## Important routes
- Frontend: `/`, `/about`, `/services`, `/services/{service:slug}`, `/projects`, `/projects/{project:slug}`, `/gallery`, `/contact`, `/competency`, `/equipment-list`.
- SEO/system: `/sitemap.xml`, `/robots.txt`.
- Contact POST: `POST /contact` with throttling.
- Admin: `/admin/login`, `/admin/dashboard`, settings, homepage, appearance, content CRUD routes under the authenticated `admin.` route group.

## Important models
- `SiteSetting`, `ThemeSetting`
- `MenuItem`, `SocialLink`, `FooterLink`
- `Slider`, `FeatureItem`, `HomepageSection`
- `ProjectCategory`, `Project`, `Service`, `GalleryImage`
- `ContactMessage`, `User`

## Cache keys
- `site_settings`, `theme_settings`
- `social_links_active_ordered`, `menu_items_active_ordered`, `footer_links_active_ordered`
- `homepage_sliders_active`, `feature_items_active_ordered`, `homepage_section_{key}`, `homepage_projects_featured`, `homepage_services_active`
- `project_categories_active_ordered`, `projects_active_ordered`, `projects_featured_ordered`
- `services_active_ordered`, `gallery_images_active_ordered`, `gallery_categories`
- `sitemap_services_active`, `sitemap_projects_active`
- `contact_messages_unread_count`

## Upload paths
- Logos: `uploads/settings/logos`
- Favicons: `uploads/settings/favicons`
- Profile PDFs: `uploads/settings/profile`
- Sliders: `uploads/sliders`
- Features: `uploads/features`
- Homepage sections: `uploads/homepage-sections` and `uploads/homepage-sections/backgrounds`
- Projects: `uploads/projects`
- Services: `uploads/services`
- Gallery: `uploads/gallery`

## Seeder behavior
`DatabaseSeeder` creates baseline CMS records and an initial admin user. Review seed credentials before production use and rotate the password after launch.

## Adding a future CMS module
Add a migration, model with fillable/casts/scopes, form requests, admin controller, admin views, protected resource routes, cache invalidation, and frontend display only if required. Keep route names consistent and document any new cache keys.

## Theme CSS variables
`resources/views/frontend/partials/theme-css.blade.php` maps `ThemeSetting` values into CSS custom properties used by frontend CSS. Custom CSS is appended after script/style tag stripping.

## SEO partial
`resources/views/frontend/partials/seo.blade.php` builds title, description, canonical, Open Graph, Twitter tags, and robots settings from section-provided data with site setting fallbacks.

## Sitemap
`SitemapController` emits XML with static pages plus active services and active projects only. Sitemap model lists are cached for one hour and cleared by relevant admin content changes.

## Contact form
`ContactController` validates contact submissions, applies a honeypot field named `website`, stores IP/user-agent metadata, marks messages unread, clears the unread-count cache, and redirects back with a success message.

## Known limitations
- Equipment is fallback/static for now.
- Competency is fallback/static for now.
- No project image gallery table yet.
- No advanced SEO table yet.
