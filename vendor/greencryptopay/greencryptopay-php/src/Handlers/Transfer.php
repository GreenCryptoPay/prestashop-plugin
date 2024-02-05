<?php

namespace GcpSdk\Handlers;
use GcpSdk\Request;

class Transfer
{
    CONST API_URL = 'https://api.greencryptopay.com/transfer/v1/';

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
     * @return mixed
     */
    public function merchant()
    {
        return $this->request->merchant(function () {
            return $this->request->get('merchant');
        });
    }

    public function setMerchantId($merchantId)
    {
        $this->merchantId = $merchantId;
    }

    /**
     * @param string $currency
     * @param string $recipientAddress
     * @param string $feeType
     * @param string $callbackUrl
     * @param string $orderId
     * @param string $currencyFrom
     * @param string $amountFrom
     * @return \Psr\Http\Message\StreamInterface|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function paymentAddress(
        string $currency,
        string $recipientAddress,
        string $feeType,
        string $callbackUrl,
        string $orderId,
        string $currencyFrom,
        string $amountFrom
    )
    {
        return $this->request->post('payment_address', [
            'currency' => $currency,
            'recipient_address' => $recipientAddress,
            'fee_type' => $feeType,
            'callback_url' => $callbackUrl,
            'merchant_id' => $this->merchantId,
            'order_id' => $orderId,
            'currency_from' => $currencyFrom,
            'amount_from' => $amountFrom,
        ]);
    }

    /**
     * @param string $currency
     * @param string $paymentAddress
     * @return \Psr\Http\Message\StreamInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function paymentAddressState(string $currency, string $paymentAddress)
    {
        return $this->request->get('payment_address/state', [
            'currency' => $currency,
            'payment_address' => $paymentAddress,
        ]);
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
     * @return \Psr\Http\Message\StreamInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function paymentAddressPayments(
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

        return $this->request->get('payment_address/payments', $params);
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
     * @return \Psr\Http\Message\StreamInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function merchantState(string $currency)
    {
        return $this->request->get('merchant/state', [
            'currency' => $currency,
            'merchant_id' => $this->merchantId,
        ]);
    }

    /**
     * @param string $currency
     * @param string|null $fromTimestamp
     * @param string|null $toTimestamp
     * @param int|null $limit
     * @param int|null $page
     * @param string|null $order
     * @return \Psr\Http\Message\StreamInterface
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
            'currency' => $currency,
            'merchant_id' => $this->merchantId,
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
     * @param string|null $txid
     * @param string|null $fromTimestamp
     * @param string|null $toTimestamp
     * @param int|null $limit
     * @param int|null $page
     * @param string|null $order
     * @return \Psr\Http\Message\StreamInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function merchantPayments(
        string $currency,
        string $txid = null,
        string $fromTimestamp = null,
        string $toTimestamp = null,
        int $limit = null,
        int $page = null,
        string $order = null
    )
    {
        $params = [
            'currency' => $currency,
            'merchant_id' => $this->merchantId,
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

        return $this->request->get('merchant/payments', $params);
    }
}