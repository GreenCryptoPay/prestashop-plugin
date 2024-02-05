<?php

namespace GcpSdk\Handlers;
use GcpSdk\Request;

class Standard
{
    const API_URL = 'https://api.greencryptopay.com/standard/v1/';

    /**
     * @var Request
     */
    private $request;

    /**
     * @var string
     */
    private $merchantId;

    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @param string $feeType
     * @param string $callbackUrl
     * @return mixed
     */
    public function merchant(string $feeType, string $callbackUrl)
    {
        return $this->request->merchant(function () use ($feeType, $callbackUrl) {
            return $this->request->post('merchant', [
                'fee_type' => $feeType,
                'callback_url' => $callbackUrl
            ]);
        });
    }

    public function setMerchantId($merchantId)
    {
        $this->merchantId = $merchantId;
    }

    public function setSecretKey($secretKey)
    {
        $this->request->setSecretKey($secretKey);
    }

    /**
     * @param string $currency
     * @param string $callbackUrl
     * @param string $orderId
     * @param string $currencyFrom
     * @param float $amountFrom
     * @return \Psr\Http\Message\StreamInterface|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function paymentAddress(
        string $currency,
        string $callbackUrl,
        string $orderId,
        string $currencyFrom,
        float $amountFrom
    )
    {
        return $this->request->post('payment_address', [
            'merchant_id' => $this->merchantId,
            'currency' => $currency,
            'callback_url' => $callbackUrl,
            'order_id' => $orderId,
            'currency_from' => $currencyFrom,
            'amount_from' => (string) $amountFrom,
        ]);
    }

    /**
     * @param string $currency
     * @param array $recipients
     * @return \Psr\Http\Message\StreamInterface|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function withdraw(string $currency, array $recipients)
    {
        return $this->request->post('withdraw', [
            'merchant_id' => $this->merchantId,
            'currency' => $currency,
            'recipients' => $recipients
        ]);
    }

    /**
     * @param string $currency
     * @param string $recipientAddress
     * @return \Psr\Http\Message\StreamInterface|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function withdrawAll(string $currency, string $recipientAddress)
    {
        return $this->request->post('withdraw_all', [
            'merchant_id' => $this->merchantId,
            'currency' => $currency,
            'recipient_address' => $recipientAddress
        ]);
    }

    /**
     * @param string $currency
     * @return \Psr\Http\Message\StreamInterface|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function merchantState(string $currency)
    {
        return $this->request->get('merchant/state', [
            'merchant_id' => $this->merchantId,
            'currency' => $currency,
        ]);
    }

    /**
     * @param string $currency
     * @param string|null $fromTimestamp
     * @param string|null $toTimestamp
     * @param int|null $limit
     * @param int|null $page
     * @param string|null $order
     * @return \Psr\Http\Message\StreamInterface|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function merchantPaymentAddress(
        string $currency,
        string $fromTimestamp = null,
        string $toTimestamp = null,
        int $limit = null,
        int $page = null,
        string $order = null
    )
    {
        $params = [
            'merchant_id' => $this->merchantId,
            'currency' => $currency,
        ];

        if (!empty($fromTimestamp)) {
            $params['from_timestamp'] = $fromTimestamp;
        }

        if (!empty($toTimestamp)) {
            $params['to_timestamp'] = $toTimestamp;
        }

        if (!empty($limit)) {
            $params['limit'] = $limit;
        }

        if (!empty($page)) {
            $params['page'] = $page;
        }

        if (!empty($order)) {
            $params['order'] = $order;
        }

        return $this->request->get('merchant/payment_addresses', $params);
    }

    /**
     * @param string $currency
     * @param string|null $paymentAddress
     * @param string|null $txid
     * @param string|null $fromTimestamp
     * @param string|null $toTimestamp
     * @param int|null $limit
     * @param int|null $page
     * @param string|null $order
     * @return \Psr\Http\Message\StreamInterface|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function merchantIncomingPayments(
        string $currency,
        string $paymentAddress = null,
        string $txid = null,
        string $fromTimestamp = null,
        string $toTimestamp = null,
        int $limit = null,
        int $page = null,
        string $order = null
    )
    {
        $params = [
            'merchant_id' => $this->merchantId,
            'currency' => $currency,
        ];

        if (!empty($paymentAddress)) {
            $params['payment_address'] = $paymentAddress;
        }

        if (!empty($txid)) {
            $params['txid'] = $txid;
        }

        if (!empty($fromTimestamp)) {
            $params['from_timestamp'] = $fromTimestamp;
        }

        if (!empty($toTimestamp)) {
            $params['to_timestamp'] = $toTimestamp;
        }

        if (!empty($limit)) {
            $params['limit'] = $limit;
        }

        if (!empty($page)) {
            $params['page'] = $page;
        }

        if (!empty($order)) {
            $params['order'] = $order;
        }

        return $this->request->get('merchant/incoming_payments', $params);
    }

    /**
     * @param string $currency
     * @param string|null $fromTimestamp
     * @param string|null $toTimestamp
     * @param int|null $limit
     * @param int|null $page
     * @param string|null $order
     * @return \Psr\Http\Message\StreamInterface|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function merchantWithdrawals(
        string $currency,
        string $fromTimestamp = null,
        string $toTimestamp = null,
        int $limit = null,
        int $page = null,
        string $order = null
    )
    {
        $params = [
            'merchant_id' => $this->merchantId,
            'currency' => $currency,
        ];

        if (!empty($fromTimestamp)) {
            $params['from_timestamp'] = $fromTimestamp;
        }

        if (!empty($toTimestamp)) {
            $params['to_timestamp'] = $toTimestamp;
        }

        if (!empty($limit)) {
            $params['limit'] = $limit;
        }

        if (!empty($page)) {
            $params['page'] = $page;
        }

        if (!empty($order)) {
            $params['order'] = $order;
        }

        return $this->request->get('merchant/incoming_payments', $params);
    }

    /**
     * @param string $currency
     * @param string $paymentAddress
     * @param string|null $txid
     * @param string|null $fromTimestamp
     * @param string|null $toTimestamp
     * @param int|null $limit
     * @param int|null $page
     * @param string|null $order
     * @return \Psr\Http\Message\StreamInterface|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function paymentAddressCallbacks(
        string $currency,
        string $paymentAddress,
        string $txid = null,
        string $fromTimestamp = null,
        string $toTimestamp = null,
        int $limit = null,
        int $page = null,
        string $order = null
    )
    {
        $params = [
            'merchant_id' => $this->merchantId,
            'currency' => $currency,
            'payment_address' => $paymentAddress
        ];

        if (!empty($txid)) {
            $params['txid'] = $txid;
        }

        if (!empty($fromTimestamp)) {
            $params['from_timestamp'] = $fromTimestamp;
        }

        if (!empty($toTimestamp)) {
            $params['to_timestamp'] = $toTimestamp;
        }

        if (!empty($limit)) {
            $params['limit'] = $limit;
        }

        if (!empty($page)) {
            $params['page'] = $page;
        }

        if (!empty($order)) {
            $params['order'] = $order;
        }

        return $this->request->get('payment_address/callbacks', $params);
    }

    /**
     * @param string $currency
     * @param string $paymentAddress
     * @return \Psr\Http\Message\StreamInterface|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function paymentAddressState(string $currency, string $paymentAddress)
    {
        $params = [
            'merchant_id' => $this->merchantId,
            'currency' => $currency,
            'payment_address' => $paymentAddress
        ];

        return $this->request->get('payment_address/state', $params);
    }
}