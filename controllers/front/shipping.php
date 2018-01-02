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

require_once 'KbCore.php';

class KbmarketplaceShippingModuleFrontController extends KbmarketplaceCoreModuleFrontController
{
    public $controller_name = 'shipping';
    public $seller_carrier;

    public function __construct()
    {
        parent::__construct();
    }

    public function setMedia()
    {
        parent::setMedia();
        if (Tools::getIsset('action')) {
            if (Tools::getValue('action') == 'edit') {
                $this->addCSS($this->getKbModuleDir() . 'views/css/front/kb-forms.css');
                $this->addJqueryPlugin('typewatch');
                $this->addJs($this->getKbModuleDir() . 'views/js/front/shipping.js');
            }
        }
    }

    public function postProcess()
    {
        parent::postProcess();
        if (Tools::isSubmit('ajax')) {
            $this->json = array();
            $renderhtml = false;
            if (Tools::isSubmit('method')) {
                switch (Tools::getValue('method')) {
                    case 'getSellerShippings':
                        $this->json = $this->getAjaxSellerShippingsListHtml();
                        break;
                    case 'getSellerProducts':
                        $this->json = $this->getAjaxSellerProductListHtml(Tools::getValue('id_carrier', 0));
                        break;
                    case 'getRequestDetail':
                        $this->json = $this->getAjaxRequestDetail();
                        break;
                    case 'uploadLogo':
                        $this->json = $this->ajaxProcessUploadLogo();
                        break;
                    case 'changeRanges':
                        $this->ajaxProcessChangeRanges();
                        break;
                    case 'finish':
                        $this->json = $this->ajaxProcessFinish();
                        break;
                }
            }
            if (!$renderhtml) {
                echo Tools::jsonEncode($this->json);
            }
            die;
        } elseif (Tools::isSubmit('multiaction') && Tools::getValue('multiaction')) {
            $this->processMultiAction();
        }
        if (Tools::isSubmit('kbshipping_logo')) {
            $shipping_logo = Tools::getValue('shipping_logo');
            if ($shipping_logo != '') {
                if (file_exists($shipping_logo)) {
                    unlink($shipping_logo);
                    echo 'removed';
                    die;
                } else {
                    echo 'not_exist';
                    die;
                }
            }
        }
    }

    public function initContent()
    {
        if (Tools::getIsset('action')) {
            if (Tools::getValue('action') == 'edit') {
                $this->renderShippingForm();
            } elseif (Tools::getValue('action') == 'delete') {
                $this->deleteShipping(Tools::getValue('id_carrier', 0));
            } elseif (Tools::getValue('action') == 'mapping') {
                $this->getSellerProductList(Tools::getValue('id_carrier', 0));
            }
        } else {
            $this->renderShippingList();
        }
        parent::initContent();
    }
    
    public function getTemplateVarPage()
    {
        $page = parent::getTemplateVarPage();
        if (isset($page['meta']) && $this->seller_info) {
            $page_title = 'Shippings';
            $page['meta']['title'] =  $page_title;
            $page['meta']['keywords'] = $this->seller_info['meta_keyword'];
            $page['meta']['description'] = $this->seller_info['meta_description'];
        }
        return $page;
    }
    
    private function renderShippingList()
    {
        $this->context->smarty->assign(
            'new_shipping_link',
            $this->context->link->getModuleLink(
                $this->kb_module_name,
                $this->controller_name,
                array('action' => 'edit', 'id_carrier' => 0),
                (bool)Configuration::get('PS_SSL_ENABLED')
            )
        );

        $this->total_records = KbSellerShipping::getSellerShippings(
            $this->seller_obj->id,
            $this->context->language->id,
            true
        );

        if ($this->total_records > 0) {
            $statuses = array(
                array('value' => 1, 'label' => $this->module->l('Yes')),
                array('value' => 0, 'label' => $this->module->l('No'))
            );

            $this->filter_header = $this->module->l('Filter Your Search');
            $this->filter_id = 'seller_shipping_list';
            $this->filters = array(
                array(
                    'type' => 'select',
                    'placeholder' => 'Select',
                    'name' => 'is_free',
                    'label' => $this->module->l('Free Shipping'),
                    'values' => $statuses
                ),
                array(
                    'type' => 'select',
                    'placeholder' => $this->module->l('Select'),
                    'name' => 'active',
                    'label' => $this->module->l('Status'),
                    'values' => $statuses
                )
            );
            $this->filter_action_name = 'getSellerShippings';
            $this->context->smarty->assign('kbfilter', $this->renderKbListFilter());

            $this->table_id = $this->filter_id;
            $this->table_header = array(
                array(
                    'label' => $this->module->l('ID'),
                    'align' => 'right',
                    'width' => '60'
                ),
                array(
                    'label' => $this->module->l('Name'),
                    'align' => 'left',
                ),
                array(
                    'label' => $this->module->l('Logo'),
                    'align' => 'left',
                    'class' => '',
                    'width' => '70',
                ),
                array(
                    'label' => $this->module->l('Delay'),
                    'align' => 'left',
                ),
                array(
                    'label' => $this->module->l('Status'),
                    'align' => 'left'
                ),
                array(
                    'label' => $this->module->l('Free Shipping'),
                    'align' => 'left',
                ),
                array(
                    'label' => $this->module->l('Action'),
                    'align' => 'left',
                )
            );

            $shippings = KbSellerShipping::getSellerShippings(
                $this->seller_obj->id,
                $this->context->language->id,
                false,
                $this->getPageStart(),
                $this->tbl_row_limit
            );
            foreach ($shippings as $ct) {
                $actions = array();
                if ($ct['is_default_shipping'] == 0) {
                    $actions = array(
                        array(
                            'type' => 'edit',
                            'href' => $this->context->link->getModuleLink(
                                $this->kb_module_name,
                                $this->controller_name,
                                array('action' => 'edit', 'id_carrier' => $ct['id_carrier']),
                                (bool)Configuration::get('PS_SSL_ENABLED')
                            )
                        ),
                        array(
                            'type' => 'delete',
                            'href' => $this->context->link->getModuleLink(
                                $this->kb_module_name,
                                $this->controller_name,
                                array('action' => 'delete', 'id_carrier' => $ct['id_carrier']),
                                (bool)Configuration::get('PS_SSL_ENABLED')
                            )
                        ),
                        array(
                            'type' => 'extra',
                            'href' => $this->context->link->getModuleLink(
                                $this->kb_module_name,
                                $this->controller_name,
                                array('action' => 'mapping', 'id_carrier' => $ct['id_carrier']),
                                (bool)Configuration::get('PS_SSL_ENABLED')
                            ),
                            'title' => $this->module->l('Click to map product(s)'),
                            'label' => $this->module->l('Mapping'),
                        )
                    );
                }
                $yes_txt = $this->module->l('Yes');
                $no_txt = $this->module->l('No');
                $this->table_content[] = array(
                    array(
                        'value' => '#' . $ct['id_carrier'],
                        'class' => 'kb-tright'
                    ),
                    array('value' => str_replace(' - ' . $this->seller_info['seller_name'], '', $ct['name'])),
                    array(
                        'value' => $this->getShippingLogo($ct['id_carrier']),
                        'class' => 'kb-tcenter'
                    ),
                    array('value' => $ct['delay']),
                    array(
                        'value' => (($ct['active']) ? $yes_txt : $no_txt)
                    ),
                    array(
                        'value' => (($ct['is_free']) ? $yes_txt : $no_txt)
                    ),
                    array(
                        'input' => array('type' => 'action'),
                        'class' => 'kb-tcenter',
                        'actions' => $actions
                    )
                );
            }

            $this->list_row_callback = $this->filter_action_name;
        }

        $this->context->smarty->assign('kblist', $this->renderKbList());

        $this->setKbTemplate('seller/shipping/list.tpl');
    }

