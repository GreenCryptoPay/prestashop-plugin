<?php

namespace GcpSdk;

use GcpSdk\Exceptions\GcpSdkRequestException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class Request
{
    /**
     * @var Client
     */
    protected $httpClient;

    /**
     * @var string[]
     */
    protected $headers = [
        'Accept' => 'application/json',
        'Content-Type' => 'application/json'
    ];

    /**
     * @param string $apiUri
     * @param bool $testnet
     */
    public function __construct(string $apiUri, bool $testnet = false)
    {
        if ($testnet) {
            $apiUri .= 'testnet/';
        }

        $this->httpClient = new Client([
            'base_uri' => $apiUri,
        ]);
    }

    /**
     * @param callable $callback
     * @return mixed
     */
    public function merchant(callable $callback)
    {
        return $callback();
    }

    /**
     * @param string $url
     * @param array $params
     * @param array $headers
     * @return mixed
     */
    public function get(string $url, array $params = [], $headers = [])
    {
        $requestParams = [
            'query' => $params,
            'headers' => array_merge($this->headers, $headers)
        ];

        return $this->request(function () use ($url, $requestParams) {
            return json_decode($this->httpClient->get($url, $requestParams)->getBody()->getContents(), true);
        });
    }

    /**
     * @param string $url
     * @param array $params
     * @param array $headers
     * @return mixed
     */
    public function post(string $url, array $params = [], array $headers = [])
    {
        $requestParams = [
            'json' => $params,
            'headers' => array_merge($this->headers, $headers)
        ];

        return $this->request(function () use ($url, $requestParams) {
            return json_decode($this->httpClient->post($url, $requestParams)->getBody()->getContents(), true);
        });
    }

    /**
     * @param $secretKey
     */
    public function setSecretKey($secretKey)
    {
        if (!empty($secretKey)) {
            $this->headers['X-Secret-Key'] = $secretKey;
        }
    }

    /**
     * @param $callback
     * @return mixed
     * @throws GcpSdkRequestException
     */
    protected function request($callback)
    {
        try {
            return $callback();
        } catch (RequestException $e) {
            throw new GcpSdkRequestException($e->getMessage(), $e->getCode());
        }
    }
}