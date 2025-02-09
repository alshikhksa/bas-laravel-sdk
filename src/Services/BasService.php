<?php

namespace ShikhBas\BasLaravelSdk\Services;

use GuzzleHttp\Client;
use ShikhBas\BasLaravelSdk\Exceptions\BasApiException;
use Exception;

class BasService
{
    protected $config;
    protected $httpClient;
    protected $iv;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->httpClient = new Client([
            'base_uri' => $this->config['base_url'],
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
        ]);
        $this->iv = $this->config['iv'];
    }

    public function getBaseUrl()
    {
        return $this->config['base_url'];
    }





    public function getAccessToken(string $authCode)
    {
        $endpoint = '/api/v1/auth/token';
        $payload = [
            'client_id' => $this->config['client_id'] . '.APP',
            'client_secret' => $this->config['client_secret'],
            'grant_type' => 'authorization_code',
            'code' => $authCode,
            'redirect_uri' => $this->getBaseUrl() . '/api/v1/auth/callback', // Adjust callback URL if needed
        ];

        return $this->post($endpoint, $payload);
    }

    public function getUserInfo(string $accessToken)
    {
        $endpoint = '/api/v1/auth/userinfo';
        $headers = [
            'Authorization' => 'Bearer ' . $accessToken,
        ];
        return $this->get($endpoint, $headers);
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









    public function initiateTransactionOrder(array $payload)
    {
        $endpoint = '/api/v1/merchant/secure/transaction/initiate';
        return $this->post($endpoint, $payload);
    }

    public function checkTransactionStatus(array $payload)
    {
        $endpoint = '/api/v1/merchant/secure/transaction/status';
        return $this->post($endpoint, $payload);
    }

    public function refundPayment(array $payload)
    {
        $endpoint = '/api/v1/merchant/refund-payment/request';
        return $this->post($endpoint, $payload);
    }

    public function generateBasPaymentJS(array $payload): string
    {
        $payloadJson = json_encode($payload);
        return <<<JS
        function basPayment(){JSBridge.call('basPayment', $payloadJson).then(function(result) {
                console.log('basPayment result:', result);
                // يمكنك إضافة المزيد من كود JavaScript هنا على أسطر جديدة
                // واستخدام المسافات البادئة لتنظيم الكود JavaScript
                if (result && result.status === 1) {
                    // ... إجراءات النجاح ...
                } else {
                    // ... إجراءات الفشل ...
                }
            });
                }
        JS; // علامة الإغلاق JS; تبدأ من بداية السطر بدون مسافات بادئة
    }








    public function post(string $endpoint, array $data = [], array $headers = [])
    {
        $signature = $this->generateSignature($data);
        $headers['Signature'] = $signature;

        try {
            $response = $this->httpClient->post($endpoint, [
                'headers' => $headers,
                'json' => $data,
            ]);

            return json_decode($response->getBody(), true);

        } catch (\Exception $e) {
            throw new BasApiException("BAS API Request Failed: " . $e->getMessage(), $e->getCode());
        }
    }

    public function get(string $endpoint, array $headers = [])
    {
        try {
            $response = $this->httpClient->get($endpoint, [
                'headers' => $headers,
            ]);

            return json_decode($response->getBody(), true);

        } catch (\Exception $e) {
            throw new BasApiException("BAS API Request Failed: " . $e->getMessage(), $e->getCode());
        }
    }

    // Checksum/Signature Functions
    public function generateSignature($params) {
        if(!is_array($params) && !is_string($params)){
            throw new Exception("string or array expected, ".gettype($params)." given");
        }
        if(is_array($params)){
            $params = $this->getStringByParams($params);
        }
        return $this->generateSignatureByString($params);
    }
    private function getStringByParams($params) {
        ksort($params);
        $params = array_map(function ($value){
            return ($value !== null && strtolower($value) !== "null") ? $value : "";
        }, $params);
        return implode("|", $params);
    }
    private function generateSignatureByString($params){
        $salt = $this->generateRandomString(4);
        return $this->calculateChecksum($params,$salt);
    }
    private function generateRandomString($length) {
        $data = "9876543210ZYXWVUTSRQPONMLKJIHGFEDCBAabcdefghijklmnopqrstuvwxyz!@#$&_";
        return substr(str_shuffle(str_repeat($data, $length)), 0, $length);
    }
    private function calculateChecksum($params, $salt){
        $hashString = $this->calculateHash($params, $salt);
        return $this->encrypt($hashString);
    }
    private function calculateHash($params, $salt) {
        return hash("sha256", $params . "|" . $salt) . $salt;
    }
    private function encrypt($input) {
        $key = html_entity_decode($this->config['merchant_key']);
        $password = substr(hash('sha256', $key, true), 0, 32);
        $data = openssl_encrypt($input, 'aes-256-cbc', $password, OPENSSL_RAW_DATA, $this->iv);
        return base64_encode($data);
    }
    public function verifySignature($params, $checksum){
        if(!is_array($params) && !is_string($params)){
            throw new Exception("string or array expected, ".gettype($params)." given");
        }
        if(isset($params['CHECKSUMHASH'])){
            unset($params['CHECKSUMHASH']);
        }
        if(is_array($params)){
            $params = $this->getStringByParams($params);
        }
        return $this->verifySignatureByString($params,$checksum);
    }

    private function verifySignatureByString($params, $checksum)
    {
        $bas_hash = $this->decrypt($checksum);
        $salt = substr($bas_hash, -4);
        return $bas_hash === $this->calculateHash($params, $salt);
    }
    private function decrypt($encrypted) {
        $key = html_entity_decode($this->config['merchant_key']);
        $password = substr(hash('sha256', $key, true), 0, 32);
        return openssl_decrypt($encrypted , "aes-256-cbc" ,$password,0, $this->iv);
    }
}