    private function renderShippingForm()
    {
        $id_carrier = (int)Tools::getValue('id_carrier');

        $show_form = true;
        if ($id_carrier != 0 && !KbSellerShipping::isSellerShipping($this->seller_obj->id, $id_carrier)) {
            $show_form = false;
        }
        if ($show_form) {
            $this->context->smarty->assign(
                'shipping_submit_url',
                $this->context->link->getModuleLink(
                    $this->kb_module_name,
                    $this->controller_name,
                    array('action' => 'edit', 'id_carrier' => 0),
                    (bool)Configuration::get('PS_SSL_ENABLED')
                )
            );

            $this->context->smarty->assign(
                'shipping_method_price',
                Carrier::SHIPPING_METHOD_PRICE
            );
            $this->context->smarty->assign(
                'shipping_method_weight',
                Carrier::SHIPPING_METHOD_WEIGHT
            );

            $carrier = new Carrier($id_carrier, $this->context->language->id);
            if ($id_carrier == 0) {
                $this->context->smarty->assign(
                    'form_heading',
                    $this->module->l('Add New Shipping')
                );
                $this->context->smarty->assign('mapping_shipping', 1);
            } else {
                $this->context->smarty->assign(
                    'form_heading',
                    $this->module->l('Edit') . ': ' . $carrier->name
                );
            }
            $this->context->smarty->assign('carrier', $carrier);

            $this->getTplRangesVarsAndValues($carrier);

            $image_path = KbGlobal::getBaseLink((bool)Configuration::get('PS_SSL_ENABLED'));

            if (Tools::file_exists_no_cache(_PS_SHIP_IMG_DIR_ . $carrier->id . '.jpg')) {
                $this->context->smarty->assign(
                    'shipping_logo',
                    str_replace(_PS_ROOT_DIR_ . '/', '', $image_path . _PS_SHIP_IMG_DIR_) . $carrier->id . '.jpg'
                );
                $this->context->smarty->assign(
                    'is_kb_shipping_logo_updated',
                    1
                );
                $this->context->smarty->assign(
                    'kb_shipping_logo_path',
                    _PS_ROOT_DIR_ .'/img/s/'. $carrier->id . '.jpg'
                );
            } else {
                $this->context->smarty->assign(
                    'shipping_logo',
                    str_replace(_PS_ROOT_DIR_ . '/', '', $image_path . _PS_IMG_DIR_) . 'admin/carrier-default.jpg'
                );
                $this->context->smarty->assign(
                    'is_kb_shipping_logo_updated',
                    0
                );
            }

            $this->context->smarty->assign(
                'ship_default_logo',
                str_replace(_PS_ROOT_DIR_ . '/', '', $image_path . _PS_IMG_DIR_) . 'admin/carrier-default.jpg'
            );
            $this->context->smarty->assign(
                'kb_shipping_url',
                $this->context->link->getModuleLink(
                    $this->kb_module_name,
                    $this->controller_name,
                    array(),
                    (bool)Configuration::get('PS_SSL_ENABLED')
                )
            );
            $this->context->smarty->assign('shipping_id_seller', $this->seller_obj->id);
            $this->context->smarty->assign('default_lang', $this->context->language->id);
            $this->context->smarty->assign('ps_dimension_unit', Configuration::get('PS_DIMENSION_UNIT'));
            $this->context->smarty->assign('ps_weight_unit', Configuration::get('PS_WEIGHT_UNIT'));
            $this->context->smarty->assign('ps_currency_unit', $this->seller_currency->sign);
            $this->context->smarty->assign('kb_img_frmats', $this->img_formats);
            $this->setKbTemplate('seller/shipping/form.tpl');
        } else {
            $redirect_link = $this->context->link->getModuleLink(
                $this->kb_module_name,
                $this->controller_name,
                array(),
                (bool)Configuration::get('PS_SSL_ENABLED')
            );

            $this->context->cookie->__set(
                'redirect_success',
                $this->module->l('The requested shipping method is not yours')
            );
            Tools::redirect($redirect_link);
        }
    }

