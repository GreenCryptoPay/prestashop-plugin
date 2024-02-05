<?php

require_once(_PS_MODULE_DIR_ . 'greencryptopay/Helpers.php');

use Greencryptopay\Helpers;

class GreencryptopayRedirectModuleFrontController extends ModuleFrontController
{
    use Helpers;

    const PAY_PAGE_URL = 'index.php?fc=module&module=greencryptopay&controller=payment';

    const TO_CURRENCIES = [
        'btc'
    ];

    const FROM_CURRENCIES = [
        'usd'
    ];

    public function __construct()
    {
        parent::__construct();
        $this->setSettings();
    }

    public function initContent()
    {
        parent::initContent();

        if ($this->module->active == false) {
            die;
        }

        $cart_id = Context::getContext()->cart->id;
        $customer_id = Context::getContext()->customer->id;
        $amount = Context::getContext()->cart->getOrderTotal();

        if (!$this->module->checkCurrency(Context::getContext()->cart)) {
            Tools::redirect('index.php?controller=order');
        }

        Context::getContext()->cart = new Cart((int) $cart_id);
        Context::getContext()->customer = new Customer((int) $customer_id);
        Context::getContext()->currency = new Currency((int) Context::getContext()->cart->id_currency);
        Context::getContext()->language = new Language((int) Context::getContext()->customer->id_lang);

        $secure_key = Context::getContext()->customer->secure_key;
        $module_name = $this->module->displayName;
        $currency_id = (int) Context::getContext()->currency->id;

        $this->module->validateOrder($cart_id, Configuration::get('GREENCRYPTOPAY_PENDING'), $amount, $module_name, null, array(), $currency_id, false, $secure_key);

        $order = Context::getContext()->cart;
        $client = $this->make_client();
        $total = Context::getContext()->cart->getOrderTotal();

        $to_currency = self::TO_CURRENCIES[0];
        $from_currency = self::FROM_CURRENCIES[0];

        $context = Context::getContext();
        $link = $context->link->getModuleLink('greencryptopay', 'callback');

        $response = $client->paymentAddress(
            $to_currency,
            $link,
            (string) $order->id,
            $from_currency,
            (float) $total
        );

        Db::getInstance()->insert('greencryptopay', array(
            'order_id' => pSQL($order->id),
            'callback_secret' => pSQL($response['callback_secret']),
            'payment_currency' => pSQL($to_currency),
            'payment_amount' => pSQL($response['amount']),
            'payment_address' => pSQL($response['payment_address'])
        ));

        $query_string = [
            'order_id' => $order->id
        ];

        $query_string['signature'] = $this->makeSignature($query_string);

        $paymentUrl = $context->link->getModuleLink('greencryptopay', 'payment');

        Tools::redirect($paymentUrl . '&' . http_build_query($query_string));
    }
}
