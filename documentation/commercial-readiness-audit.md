# Buildora CMS Commercial Readiness Audit

## Phase status

**Historical Phase A — Repository Hygiene and Release Safety Foundation**

Audit date: 2026-07-26. Approval status: **Phase A implemented; Phase C identity remediation recorded below; commercial distribution is not approved.** Later phases require explicit approval.

## Existing architecture and CMS modules

The product is a Laravel 12/PHP 8.2 application using Blade, Eloquent, MySQL-compatible migrations, Vite, Bootstrap and an admin area. Public modules cover home, about, services, projects, gallery, contact, sitemap and robots responses. Admin CRUD covers authentication, dashboard, contact messages, features, footer/menu links, gallery, homepage sections, organizations, partner messages, project categories/projects, services, sliders, social links and general/theme settings.

## Settings architecture

Settings currently use `SiteSetting` and `ThemeSetting` models, dedicated admin requests/controllers, frontend view sharing and the existing cache behavior. Phase A does not centralize, migrate, rename or otherwise change settings.

## Existing security controls

Observed controls include Laravel CSRF/session authentication, validation request classes, contact throttling, upload handling, safe CMS URL handling, trusted Google Maps host validation, escaped Blade output and production debug guidance. These controls were not redesigned in Phase A.

## Findings

### Commercial release blockers

- `vendor/` is tracked (8,574 files at audit time). Shared-hosting guidance permits uploading it when Composer is unavailable, but no approved dependency-included package or complete redistribution review exists.
- Tracked production-like uploads have unknown provenance and must not be placed in a commercial archive.
- Branding remains intentionally hard-coded pending a later, separately approved phase.
- Asset provenance and a final product license/changelog/release server-requirements deliverable require release-owner approval.

### Hard-coded branding inventory

Repository searches find `BrightCon`, `Bright Construction`, and `brightconeng.com` in package metadata, seed content, views, documentation and/or tests. Phase A records and warns about these references; it does not alter public text, branding, seed data or behavior.

### Sensitive-data findings

The required keyword search found credential-related configuration keys, validation fields, framework/vendor implementation text and test/default patterns. Findings were reviewed by path with values redacted; no tracked real `.env`, private key or local database was identified. A tracked runtime log was identified and removed from the index. A vendor SQL fixture is dependency source, not an application database dump. Release archives must still undergo the audit command and manual secret review.

### Generated-artifact findings

One Laravel runtime log, twelve compiled Blade views, and generated `bootstrap/cache/packages.php` and `bootstrap/cache/services.php` were tracked. They are reproducible and are removed from tracking while Laravel directory placeholders remain. `public/build` contains fourteen committed Vite artifacts and remains tracked because shared-hosting deployment documentation explicitly supports locally building then uploading it.

### Third-party dependency findings

Composer reported installed package licenses successfully and npm's dependency tree resolved successfully (optional Vite platform/tool integrations were absent as expected). Direct dependency details are in `THIRD-PARTY-LICENSES.md`; visual/upload provenance remains unverified as documented in the asset inventory.

## Implementation phases

1. **Phase A (this change):** hygiene policy, generated-file cleanup, documentation, dependency/asset inventory and non-destructive release auditing.
2. **Later phase, not implemented:** generic branding and settings work, only after approval.
3. **Later phase, not implemented:** seed/package/installer decisions, only after approval.
4. **Later phase, not implemented:** licensing/distribution integration, only after legal and product approval.

## Regression risks

Risks are limited to packaging omissions, scanner false positives/negatives and deployment assumptions. Runtime application behavior, routes, schema, migrations, seeds, settings and frontend output are unchanged. Committed Vite assets are retained to avoid shared-hosting regression; writable directory placeholders are retained.

## Approval status

Phase A is ready for review. **Gumroad release approval is withheld** until the vendor-package strategy, upload/asset provenance, branding phase and final legal/release checklist are resolved. Do not begin later phases without approval.

## Phase C identity remediation (current state)

Runtime and active metadata now use Buildora CMS product defaults while preserving buyer-configured company identity. The hard-coded branding inventory above is retained as historical audit evidence, not a description of active runtime branding. Existing database rows and uploaded company assets are not rewritten. The final owner-approved `LICENSE`, upload/design provenance, and packaging decisions remain release blockers.