    protected function getTplRangesVarsAndValues($carrier)
    {
        $tpl_vars = array();
        $zones = Zone::getZones(false);
        $tpl_vars['zones'] = $zones;
        $carrier_zones = $carrier->getZones();
        $carrier_zones_ids = array();
        if (is_array($carrier_zones)) {
            foreach ($carrier_zones as $carrier_zone) {
                $carrier_zones_ids[] = $carrier_zone['id_zone'];
            }
        }

        $range_table = $carrier->getRangeTable();
        $shipping_method = $carrier->getShippingMethod();

        foreach ($zones as $zone) {
            $tpl_vars['fields_value']['zones'][$zone['id_zone']] = Tools::getValue(
                'zone_' . $zone['id_zone'],
                (in_array($zone['id_zone'], $carrier_zones_ids))
            );
        }

        if ($shipping_method == Carrier::SHIPPING_METHOD_FREE) {
            $range_obj = $carrier->getRangeObject($carrier->shipping_method);
            $price_by_range = array();
        } else {
            $range_obj = $carrier->getRangeObject();
            $price_by_range = Carrier::getDeliveryPriceByRanges($range_table, (int)$carrier->id);
        }

        foreach ($price_by_range as $price) {
            $tpl_vars['price_by_range'][$price['id_' . $range_table]][$price['id_zone']] = $price['price'];
        }

        $tmp_range = $range_obj->getRanges((int)$carrier->id);
        $tpl_vars['ranges'] = array();
        if ($shipping_method != Carrier::SHIPPING_METHOD_FREE) {
            foreach ($tmp_range as $range) {
                $tpl_vars['ranges'][$range['id_' . $range_table]] = $range;
                $tpl_vars['ranges'][$range['id_' . $range_table]]['id_range'] = $range['id_' . $range_table];
            }
        }

        // init blank range
        if (!count($tpl_vars['ranges'])) {
            $tpl_vars['ranges'][] = array('id_range' => 0, 'delimiter1' => 0, 'delimiter2' => 0);
        }
        //d($tpl_vars);
        $this->context->smarty->assign($tpl_vars);
    }

    protected function getShippingLogo($id_carrier)
    {
        $path_to_image = _PS_IMG_DIR_ . 's/' . (int)$id_carrier . '.jpg';

        if (Tools::file_exists_no_cache($path_to_image)) {
            return ImageManager::thumbnail(
                $path_to_image,
                'carrier_mini_' . $id_carrier . '_' . $this->context->shop->id . '.jpg',
                45,
                'jpg'
            );
        } else {
            return '--';
        }
    }

    public function ajaxProcessChangeRanges()
    {
        $id_carrier = Tools::getValue('id_carrier');
        $shipping_method = Tools::getValue('shipping_method');
        if ($id_carrier != 0 && !KbSellerShipping::isSellerShipping($this->seller_obj->id, $id_carrier)) {
            die(Tools::displayError('The requested shipping method is not yours'));
        }
        if (!in_array($shipping_method, array(Carrier::SHIPPING_METHOD_PRICE, Carrier::SHIPPING_METHOD_WEIGHT))) {
            die(Tools::displayError('Billing type is not valid'));
        }

        $carrier = new Carrier($id_carrier, $this->context->language->id);
        $carrier->shipping_method = $shipping_method;
        $this->context->smarty->assign('carrier', $carrier);

        $this->context->smarty->assign(
            'shipping_method_price',
            Carrier::SHIPPING_METHOD_PRICE
        );
        $this->context->smarty->assign(
            'shipping_method_weight',
            Carrier::SHIPPING_METHOD_WEIGHT
        );

        $this->context->smarty->assign('zones', $this->getFieldValue($carrier, 'zones'));
        $this->getTplRangesVarsAndValues($carrier);
        $this->context->smarty->assign('change_ranges', 1);
        $this->context->smarty->assign('ps_weight_unit', Configuration::get('PS_WEIGHT_UNIT'));
        $this->context->smarty->assign('ps_currency_unit', $this->seller_currency->sign);

        $this->setKbTemplate('seller/shipping/ranges.tpl');
        die($this->context->smarty->fetch($this->kbtemplate));
    }

