# Laravel BAS SDK

This SDK simplifies integration with the BAS Mini Apps Platform in Laravel applications. It provides convenient services and facades to interact with BAS APIs for authentication, payment, and other functionalities.

## Installation

1.  **Require the package via Composer:**

    ```bash
    composer require your-vendor/bas-laravel-sdk
    ```

    *(Replace `your-vendor/bas-laravel-sdk` with your actual package name)*

2.  **Publish the configuration file (optional, but recommended):**

    ```bash
    php artisan vendor:publish --provider="YourVendor\BasLaravelSdk\BasServiceProvider" --tag="bas-config"
    ```

    This command will copy the `config/bas.php` file to your application's `config` directory, allowing you to customize the configuration if needed (though environment variables are the primary way to configure the SDK).

3.  **Configure your BAS Credentials:**

    You **must** configure your BAS API credentials by adding environment variables to your application's `.env` file.

## Configuration

1.  **Edit your `.env` file:**

    Open your Laravel application's `.env` file and add the following environment variables, replacing the placeholder values with your actual BAS credentials.

    ```env
    BAS_BASE_URL=YOUR_BAS_BASE_URL_HERE
    BAS_CLIENT_ID=YOUR_BAS_CLIENT_ID_HERE
    BAS_CLIENT_SECRET=YOUR_BAS_CLIENT_SECRET_HERE
    BAS_AUTH_CLIENT_ID=YOUR_BAS_AUTH_CLIENT_ID_HERE
    BAS_AUTH_CLIENT_SECRET=YOUR_BAS_AUTH_CLIENT_SECRET_HERE
    BAS_MERCHANT_KEY=YOUR_BAS_MERCHANT_KEY_HERE
    BAS_IV=YOUR_BAS_IV_HERE
    BAS_ENVIRONMENT=staging # or production
    ```

2.  **Environment Variable Descriptions:**

    *   **`BAS_BASE_URL`**:  The base URL for the BAS API platform. This should be set to your Staging or Production API endpoint (e.g., `https://staging-api.bas-platform.com`).
    *   **`BAS_CLIENT_ID`**: Your Mini App's Client ID (App ID) provided by BAS when you register your Mini App.
    *   **`BAS_CLIENT_SECRET`**: Your Mini App's Client Secret (Merchant Key). Keep this secret and do not share it publicly. Provided by BAS.
    *   **`BAS_AUTH_CLIENT_ID`**: Authentication Client ID required for the Login Flow. Provided by BAS if you are using BAS Mini Apps Login feature.
    *   **`BAS_AUTH_CLIENT_SECRET`**: Authentication Client Secret required for the Login Flow. Keep this secret and do not share it publicly. Provided by BAS.
    *   **`BAS_MERCHANT_KEY`**: Merchant Key used to generate checksum/signature for API requests. Provided by BAS.
    *   **`BAS_IV`**: Initialization Vector (IV) for checksum/signature encryption. This is a fixed value provided by BAS.
    *   **`BAS_ENVIRONMENT`**:  The environment your application is running in. Set to `staging` for development and testing, or `production` for live environments.

    **Important Security Notes:**

    *   **Never hardcode your BAS credentials directly into your code or configuration files.** Always use environment variables to keep your credentials secure and separate from your codebase.
    *   **Keep your `BAS_CLIENT_SECRET`, `BAS_AUTH_CLIENT_SECRET`, and `BAS_MERCHANT_KEY` secret and protected.** Do not commit them to public Git repositories or share them insecurely.

## Usage

The SDK provides Facades for easy access to its services:

*   `AuthBas`: For authentication-related functionalities (Login Flow).
*   `PaymentBas`: For payment-related functionalities (Payment Flow).
*   `Bas`: For general BAS service functionalities (like retrieving the base URL).

**Example - Authentication (Login Flow):**

**PHP (Controller/Service):**

```php
use AuthBas;

// ... in your controller or service ...

$authCode = // ... Obtain authCode from BAS Mini App (using basFetchAuthCode JS)

$accessTokenResponse = AuthBas::getAccessToken($authCode);

if (isset($accessTokenResponse['access_token'])) {
    $accessToken = $accessTokenResponse['access_token'];
    $userInfoResponse = AuthBas::getUserInfo($accessToken);

    // Process user info and access token
    dump($userInfoResponse);
} else {
    // Handle error
    dump($accessTokenResponse);
}