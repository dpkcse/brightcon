# Provider-neutral licensing architecture

## Security and core boundary

Licensing is disabled by default so source installs and upgrades cannot be locked by
an incomplete marketplace integration. A commercial distribution enables
`CMS_LICENSE_ENFORCE=true` only after configuring and testing an operational adapter.
The `license.valid` middleware protects public CMS routes and authenticated admin
routes. Installer, login, health, recovery, and CLI activation tooling remain
reachable so a license failure cannot prevent remediation.

Core code uses `LicenseProvider`, `ProviderCapabilities`, `ActivationRequest`, and
`LicenseDecision`. Marketplace payloads, endpoints, terminology, and rules belong in
provider adapters and their tests. The database stores only neutral provider/status
fields, keyed hashes of credentials and hosts, and encrypted adapter metadata. Raw
credentials are never persisted or accepted as command-line arguments.

Demo data never activates, grants, extends, or bypasses a license. The clean/demo
seed choice and production demo acknowledgements remain content safeguards only.
Turning enforcement off is a deliberate distribution configuration decision, not a
runtime demo-mode feature.

## Provider status

| Provider | Status | Notes |
| --- | --- | --- |
| Manual/offline | Operational | Locally verifies an RSA/SHA-256-signed token against a configured public key. |
| Gumroad | Contract placeholder | No operational adapter is claimed. |
| Lemon Squeezy | Contract placeholder | No operational adapter is claimed. |
| Paddle | Contract placeholder | No operational adapter is claimed. |
| Envato / CodeCanyon | Contract placeholder | No operational adapter is claimed. |
| WooCommerce | Contract placeholder | No operational adapter is claimed. |
| Shopify / other commerce | Contract placeholder | No operational adapter is claimed. |
| Custom API | Contract placeholder | No operational adapter is claimed. |

Placeholder providers fail closed in `ProviderRegistry`. To add one, implement the
provider contract, map its response into a neutral decision, declare only its tested
capabilities, add provider-specific contract tests based on the current official API,
and only then set its configuration entry to operational.

## Offline token and activation

Keep the RSA private signing key outside every distributed copy. Configure the
base64-encoded PEM public key in `CMS_LICENSE_OFFLINE_PUBLIC_KEY`. A token is
`base64url(payload).base64url(detached_signature)`. The JSON payload requires
`product`; it may contain `license_id`, an ISO-8601 `expires_at`, `hosts`, and
`entitlements`. Host matching is exact after lowercase/trailing-dot normalization.

Write the token to a permission-restricted file outside the public root, then run:

```bash
php artisan cms:license:activate --provider=offline --license-file=/secure/license.token --host=example.com
php artisan cms:license:status
```

Delete the input file when operational policy permits. Activation errors deliberately
avoid printing the credential. Rotating the application key requires reactivation
because encrypted metadata and keyed hashes depend on that key.