    private function ajaxProcessFinish()
    {
        $return = array('has_error' => false);
        //return $return;
        $id_carrier = (int)Tools::getValue('id_carrier');
        if ($id_carrier != 0 && !KbSellerShipping::isSellerShipping($this->seller_obj->id, $id_carrier)) {
            $return['has_error'] = true;
            $return['errors'][] = Tools::displayError('The requested shipping method is not yours');
        } else {
            if ($id_carrier > 0) {
                $current_carrier = new Carrier((int)$id_carrier);

                // if update we duplicate current Carrier
                /** @var Carrier $new_carrier */
                $new_carrier = $current_carrier->duplicateObject();

                if (Validate::isLoadedObject($new_carrier)) {
                    $this->copyFromPost($new_carrier);

                    if ($error_mesg = $new_carrier->validateFields(false, true) !== true) {
                        $return['has_error'] = true;
                        $return['errors'][] = $error_mesg;
                    }

                    if ($error_mesg = $new_carrier->validateFieldsLang(false, true) !== true) {
                        $return['has_error'] = true;
                        $return['errors'][] = $error_mesg;
                    }

                    if (!$return['has_error']) {
                        // Set flag deteled to true for historization
                        $current_carrier->deleted = true;
                        $current_carrier->update();

                        // Fill the new carrier object
                        $new_carrier->position = $current_carrier->position;
                        $new_carrier->shipping_handling = false;
                        
                        if ($new_carrier->update()) {
                            $seller_shipping = new KbSellerShipping(
                                KbSellerShipping::getIdByReference($new_carrier->id_reference, $this->seller_obj->id)
                            );

                            $seller_shipping->id_carrier = $new_carrier->id;
                            $seller_shipping->save();
                        }

                        $this->duplicateLogo((int)$new_carrier->id, (int)$current_carrier->id);

                        if (count($this->Kberrors)) {
                            $return['has_error'] = true;
                            $return['errors'] = array_merge($return['errors'], $this->Kberrors);
                        }

                        $carrier = $new_carrier;
                    }
                }
            } else {
                $carrier = new Carrier();
                $carrier->shipping_handling = false;
                $this->copyFromPost($carrier);
                if ($error_mesg = $carrier->validateFields(false, true) !== true) {
                    $return['has_error'] = true;
                    $return['errors'][] = $error_mesg;
                }

                if ($error_mesg = $carrier->validateFieldsLang(false, true) !== true) {
                    $return['has_error'] = true;
                    $return['errors'][] = $error_mesg;
                }
                if (!$return['has_error']) {
                    if (!$carrier->add()) {
                        $return['has_error'] = true;
                        $return['errors'][] = $this->l('An error occurred while saving this carrier.');
                    } else {
                        KbSellerShipping::mapWithPaymentModules($carrier->id);
                        $seller_shipping = new KbSellerShipping();
                        $seller_shipping->id_seller = $this->seller_obj->id;
                        $seller_shipping->id_carrier = $carrier->id;
                        $seller_shipping->id_reference = $carrier->id;
                        $seller_shipping->save();
                        //mapped shipping with all sellers products if checked mapped all
                        $this->mappedShipping($carrier->id);
                    }
                }
            }

            if ($carrier->is_free) {
                //if carrier is free delete shipping cost
                $carrier->deleteDeliveryPrice('range_weight');
                $carrier->deleteDeliveryPrice('range_price');
            }

            if (Validate::isLoadedObject($carrier)) {
                $carrier->setGroups(KbSellerShipping::getCustomerGroups());

                $success = true;
                $zones = Zone::getZones(false);
                foreach ($zones as $zone) {
                    if (count($carrier->getZone($zone['id_zone']))) {
                        if (!Tools::getIsset('zone_' . $zone['id_zone'])
                            || !Tools::getValue('zone_' . $zone['id_zone'])) {
                            $success = $carrier->deleteZone((int)$zone['id_zone']);
                        }
                    } elseif (Tools::getIsset('zone_' . $zone['id_zone'])
                        && Tools::getValue('zone_' . $zone['id_zone'])) {
                        $success = $carrier->addZone((int)$zone['id_zone']);
                    }
                }

                if (!$success) {
                    $return['has_error'] = true;
                    $return['errors'][] = $this->module->l(
                        'An error occurred while saving carrier zones.'
                    );
                }

                if (!$carrier->is_free) {
                    if (!$this->processRanges((int)$carrier->id)) {
                        $return['has_error'] = true;
                        $return['errors'][] = $this->module->l(
                            'An error occurred while saving carrier ranges.'
                        );
                    }
                }

                $definition = ObjectModel::getDefinition($carrier);
                $shop_mapping = array(
                    array(
                        $definition['primary'] => (int)$carrier->id,
                        'id_shop' => (int)$this->seller_obj->id_shop
                    )
                );

                Db::getInstance()->insert(
                    pSQL($definition['table']) . '_shop',
                    $shop_mapping,
                    false,
                    true,
                    (int) Db::INSERT_IGNORE
                );

                if (!$carrier->setTaxRulesGroup(0)) {
                    $return['has_error'] = true;
                    $return['errors'][] = $this->module->l(
                        'An error occurred while saving the tax rules group.'
                    );
                }

                if (Tools::getValue('logo')) {
                    if (Tools::getValue('logo') == 'null' && file_exists(_PS_SHIP_IMG_DIR_ . $carrier->id . '.jpg')) {
                        unlink(_PS_SHIP_IMG_DIR_ . $carrier->id . '.jpg');
                    } else {
                        $logo = basename(Tools::getValue('logo'));
                        if (!file_exists(_PS_TMP_IMG_DIR_ . $logo)
                            || !copy(_PS_TMP_IMG_DIR_ . $logo, _PS_SHIP_IMG_DIR_ . $carrier->id . '.jpg')) {
                            $return['has_error'] = true;
                            $return['errors'][] = $this->module->l(
                                'An error occurred while saving carrier logo.'
                            );
                        }
                    }
                }
                $return['id_carrier'] = $carrier->id;
            }
        }

        if (!$return['has_error']) {
            $this->context->cookie->__set(
                'redirect_success',
                $this->module->l('Shipping successfully saved')
            );
        }
        return $return;
    }

    private function copyFromPost(&$object)
    {
        foreach ($_POST as $key => $value) {
            if (array_key_exists($key, $object) && $key != 'id_product') {
                $object->{$key} = $value;
            }
        }

        /* Multilingual fields */
        $languages = Language::getLanguages(false);
        $class_vars = get_class_vars(get_class($object));
        $fields = array();
        if (isset($class_vars['definition']['fields'])) {
            $fields = $class_vars['definition']['fields'];
        }

        foreach ($fields as $field => $params) {
            if (array_key_exists('lang', $params) && $params['lang']) {
                $default_val = Tools::getValue(
                    $field . '_' . (int)$this->context->language->id
                );
                foreach ($languages as $language) {
                    if (Tools::getIsset($field . '_' . (int)$language['id_lang'])) {
                        $object->{$field}[(int)$language['id_lang']] = Tools::getValue(
                            $field . '_' . (int)$language['id_lang']
                        );
                    } elseif (isset($object->{$field}[(int)$language['id_lang']])) {
                        $object->{$field}[(int)$language['id_lang']] = $object->{$field}[(int)$language['id_lang']];
                    } else {
                        $object->{$field}[(int)$language['id_lang']] = $default_val;
                    }
                }
            }
        }
    }

    public function duplicateLogo($new_id, $old_id)
    {
        $old_logo = _PS_SHIP_IMG_DIR_ . '/' . (int)$old_id . '.jpg';
        if (file_exists($old_logo)) {
            copy($old_logo, _PS_SHIP_IMG_DIR_ . '/' . (int)$new_id . '.jpg');
        }

        $old_tmp_logo = _PS_TMP_IMG_DIR_ . '/carrier_mini_' . (int)$old_id . '.jpg';
        if (file_exists($old_tmp_logo)) {
            if (!isset($_FILES['logo'])) {
                copy($old_tmp_logo, _PS_TMP_IMG_DIR_ . '/carrier_mini_' . $new_id . '.jpg');
            }
            unlink($old_tmp_logo);
        }
    }

