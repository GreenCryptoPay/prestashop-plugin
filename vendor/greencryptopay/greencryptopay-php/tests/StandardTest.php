<?php


use PHPUnit\Framework\TestCase;
use GcpSdk\Handlers\Standard;

class StandardTest extends TestCase
{
    private static $standard;
    private static $faker;
    private static $callbackUrl;

    public static function setUpBeforeClass (): void
    {
        self::$faker = Faker\Factory::create();
        self::$callbackUrl = self::$faker->url;
    }

    public function testSignUp()
    {
        self::$standard = new Standard(new \GcpSdk\tests\Request(Standard::API_URL, true));
        $response = self::$standard->merchant('percent', self::$callbackUrl);

        self::$standard->setMerchantId($response['merchant_id']);
        self::$standard->setSecretKey($response['secret_key']);

        $this->assertInstanceOf(Standard::class, self::$standard);
    }

    public function testPaymentAddress()
    {
        $response = self::$standard->paymentAddress(
            'btc',
            self::$callbackUrl,
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


    public function testWithdraw()
    {
        $response = self::$standard->withdraw(
            'btc',
            [
                [
                    "address" => getenv('RECIPIENT_ADDRESS'),
                    "amount" => (string) self::$faker->randomFloat(2, 0, 50)
                ]
            ]
        );

        $data = json_decode($response->getBody()->getContents(), true);

        $this->assertContains($response->getStatusCode(), [200, 400]);

        if (!empty($data)) {

            if ($response->getStatusCode() == 400) {

                $this->assertArrayHasKey('available_amount', $data);
                $this->assertArrayHasKey('outgoing_fee_amount', $data);
                $this->assertGreaterThan($data['available_amount'], $data['outgoing_fee_amount']);

            } else if ($response->getStatusCode() == 200) {

                $data = $data[0];

                $this->assertArrayHasKey('txid', $data);
                $this->assertArrayHasKey('address', $data);
                $this->assertArrayHasKey('amount', $data);
            }
        }
    }

    public function testWithdrawAll()
    {
        $response = self::$standard->withdrawAll(
        'btc',
                getenv('RECIPIENT_ADDRESS')
        );

        $data = json_decode($response->getBody()->getContents(), true);

        $this->assertContains($response->getStatusCode(), [200, 400]);

        if (!empty($data)) {

            if ($response->getStatusCode() == 400) {

                $this->assertArrayHasKey('available_amount', $data);
                $this->assertArrayHasKey('outgoing_fee_amount', $data);
                $this->assertGreaterThan($data['available_amount'], $data['outgoing_fee_amount']);

            } else if ($response->getStatusCode() == 200) {

                $data = $data[0];

                $this->assertArrayHasKey('txid', $data);
                $this->assertArrayHasKey('address', $data);
                $this->assertArrayHasKey('amount', $data);
            }
        }
    }

    public function testMerchantState()
    {
        $response = self::$standard->merchantState(
        'btc',
        );

        $data = json_decode($response->getBody()->getContents(), true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArrayHasKey('created_timestamp', $data);
        $this->assertArrayHasKey('merchant_id', $data);
        $this->assertArrayHasKey('fee_type', $data);
        $this->assertArrayHasKey('callback_url', $data);
        $this->assertArrayHasKey('payment_addresses', $data);
        $this->assertArrayHasKey('balance_amount', $data);
        $this->assertArrayHasKey('confirmed_payments', $data);
        $this->assertArrayHasKey('confirmed_payments_amount', $data);
        $this->assertArrayHasKey('pending_payments', $data);
        $this->assertArrayHasKey('pending_payments_amount', $data);
        $this->assertArrayHasKey('fee_amount', $data);
        $this->assertArrayHasKey('withdrawals', $data);
        $this->assertArrayHasKey('withdrawn_amount', $data);
        $this->assertArrayHasKey('success_callbacks', $data);
        $this->assertArrayHasKey('failed_callbacks', $data);
    }

    public function testMerchantPaymentAddress()
    {
        $response = self::$standard->merchantPaymentAddress(
            'btc',
        );

        $data = json_decode($response->getBody()->getContents(), true);

        $this->assertEquals(200, $response->getStatusCode());

        if (!empty($data)) {

            $data = $data[0];

            $this->assertArrayHasKey('created_timestamp', $data);
            $this->assertArrayHasKey('payment_address', $data);
            $this->assertArrayHasKey('callback_url', $data);
            $this->assertArrayHasKey('callback_secret', $data);
        }
    }

    public function testMerchantIncomingPayments()
    {
        $response = self::$standard->merchantIncomingPayments(
            'btc',
        );

        $data = json_decode($response->getBody()->getContents(), true);

        $this->assertEquals(200, $response->getStatusCode());

        if (!empty($data)) {

            $data = $data[0];

            $this->assertArrayHasKey('timestamp', $data);
            $this->assertArrayHasKey('payment_address', $data);
            $this->assertArrayHasKey('txid', $data);
            $this->assertArrayHasKey('amount', $data);
            $this->assertArrayHasKey('merch_amount', $data);
            $this->assertArrayHasKey('fee_amount', $data);
            $this->assertArrayHasKey('confirmations', $data);
            $this->assertArrayHasKey('confirmed', $data);
            $this->assertArrayHasKey('confirmed', $data);
        }
    }


    public function testMerchantWithdrawals()
    {
        $response = self::$standard->merchantWithdrawals(
            'btc',
        );

        $data = json_decode($response->getBody()->getContents(), true);

        $this->assertEquals(200, $response->getStatusCode());

        if (!empty($data)) {

            $data = $data[0];

            $this->assertArrayHasKey('timestamp', $data);
            $this->assertArrayHasKey('withdrawal_amount', $data);
            $this->assertArrayHasKey('fee_amount', $data);
            $this->assertArrayHasKey('transactions', $data);
            $this->assertIsArray($data['transactions']);
        }
    }


    public function testPaymentAddressCallbacks()
    {
        $response = self::$standard->merchantPaymentAddress(
            'btc',
        );

        $data = json_decode($response->getBody()->getContents(), true);

        if (!empty($data) && !empty($data[0]['payment_address'])) {

            $response = self::$standard->paymentAddressCallbacks(
            'btc',
                    $data[0]['payment_address']
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
                $this->assertArrayHasKey('callback_secret', $data);
            }
        }
    }


    public function testPaymentAddressState()
    {
        $response = self::$standard->merchantPaymentAddress(
            'btc',
        );

        $data = json_decode($response->getBody()->getContents(), true);

        if (!empty($data) && !empty($data[0]['payment_address'])) {

            $response = self::$standard->paymentAddressState(
                'btc',
                $data[0]['payment_address']
            );

            $data = json_decode($response->getBody()->getContents(), true);

            $this->assertEquals(200, $response->getStatusCode());
            $this->assertArrayHasKey('created_timestamp', $data);
            $this->assertArrayHasKey('merchant_id', $data);
            $this->assertArrayHasKey('payment_address', $data);
            $this->assertArrayHasKey('fee_type', $data);
            $this->assertArrayHasKey('callback_url', $data);
            $this->assertArrayHasKey('callback_secret', $data);
            $this->assertArrayHasKey('confirmed_payments', $data);
            $this->assertArrayHasKey('confirmed_payments_amount', $data);
            $this->assertArrayHasKey('pending_payments', $data);
            $this->assertArrayHasKey('pending_payments_amount', $data);
            $this->assertArrayHasKey('fee_amount', $data);
            $this->assertArrayHasKey('success_callbacks', $data);
            $this->assertArrayHasKey('failed_callbacks', $data);
        }
    }
}