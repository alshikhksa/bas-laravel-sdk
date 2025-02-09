<?php

namespace ShikhBas\BasLaravelSdk\Services;

class AuthService
{
    protected $basService;
    protected $config;

    public function __construct(BasService $basService, array $config)
    {
        $this->basService = $basService;
        $this->config = $config;
    }

    public function getAccessToken(string $authCode)
    {
        $endpoint = '/api/v1/auth/token';
        $payload = [
            'client_id' => $this->config['client_id'] . '.APP',
            'client_secret' => $this->config['client_secret'],
            'grant_type' => 'authorization_code',
            'code' => $authCode,
            'redirect_uri' => $this->basService->getBaseUrl() . '/api/v1/auth/callback', // Adjust callback URL if needed
        ];

        return $this->basService->post($endpoint, $payload);
    }

    public function getUserInfo(string $accessToken)
    {
        $endpoint = '/api/v1/auth/userinfo';
        $headers = [
            'Authorization' => 'Bearer ' . $accessToken,
        ];
        return $this->basService->get($endpoint, $headers);
    }

    public function generateFetchAuthCodeJS(string $clientId = null): string
    {
        $clientId = $clientId ?: $this->config['client_id'];
        return <<<JS
            function basFetchAuthCode(){JSBridge.call('basFetchAuthCode', {
                    clientId: "$clientId"
                }).then(function(result) {
                console.log('basFetchAuthCode result:', result);
                    // Handle the result in your JavaScript code
                });
            }
                window.addEventListener('JSBridgeReady', function(event) {
                    console.log('JSBridgeReady fired');
                    basFetchAuthCode();
                });
            JS;
    }



}