    public function processRanges($id_carrier)
    {
        $carrier = new Carrier((int)$id_carrier);

        $range_inf = Tools::getValue('range_inf');
        $range_sup = Tools::getValue('range_sup');
        $range_type = Tools::getValue('shipping_method');

        $fees = Tools::getValue('fees');

        $carrier->deleteDeliveryPrice($carrier->getRangeTable());
        if ($range_type != Carrier::SHIPPING_METHOD_FREE) {
            foreach ($range_inf as $key => $delimiter1) {
                if (!isset($range_sup[$key])) {
                    continue;
                }
                $add_range = true;
                if ($range_type == Carrier::SHIPPING_METHOD_WEIGHT) {
                    if (!RangeWeight::rangeExist(
                        null,
                        (float)$delimiter1,
                        (float)$range_sup[$key],
                        $carrier->id_reference
                    )) {
                        $range = new RangeWeight();
                    } else {
                        $range = new RangeWeight((int)$key);
                        $range->id_carrier = (int)$carrier->id;
                        $range->save();
                        $add_range = false;
                    }
                }

                if ($range_type == Carrier::SHIPPING_METHOD_PRICE) {
                    if (!RangePrice::rangeExist(
                        null,
                        (float)$delimiter1,
                        (float)$range_sup[$key],
                        $carrier->id_reference
                    )) {
                        $range = new RangePrice();
                    } else {
                        $range = new RangePrice((int)$key);
                        $range->id_carrier = (int)$carrier->id;
                        $range->save();
                        $add_range = false;
                    }
                }
                if ($add_range) {
                    $range->id_carrier = (int)$carrier->id;
                    $range->delimiter1 = (float)$delimiter1;
                    $range->delimiter2 = (float)$range_sup[$key];
                    $range->save();
                }

                if (!Validate::isLoadedObject($range)) {
                    return false;
                }
                $price_list = array();
                if (is_array($fees) && count($fees)) {
                    foreach ($fees as $id_zone => $fee) {
                        $price_list[] = array(
                            'id_range_price' => ($range_type == Carrier::SHIPPING_METHOD_PRICE
                                ? (int)$range->id : null),
                            'id_range_weight' => ($range_type == Carrier::SHIPPING_METHOD_WEIGHT
                                ? (int)$range->id : null),
                            'id_carrier' => (int)$carrier->id,
                            'id_zone' => (int)$id_zone,
                            'price' => isset($fee[$key]) ? (float)str_replace(',', '.', $fee[$key]) : 0,
                        );
                    }
                }

                if (count($price_list) && !$carrier->addDeliveryPrice($price_list, true)) {
                    return false;
                }
            }
        }
        return true;
    }

    public function ajaxProcessUploadLogo()
    {
        $json = array();
        $logo = (isset($_FILES['file']) ? $_FILES['file'] : false);

        if ($logo && !empty($logo['tmp_name'][0]) && $logo['tmp_name'][0] != 'none'
            && (!isset($logo['error'][0]) || !$logo['error'][0])
            && preg_match('/\.(jpe?g|gif|png)$/', $logo['name'][0])
            && is_uploaded_file($logo['tmp_name'][0])
            && ImageManager::isRealImage($logo['tmp_name'][0], $logo['type'][0])) {
            $file = $logo['tmp_name'][0];
            do {
                $tmp_name = uniqid() . '.jpg';
            } while (file_exists(_PS_TMP_IMG_DIR_ . $tmp_name));

            if (!ImageManager::resize($file, _PS_TMP_IMG_DIR_ . $tmp_name)) {
                $json['error'] = sprintf(
                    $this->module->l('Impossible to resize the image into %s'),
                    Tools::safeOutput(_PS_TMP_IMG_DIR_)
                );
            }
            @unlink($file);
            $json['success'] = Tools::safeOutput(_PS_TMP_IMG_ . $tmp_name);
        } else {
            $json['error'] = $this->module->l('Cannot upload file');
        }

        return $json;
    }

    protected function deleteShipping($id_carrier)
    {
        $redirect_link = $this->context->link->getModuleLink(
            $this->kb_module_name,
            $this->controller_name,
            array(),
            (bool)Configuration::get('PS_SSL_ENABLED')
        );
        if (!KbSellerShipping::isSellerShipping($this->seller_obj->id, $id_carrier)) {
            $this->context->cookie->__set(
                'redirect_success',
                $this->module->l('The requested shipping method is not yours')
            );
        }
        $object = new Carrier($id_carrier);
        $object->deleted = 1;
        if ($object->update()) {
            $this->context->cookie->__set(
                'redirect_success',
                $this->module->l('Shipping method successfully deleted.')
            );
        } else {
            $this->context->cookie->__set(
                'redirect_error',
                $this->module->l('Not able to delete requested shipping method.')
            );
        }

        Tools::redirect($redirect_link);
    }

