# License activation policy

The only operational provider is manual/offline RSA/SHA-256 verification. The private signing key remains solely with the licensor and must never be distributed; Buildora CMS contains only the public verification key. A signed license binds one normalized production domain. Localhost and approved associated staging installations do not replace that binding.

License, installer, and demo states are independent. Missing or invalid activation never deletes data and, by default, leaves public pages, ordinary admin content management, backup, and export available. Update downloads and future premium entitlements may require a valid license. Warnings are administrator-only by default. Marketplace adapters are not operational unless separately implemented and tested.

For commands and secure token handling, follow `offline-license-activation.md`. Never commit a token, private key, customer identifier, or real license data.

License metadata: Naxas Limited is the licensor; the owner-approved license is effective 27 July 2026. Legal and support contact: info.naxasltd@gmail.com. `LICENSE` controls.

## Naxas portal activation (pending deployment)

The CMS includes a disabled-by-default foundation for an official, marketplace-independent Naxas License Portal. When enabled by the deployer, an administrator can create a 24-hour one-time **activation request token**, copy it to the portal, and check approval. That token is not a license. The returned signed license is the entitlement and is always RSA/SHA-256 verified locally. Manual paste or file upload remains available. An outage does not disable the public website or core administration.
