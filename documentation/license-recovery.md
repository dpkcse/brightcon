# License recovery

Public pages, core administration, backup/export, system diagnostics and license management remain available for malformed, expired, wrong-product, wrong-domain and invalid-signature licenses. They also remain available when an adapter is unavailable or verification configuration is missing.

If `APP_KEY` changes, encrypted provider metadata may be unreadable. Buildora reports `recovery_required`, preserves the database ciphertext, and asks an administrator to enter a replacement signed token. It does not erase the activation automatically. Restore the original key when appropriate, or configure the offline public key and replace the token at **Admin → License** or with the CLI activation command.

Never paste tokens into logs, tickets or command arguments. The application stores credential and host HMAC fingerprints, not the credential. A replacement activation safely overwrites unusable metadata only after verification has produced a decision.

## Portal request recovery

An expired, rejected, scope-changed, or unreadable activation request can be replaced from Admin → License. Completed tokens cannot be reused. During portal outage, retain any valid license and use manual signed-license activation; the installation is not classified as pirated or destructively locked.