    private function getAjaxSellerShippingsListHtml()
    {
        $json = array();

        $custom_filter = '';
        if (Tools::getIsset('active') && Tools::getValue('active') != '') {
            $custom_filter .= ' AND c.active = "' . pSQL(Tools::getValue('active')) . '"';
        }

        if (Tools::getIsset('is_free') && Tools::getValue('is_free') != '') {
            $custom_filter .= ' AND c.is_free = "' . pSQL(Tools::getValue('is_free')) . '"';
        }

        $this->total_records = KbSellerShipping::getSellerShippings(
            $this->seller_obj->id,
            $this->context->language->id,
            true,
            null,
            null,
            null,
            null,
            $custom_filter
        );
        if ($this->total_records > 0) {
            if (Tools::getIsset('start') && (int)Tools::getValue('start') > 0) {
                $this->page_start = (int)Tools::getValue('start');
            }

            $this->table_id = 'seller_shipping_list';

            $results = KbSellerShipping::getSellerShippings(
                $this->seller_obj->id,
                $this->context->language->id,
                false,
                $this->getPageStart(),
                $this->tbl_row_limit,
                null,
                null,
                $custom_filter
            );

            $row_html = '';
            foreach ($results as $result) {
                $actions = array();
                if ($result['is_default_shipping'] == 0) {
                    $actions = array(
                        array(
                            'type' => 'edit',
                            'href' => $this->context->link->getModuleLink(
                                $this->kb_module_name,
                                $this->controller_name,
                                array('action' => 'edit', 'id_carrier' => $result['id_carrier']),
                                (bool)Configuration::get('PS_SSL_ENABLED')
                            )
                        ),
                        array(
                            'type' => 'delete',
                            'href' => $this->context->link->getModuleLink(
                                $this->kb_module_name,
                                $this->controller_name,
                                array('action' => 'delete', 'id_carrier' => $result['id_carrier']),
                                (bool)Configuration::get('PS_SSL_ENABLED')
                            )
                        )
                    );
                }

                $yes_txt = $this->module->l('Yes');
                $row_html .= '<tr>
						<td class="kb-tright ">#' . $result['id_carrier'] . '</td>
						<td>' . str_replace(' - ' . $this->seller_info['seller_name'], '', $result['name']) . '</td>
                        <td class="kb-tcenter ">' . $this->getShippingLogo($result['id_carrier']) . '</td>
                        <td>' . $result['delay'] . '</td>
                        <td>'
                        . (($result['active']) ? $yes_txt : $this->module->l('No'))
                        . '</td>
                        <td>'
                        . (($result['is_free']) ? $yes_txt : $this->module->l('No'))
                        . '</td>';
                $row_html .= '<td class="kb-tcenter">';
                if (!empty($actions)) {
                    foreach ($actions as $action) {
                        if ($action['type'] == 'edit') {
                            $row_html .= '<a class="kb_list_action" href="' . $action['href'] . '" >'
                                . $this->module->l('Edit') . '</a> ';
                        } elseif ($action['type'] == 'delete') {
                            $row_html .= '<a class="kb_list_action" 
                                href="javascript:void(0)" data-href="' . $action['href'] . '" >'
                                . $this->module->l('Delete') . '</a>';
                        }
                    }
                } else {
                    $row_html .= '--';
                }
                $row_html .= '</td>';
                $row_html .= '</tr>';
            }

            $this->list_row_callback = 'getSellerShippings';
            $json['status'] = true;
            $json['html'] = $row_html;
            $json['pagination'] = $this->generatePaginator(
                $this->page_start,
                $this->total_records,
                $this->getTotalPages(),
                $this->list_row_callback
            );
        } else {
            $json['status'] = false;
            $json['msg'] = $this->module->l('No Data Found');
        }
        return $json;
    }

    public function getSellerProductList($id_carrier)
    {
        $redirect_link = $this->context->link->getModuleLink(
            $this->kb_module_name,
            $this->controller_name,
            array(),
            (bool)Configuration::get('PS_SSL_ENABLED')
        );
        if (!KbSellerShipping::isSellerShipping($this->seller_obj->id, $id_carrier)) {
            $this->context->cookie->__set(
                'redirect_success',
                $this->module->l('The requested shipping method is not yours')
            );
            Tools::redirect($redirect_link);
        }
        $this->seller_carrier = new Carrier($id_carrier);
        $this->renderProductList($this->seller_carrier->id_reference);
    }

    public function renderProductList($id_reference)
    {
        $this->total_records = KbSellerProduct::getSellerProducts($this->seller_info['id_seller'], true);

        if ($this->total_records > 0) {
            $categories = $this->getCategoryList();

            $filter_category_list = array();
            foreach ($categories as $cat) {
                $filter_category_list[] = array('value' => $cat['id_category'], 'label' => $cat['name']);
            }

            $tmp = KbGlobal::getApporvalStatus();
            $approve_statuses = array();
            foreach ($tmp as $key => $val) {
                $approve_statuses[] = array('value' => $key, 'label' => $val);
            }

            $this->filter_header = $this->module->l('Filter Your Search', 'Product');
            $this->filter_id = 'seller_shipping_mapping_product';
            $this->filters = array(
                array(
                    'type' => 'text',
                    'name' => 'reference',
                    'label' => $this->module->l('Reference', 'Product'),
                ),
                array(
                    'type' => 'text',
                    'name' => 'name',
                    'label' => $this->module->l('Product Name', 'Product'),
                ),
                array(
                    'type' => 'select',
                    'placeholder' => $this->module->l('Select', 'Product'),
                    'name' => 'id_category_default',
                    'label' => $this->module->l('Default Category', 'Product'),
                    'values' => $filter_category_list,
                    'validate' => 'isInt'
                ),
                array(
                    'type' => 'select',
                    'placeholder' => $this->module->l('Select', 'Product'),
                    'name' => 'approved',
                    'label' => $this->module->l('Status', 'Product'),
                    'values' => $approve_statuses,
                    'validate' => 'isInt'
                ),
                array(
                    'type' => 'select',
                    'placeholder' => $this->module->l('Select', 'Product'),
                    'name' => 'active',
                    'label' => $this->module->l('Active', 'Product'),
                    'values' => array(
                        array('value' => 0, 'label' => $this->module->l('No', 'Product')),
                        array('value' => 1, 'label' => $this->module->l('Yes', 'Product'))),
                    'validate' => 'isInt'
                ),
                array(
                    'type' => 'select',
                    'placeholder' => $this->module->l('Select', 'Product'),
                    'name' => 'mapped_status',
                    'label' => $this->module->l('Mapped Status'),
                    'values' => array(
                        array('value' => 0, 'label' => $this->module->l('No', 'Product')),
                        array('value' => 1, 'label' => $this->module->l('Yes', 'Product'))),
                    'validate' => 'isInt'
                ),
                array(
                    'type' => 'hidden',
                    'name' => 'id_carrier',
                    'default' => $this->seller_carrier->id,
                ),
            );
            $this->filter_action_name = 'getSellerProducts';
            $this->context->smarty->assign('kbfilter', $this->renderKbListFilter());

            $this->table_id = $this->filter_id;
            $this->table_header = array(
                array(
                    'label' => $this->module->l('ID'),
                    'align' => 'right',
                    'class' => '',
                    'width' => '60'
                ),
                array(
                    'label' => $this->module->l('Product Name'),
                    'align' => 'left',
                    'class' => '',
                ),
                array(
                    'label' => $this->module->l('Reference'),
                    'align' => 'left',
                    'class' => '',
                    'width' => '120',
                ),
                array(
                    'label' => $this->module->l('Type'),
                    'align' => 'left',
                ),
                array(
                    'label' => $this->module->l('Status'),
                    'align' => 'left',
                    'width' => '80',
                ),
                array(
                    'label' => $this->module->l('Active'),
                    'align' => 'left',
                    'class' => '',
                    'width' => '40',
                ),
                array(
                    'label' => $this->module->l('Mapped Status'),
                    'align' => 'left',
                    'class' => '',
                    'width' => '40',
                ),
                array(
                    'label' => $this->module->l('Action'),
                    'align' => 'left',
                    'class' => '',
                    'width' => '90',
                )
            );

            $orderby = null;
            if (Tools::getIsset('orderby') && Tools::getValue('orderby') != '') {
                $orderby = Tools::getValue('orderby');
            }

            $orderway = null;
            if (Tools::getIsset('orderway') && Tools::getValue('orderway') != '') {
                $orderway = Tools::getValue('orderway');
            }

            $sellers_products = KbSellerProduct::getSellerProducts(
                $this->seller_info['id_seller'],
                false,
                $this->getPageStart(),
                $this->tbl_row_limit,
                $orderby,
                $orderway
            );

            foreach ($sellers_products as $val) {
                $product = new Product($val['id_product'], false, $this->seller_info['id_default_lang']);
                $selected_carrier = $product->getCarriers();
                $is_mapped = false;
                if (count($selected_carrier)) {
                    foreach ($selected_carrier as $key => $carrier) {
                        if ((int)$carrier['id_reference'] == $id_reference) {
                            $is_mapped = true;
                            break;
                        }
                    }
                }

                $view_link = $this->context->link->getProductLink(
                    $product,
                    null,
                    null,
                    null,
                    $this->seller_info['id_default_lang']
                );

                $yes_txt = $this->module->l('Yes');
                $this->table_content[$product->id] = array(
                    array('value' => '#' . $product->id),
                    array(
                        'link' => array(
                            'href' => $view_link,
                            'function' => '',
                            'title' => $this->module->l('Click to view product'),
                            'target' => '_blank'
                        ),
                        'value' => $product->name,
                        'class' => '',
                    ),
                    array('value' => $product->reference),
                    array('value' => $this->getProductType($product)),
                    array('value' => KbGlobal::getApporvalStatus($val['approved'])),
                    array('value' => ($product->active) ? $yes_txt : $this->module->l('No')),
                    array('value' => ($is_mapped) ? $yes_txt : $this->module->l('No')),
                    array(
                        'actions' => array(
                            array(
                                'title' => $this->module->l('Click to mapped product'),
                                'function' => 'kbMappedAction('.$product->id.','.$id_reference.')',
                                'icon-class' => 'retweet'
                            )
                        )
                    ),
                );
            }

            $this->table_enable_multiaction = true;
            $this->list_row_callback = 'getSellerShippingProducts';

            //Show Multi actions

            $this->kb_multiaction_params['multiaction_values'] = array(
                array('label' => $this->module->l('Mapped shipping'), 'value' => $id_reference)
             );
            $this->kb_multiaction_params['carrier_id'] = $id_reference;

            $this->kb_multiaction_params['multiaction_related_to_table'] = $this->table_id;
            $this->kb_multiaction_params['has_reason_popup'] = false;
            $this->kb_multiaction_params['submit_action'] = $this->context->link->getModuleLink(
                $this->kb_module_name,
                $this->controller_name,
                array('multiaction' => true),
                (bool)Configuration::get('PS_SSL_ENABLED')
            );

            $this->context->smarty->assign('kbmutiaction', $this->renderKbMultiAction());
        }

        $this->context->smarty->assign('kblist', $this->renderKbList());
        $this->context->smarty->assign('seller_name', $this->seller_carrier->name);

        $this->setKbTemplate('seller/shipping/product_list.tpl');
    }
    
