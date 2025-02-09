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