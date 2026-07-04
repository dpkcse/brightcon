# Phase 10 QA Checklist

## Audit summary
Routes, controllers, models, views, uploads, cache behavior, frontend pages, SEO tags, sitemap, robots, contact form, and admin protection were reviewed from source. Runtime browser checks were not claimed unless run in the target environment.

## Confirmed bugs found and fixed
- Unsafe CMS URL fields could accept `javascript:` or protocol-relative values in menu/footer/homepage/slider links. Validation now allows only safe relative paths, anchors, and `http(s)` URLs.
- Admin `GET /admin/logout` performed a state-changing logout without CSRF. Logout remains available through the POST route.
- Custom CSS stripping removed script tags but not closing/opening style tags, which could break out of the style block. Style tags are now stripped too.

## Security review checklist
- Admin routes are grouped behind `auth` middleware.
- Login routes use `guest` middleware.
- Logout is POST + CSRF.
- Contact POST is throttled and validates required fields.
- Honeypot field `website` is checked before storage.
- Uploads use Laravel image/file validation with MIME and size rules.
- Public uploads are stored on the `public` disk.
- Custom CSS strips script/style tags before output.
- Blade output uses escaped `{{ }}` for user content except intentional CSS output.
- Delete actions use forms with CSRF, DELETE/PATCH verbs, and confirm prompts.
- Production must use `APP_DEBUG=false`.

## Responsive QA checklist
Review at these widths: 320-575, 576-767, 768-991, 992-1199, and 1200+ px.
- Navigation hamburger opens/closes and menu items remain readable.
- Header logo/contact/profile button stack cleanly.
- Hero text remains readable over images.
- Feature cards stack without overflow.
- Project and service cards maintain image ratio and readable text.
- Gallery grid wraps cleanly.
- Equipment table/cards do not overflow small screens.
- Footer columns stack cleanly.
- Contact forms and footer contact areas fit mobile screens.
- Admin tables remain usable via responsive wrappers; admin forms stack correctly.

## Browser QA checklist
Do not mark complete until actually tested:
- Chrome desktop
- Firefox desktop
- Edge desktop
- Safari desktop if available
- Mobile Chrome
- Mobile Safari

## Admin panel QA checklist
- Login and logout.
- Dashboard cards and recent contact messages.
- General settings update.
- Logo upload.
- Favicon upload.
- Profile PDF upload.
- Theme settings update.
- Social links CRUD.
- Menu items CRUD.
- Footer links CRUD.
- Sliders CRUD.
- Feature highlights CRUD.
- Homepage section edit.
- Contact messages list/show/mark read/delete.
- Project categories CRUD.
- Project category delete guard when projects exist.
- Projects CRUD.
- Services CRUD.
- Gallery images CRUD.

## Frontend QA checklist
- Home page sections and fallbacks.
- Hero fallback.
- Feature fallback.
- About section.
- Featured projects.
- Services section.
- Gallery CTA.
- About page.
- Services list and detail.
- Inactive service returns 404.
- Projects list and category filter.
- Project detail.
- Inactive project returns 404.
- Competency page.
- Equipment list.
- Gallery filter.
- Contact page and contact form.
- Footer contact/details.
- Header menu.
- Profile download button.
- Social links.
- `/sitemap.xml`.
- `/robots.txt`.
- Custom 404 page and noindex behavior.

## Maintenance checklist
### Weekly
- Check contact messages.
- Check broken images/content.
- Backup database.
- Review storage usage.

### Monthly
- Update project/service content.
- Check sitemap.
- Check speed.
- Check Laravel logs.
- Check Laravel/security dependency updates.

### Before publishing content
- Optimize images.
- Add SEO title/description.
- Check mobile preview.
- Check links.