    public function getAjaxSellerProductListHtml()
    {
        $json = array();
        $id_carrier = Tools::getValue('id_carrier', 0);
        if ($id_carrier > 0) {
            if (!KbSellerShipping::isSellerShipping($this->seller_obj->id, $id_carrier)) {
                $this->context->cookie->__set(
                    'redirect_success',
                    $this->module->l('The requested shipping method is not yours')
                );
                $json['status'] = false;
                $json['msg'] = $this->module->l('No Data Found');
                return $json;
            }
            $this->seller_carrier = new Carrier($id_carrier);
        }

        $query = 'Select {{COLUMN}} from ' . _DB_PREFIX_ . 'product as p 
			LEFT JOIN ' . _DB_PREFIX_ . 'product_lang as pl on (p.id_product = pl.id_product) 
			INNER JOIN ' . _DB_PREFIX_ . 'kb_mp_seller_product as p2s on (p.id_product = p2s.id_product) ';
        
        if (Tools::getIsset('mapped_status') && Tools::getValue('mapped_status')
            != '' && Tools::getValue('mapped_status') == 1) {
            $query .= 'INNER JOIN '._DB_PREFIX_.'product_carrier as p2c on
                    (p.id_product = p2c.id_product AND
                    p2c.id_carrier_reference ='.(int) $this->seller_carrier->id_reference.') ';
        } elseif (Tools::getIsset('mapped_status') && Tools::getValue('mapped_status')
            != '' && Tools::getValue('mapped_status') == 0) {
            $query .= 'INNER JOIN '._DB_PREFIX_.'product_carrier as p2c on (p.id_product = p2c.id_product)';
        }

        if (Tools::getIsset('mapped_status') && Tools::getValue('mapped_status')
            != '' && Tools::getValue('mapped_status') == 0) {
            $query .= 'WHERE p2s.id_seller = '.(int) $this->seller_info['id_seller']
                .' AND pl.id_lang = '.(int) $this->seller_info['id_default_lang'].' AND p2s.id_product NOT IN '
                .'(Select id_product from '._DB_PREFIX_.'product_carrier where '
                .'id_carrier_reference ='.(int) $this->seller_carrier->id_reference.' )';
        } else {
            $query .= 'WHERE p2s.id_seller = ' . (int)$this->seller_info['id_seller']
                . ' AND pl.id_lang = ' . (int)$this->seller_info['id_default_lang'] . ' ';
        }

        $custom_filter = '';
        if (Tools::getIsset('reference') && Tools::getValue('reference') != '') {
            $custom_filter .= ' AND p.reference like "%' . pSQL(Tools::getValue('reference')) . '%"';
        }

        if (Tools::getIsset('id_category_default') && Tools::getValue('id_category_default') != '') {
            $custom_filter .= ' AND p.id_category_default = ' . (int)Tools::getValue('id_category_default');
        }

        if (Tools::getIsset('active') && Tools::getValue('active') != '') {
            $custom_filter .= ' AND p.active = ' . (int)Tools::getValue('active');
        }

        if (Tools::getIsset('approved') && Tools::getValue('approved') != '') {
            $custom_filter .= ' AND p2s.approved = "' . (int)Tools::getValue('approved') . '"';
        }

        if (Tools::getIsset('name') && Tools::getValue('name') != '') {
            $custom_filter .= ' AND pl.name like "%' . pSQL(Tools::getValue('name')) . '%"';
        }

        $query .= pSQL($custom_filter);

        $this->total_records = DB::getInstance(_PS_USE_SQL_SLAVE_)->getValue(
            str_replace(
                '{{COLUMN}}',
                'COUNT(distinct p.id_product) as total',
                $query
            )
        );

        if ($this->total_records > 0) {
            if (Tools::getIsset('start') && (int)Tools::getValue('start') > 0) {
                $this->page_start = (int)Tools::getValue('start');
            }

            $query .= ' ORDER BY p.id_product DESC LIMIT '
                . (int)$this->getPageStart() . ', ' . (int)$this->tbl_row_limit;
            $results = DB::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
                str_replace(
                    '{{COLUMN}}',
                    'distinct p.id_product, p2s.approved',
                    $query
                )
            );

            $row_html = '';
            foreach ($results as $val) {
                $product = new Product($val['id_product'], false, $this->seller_info['id_default_lang']);
                $selected_carrier = $product->getCarriers();
                $is_mapped = false;
                if (count($selected_carrier)) {
                    foreach ($selected_carrier as $carrier) {
                        if ((int)$carrier['id_reference'] == $this->seller_carrier->id_reference) {
                            $is_mapped = true;
                            break;
                        }
                    }
                }

                $view_link = $this->context->link->getProductLink(
                    $product,
                    null,
                    null,
                    null,
                    $this->seller_info['id_default_lang']
                );

                $yes_txt = $this->module->l('Yes');
                $row_html .= '<tr>
							<td class="kb-tcenter">
                            <div class="checker"><span>
								<input type="checkbox" class="kb_list_row_checkbox" name="row_item_id[]" 
								value="' . $product->id . '" title=""></span></div>
							</td>
							<td class="kb-tright">
								#' . $product->id . '
							</td>
							<td class=" ">
								<a href="' . $view_link . '" 
								title="' . $this->module->l('Click to view product')
                            . '" onclick="" target="_blank">'
                    . $product->name . '</a>
							</td>
							<td class=" ">' . $product->reference . '</td>
                            <td class=" ">' . $this->getProductType($product) . '</td>
							<td class=" ">' . KbGlobal::getApporvalStatus($val['approved']) . '</td>
							<td class=" ">'
                    . (($product->active) ? $yes_txt : $this->module->l('No'))
                    . '</td>
							<td class=" ">'
                    . (($is_mapped) ? $yes_txt : $this->module->l('No', 'Product'))
                    . '</td><td>
                        <a href="javascript:void(0)"
								title="' . $this->module->l('Click to mapped product')
                            . '" class="btn btn-default kb-multiaction-link" onclick="kbMappedAction('
                        .$product->id.','.$this->seller_carrier->id_reference
                        .')"><i class="icon-retweet kb-multiaction-icon"></i></a></td>
						</tr>';
            }
            $this->table_id = 'seller_shipping_mapping_product';
            $this->list_row_callback = 'getSellerShippingProducts';
            $json['status'] = true;
            $json['html'] = $row_html;
            $json['pagination'] = $this->generatePaginator(
                $this->page_start,
                $this->total_records,
                $this->getTotalPages(),
                $this->list_row_callback
            );
        } else {
            $json['status'] = false;
            $json['msg'] = $this->module->l('No Data Found');
        }
        return $json;
    }

    public function processMultiAction()
    {
        $id_carrier_reference = Tools::getValue('id_carrier', 0);
        $default_shipping = KbSellerShipping::getDefaultShippingId($this->seller_obj->id);
        if (Tools::getIsset('mutiaction_type') && $id_carrier_reference > 0) {
            $product_ids = explode(',', trim(Tools::getValue('selected_table_item_ids')));
            foreach ($product_ids as $id) {
                if ((int)$id > 0) {
                    $product = new Product($id);
                    $shippings = array();
                    $shippings[] = $id_carrier_reference;
                    $selected_carrier = $product->getCarriers();
                    if (count($selected_carrier)) {
                        foreach ($selected_carrier as $carrier) {
                            if ((int)$carrier['id_carrier'] != $default_shipping) {
                                $shippings[] = (int)$carrier['id_reference'];
                            }
                        }
                    }
                    $product->setCarriers($shippings);
                }
            }
            $this->context->cookie->__set(
                'redirect_success',
                $this->module->l('Product(s) has been mapped successfully')
            );
        } else {
            $this->context->cookie->__set(
                'redirect_error',
                $this->module->l('Please select valid action', 'Product')
            );
        }

        Tools::redirect(
            $this->context->link->getModuleLink(
                $this->kb_module_name,
                $this->controller_name,
                array(),
                (bool)Configuration::get('PS_SSL_ENABLED')
            )
        );
    }

    public function mappedShipping($id_carrier)
    {
        if (Tools::getisset('mapped_all') && Tools::getValue('mapped_all') == 1) {
            $sellers_products = KbSellerProduct::getSellerProducts(
                $this->seller_obj->id,
                false,
                null,
                null,
                null
            );

            $default_shipping = KbSellerShipping::getDefaultShippingId($this->seller_obj->id);
            foreach ($sellers_products as $val) {
                $shippings = array();
                $shippings[] = $id_carrier;
                $product = new Product($val['id_product'], false, $this->seller_obj->id_default_lang);
                $selected_carrier = $product->getCarriers();
                if (count($selected_carrier)) {
                    foreach ($selected_carrier as $carrier) {
                        if ((int)$carrier['id_carrier'] != $default_shipping) {
                            $shippings[] = (int)$carrier['id_reference'];
                        }
                    }
                }
                $product->setCarriers($shippings);
            }
        }
    }
}
