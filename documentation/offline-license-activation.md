# Offline signed-license activation

The manual/offline RSA/SHA-256 provider is currently the only operational provider. Configure `CMS_LICENSE_OFFLINE_PUBLIC_KEY`, then use the authenticated **Admin → License** form or `php artisan cms:license:activate --license-file=/secure/path/token --host=example.com`. Keep the signing private key outside the CMS and keep token files outside the public root.

Verification covers signature, product, normalized host and expiry. Missing/invalid keys, malformed tokens, invalid signatures, wrong products, domain mismatch and expiry produce distinct safe policy states; none disables the existing website. Offline activation makes no provider request and does not use marketplace outage grace.

Marketplace entries are capability declarations and extension points only. Selecting one does not fall back or claim success; it reports `adapter_unavailable` and permits switching back to offline activation.
