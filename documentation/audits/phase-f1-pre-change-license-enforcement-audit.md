# Phase F.1 pre-change license enforcement audit

This audit records the Phase F behavior observed before the no-lockout refactor.

## Enforcement inventory and responses

`license.valid` wrapped every installed public route (homepage, content, contact, sitemap and robots) and every authenticated administrator route. When `CMS_LICENSE_ENFORCE=true`, any missing, inactive, expired, revoked or invalid database activation returned HTTP 503 with a generic license message. HTML and JSON received the framework 503 response. Login, installer and `/up` were outside the middleware; CLI status returned failure when inactive. License activation existed only through CLI. With enforcement disabled, all routes passed regardless of status.

Consequently public content and authenticated content management, system information and logout could be unavailable. No backup/export or web license-management module existed. Installation middleware and demo seed selection were structurally separate from licensing, and license failure did not reopen the installer. An unavailable adapter threw before persistence; invalid offline results were safely hashed and stored. The default clean installation remained usable only because enforcement defaulted off; enabling it affected both new and legacy installed sites.

Offline verification distinguished only active, expired and generic invalid states. Invalid signature, wrong product, host mismatch, malformed token and missing/invalid public key all denied equally under enforcement. Marketplace unavailability raised an exception. Encrypted metadata used Laravel encrypted casts; an `APP_KEY` change could throw when metadata was read, with no recovery state. The credential itself was neither stored nor logged, although provider reasons were not centrally classified.

## Impact matrix

| Route/module | Previous behavior | Importance | Lockout risk | Phase F.1 behavior | Required check | Compatibility |
|---|---|---:|---:|---|---|---|
| Public CMS, sitemap, robots | Blanket 503 when enforced | Critical | Public outage | Always available after installation | Unlicensed public route tests | Names and URLs unchanged |
| Admin content and logout | Blanket 503 after authentication | Critical | Data/recovery lockout | Core administration remains available | Policy and route middleware tests | Existing access control retained |
| Login | Never license-blocked | Critical | Low | Remains independent | Unlicensed login test | URL unchanged |
| Installer | Never license-blocked | Critical | Reinstallation risk if coupled later | Installation state only | Installation regression suite | Existing locks retained |
| System information | Blanket 503 | High | Diagnostics lockout | Always allowed | Authenticated route/policy test | URL unchanged |
| Backup/export | No module; would have shared admin blanket | Critical | Potential data portability loss | Named policy actions always allowed | Policy tests | Foundation only; no fake module added |
| License remediation | CLI only | Critical | Recovery friction | Admin replace form and CLI remain available | Activation/recovery tests | CLI unchanged |
| Future updates | No narrow entitlement | Medium | Overbroad enforcement | `updates.download` alone requires validity | Middleware allow/deny tests | No updater added |
| Unavailable adapter | Exception | High | Configuration crash | Distinct safe state, no fallback | Provider test | Provider registry retained |
| Encrypted metadata | Decrypt exception on access | High | Repeated request crash | `recovery_required`, encrypted value preserved | Corrupt-ciphertext test | Existing schema/cast retained |
| Demo seed mode | Separate; no license bypass | Medium | Policy ambiguity | Explicitly remains separate | Combination tests | Seeder behavior unchanged |

## Safety conclusions

License, installation and demo data selection are independent. Licensing never deletes customer data, never rewrites installation state, and does not reveal credentials. Offline providers have no recurring network verification, so marketplace outage grace is not applied to them. Future remote providers must declare remote-validation capability before grace is relevant.
