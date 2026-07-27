# Naxas portal pre-change audit and impact matrix

The audit found a provider-neutral `LicenseProvider` contract and registry; one operational offline provider using the existing two-part detached RSA/SHA-256 token; a locally persisted installation UUID; lowercase/trailing-dot host normalization; encrypted provider metadata and HMAC fingerprints; admin-only CSRF-protected manual activation; non-destructive entitlement policy; installer completion with no licensing call; Laravel's HTTP client but no licensing HTTP configuration; no licensing retry implementation; no raw-token logging; and release scans which exclude secret key extensions. The repository snapshot contains no `owner-tools/` directory or `OfflineLicenseIssuerTest`; this is a packaging/source limitation, not a reason to recreate a private-key issuer.

| Area | Current behavior | Proposed behavior | External dependency | Security risk | Installer impact | Compatibility | Required tests |
|---|---|---|---|---|---|---|---|
| Provider/verifier | Offline RSA verifier is final authority | Retain it for portal responses | None for verification | Invalid signature | None | Additive | RSA regression |
| Request state | None | Encrypted transient proof, HMAC, masked token, lifecycle record | Naxas on explicit action | token disclosure/replay | None | Additive table | storage/lifecycle |
| Admin UI/routes | Paste token | Request/copy/check plus paste/upload | Only request/check | CSRF/auth/demo mutation | None | Existing routes retained | authorization/UI |
| HTTP | No licensing calls | fixed configured HTTPS origin, no redirects, bounded retry/timeouts, schema checks | Naxas | SSRF/raw payload leakage | None | Additive | trust/outage/schema |
| Policy | Core/public remain usable | Unchanged | None | destructive lockout | None | Unchanged | outage regression |
| Installer | Completes independently | Guidance only; no network call | None | secret exfiltration | non-blocking | Unchanged | installer regression |
| Release | Offline buyer client/docs; key files excluded | Include portal CMS foundation/docs; keep server/issuer/private keys out | None | key disclosure | None | Additive | package audit |
| Owner issuer | Not present in this checkout | Do not add/delete; documented fallback | None | private-key placement | None | External artifact | issuer compatibility when available |
