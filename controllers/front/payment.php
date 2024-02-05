<?php

require_once(_PS_MODULE_DIR_ . 'greencryptopay/Helpers.php');

use Greencryptopay\Helpers;

class GreencryptopayPaymentModuleFrontController extends ModuleFrontController
{
    use Helpers;

    public function __construct()
    {
        parent::__construct();
        $this->setSettings();
    }

    public function initContent()
    {
        parent::initContent();

        $cart = $this->context->cart;

        if (!$this->module->checkCurrency($cart)) {
            Tools::redirect('index.php?controller=order');
        }

        $currency = new Currency((int) ($cart->id_currency));

        $this->checkSignature($_GET);

        $order = new Order((int) $_GET['order_id']);
        $greenCryptoPayData = Db::getInstance()->getRow('SELECT * from ' . _DB_PREFIX_ . 'greencryptopay WHERE order_id=' . pSQL((int) $_GET['order_id']) . ' ORDER BY ID desc');

        $this->context->smarty->assign(array(
            'order_id' => $_GET['order_id'],
            'payment_address' => $greenCryptoPayData['payment_address'],
            'total' => $order->total_paid,
            'amount' => $greenCryptoPayData['payment_amount'],
            'payment_method' => $greenCryptoPayData['payment_currency'],
            'currency' => $currency->iso_code,
            'assets_path' => _PS_BASE_URL_ . '/modules/greencryptopay/assets/',
            'wallet_link' => $this->wallet_link,
            'time_to_pay' => $this->time_to_pay
        ));

        $this->setTemplate('module:greencryptopay/views/templates/front/payment_execution.tpl');
    }
}
