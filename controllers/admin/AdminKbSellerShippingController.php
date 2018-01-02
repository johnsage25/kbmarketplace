<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 * We offer the best and most useful modules PrestaShop and modifications for your online store.
 *
 * @author    knowband.com <support@knowband.com>
 * @copyright 2017 Knowband
 * @license   see file: LICENSE.txt
 * @category  PrestaShop Module
 *
 */

require_once dirname(__FILE__) . '/AdminKbMarketplaceCoreController.php';

class AdminKbSellerShippingController extends AdminKbMarketplaceCoreController
{
    protected $seller_info = array();

    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'carrier';
        $this->className = 'Carrier';
        $this->identifier = 'id_carrier';
        $this->deleted = true;

        $this->lang = true;
        $this->display = 'list';

        parent::__construct();
        $this->toolbar_title = $this->module->l('Sellers Shippings');

        $this->fields_list = array(
            'id_carrier' => array(
                'title' => $this->module->l('ID'),
                'align' => 'text-center',
                'class' => 'fixed-width-xs'
            ),
            'email' => array(
                'title' => $this->module->l('Seller Email'),
                'havingFilter' => true,
                'filter_key' => 'c!email',
                'search' => true
            ),
            'name' => array(
                'title' => $this->module->l('Name')
            ),
            'image' => array(
                'title' => $this->module->l('Logo'),
                'align' => 'center',
                'image' => 's',
                'class' => 'fixed-width-xs',
                'orderby' => false,
                'search' => false
            ),
            'delay' => array(
                'title' => $this->module->l('Delay'),
                'orderby' => false
            ),
            'active' => array(
                'title' => $this->module->l('Status'),
                'callback' => 'showNonClickableStatus',
                'align' => 'center',
//                'type' => 'bool',
                'class' => 'fixed-width-sm',
                'orderby' => false,
            ),
            'is_free' => array(
                'title' => $this->module->l('Free Shipping'),
                'callback' => 'showNonClickableFreeStatus',
                'align' => 'center',
//                'type' => 'bool',
                'class' => 'fixed-width-sm',
                'orderby' => false,
            ),
            'position' => array(
                'title' => $this->module->l('Position'),
                'filter_key' => 'a!position',
                'align' => 'center',
                'class' => 'fixed-width-sm',
                'position' => 'position'
            )
        );

        $this->_select = 'c.`email`,a.`active` as c_active';

        $this->_join = '
            INNER JOIN ' . _DB_PREFIX_ . 'kb_mp_seller_shipping as sc on (a.id_carrier = sc.id_carrier) 
			INNER JOIN `' . _DB_PREFIX_ . 'kb_mp_seller` s ON (sc.`id_seller` = s.`id_seller`) 
			LEFT JOIN `' . _DB_PREFIX_ . 'customer` c ON (s.`id_customer` = c.`id_customer`)';
        $this->_where = 'AND b.id_lang='. (int) $this->context->language->id
            .' AND b.id_shop IN ('. (int) $this->context->shop->id.')';

        $this->_orderBy = 'a.id_carrier';
        $this->_orderWay = 'DESC';
        $this->bulk_actions = array();

