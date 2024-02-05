<?php

namespace GcpSdk\tests;

use GuzzleHttp\Client;

class Request extends \GcpSdk\Request
{
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
            'http_errors' => false
        ]);

    }

    /**
     * @param callable $callback
     * @return mixed
     */
    public function merchant(callable $callback)
    {
        return json_decode($callback()->getBody()->getContents(), true);
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
            return $this->httpClient->get($url, $requestParams);
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
            return $this->httpClient->post($url, $requestParams);
        });
    }
}