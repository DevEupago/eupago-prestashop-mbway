<?php
/**
 * 2007-2015 PrestaShop
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
 * @author    euPago, Instituição de Pagamento Lda <suporte@eupago.pt>
 * @copyright 2016 euPago, Instituição de Pagamento Lda
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

use PrestaShop\PrestaShop\Core\Payment\PaymentOption;

include_once dirname(__FILE__) . '/../../classes/exception/PrestaShopPaymentException.php';

if (!defined('_PS_VERSION_')) {
    exit;
}

class EupagoMbw extends PaymentModule
{

    public $chave_api;
    public $subent_id;
    public $chaveAntiPhishing;
    public $cart;
    protected $config_form = false;

    public function __construct()
    {
        $this->module_key = '8adbcf80cb83b3ebce0c7865f2f44183';
        $this->name = 'eupagombw';
        $this->tab = 'payments_gateways';
        $this->version = '1.8.5';
        $this->author = 'euPago';
        $this->need_instance = 1;
        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);

        $config = Configuration::getMultiple(array('EUPAGO_MBWAY_CHAVE_API'));

        if (Tools::getIsset($config['EUPAGO_MBWAY_CHAVE_API'])) {
            $this->chave_api = $config['EUPAGO_MBWAY_CHAVE_API'];
        }

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;
        $this->currencies = true;

        parent::__construct();

        $this->page = basename(__FILE__, '.php');
        $this->displayName = $this->l('euPago - MB Way');
        $this->description = $this->l(
            "Portuguese payment method that sends to your customers MB WAY's app a request to accept the payment."
        );
        $this->confirmUninstall = $this->l('Tem a certeza de que quer desinstalar o MB WAY?');

        if ($config['EUPAGO_MBWAY_CHAVE_API'] == '') {
            $this->warning = $this->l('The key must be entered.');
        }

        $this->limited_currencies = array('EUR');

        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);

        $this->context->link->getModuleLink($this->name, 'display');
    }

    public function install()
    {
        if (extension_loaded('curl') == false) {
            $this->_errors[] = $this->l('You have to enable the cURL extension on your server to install this module');
            return false;
        }

        $this->createOrderState();

        $this->copyFilesEmails();

        include dirname(__FILE__) . '/sql/install.php';

        return parent::install() &&
        $this->registerHook('header') &&
        $this->registerHook('backOfficeHeader') &&
        $this->registerHook('paymentOptions') &&
        $this->registerHook('displayOrderDetail') &&
        $this->registerHook('displayAdminOrder') &&
        $this->registerHook('displayPayment') &&
        $this->registerHook('PaymentReturn');
    }

    /**
     * Create a new order state
     */
    public function createOrderState()
    {
        if (!Configuration::get('EUPAGO_A_AGUARDAR_PAGAMENTO_MBWAY')) {
            $order_state = new OrderState();
            $order_state->name = array();

            foreach (Language::getLanguages() as $language) {
                if (Tools::strtolower($language['iso_code']) == 'pt') {
                    $order_state->name[$language['id_lang']] = 'euPago - A aguardar pagamento por MB Way';
                } else {
                    $order_state->name[$language['id_lang']] = 'euPago - Waiting MB Way payment confirmation';
                }
            }

            $order_state->module_name = $this->name;
            $order_state->send_email = false;
            $order_state->color = '#ec2e15';
            $order_state->hidden = false;
            $order_state->delivery = false;
            $order_state->logable = false;
            $order_state->invoice = false;
            $order_state->unremovable = true;

            if ($order_state->add()) {
                $source = dirname(__FILE__) . '/views/img/0.gif';
                $destination = dirname(__FILE__) . '/../../img/os/' . (int) $order_state->id . '.gif';
                copy($source, $destination);
            }
            Configuration::updateValue('EUPAGO_A_AGUARDAR_PAGAMENTO_MBWAY', (int) $order_state->id);
        }

        if (!Configuration::get('EUPAGO_CONFIRMADO_PAGAMENTO_MBWAY')) {
            $order_state = new OrderState();
            $order_state->name = array();

            foreach (Language::getLanguages() as $language) {
                if (Tools::strtolower($language['iso_code']) == 'pt') {
                    $order_state->name[$language['id_lang']] = 'euPago - Confirmado pagamento por MB Way';
                } else {
                    $order_state->name[$language['id_lang']] = 'euPago - Accepted MB Way payment';
                }
            }

            $order_state->module_name = $this->name;
            $order_state->send_email = true;
            $order_state->template = "payment";
            $order_state->color = '#32CD32';
            $order_state->hidden = false;
            $order_state->delivery = false;
            $order_state->logable = false;
            $order_state->invoice = false;
            $order_state->unremovable = true;

            if ($order_state->add()) {
                $source = dirname(__FILE__) . '/views/img/0.gif';
                $destination = dirname(__FILE__) . '/../../img/os/' . (int) $order_state->id . '.gif';
                copy($source, $destination);
            }

            Configuration::updateValue('EUPAGO_CONFIRMADO_PAGAMENTO_MBWAY', (int) $order_state->id);
        }
        if (!Configuration::get("DESCRICAO_MBWAY")) {
            Configuration::updateValue("DESCRICAO_MBWAY", " ");
        }
    }

    /**
     * Copy files emails templates
     */
    public function copyFilesEmails()
    {
        copy(
            dirname(__FILE__) . '/mails/en/order_conf_eupagombw.txt',
            dirname(__FILE__) . '/../../mails/en/order_conf_eupagombw.txt'
        ) &&
        copy(
            dirname(__FILE__) . '/mails/en/order_conf_eupagombw.html',
            dirname(__FILE__) . '/../../mails/en/order_conf_eupagombw.html'
        ) &&
        copy(
            dirname(__FILE__) . '/mails/pt/order_conf_eupagombw.txt',
            dirname(__FILE__) . '/../../mails/pt/order_conf_eupagombw.txt'
        ) &&
        copy(
            dirname(__FILE__) . '/mails/pt/order_conf_eupagombw.html',
            dirname(__FILE__) . '/../../mails/pt/order_conf_eupagombw.html'
        );
    }

    public function uninstall()
    {
        Configuration::deleteByName('EUPAGO_MBWAY_LIVE_MODE');

        $this->deleteFilesEmails();

        $this->deleteOrderStates();

        include dirname(__FILE__) . '/sql/uninstall.php';

        return parent::uninstall();
    }

    /**
     * Delete files emails templates when unistall module
     */
    public function deleteFilesEmails()
    {
        unlink(dirname(__FILE__) . '/../../mails/en/order_conf_eupagombw.txt') &&
        unlink(dirname(__FILE__) . '/../../mails/en/order_conf_eupagombw.html') &&
        unlink(dirname(__FILE__) . '/../../mails/pt/order_conf_eupagombw.txt') &&
        unlink(dirname(__FILE__) . '/../../mails/pt/order_conf_eupagombw.html');
    }

    /**
     * Delete order states of module when module uninstall
     */
    public function deleteOrderStates()
    {
        Db::getInstance()->delete(
            'prefix_order_state_lang',
            '`id_order_state`  = ' . Configuration::get('EUPAGO_A_AGUARDAR_PAGAMENTO_MBWAY')
        );
        Db::getInstance()->delete(
            'prefix_order_state_lang',
            '`id_order_state`  = ' . Configuration::get('EUPAGO_CONFIRMADO_PAGAMENTO_MBWAY')
        );
        Db::getInstance()->delete(
            'prefix_order_state',
            '`id_order_state`  = ' . Configuration::get('EUPAGO_A_AGUARDAR_PAGAMENTO_MBWAY')
        );
        Db::getInstance()->delete(
            'prefix_order_state',
            '`id_order_state`  = ' . Configuration::get('EUPAGO_CONFIRMADO_PAGAMENTO_MBWAY')
        );
    }

    public function smartCopy($source, $dest, $options = array('folderPermission' => 0755, 'filePermission' => 0755))
    {
        $result = false;

        if (is_file($source)) {
            if ($dest[Tools::strlen($dest) - 1] == '/') {
                if (!file_exists($dest)) {
                    mkdir($dest, $options['folderPermission'], true);
                }
                $__dest = $dest . "/" . basename($source);
            } else {
                $__dest = $dest;
            }
            $result = copy($source, $__dest);
            chmod($__dest, $options['filePermission']);
        } elseif (is_dir($source)) {
            if ($dest[Tools::strlen($dest) - 1] == '/') {
                if ($source[Tools::strlen($source) - 1] == '/') {
                    //Copy only contents
                } else {
                    //Change parent itself and its contents
                    $dest = $dest . basename($source);
                    @mkdir($dest);
                    chmod($dest, $options['filePermission']);
                }
            } else {
                if ($source[Tools::strlen($source) - 1] == '/') {
                    //Copy parent directory with new name and all its content
                    @mkdir($dest, $options['folderPermission']);
                    chmod($dest, $options['filePermission']);
                } else {
                    //Copy parent directory with new name and all its content
                    @mkdir($dest, $options['folderPermission']);
                    chmod($dest, $options['filePermission']);
                }
            }

            $dirHandle = opendir($source);
            while ($file = readdir($dirHandle)) {
                if ($file != "." && $file != "..") {
                    if (!is_dir($source . "/" . $file)) {
                        $__dest = $dest . "/" . $file;
                    } else {
                        $__dest = $dest . "/" . $file;
                    }

                    $result = $this->smartCopy($source . "/" . $file, $__dest, $options);
                }
            }
            closedir($dirHandle);
        } else {
            $result = false;
        }
        return $result;
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        /**
         * If values have been submitted in the form, process.
         */

        $notification = null;
        if (((bool) Tools::isSubmit('submitEupago_mbwayModule')) == true) {
            $chave = Tools::getValue('EUPAGO_MBWAY_CHAVE_API');

            if (!$chave ||
                empty($chave) ||
                !Validate::isGenericName($chave)
            ) {
                $notification = false;
            } else {
                $notification = true;
                $this->postProcess();
            }
        }

        $this->context->smarty->assign('module_dir', $this->_path);

        if (isset($notification)) {
            $output = $notification ?
            $this->displayConfirmation(
                $this->l('Settings updated')
            ) :
            $this->displayError(
                $this->l('Invalid Configuration value')
            );
            return $output . $this->renderForm();
        } else {
            return $this->renderForm();
        }
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
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        return array(
            'EUPAGO_A_AGUARDAR_PAGAMENTO_MBWAY' => Configuration::get('EUPAGO_A_AGUARDAR_PAGAMENTO_MBWAY', null),
            'EUPAGO_CONFIRMADO_PAGAMENTO_MBWAY' => Configuration::get('EUPAGO_CONFIRMADO_PAGAMENTO_MBWAY', null),
            'DESCRICAO_MBWAY' => Configuration::get('DESCRICAO_MBWAY', null),

            'EUPAGO_MBWAY_CHAVE_API' => Configuration::get('EUPAGO_MBWAY_CHAVE_API', null),
        );
    }

    /*
     * Check if order id already exist in eupago_mbway DB
     * @param int $order_id
     */

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
        $helper->submit_action = 'submitEupago_mbwayModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
        . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
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
        $lang = Configuration::get('PS_LANG_DEFAULT', null);
        $sql = 'SELECT * FROM ' .
        _DB_PREFIX_ . 'order_state_lang,' .
        _DB_PREFIX_ . 'order_state WHERE ' .
        _DB_PREFIX_ . 'order_state_lang.id_order_state=' .
        pSQL(_DB_PREFIX_ . 'order_state.id_order_state') . ' and ' .
        _DB_PREFIX_ . 'order_state.deleted=' . (int) 0 . ' and ' .
        _DB_PREFIX_ . 'order_state_lang.id_lang=' . (int) $lang;
        $estados = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        return array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Settings'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'EUPAGO_A_AGUARDAR_PAGAMENTO_MBWAY' => Configuration::get(
                            'EUPAGO_A_AGUARDAR_PAGAMENTO_MBWAY',
                            null
                        ),

                        'type' => 'select',
                        'label' => $this->l('State of Awaiting payment:'),
                        'name' => 'EUPAGO_A_AGUARDAR_PAGAMENTO_MBWAY',

                        'required' => false,
                        'options' => array(
                            'query' => $estados,
                            'id' => 'id_order_state',
                            'name' => 'name',
                        ),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('State of Success payment:'),
                        'name' => 'EUPAGO_CONFIRMADO_PAGAMENTO_MBWAY',
                        'required' => false,

                        'options' => array(
                            'query' => $estados,
                            'id' => 'id_order_state',
                            'name' => 'name',
                        ),
                    ),
                    array(
                        'col' => 7,
                        'type' => 'text',
                        'label' => $this->l('Description (visible on MBWay App):'),
                        'name' => 'DESCRICAO_MBWAY',
                        'required' => false,

                    ),
                    array(
                        'col' => 7,
                        'type' => 'text',
                        'prefix' => '<i class="icon icon-key"></i>',
                        'desc' => $this->l(
                            'This key is provided by euPago if you don´t have it please contact us - www.eupago.pt'
                        ),
                        'name' => 'EUPAGO_MBWAY_CHAVE_API',
                        'label' => $this->l('Api key'),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }

    /**
     * Load currency
     *
     * @param object $cart
     */
    public function checkCurrency($cart)
    {
        $currency_order = new Currency((int) ($cart->id_currency));
        $currencies_module = $this->getCurrency((int) $cart->id_currency);

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
     * This method is used to render the payment button in version 1.7,
     * Take care if the button should be displayed or not.
     */
    public function hookPaymentOptions($params)
    {
        $payment_option = array();

        if (!$this->active) {
            return;
        }

        $currency_id = $params['cart']->id_currency;
        $currency = new Currency((int) $currency_id);

        if (in_array($currency->iso_code, $this->limited_currencies) == false) {
            return false;
        }

        $this->smarty->assign('module_dir', $this->_path);
        $this->smarty->assign('cart', $params['cart']);

        $newOption = new PaymentOption();

        $newOption->setModuleName($this->name)
            ->setCallToActionText("MB Way")
            ->setAction($this->context->link->getModuleLink($this->name, 'redirect'))
            ->setLogo(Media::getMediaPath(_PS_MODULE_DIR_ . $this->name . '/views/img/eupagombw.png'));

        $payment_option[] = $newOption;

        return $payment_option;
    }

    /**
     * Add the CSS & JavaScript files you want to be loaded in the BO.
     */
    public function hookBackOfficeHeader()
    {
        if (Tools::getValue('module_name') == $this->name) {
            $this->context->controller->addJS($this->_path . 'views/js/back.js');
            $this->context->controller->addCSS($this->_path . 'views/css/back.css');
        }
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        $this->context->controller->addJS($this->_path . '/views/js/front.js', 'all');
        $this->context->controller->addCSS($this->_path . '/views/css/front.css', 'all');
    }

    /**
     * Payment execute
     *
     * @param object $cart
     */
    public function execPayment($cart)
    {
        if (!$this->active) {
            return;
        }

        $this->context->smarty->assign(
            array(
                'nbProducts' => $cart->nbProducts(),
                'cust_currency' => $cart->id_currency,
                'currencies' => $this->getCurrency((int) $this->cart->id_currency),
                'total' => $cart->getOrderTotal(true, Cart::BOTH),
                'isoCode' => Language::getIsoById((int) ($this->cookie->id_lang)),
                'subent_id' => $this->subent_id,
                'this_path' => $this->_PS_MODULE_DIR_ . 'modules/' . $this->name . '/',
            )
        );
        //return $this->display(__FILE__, 'views/templates/hook/executar_mbway.tpl');
    }

    /**
     * Return hook payment
     *
     * @param object $params
     */
    public function hookPaymentReturn($params)
    {

        if ($this->active == false) {
            return;
        }

        $order = (_PS_VERSION_ >= '1.7' ? $params['order'] : $params['objOrder']);

        $exist = $this->getOrderIdObjectFromEupagoTable($order->id);

        if ($exist) {
            $result = (object) $exist[0];
            $result->estado = 0;
        } else {
            $result->estado = 1;
        }

        if ($result->estado != 0) {
            $history = new OrderHistory();
            $history->id_order = (int) $order->id;
            $erro = "Erro: " . $result->resposta;
            $history->changeIdOrderState((int) Configuration::get('PS_OS_ERROR'), (int) ($order->id));
            $this->smarty->assign('status', 'Nok');
            $this->smarty->assign('erro', $erro);
        } else {
            $telemovel = explode("#", $result->alias);

            $this->smarty->assign('resposta', 'OK');
            $this->smarty->assign('shop_name', $this->context->shop->name);
            $this->smarty->assign('telemovel', $telemovel[1]);
            $this->smarty->assign('id_pedido', $result->referencia);
            $this->smarty->assign('valor', Tools::displayPrice($result->valor, null, false));

            $this->sendEmailPaymentDetails($order, $result);

            $this->smarty->assign(
                array(
                    'id_order' => $order->id,
                    'reference' => $order->reference,
                    'params' => $params,
                    'total' => Tools::displayPrice($order->total_paid, null, false),
                    'module_dir' => $this->_path,
                    'estado_mbway' => $result->estado,
                )
            );
        }

        return $this->fetch(_PS_MODULE_DIR_ . 'eupagombw/views/templates/front/dados_mbway.tpl');
    }

    public function getOrderIdObjectFromEupagoTable($order_id)
    {
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'eupago_mbway where order_id = ' . (int) $order_id;
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    }

    /**
     * Send email with payment details
     *
     * @param object $order
     * @param object $result
     */
    public function sendEmailPaymentDetails($order, $result)
    {
        if (Validate::isEmail($this->context->customer->email)) {
            $email_tpl_vars = $this->getEmailVars($order, $result);
            $lang = new Language($order->id_lang);
            $diretorio = str_replace(
                "//",
                "/",
                _PS_MODULE_DIR_ . '/' . $this->name . '/mails/' . $lang->iso_code . '/'
            );
            $subject = ($lang->iso_code == "pt") ? 'Aguardar Pagamento' : 'Waiting for payment';

            Mail::Send(
                (int) $order->id_lang,
                'order_conf_eupagombw',
                Mail::l($subject, (int) $order->id_lang),
                $email_tpl_vars,
                $this->context->customer->email,
                $this->context->customer->firstname . ' ' . $this->context->customer->lastname,
                null,
                null,
                null,
                null,
                $diretorio,
                false,
                (int) $order->id_shop
            );
        }
    }

    /**
     * Return array
     *
     * @param object $order
     * @param object $result
     */
    public function getEmailVars($order, $result)
    {
        $telemovel = explode("#", $result->alias);

        $data = array(
            '{firstname}' => $this->context->customer->firstname,
            '{lastname}' => $this->context->customer->lastname,
            '{email}' => $this->context->customer->email,
            '{order_name}' => $order->getUniqReference(),
            '{referencia}' => $result->referencia,
            '{mobile_number}' => $telemovel[1],
            '{total_paid}' => Tools::displayPrice($order->total_paid, $this->context->currency, false),
            '{this_path}' => _PS_BASE_URL_ . __PS_BASE_URI__ . '/modules/' . $this->name,
            '{shop_name}' => $this->context->shop->name,

        );

        return $data;
    }

    /**
     * Soap call and generate reference
     *
     * @param object $order
     */
    public function generateReference($id, $total_paid, $num_tel)
    {
        //VAI BUSCAR AS VARIAVEIS CONFIGURADAS NO BACKOFFICE
        $chave_api = Configuration::get('EUPAGO_MBWAY_CHAVE_API');

        // PREPARA O URL DA CHAMADA
        $demo = explode("-", $chave_api);
        if ($demo['0'] == 'demo') {
            $url_curl = 'https://sandbox.eupago.pt/clientes/rest_api/mbway/create';
        } else {
            $url_curl = 'https://seguro.eupago.pt/clientes/rest_api/mbway/create';
        }

        $num_tel = Tools::getValue('alias');
        $arraydados = array(
            "chave" => $chave_api,
            "valor" => $total_paid,
            "id" => $id,
            "alias" => $num_tel,
            "descricao" => Configuration::get("DESCRICAO_MBWAY"),
        );

        $reposta = $this->curlRequest($url_curl, $arraydados);
        $result = Tools::jsonDecode($reposta);

        if ($result->estado == 0) {
            $this->saveResults($result, $id);
        }

        return $result;
    }

    /**
     * Call cURL
     *
     * @param object $url
     * @param string $post
     * @param int $retries
     */
    private function curlRequest($url, $post = null, $retries = 3)
    {
        $curl = curl_init($url);
        $result = array();
        if (is_resource($curl) === true) {
            curl_setopt($curl, CURLOPT_FAILONERROR, true);
            curl_setopt($curl, CURLOPT_ENCODING, "");
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_USERAGENT, "euPago");
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 120);
            curl_setopt($curl, CURLOPT_TIMEOUT, 120);

            if (isset($post) === true) {
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt(
                    $curl,
                    CURLOPT_POSTFIELDS,
                    (is_array($post) === true) ? http_build_query($post, '', '&') : $post
                );
            }

            $result = false;

            while (($result === false) && (--$retries > 0)) {
                $result['resultado'] = curl_exec($curl);
                $result['estado'] = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            }

            curl_close($curl);
        }
        //var_dump($result);
        return $result['resultado'];
    }

    /**
     * Save result from euPago server in DB
     *
     * @param object $result
     * @param int $order_id
     */
    public function saveResults($result, $order_id)
    {
        Db::getInstance()->insert(
            'eupago_mbway',
            array(
                'id_eupago_mbway' => '',
                'order_id' => (int) $order_id,
                'valor' => (float) $result->valor,
                'alias' => pSQL($result->alias),
                'referencia' => pSQL($result->referencia),
                'chave' => pSQL(Tools::getValue('key')),
                'estado_mbway' => 'pendente',
            )
        );
    }

    /**
     * Create new register MB Way payment
     *
     * @param object $order
     * @param string alias
     * @param string referencia
     * @param int valor
     * @param int estado
     */
    public function setEupagombwOrderDb($order, $alias, $referencia, $valor, $estado)
    {
        Db::getInstance()->insert(
            'eupago_mbway',
            array(
                'id_eupago_mbway' => pSQL(''),
                'order_id' => (int) $order,
                'alias' => pSQL($alias),
                'valor' => (float) $valor,
                'referencia' => pSQL($referencia),
                'estado_mbway' => pSQL($estado),
            )
        );
    }

    /**
     * Show register from order_id of MB Way payment
     *
     * @param int order_id
     */
    public function getEupagoMbwData($order_id)
    {
        $dados = Db::getInstance()->getRow(
            '
		SELECT *
		FROM `' . _DB_PREFIX_ . 'eupago_mbway`
		WHERE `order_id`=\'' . (int) $order_id . '\' and `chave` is null
		ORDER BY id_eupago_mbway desc
		'
        );

        return $dados;
    }

    public function centerTrim($str)
    {
        return preg_replace("/\s+/", "", $str);
    }

    /**
     * Function for callback handling
     *
     * @param string referencia
     * @param int valor
     * @param string chave
     */
    public function callback($referencia, $valor, $chave)
    {
        $chaveReg = Configuration::get('EUPAGO_MBWAY_CHAVE_API');

        $context = Context::getContext();
        $context->link = new Link();
        if ($chave == $chaveReg) {
            $valor = str_replace(',', '.', $valor);
            //echo $referencia;
            $orderId = $this->getEupagoMbwOrderDb($referencia, $valor);
            if ($orderId) {
                $query = "UPDATE `" .
                _DB_PREFIX_ . "orders` SET current_state=" .
                (int) Configuration::get('EUPAGO_CONFIRMADO_PAGAMENTO_MBWAY') . " WHERE id_order = " . (int) $orderId;
                Db::getInstance()->Execute($query);
                $query = "INSERT INTO `" . _DB_PREFIX_ .
                "order_history`(id_employee,id_order,id_order_state,date_add) values(0," .
                $orderId . "," .
                (int) Configuration::get('EUPAGO_CONFIRMADO_PAGAMENTO_MBWAY') . ",now());";
                Db::getInstance()->Execute($query);

                $this->updateEupagoMbwOrderDb($orderId);
                $this->updateValidateOrder($orderId, $valor);
                echo 'Atualizada para paga.'; //atualizada para paga
                return "Atualizada para paga.";
            } else {
                echo 'Referência já paga.'; //Já paga
            }
        } else {
            echo 'Chave API inválida'; //Chave inválida
        }
    }

    /**
     * Show register from valor and referencia of MB Way payment
     *
     * @param string referencia
     * @param int valor
     */
    public function getEupagoMbwOrderDb($referencia, $valor)
    {
        $valor = (str_replace(',', '.', $valor));
        $pagamentos = Db::getInstance()->getRow(
            '
		SELECT order_id
		FROM  ' . _DB_PREFIX_ . 'eupago_mbway
		WHERE referencia = ' . pSQL($referencia) . ' and valor=' . (float) $valor . ' and estado_mbway = \'pendente\'
		ORDER BY id_eupago_mbway desc'
        );

        return $pagamentos['order_id'];
    }

    /*
     * GET order validate and update total_paid_real in Orders DB by order and paid value
     */

    /**
     * Update register from order_id of MB Way payment
     *
     * @param int order_id
     */
    public function updateEupagoMbwOrderDb($orderId)
    {
        Db::getInstance()->Execute('
				UPDATE `' . _DB_PREFIX_ . 'eupago_mbway`
				SET `chave` = \'PAGO\',
					`estado_mbway` = \'pago\'
				WHERE `order_id`=' . (int) $orderId);
    }

    public function updateValidateOrder($order_id, $valor)
    {
        $query = "UPDATE `" . _DB_PREFIX_ . "orders` SET total_paid_real=" . (float) $valor .
        ", valid=1 WHERE id_order = " . (int) $order_id;
        Db::getInstance()->Execute($query);
    }

    /**
     * Function for display order from backOffice
     *
     * @param object params
     */
    public function hookdisplayAdminOrder($params)
    {
        $order_id = $params['id_order'];

        $referencia = Db::getInstance()->getRow(
            '
		SELECT *
		FROM `' . _DB_PREFIX_ . 'eupago_mbway`
		WHERE `order_id`=\'' . (int) $order_id . '\'
		ORDER BY id_eupago_mbway desc
		'
        );

        $this->context->smarty->assign('module_dir', $this->_path);

        if ($referencia) {
            $n_telemovel = explode("#", $referencia['alias']);

            $this->context->smarty->assign(
                array(
                    'alias' => $n_telemovel[1],
                    'referencia' => $referencia['referencia'],
                    'valor' => $referencia['valor'],
                    'order_id' => $order_id,
                    'token' => $this->context->controller->token,
                    'estadoenvio' => Tools::getValue("estadoenvio"),
                    'estadolembrete' => Tools::getValue("estadolembrete"),
                    'this_path' => _PS_MODULE_DIR_ . $this->name . '/',
                )
            );

            return $this->display(__FILE__, 'views/templates/admin/admin_dados_mbway.tpl');
        } else {
            return "";
        }
    }

    public function hookDisplayOrderDetail($params)
    {
        $order = $params['order'];

        if ($order->payment != $this->name) {
            return;
        }

        $dados = $this->getOrderIdObjectFromEupagoTable($order->id);

        if ($dados) {
            $telemovel = explode("#", $dados[0]['alias']);

            $this->smarty->assign(
                array(
                    'modules_dir' => $this->_path,
                    'telemovel' => $telemovel[1],
                    'referencia' => $dados[0]['referencia'],
                    'total' => $dados[0]['valor'],
                )
            );
        } else {
            return false;
        }

        return $this->display(__FILE__, 'views/templates/front/paymentDetails.tpl');
    }

    /**
     * @param Object Address $the_address that needs to be txt formated
     * @return String the txt formated address block
     */
    protected function getFormatedAddress(Address $the_address, $line_sep, $fields_style = array())
    {
        return AddressFormat::generateAddress($the_address, array('avoid' => array()), $line_sep, ' ', $fields_style);
    }
}
