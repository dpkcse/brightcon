<?php

use App\Licensing\Providers\OfflineSignedLicenseProvider;

return [
    // Core access defaults are deliberately constants rather than environment
    // toggles: a deployment variable must never silently take a site offline.
    'enforcement' => [
        'default_level' => 'updates_only',
        'public_site_requires_valid_license' => false,
        'admin_content_requires_valid_license' => false,
        'backup_requires_valid_license' => false,
        'export_requires_valid_license' => false,
        'updates_require_valid_license' => true,
        'premium_features_require_valid_license' => true,
        'remote_grace_days' => 7,
    ],
    'product_id' => env('CMS_LICENSE_PRODUCT_ID', 'buildora-cms'),
    'default_provider' => env('CMS_LICENSE_PROVIDER', 'offline'),

    'offline' => [
        // URL-safe or standard base64 PEM public key. Keep the signing key outside the product.
        'public_key' => env('CMS_LICENSE_OFFLINE_PUBLIC_KEY'),
    ],

    // This endpoint is deployment-controlled and is never accepted from an admin request.
    // Keep disabled until the Naxas service has completed acceptance testing.
    'naxas_portal' => [
        'enabled' => (bool) env('CMS_NAXAS_PORTAL_ENABLED', false),
        'base_url' => env('CMS_NAXAS_LICENSE_SERVER_URL', 'https://licenses.naxasltd.com'),
        'request_path' => '/api/v1/activation-requests',
        'status_path' => '/api/v1/activation-requests/{request_id}/status',
        'timeout_seconds' => 10,
        'connect_timeout_seconds' => 5,
        'retries' => 1,
        'request_token_ttl_minutes' => 1440,
        'allow_http_local' => (bool) env('CMS_NAXAS_ALLOW_HTTP_LOCAL', false),
        'create_rate_limit' => 5,
        'status_rate_limit' => 20,
    ],

    // Non-operational entries are intentional capability declarations, not adapters.
    // An integration may be marked operational only after its official contract is
    // implemented and covered by provider-specific contract tests.
    'providers' => [
        'offline' => ['label' => 'Manual / offline', 'operational' => true, 'adapter' => OfflineSignedLicenseProvider::class, 'capabilities' => ['activation', 'offline']],
        'gumroad' => ['label' => 'Gumroad', 'operational' => false, 'adapter' => null, 'capabilities' => []],
        'lemon_squeezy' => ['label' => 'Lemon Squeezy', 'operational' => false, 'adapter' => null, 'capabilities' => []],
        'paddle' => ['label' => 'Paddle', 'operational' => false, 'adapter' => null, 'capabilities' => []],
        'envato' => ['label' => 'Envato / CodeCanyon', 'operational' => false, 'adapter' => null, 'capabilities' => []],
        'woocommerce' => ['label' => 'WooCommerce', 'operational' => false, 'adapter' => null, 'capabilities' => []],
        'shopify' => ['label' => 'Shopify / commerce integration', 'operational' => false, 'adapter' => null, 'capabilities' => []],
        'custom' => ['label' => 'Custom licensing API', 'operational' => false, 'adapter' => null, 'capabilities' => []],
    ],
];