        $this->addRowAction('view');
    }
    
    //Bulk enable selection
    protected function processBulkEnableSelection()
    {
        $this->context->cookie->__set(
            'kb_redirect_error',
            $this->module->l("Admin can not enable seller's shipping.")
        );
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminKbSellerShipping'));
    }
    
    //bulk disable selection
    protected function processBulkDisableSelection()
    {
        $this->context->cookie->__set(
            'kb_redirect_error',
            $this->module->l("Admin can not disable seller's shipping.")
        );
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminKbSellerShipping'));
    }
    
    //Process the bulk selection
    protected function processBulkActiveSelection()
    {
        $this->context->cookie->__set(
            'kb_redirect_error',
            $this->module->l("Admin can not enable/disable seller's shipping.")
        );
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminKbSellerShipping'));
    }
    
    public function postProcess()
    {
        parent::postProcess();
    }

    public function setMedia()
    {
        parent::setMedia();
    }

    public function initContent()
    {
        parent::initContent();
    }

    public function initToolbar()
    {
        parent::initToolbar();
        unset($this->toolbar_btn['new']);
    }

    public function initPageHeaderToolbar()
    {
        if (!empty($this->display) && $this->display == 'view') {
            $object = $this->loadObject(true);
            if (Validate::isLoadedObject($object)) {
                $this->seller_info = KbSellerShipping::getSellerDetailByShippingId($object->id);
                if (!empty($this->seller_info)) {
                    if (!empty($this->seller_info['title'])) {
                        $this->toolbar_title = sprintf(
                            $this->module->l('Carrier Name: %s, Seller: %s'),
                            $object->name,
                            $this->seller_info['title']
                        );
                    } else {
                        $this->toolbar_title = sprintf(
                            $this->module->l('Carrier Name: %s, Seller: %s'),
                            $object->name,
                            $this->seller_info['seller_name']
                        );
                    }
                } else {
                    $this->toolbar_title = sprintf(
                        $this->module->l('Carrier Name: %s, Seller: %s'),
                        $object->name,
                        '--'
                    );
                }
            }
            $this->page_header_toolbar_btn['cancel'] = array(
                'href' => self::$currentIndex . '&token=' . $this->token,
                'desc' => $this->module->l('Back to List'),
                'icon' => 'process-icon-cancel'
            );
        }

        parent::initPageHeaderToolbar();
    }

    public function renderView()
    {
        $object = new $this->className(
            Tools::getValue($this->identifier, 0),
            $this->context->language->id
        );
        if (!Validate::isLoadedObject($object)) {
            $this->context->cookie->__set(
                'kb_redirect_error',
                $this->module->l('You are trying to view information of unknown carrier.')
            );
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminKbSellerShipping'));
        } else {
            $customer = new Customer($this->seller_info['id_customer']);
            $gender = new Gender($customer->id_gender, $this->context->language->id);
            $seller_lang = new Language($this->seller_info['id_default_lang']);
            $shop = new Shop($this->seller_info['id_shop']);

            $taxas = Tax::getTaxes($this->context->language->id);
            $carrier_tax_id = Carrier::getIdTaxRulesGroupByIdCarrier($object->id);
            $tax_string = $this->l('No Tax');
            foreach ($taxas as $t) {
                if ($t['id_tax'] == $carrier_tax_id) {
                    $tax_string = $t['name'];
                    break;
                }
            }

            $billing_string = $this->module->l('[undefined]');
            $unit = $this->context->currency->sign;
            if ($object->shipping_method == Carrier::SHIPPING_METHOD_PRICE) {
                $billing_string = $this->module->l('According to total price');
            } elseif ($object->shipping_method == Carrier::SHIPPING_METHOD_WEIGHT) {
                $billing_string = $this->module->l('According to total weight');
                $unit = Configuration::get('PS_WEIGHT_UNIT');
            }

            $ship_grup = array();
            foreach ($object->getGroups() as $gr) {
                $ship_grup[] = $gr['id_group'];
            }
            $groups = array();
            foreach (Group::getGroups(Context::getContext()->language->id) as $gr) {
                if (in_array($gr['id_group'], $ship_grup)) {
                    $groups[] = $gr['name'];
                }
            }

            $payment_info = Tools::unSerialize($this->seller_info['payment_info']);
            if (isset($payment_info['name'])) {
                $payment_info['name'] = $this->getPaymentMethodname($payment_info['name']);
            } elseif (isset($payment_info['paypal_id'])) {
                $payment_info['name'] = $this->module->l('Paypal');
                $payment_info['data'] = array(
                    'paypal_id' => array(
                        'label' => $this->module->l('Paypal Id'),
                        'value' => $payment_info['paypal_id']
                    )
                );
            }

            $tpl_vars = array(
                'seller_info' => $this->seller_info,
                'payment_info' => $payment_info,
                'gender' => $gender,
                'carrier' => $object,
                'tax_string' => $tax_string,
                'billing_string' => $billing_string,
                'groups' => $groups,
                'unit' => $unit,
                'registration_date' => Tools::displayDate($this->seller_info['date_add'], null, true),
                'shop_is_feature_active' => Shop::isFeatureActive(),
                'name_shop' => $shop->name,
                'customerLanguage' => $seller_lang,
            );

            $this->getTplRangesVarsAndValues($object, $tpl_vars);

            $default_template_dir = $this->context->smarty->getTemplateDir(0);
            $this->context->smarty->setTemplateDir(
                _PS_MODULE_DIR_ . $this->kb_module_name . '/views/templates/admin/'
            );
            $this->override_folder = $this->tpl_folder = 'carrierview/';
            $helper = new HelperView($this);
            $this->setHelperDisplay($helper);
            $helper->tpl_vars = $tpl_vars;
            if (!is_null($this->base_tpl_view)) {
                $helper->base_tpl = $this->base_tpl_view;
            }
            $view = $helper->generateView();
            $this->context->smarty->setTemplateDir($default_template_dir);
            return $view;
        }
    }

    /*
     * Display free status without clickable
     */

    public function showNonClickableFreeStatus($id_row, $tr)
    {
        unset($id_row);
        if ($tr['is_free'] == 1) {
            return '<a class="list-action-enable action-enabled" href="javascript:void(0)" 
				title="' . $this->module->l('Yes') . '"><i class="icon-check"></i></a>';
        } else {
            return '<a class="list-action-enable action-disabled" href="javascript:void(0)" 
				title="' . $this->module->l('No') . '"><i class="icon-remove"></i></a>';
        }
    }

    protected function getTplRangesVarsAndValues($carrier, &$tpl_vars)
    {
        $carrier_zones = $carrier->getZones();
        $zones = array();
        if (is_array($carrier_zones)) {
            foreach ($carrier_zones as $carrier_zone) {
                $zones[] = $carrier_zone['name'];
            }
        }
        $shipping_method = $carrier->getShippingMethod();

        if ($shipping_method == Carrier::SHIPPING_METHOD_FREE) {
            $range_obj = $carrier->getRangeObject($carrier->shipping_method);
        } else {
            $range_obj = $carrier->getRangeObject();
        }

        $tmp_range = $range_obj->getRanges((int)$carrier->id);
        $range_inf = 0;
        $range_sup = 0;

        if ($shipping_method != Carrier::SHIPPING_METHOD_FREE) {
            foreach ($tmp_range as $range) {
                if ($range_inf == 0 || $range_inf > $range['delimiter1']) {
                    $range_inf = $range['delimiter1'];
                }

                if ($range_sup == 0 || $range_sup < $range['delimiter2']) {
                    $range_sup = $range['delimiter2'];
                }
            }
        }
        $tpl_vars['range_inf'] = $range_inf;
        $tpl_vars['range_sup'] = $range_sup;
        $tpl_vars['zones'] = $zones;
    }

    public function getPaymentMethodname($name)
    {
        $payment_methods =  array(
            'bankwire' => $this->module->l('Bank Wire'),
            'check' => $this->module->l('Payment by Check'),
            'kbpaypal' => $this->module->l('Paypal'),
            'creditcard' => $this->module->l('Credit Card')
        );
        return $payment_methods[$name];
    }
}
