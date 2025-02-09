# Laravel BAS SDK

This SDK simplifies integration with the BAS Mini Apps Platform in Laravel applications.

## Installation

1.  **Require the package via Composer:**

    ````bash
    composer require shikhbas/bas-laravel-sdk
    ````

    *(Replace `shikhbas/bas-laravel-sdk` with your actual package name)*

2.  **Publish the configuration file:**

    ````bash
    php artisan vendor:publish --provider="ShikhBas\BasLaravelSdk\BasServiceProvider" --tag="bas-config"
    ````

3.  **Configure your BAS credentials:**

    Edit the `config/bas.php` file in your Laravel application and set your `BAS_BASE_URL`, `BAS_CLIENT_ID`, `BAS_CLIENT_SECRET`, `BAS_MERCHANT_KEY`, `BAS_IV` and other necessary credentials in your ``.env`` file.

## Usage

**Configuration:**

*   Ensure you have configured your BAS credentials in the ``.env`` file and ``config/bas.php``.

**Example - Authentication (Login Flow):**

```php
use AuthBas;

// In your controller or service:

$authCode = // ... Obtain authCode from BAS Mini App (using basFetchAuthCode JS)

$accessTokenResponse = AuthBas::getAccessToken($authCode);

if (isset($accessTokenResponse['access_token'])) {
    $accessToken = $accessTokenResponse['access_token'];
    $userInfoResponse = AuthBas::getUserInfo($accessToken);

    // ... Process user info and access token ...
    dump($userInfoResponse);
} else {
    // Handle error
    dump($accessTokenResponse);
}


// In your Blade template (to generate basFetchAuthCode JS):
    {!! AuthBas::generateFetchAuthCodeJS() !!}
```

**Example - Payment Flow:**

```php
use PaymentBas;

// In your controller or service:

$payload = [
    'appId' =
    'orderId' =
    'amount' =
        'value' =
        'currency' =
    ],
    'callBackUrl' =, // Replace with your actual callback URL
    // ... other required parameters for initiateTransactionOrder API
];

$initiateResponse = PaymentBas::initiateTransactionOrder($payload);

if (isset($initiateResponse['body']['trxToken'])) {
    $transactionToken = $initiateResponse['body']['trxToken'];

    // Generate basPayment JS code and include it in your view
    $paymentPayload = [
        'amount' =
            'value' =
            'currency' =
        ],
        'orderId' =
        'trxToken' =
        'appId' =
    ];

    $basPaymentJS = PaymentBas::generateBasPaymentJS($paymentPayload);

    // ... Pass $basPaymentJS to your Blade view ...

} else {
    // Handle error
    dump($initiateResponse);
}


// In your Blade template (to include basPayment JS):
    {!! $basPaymentJS !!}
    // Call basPayment() when needed (e.g., on button click)
```
