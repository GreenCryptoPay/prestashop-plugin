<?php
/**
* 2007-2023 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2023 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class Greencryptopay extends PaymentModule
{
    const __MA_MAIL_DELIMITER__ = ',';

    const PENDING_STATUS_TITLE = 'Pending payment | Green Crypto Processing';
    const PAYED_STATUS_TITLE = 'Order paid | Green Crypto Processing';

    CONST AVAILABLE_CURRENCIES = [
        'usd'
    ];

    public function __construct()
    {
        $this->name = 'greencryptopay';
        $this->tab = 'payments_gateways';
        $this->version = '1.0.0';
        $this->author = 'GreenCryptoPay';

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Green Crypto Processing');
        $this->description = $this->l('Accept Bitcoin and other cryptocurrencies as a payment method with Green Crypto Processing');
        $this->confirmUninstall = $this->l('Are you sure you want to delete your details?');
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        if (extension_loaded('curl') == false)
        {
            $this->_errors[] = $this->l('You have to enable the cURL extension on your server to install this module');
            return false;
        }

        include(dirname(__FILE__).'/sql/install.php');

        $order_pending = new OrderState();
        $order_pending->name = array_fill(0, 10, self::PENDING_STATUS_TITLE);
        $order_pending->send_email = 0;
        $order_pending->invoice = 0;
        $order_pending->color = 'RoyalBlue';
        $order_pending->unremovable = false;
        $order_pending->logable = 0;

        $order_paid = new OrderState();
        $order_paid->name = array_fill(0, 10, self::PAYED_STATUS_TITLE);
        $order_paid->send_email = 1;
        $order_paid->invoice = 0;
        $order_paid->color = '#d9ff94';
        $order_paid->unremovable = false;
        $order_paid->logable = 0;
        $order_paid->template = 'payment';
        $order_paid->paid = true;

        $order_pending->add();
        $order_paid->add();

        Configuration::updateValue('GREENCRYPTOPAY_PENDING', $order_pending->id);
        Configuration::updateValue('GREENCRYPTOPAY_PAID', $order_paid->id);

        return parent::install() &&
            $this->registerHook('actionOrderStatusUpdate') &&
            $this->registerHook('payment') &&
            $this->registerHook('paymentReturn') &&
            $this->registerHook('paymentOptions');
    }

    public function uninstall()
    {
        include(dirname(__FILE__).'/sql/uninstall.php');

        $order_state_pending = new OrderState(Configuration::get('GREENCRYPTOPAY_PENDING'));
        $order_state_paid = new OrderState(Configuration::get('GREENCRYPTOPAY_PAID'));

        return (
            Configuration::deleteByName('GREENCRYPTOPAY_TESTNET') &&
            Configuration::deleteByName('GREENCRYPTOPAY_MERCHANT_ID') &&
            Configuration::deleteByName('GREENCRYPTOPAY_SECRET_KEY') &&
            Configuration::deleteByName('GREENCRYPTOPAY_NUMBER_OF_CONFIRMATIONS') &&
            Configuration::deleteByName('GREENCRYPTOPAY_REQUEST_SIGNATURE') &&
            Configuration::deleteByName('GREENCRYPTOPAY_TITLE') &&
            $order_state_pending->delete() &&
            $order_state_paid->delete() &&
            parent::uninstall()
        );
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        /**
         * If values have been submitted in the form, process.
         */
        if (((bool)Tools::isSubmit('submitGreencryptopayModule')) == true) {
            $this->postProcess();
        }

        $this->context->smarty->assign('module_dir', $this->_path);

        $output = $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');

        return $output.$this->renderForm();
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitGreencryptopayModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigForm()));
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        return array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Settings'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Testnet'),
                        'name' => 'GREENCRYPTOPAY_TESTNET',
                        'is_bool' => true,
                        'desc' => $this->l('Enable testnet'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'desc' => $this->l('Set merchant id see more https://greencryptopay.com/ru/standard'),
                        'name' => 'GREENCRYPTOPAY_MERCHANT_ID',
                        'label' => $this->l('Merchant id'),
                    ),
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'desc' => $this->l('Set secret key see more https://greencryptopay.com/ru/standard'),
                        'name' => 'GREENCRYPTOPAY_SECRET_KEY',
                        'label' => $this->l('Secret Key'),
                    ),
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'desc' => $this->l('Specify the number of confirmations for to confirm the payment'),
                        'name' => 'GREENCRYPTOPAY_NUMBER_OF_CONFIRMATIONS',
                        'label' => $this->l('Number of confirmations'),
                    ),
                    array(
                        'col' => 3,
                        'type' => 'hidden',
                        'desc' => $this->l('Arbitrary string for request signature.'),
                        'name' => 'GREENCRYPTOPAY_REQUEST_SIGNATURE',
                        'label' => $this->l('Request signature')
                    ),
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'desc' => $this->l('The payment method title which a customer sees at the checkout of your store.'),
                        'name' => 'GREENCRYPTOPAY_TITLE',
                        'label' => $this->l('Title'),
                    ),
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'desc' => $this->l('Link to open a wallet.'),
                        'name' => 'GREENCRYPTOPAY_WALLET_LINK',
                        'label' => $this->l('Wallet link'),
                    ),
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'desc' => $this->l('Time for payment in minutes.'),
                        'name' => 'GREENCRYPTOPAY_TIME_TO_PAY',
                        'label' => $this->l('Time to pay')
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }

    /**
     * Set values for the inputs.
     */
    public function getConfigFormValues()
    {
        $settings = array();
        $settings['GREENCRYPTOPAY_TESTNET'] = Configuration::get('GREENCRYPTOPAY_TESTNET', false, false, false, false);
        $settings['GREENCRYPTOPAY_MERCHANT_ID'] = Configuration::get('GREENCRYPTOPAY_MERCHANT_ID');
        $settings['GREENCRYPTOPAY_SECRET_KEY'] = Configuration::get('GREENCRYPTOPAY_SECRET_KEY');
        $settings['GREENCRYPTOPAY_NUMBER_OF_CONFIRMATIONS'] = Configuration::get('GREENCRYPTOPAY_NUMBER_OF_CONFIRMATIONS', false, false, false,  3);
        $settings['GREENCRYPTOPAY_REQUEST_SIGNATURE'] = Configuration::get('GREENCRYPTOPAY_REQUEST_SIGNATURE', false, false, false, md5(time() . random_bytes(10)));
        $settings['GREENCRYPTOPAY_TITLE'] = Configuration::get('GREENCRYPTOPAY_TITLE', false, false, false, $this->l('Cryptocurrencies via Green Crypto Processing'));
        $settings['GREENCRYPTOPAY_WALLET_LINK'] = Configuration::get('GREENCRYPTOPAY_WALLET_LINK');
        $settings['GREENCRYPTOPAY_TIME_TO_PAY'] = Configuration::get('GREENCRYPTOPAY_TIME_TO_PAY', false, false, false, 10);
        return $settings;
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $form_values = $this->getConfigFormValues();

        foreach (array_keys($form_values) as $key) {
            Configuration::updateValue($key, Tools::getValue($key));
        }
    }

    /**
     * This method is used to render the payment button,
     * Take care if the button should be displayed or not.
     */
    public function hookPayment($params)
    {
        $this->smarty->assign('module_dir', $this->_path);
        return $this->display(__FILE__, 'views/templates/hook/payment.tpl');
    }

    /**
     * This hook is used to display the order confirmation page.
     */
    public function hookPaymentReturn($params)
    {
        if ($this->active == false) {
            return;
        }

        $order = $params['objOrder'];

        if ($order->getCurrentOrderState()->id != Configuration::get('PS_OS_ERROR'))
            $this->smarty->assign('status', 'ok');

        $this->smarty->assign(array(
            'id_order' => $order->id,
            'reference' => $order->reference,
            'params' => $params,
            'total' => Tools::displayPrice($params['total_to_pay'], $params['currencyObj'], false),
        ));

        return $this->display(__FILE__, 'views/templates/hook/confirmation.tpl');
    }

    /**
     * Return payment options available for PS 1.7+
     *
     * @param array Hook parameters
     *
     * @return array|null
     */
    public function hookPaymentOptions($params)
    {
        if (!$this->active) {
            return;
        }
        /*if (!$this->checkCurrency($params['cart'])) {
            return;
        }*/

        $settingValues = $this->getConfigFormValues();

        $option = new \PrestaShop\PrestaShop\Core\Payment\PaymentOption();
        $option->setCallToActionText($settingValues['GREENCRYPTOPAY_TITLE'])
            ->setAction($this->context->link->getModuleLink($this->name, 'redirect', array(), true))
            ->setAdditionalInformation(
                $this->context->smarty->fetch('module:greencryptopay/views/templates/hook/greencryptopay_intro.tpl')
            );

        $payment_options = array($option);

        return $payment_options;
    }

    /**
     * @param $cart
     * @return bool
     */
    public function checkCurrency($cart)
    {
        $currency_order = new Currency($cart->id_currency);

        if (!in_array(strtolower($currency_order->iso_code), self::AVAILABLE_CURRENCIES)) {
            return false;
        }

        $currencies_module = $this->getCurrency($cart->id_currency);
        if (is_array($currencies_module)) {
            foreach ($currencies_module as $currency_module) {
                if ($currency_order->id == $currency_module['id_currency']) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * @param $params
     */
    public function hookActionOrderStatusUpdate($params)
    {
        if ($params['newOrderStatus']->name != self::PAYED_STATUS_TITLE) {
            return;
        }

        $context = Context::getContext();
        $locale = $context->language->getLocale();
        $id_lang = (int) $context->language->id;
        $id_shop = (int) $context->shop->id;

        $configuration = Configuration::getMultiple(
            [
                'PS_SHOP_EMAIL',
                'PS_MAIL_METHOD',
                'PS_MAIL_SERVER',
                'PS_MAIL_USER',
                'PS_MAIL_PASSWD',
                'PS_SHOP_NAME',
                'PS_MAIL_COLOR',
            ], $id_lang, null, $id_shop
        );

        // Shop iso
        $iso = Language::getIsoById((int) Configuration::get('PS_LANG_DEFAULT'));
        $merchant_new_order_emails = explode(self::__MA_MAIL_DELIMITER__, (string) Configuration::get('MA_MERCHANT_ORDER_EMAILS'));

        // Filling-in vars for email
        $template_vars = [
            '{id_order}' => $params['id_order'],
        ];

        foreach ($merchant_new_order_emails as $merchant_mail) {
            // Default language
            $mail_id_lang = $id_lang;
            $mail_iso = $iso;

            // Use the merchant lang if he exists as an employee
            $results = Db::getInstance()->executeS('
            SELECT `id_lang` FROM `' . _DB_PREFIX_ . 'employee`
            WHERE `email` = \'' . pSQL($merchant_mail) . '\'
        ');
            if ($results) {
                $user_iso = Language::getIsoById((int) $results[0]['id_lang']);
                if ($user_iso) {
                    $mail_id_lang = (int) $results[0]['id_lang'];
                    $mail_iso = $user_iso;
                }
            }

            $dir_mail = false;
            if (file_exists(dirname(__FILE__) . '/mails/' . $mail_iso . '/order_paid.txt') &&
                file_exists(dirname(__FILE__) . '/mails/' . $mail_iso . '/order_paid.html')) {
                $dir_mail = dirname(__FILE__) . '/mails/';
            }

            if (file_exists(_PS_MAIL_DIR_ . $mail_iso . '/order_paid.txt') &&
                file_exists(_PS_MAIL_DIR_ . $mail_iso . '/order_paid.html')) {
                $dir_mail = _PS_MAIL_DIR_;
            }

            if ($dir_mail) {
                Mail::send(
                    $mail_id_lang,
                    'order_paid',
                    $this->trans(
                        'Order paid : #%d',
                        [
                            $params['id_order'],
                        ],
                        'Emails.Subject',
                        $locale),
                    $template_vars,
                    $merchant_mail,
                    null,
                    $configuration['PS_SHOP_EMAIL'],
                    $configuration['PS_SHOP_NAME'],
                    null,
                    null,
                    $dir_mail,
                    false,
                    $id_shop
                );
            }
        }
    }

    public static function getContextLocale(Context $context)
    {
        $locale = $context->getCurrentLocale();
        if (null !== $locale) {
            return $locale;
        }

        $containerFinder = new \PrestaShop\PrestaShop\Adapter\ContainerFinder($context);
        $container = $containerFinder->getContainer();
        if (null === $context->container) {
            // @phpstan-ignore-next-line
            $context->container = $container;
        }

        /** @var \PrestaShop\PrestaShop\Core\Localization\CLDR\LocaleRepository $localeRepository */
        $localeRepository = $container->get(Controller::SERVICE_LOCALE_REPOSITORY);
        $locale = $localeRepository->getLocale(
            $context->language->getLocale()
        );

        // @phpstan-ignore-next-line
        return $locale;
    }

}
