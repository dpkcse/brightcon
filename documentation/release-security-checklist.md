# Commercial Release Security Checklist

## Before assembling a candidate

- [ ] Work from a reviewed, clean commit; record the commit hash.
- [ ] Choose and record source-only versus approved dependency-included packaging.
- [ ] Run `composer install --no-dev --optimize-autoloader` and `npm ci && npm run build` in a controlled build environment.
- [ ] Confirm `.env.example` contains placeholders only and no live credentials.
- [ ] Verify every image, icon, font and template has commercial redistribution evidence.

## Candidate inspection

- [ ] Apply `documentation/release-exclusion-policy.md` to a staging directory, never to production in place.
- [ ] Run `php artisan commercial:audit --path=/absolute/path/to/candidate` and require exit code 0.
- [ ] Review every WARNING; warnings are not automatic redistribution approval.
- [ ] Run malware/secret scanning appropriate to the release platform without printing secrets into CI logs.
- [ ] Confirm no `.env`, key, database, dump, log, session, cache, production upload, user or contact data is present.
- [ ] Confirm `THIRD-PARTY-LICENSES.md`, product license, changelog and server guide are present.

## Functional release gate

- [ ] Install into an empty environment and database.
- [ ] Verify public and admin routes, contact throttling, trusted map hosts, settings cache and asset loading.
- [ ] Set `APP_ENV=production` and `APP_DEBUG=false`.
- [ ] Confirm `storage/` and `bootstrap/cache/` are writable without broad unsafe permissions.
- [ ] Hash and archive only the staged, approved candidate.
