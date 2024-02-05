<?php

require_once(_PS_MODULE_DIR_ . 'greencryptopay/Helpers.php');

use Greencryptopay\Helpers;

class GreencryptopayCallbackModuleFrontController extends ModuleFrontController
{
    use Helpers;

    public function __construct()
    {
        parent::__construct();
        $this->setSettings();
    }

    public function postProcess()
    {
        $result = [];

        $data = json_decode(file_get_contents('php://input'), true);
        $order = new Order((int) $data['order_id']);

        if (!$order || !$order->id) {
            throw new Exception('Order #' . $data['order_id'] . ' does not exists');
        }

        if ($order->payment != $this->module->displayName) {
            throw new Exception('Order #' . $data['order_id'] . ' payment method is not ' . $this->module->displayName);
        }

        $greenCryptoPayData = Db::getInstance()->getRow('SELECT * from ' . _DB_PREFIX_ . 'greencryptopay WHERE order_id=' . pSQL((int) $data['order_id']) . ' ORDER BY ID desc');

        if ($data['callback_secret'] !== $greenCryptoPayData['callback_secret']) {
            throw new Exception('Order #' . $data['order_id'] . ' unknown error');
        }

        if ($data['currency'] !== $greenCryptoPayData['payment_currency']) {
            throw new Exception('Order #' . $data['order_id'] . ' currency does not match');
        }

        if ($order->getCurrentState() == Configuration::get('GREENCRYPTOPAY_PENDING')) {
            if ($data['amount_received'] >= $greenCryptoPayData['payment_amount'] && $data['confirmations'] >= $this->number_of_confirmations) {
                $order->setCurrentState(Configuration::get('GREENCRYPTOPAY_PAID'));
                $result['stop'] = true;
            }
        }

        header('Content-Type: application/json');
        $this->ajax = true;
        $this->ajaxRender(json_encode($result));
    }
}
