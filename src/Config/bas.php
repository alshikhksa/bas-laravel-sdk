<?php

return [
    'base_url' => env('BAS_BASE_URL', 'YOUR_BAS_BASE_URL_HERE'),
    'client_id' => env('BAS_CLIENT_ID', 'YOUR_BAS_CLIENT_ID_HERE'),
    'client_secret' => env('BAS_CLIENT_SECRET', 'YOUR_BAS_CLIENT_SECRET_HERE'),
    'auth_client_id' => env('BAS_AUTH_CLIENT_ID', 'YOUR_BAS_AUTH_CLIENT_ID_HERE'),
    'auth_client_secret' => env('BAS_AUTH_CLIENT_SECRET', 'YOUR_BAS_AUTH_CLIENT_SECRET_HERE'),
    'environment' => env('BAS_ENVIRONMENT', 'staging'),
    'merchant_key' => env('BAS_MERCHANT_KEY', 'YOUR_BAS_MERCHANT_KEY_HERE'),
    'iv' => env('BAS_IV', 'fixedIvString1234567890'), // **Keep the fixed IV as confirmed**
];