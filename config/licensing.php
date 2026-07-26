<?php

use App\Licensing\Providers\OfflineSignedLicenseProvider;

return [
    'enforce' => (bool) env('CMS_LICENSE_ENFORCE', false),
    'product_id' => env('CMS_LICENSE_PRODUCT_ID', 'buildora-cms'),
    'default_provider' => env('CMS_LICENSE_PROVIDER', 'offline'),

    'offline' => [
        // URL-safe or standard base64 PEM public key. Keep the signing key outside the product.
        'public_key' => env('CMS_LICENSE_OFFLINE_PUBLIC_KEY'),
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
