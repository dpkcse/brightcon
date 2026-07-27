# Naxas license server API contract (internal; future service)

Status: **pending deployment and acceptance testing**. CMS integration is marketplace-independent.

`POST /api/v1/activation-requests` accepts JSON fields `product`, `version`, `license_type`, `installation_uuid`, `domain`, `environment`, and a random `nonce`. It returns `request_id`, a one-time `request_token` such as `BRQ-XXXX-XXXX-XXXX`, `status: pending`, ISO-8601 `expires_at`, and the trusted-origin `portal_url`.

`POST /api/v1/activation-requests/{request_id}/status` accepts the request proof in its JSON body. Pending returns `{"status":"pending"}`. Approval returns `{"status":"approved","signed_license":"<existing-compatible-token>"}`. Rejection returns `status`, a stable `failure_code`, and a non-sensitive `safe_message`. Completed and expired are terminal. Responses must be JSON, must not redirect, and must not echo raw input.

The signed-license claims for portal issuance must include `product: buildora-cms`, `license_type: single_site`, the exact `installation_uuid`, normalized domain in `hosts`, expiry where applicable, and entitlements. Signing occurs only on Naxas-controlled infrastructure. Purchase channels (manual, Gumroad, Lemon Squeezy, Paddle, WooCommerce, or another approved channel) are portal implementation details and never CMS fields.
