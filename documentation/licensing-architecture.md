# Provider-neutral licensing architecture

## Security and core boundary

Licensing uses a non-destructive named-action policy. Public routes and the core
authenticated administrator group do not carry blanket license middleware. Public
content, login, installation, health, ordinary content management, backup/export,
system recovery and license remediation remain reachable in every license state.
Only future update downloads and explicitly named premium activation actions use
`license.entitlement`; there is no updater in this release.

Core code uses `LicenseProvider`, `ProviderCapabilities`, `ActivationRequest`, and
`LicenseDecision`. Marketplace payloads, endpoints, terminology, and rules belong in
provider adapters and their tests. The database stores only neutral provider/status
fields, keyed hashes of credentials and hosts, and encrypted adapter metadata. Raw
credentials are never persisted or accepted as command-line arguments.

Demo data never activates, grants, extends, or bypasses a license. The clean/demo
seed choice and production demo acknowledgements remain content safeguards only.
Demo settings cannot strengthen or weaken license decisions. Installation, license
and demo data selection are three independent states.

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

Placeholder providers return a distinct safe unavailable state at the application
boundary, with no fallback and no false verification success. To add one, implement the
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
