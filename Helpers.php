<?php

namespace Greencryptopay;

use Configuration;
use GcpSdk\Api;
use Exception;

require_once(_PS_MODULE_DIR_ . 'greencryptopay/vendor/greencryptopay/greencryptopay-php/src/Api.php');

trait Helpers
{
    private $testnet;
    protected $merchant_id;
    private $secret_key;
    private $number_of_confirmations;
    private $request_signature;
    private $wallet_link;
    private $time_to_pay;

    public function setSettings()
    {
        $this->testnet = Configuration::get('GREENCRYPTOPAY_TESTNET');
        $this->merchant_id = Configuration::get('GREENCRYPTOPAY_MERCHANT_ID');
        $this->secret_key = Configuration::get('GREENCRYPTOPAY_SECRET_KEY');
        $this->number_of_confirmations = Configuration::get('GREENCRYPTOPAY_NUMBER_OF_CONFIRMATIONS');
        $this->request_signature = Configuration::get('GREENCRYPTOPAY_REQUEST_SIGNATURE');
        $this->wallet_link = Configuration::get('GREENCRYPTOPAY_WALLET_LINK');
        $this->time_to_pay = Configuration::get('GREENCRYPTOPAY_TIME_TO_PAY');
    }

    /**
     * @param array $requestParams
     * @return string
     */
    public function makeSignature(array $requestParams)
    {
        unset($requestParams['signature']);
        unset($requestParams['fc']);
        unset($requestParams['module']);
        unset($requestParams['controller']);
        return sha1(http_build_query($requestParams) . $this->request_signature);
    }

    /**
     * @param array $requestParams
     * @throws Exception
     */
    public function checkSignature(array $requestParams)
    {
        if ($requestParams['signature'] !== $this->makeSignature($requestParams)) {
            throw new Exception('Bad Request', 400);
        }
    }

    /**
     * @return mixed
     * @throws \GcpSdk\Exceptions\GcpSdkApiException
     */
    private function make_client()
    {
        if (empty($this->merchant_id)) {
            throw new Exception('The "Merchant id" parameter must be filled in the plugin settings.');
        }

        if (empty($this->secret_key)) {
            throw new Exception('The "Secret Key" parameter must be filled in the plugin settings.');
        }

        $client = Api::make('standard', $this->testnet);

        $client->setMerchantId($this->merchant_id);
        $client->setSecretKey($this->secret_key);

        return $client;
    }
}