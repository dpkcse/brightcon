# Activation request security

An activation request token is a short-lived one-time claim proof, **not a license**. The CMS stores it encrypted only while polling is possible, stores an HMAC fingerprint and masked form, clears the ciphertext at terminal completion/rejection/expiry, and never places it in a URL or log. Rotation is replacement after expiry or scope change; loss/decryption failure enters recovery and requires a new request.

The endpoint is deployment configuration, HTTPS is mandatory outside explicit local/testing HTTP mode, redirects are disabled, the returned portal scheme/host must exactly match the configured origin, query credentials are rejected, response schemas and sizes are bounded, and retry/timeouts are bounded. Admin authorization, CSRF, throttling and runtime-demo protection apply. Neither APP_KEY nor an installation secret is transmitted. Raw provider payloads, signed licenses, buyer credentials and request tokens must not be logged.
