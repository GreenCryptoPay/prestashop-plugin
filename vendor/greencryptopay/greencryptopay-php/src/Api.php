<?php

namespace GcpSdk;

use GcpSdk\Handlers\Transfer;
use GcpSdk\Handlers\Standard;
use GcpSdk\Exceptions\GcpSdkApiException;

class Api
{
    const HANDLERS = [
        'transfer' => Transfer::class,
        'standard' => Standard::class
    ];

    /**
     * @param $handler
     * @param false $testnet
     * @return mixed
     * @throws GcpSdkApiException
     */
    public static function make($handler, $testnet = false)
    {
        if (isset(self::HANDLERS[$handler])) {
            $api = self::HANDLERS[$handler];
            return new $api(new Request($api::API_URL, $testnet));
        }

        throw new GcpSdkApiException("Handler {$handler} not found.", 0);
    }
}