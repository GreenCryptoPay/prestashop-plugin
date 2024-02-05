<?php

use PHPUnit\Framework\TestCase;
use GcpSdk\Handlers\Transfer;

class TransferTest extends TestCase
{
    private static $transfer;
    private static $faker;

    public static function setUpBeforeClass (): void
    {
        self::$faker = Faker\Factory::create();
    }

    public function testSignUp()
    {
        self::$transfer = new Transfer(new \GcpSdk\tests\Request(Transfer::API_URL, true));
        $response = self::$transfer->merchant();

        self::$transfer->setMerchantId($response['merchant_id']);

        $this->assertInstanceOf(Transfer::class, self::$transfer);
    }

    public function testPaymentAddress()
    {
        $response = self::$transfer->paymentAddress(
            'btc',
            getenv('RECIPIENT_ADDRESS'),
            'percent',
            self::$faker->url,
            self::$faker->md5,
            'usd',
            self::$faker->randomFloat(2, 0, 50)
        );

        $data = json_decode($response->getBody()->getContents(), true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArrayHasKey('payment_address', $data);
        $this->assertArrayHasKey('callback_secret', $data);
        $this->assertArrayHasKey('amount', $data);
    }

    public function testPaymentAddressState()
    {
        $response = self::$transfer->paymentAddressState(
        'btc',
                self::$faker->md5,
        );

        $data = json_decode($response->getBody()->getContents(), true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArrayHasKey('created_timestamp', $data);
        $this->assertArrayHasKey('payment_address', $data);
        $this->assertArrayHasKey('recipient_address', $data);
        $this->assertArrayHasKey('fee_type', $data);
        $this->assertArrayHasKey('callback_secret', $data);
        $this->assertArrayHasKey('callback_url', $data);
        $this->assertArrayHasKey('payments', $data);
        $this->assertArrayHasKey('payments_amount', $data);
        $this->assertArrayHasKey('fee_amount', $data);
        $this->assertArrayHasKey('success_callbacks', $data);
        $this->assertArrayHasKey('failed_callbacks', $data);
    }

    public function testPaymentAddressPayments()
    {
        $response = self::$transfer->paymentAddressPayments(
        'btc',
                self::$faker->md5,
        );

        $data = json_decode($response->getBody()->getContents(), true);

        $this->assertEquals(200, $response->getStatusCode());

        if (!empty($data)) {

            $data = $data[0];

            $this->assertArrayHasKey('created_timestamp', $data);
            $this->assertArrayHasKey('payment_address', $data);
            $this->assertArrayHasKey('txid', $data);
            $this->assertArrayHasKey('amount', $data);
            $this->assertArrayHasKey('confirmations', $data);
            $this->assertArrayHasKey('fee_type', $data);
            $this->assertArrayHasKey('fee_amount', $data);
            $this->assertArrayHasKey('recipient_address', $data);
            $this->assertArrayHasKey('recipient_txid', $data);
            $this->assertArrayHasKey('recipient_amount', $data);
            $this->assertArrayHasKey('callback_url', $data);
            $this->assertArrayHasKey('callback_secret', $data);
        }
    }

    public function testPaymentAddressCallbacks()
    {
        $response = self::$transfer->paymentAddressCallbacks(
        'btc',
            self::$faker->md5,
        );

        $data = json_decode($response->getBody()->getContents(), true);

        $this->assertEquals(200, $response->getStatusCode());

        if (!empty($data)) {

            $data = $data[0];

            $this->assertArrayHasKey('timestamp', $data);
            $this->assertArrayHasKey('callback_url', $data);
            $this->assertArrayHasKey('http_status', $data);
            $this->assertArrayHasKey('payment_address', $data);
            $this->assertArrayHasKey('amount_received', $data);
            $this->assertArrayHasKey('txid', $data);
            $this->assertArrayHasKey('confirmations', $data);
            $this->assertArrayHasKey('recipient_address', $data);
            $this->assertArrayHasKey('recipient_txid', $data);
            $this->assertArrayHasKey('recipient_amount', $data);
            $this->assertArrayHasKey('callback_secret', $data);

        }

    }

    public function testMerchantState()
    {
        $response = self::$transfer->merchantState(
            'btc',
        );

        $data = json_decode($response->getBody()->getContents(), true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArrayHasKey('payments', $data);
        $this->assertArrayHasKey('payments_amount', $data);
        $this->assertArrayHasKey('fee_amount', $data);
        $this->assertArrayHasKey('success_callbacks', $data);
        $this->assertArrayHasKey('failed_callbacks', $data);
    }

    public function testMerchantPaymentAddress()
    {
        $response = self::$transfer->merchantPaymentAddress(
            'btc',
        );

        $data = json_decode($response->getBody()->getContents(), true);

        $this->assertEquals(200, $response->getStatusCode());

        if (!empty($data)) {

            $data = $data[0];

            $this->assertArrayHasKey('created_timestamp', $data);
            $this->assertArrayHasKey('payment_address', $data);
            $this->assertArrayHasKey('recipient_address', $data);
            $this->assertArrayHasKey('fee_type', $data);
            $this->assertArrayHasKey('callback_url', $data);
            $this->assertArrayHasKey('callback_secret', $data);

        }
    }

    public function testMerchantPayments()
    {
        $response = self::$transfer->merchantPayments(
            'btc',
        );

        $data = json_decode($response->getBody()->getContents(), true);

        $this->assertEquals(200, $response->getStatusCode());

        if (!empty($data)) {

            $data = $data[0];

            $this->assertArrayHasKey('created_timestamp', $data);
            $this->assertArrayHasKey('payment_address', $data);
            $this->assertArrayHasKey('txid', $data);
            $this->assertArrayHasKey('amount', $data);
            $this->assertArrayHasKey('confirmations', $data);
            $this->assertArrayHasKey('fee_type', $data);
            $this->assertArrayHasKey('fee_amount', $data);
            $this->assertArrayHasKey('recipient_address', $data);
            $this->assertArrayHasKey('recipient_txid', $data);
            $this->assertArrayHasKey('recipient_amount', $data);
            $this->assertArrayHasKey('callback_url', $data);
            $this->assertArrayHasKey('callback_secret', $data);
        }
    }

}