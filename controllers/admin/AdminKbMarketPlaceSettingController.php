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

require_once dirname(__FILE__).'/AdminKbMarketplaceCoreController.php';
require_once(_PS_MODULE_DIR_.'kbmarketplace/libraries/kbmarketplace/KbGlobal.php');

class AdminKbMarketPlaceSettingController extends AdminKbMarketplaceCoreController
{

    public function __construct()
    {
        $this->table   = 'kb_mp_seller_config';
        $this->display = 'edit';
        parent::__construct();

        $this->fields_form = array(
            'tinymce' => true,
            'input' => array(
                array(
                    'type' => 'text',
                    'suffix' => '%',
                    'label' => $this->module->l('Default Commission'),
                    'name' => 'kbmp_default_commission',
                    'required' => true,
                    'validation' => 'isPercentage',
                    'class' => 'fixed-width-xs',
                    'hint' => $this->module->l('Only numerical or decimal values are allowed')
                ),
                array(
                    'type' => 'text',
                    'label' => $this->module->l('Approval Request Limit'),
                    'name' => 'kbmp_approval_request_limit',
                    'required' => true,
                    'validation' => 'isInt',
                    'class' => 'fixed-width-xs',
                    'hint' => $this->module->l('Only Numeric values are allowed'),
                    'desc' => $this->module->l('Maximum number of request seller can make for approving account
						after disapproving. This limit will be set for seller after registration with his account
						and cannot be changed later.')
                ),
                array(
                    'type' => 'text',
                    'label' => $this->module->l('New Product Limit'),
                    'name' => 'kbmp_product_limit',
                    'required' => true,
                    'validation' => 'isInt',
                    'class' => 'fixed-width-xs',
                    'hint' => $this->module->l('Only Numeric values are allowed'),
                    'desc' => $this->module->l('After this limit, seller cannot add new products until he/she
						will not be approved by you.')
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->module->l('Enable Seller Registration'),
                    'name' => 'kbmp_seller_registration',
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'seller_registration_on',
                            'value' => 1,
                            'label' => $this->module->l('Enabled')
                        ),
                        array(
                            'id' => 'seller_registration_off',
                            'value' => 0,
                            'label' => $this->module->l('Disabled')
                        )
                    ),
                    'hint' => $this->module->l('Allow new or existing (who is not seller), customer to
						register as seller on store')
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->module->l('New Product Approval Required'),
                    'name' => 'kbmp_new_product_approval_required',
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'new_product_approval_required_on',
                            'value' => 1,
                            'label' => $this->module->l('Enabled')
                        ),
                        array(
                            'id' => 'new_product_approval_required_off',
                            'value' => 0,
                            'label' => $this->module->l('Disabled')
                        )
                    ),
                    'hint' => $this->module->l('New product needs approval from your side before display on front.')
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->module->l('Send email to seller on order place'),
                    'name' => 'kbmp_email_on_new_order',
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'email_on_new_order_on',
                            'value' => 1,
                            'label' => $this->module->l('Enabled')
                        ),
                        array(
                            'id' => 'email_on_new_order_off',
                            'value' => 0,
                            'label' => $this->module->l('Disabled')
                        )
                    ),
                    'hint' => $this->module->l('With this setting, system will send email to seller on new order')
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->module->l('Enable Seller Review'),
                    'name' => 'kbmp_enable_seller_review',
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'enable_seller_review_on',
                            'value' => 1,
                            'label' => $this->module->l('Enabled')
                        ),
                        array(
                            'id' => 'enable_seller_review_off',
                            'value' => 0,
                            'label' => $this->module->l('Disabled')
                        )
                    ),
                    'hint' => $this->module->l('Enable customers to give his reviews on seller.')
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->module->l('Seller Review Approval Required'),
                    'name' => 'kbmp_seller_review_approval_required',
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'seller_review_approval_required_on',
                            'value' => 1,
                            'label' => $this->module->l('Enabled')
                        ),
                        array(
                            'id' => 'seller_review_approval_required_off',
                            'value' => 0,
                            'label' => $this->module->l('Disabled')
                        )
                    ),
                    'hint' => $this->module->l('With this setting, review first needs approval by you
						before showing to customers.')
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->module->l('Display sellers on front'),
                    'name' => 'kbmp_show_seller_on_front',
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'show_seller_on front_on',
                            'value' => 1,
                            'label' => $this->module->l('Enabled')
                        ),
                        array(
                            'id' => 'show_seller_on front_off',
                            'value' => 0,
                            'label' => $this->module->l('Disabled')
                        )
                    ),
                    'hint' => $this->module->l('With this setting, customers can view the sellers list
						as well as their profile.')
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->module->l('Allow Order Handling'),
                    'name' => 'kbmp_enable_seller_order_handling',
                    'class' => 't',
                    'is_bool' => true,
                    'hint' => $this->module->l('Allow Sellers to handle orders.'),
                    'desc' => $this->module->l(
                        'This setting will enable/disable sellers to change status,'
                        .' ship, invoice printing of his own orders(order having own products).'
                    ),
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->module->l('Enable')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->module->l('Disable')
                        )
                    ),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->module->l('Allow Free Shipping'),
                    'name' => 'kbmp_enable_free_shipping',
                    'class' => 't',
                    'is_bool' => true,
                    'hint' => $this->module->l('Allow Customer to add free shipping voucher'),
                    'desc' => $this->module->l('This setting will allow/disallow to use free shipping voucher.'),
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->module->l('Enable')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->module->l('Disable')
                        )
                    ),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->module->l('Display Product Wise Seller details on success'),
                    'name' => 'kbmp_enable_seller_order_details',
                    'class' => 't',
                    'is_bool' => true,
                    'hint' => $this->module->l(
                        'Allow Customer to see product wise seller details on order confirmation page'
                    ),
                    'desc' => $this->module->l('This setting will hide/show  Seller details on success.'),
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->module->l('Enable')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->module->l('Disable')
                        )
                    ),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->module->l('Display Seller details on product page'),
                    'name' => 'kbmp_enable_seller_details',
                    'class' => 't',
                    'is_bool' => true,
                    'hint' => $this->module->l('Allow Customer to see seller details on product page'),
                    'desc' => $this->module->l('This setting will hide/show seller detail on product page.'),
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->module->l('Enable')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->module->l('Disable')
                        )
                    ),
                ),
                array(
                    'type' => 'tags',
                    'label' => $this->module->l('Listing Meta Keywords'),
                    'name' => 'kbmp_seller_listing_meta_keywords',
                    'hint' => $this->module->l('Set the keywords/tags for seller listing page on front.'),
                    'desc' => $this->module->l('Set the comma seperated keywords by which customer can search
						your seller listing page via search engines. Comma is mandatory even if your are adding only one tag. Ex-: tag1,')
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->module->l('Listing Meta Description'),
                    'rows' => 5,
                    'name' => 'kbmp_seller_listing_meta_description',
                    'hint' => $this->module->l('Set the description for seller listing page on front.'),
                    'desc' => $this->module->l('Set the description for seller listing page on front.')
                ),
                array(
                    'type' => 'textarea',
                    'lang' => true,
                    'label' => $this->module->l('Seller Agreement'),
                    'autoload_rte' => true,
                    'name' => 'kbmp_seller_agreement',
                    'hint' => $this->module->l('Leave blank, if you dont want.'),
                    'desc' => $this->module->l('Set the agreement which seller accept
                        before registering on marketplace.')
                ),
                array(
                    'type' => 'textarea',
                    'lang' => true,
                    'label' => $this->module->l('Order Email Template'),
                    'autoload_rte' => true,
                    'name' => 'kbmp_seller_order_email_template',
                    'hint' => $this->module->l('This template will used to send order detail
                        to seller, if his product is ordered.'),
                    'desc' => $this->module->l('Keywords like {{sample}} will be replace by
                        dynamic content at the time of execution. Please do not remove these type of
                        words from template, otherwise proper information will not be send in email to
                        seller as well you. You can only change the position of these keywords in the template.')
                ),
            ),
            'submit' => array('title' => 'Save'),
            'reset' => array('title' => 'Reset', 'icon' => 'process-icon-reset')
        );

        $this->show_form_cancel_button = false;
        $this->submit_action           = 'submitMarketPlaceConfiguration';
    }

    public function initContent()
    {
        if (!Configuration::get('KB_MARKETPLACE_CONFIG') || Configuration::get('KB_MARKETPLACE_CONFIG')
            == '') {
            $settings = KbGLobal::getDefaultSettings();
        } elseif (Tools::getValue('kbmp_reset_setting') == 1) {
            $settings = KbGLobal::getDefaultSettingsFirstTime();
        } else {
            $settings = Tools::unSerialize(Configuration::get('KB_MARKETPLACE_CONFIG'));
        }

//        if (Tools::getValue('kbmp_reset_setting') == 1) {
//            $category_array = array();
//        } else {
            $category_array = $settings['kbmp_allowed_categories'];
//        }

//        if (!isset($settings['kbmp_enable_seller_order_handling'])) {
//            $settings['kbmp_enable_seller_order_handling'] = 1;
//        }
//        if (!isset($settings['kbmp_enable_seller_details'])) {
//            $settings['kbmp_enable_seller_details'] = 0;
//        }
//        if (!isset($settings['kbmp_enable_seller_order_details'])) {
//            $settings['kbmp_enable_seller_order_details'] = 0;
//        }

        $root = Category::getRootCategory();
        $tree = new HelperTreeCategories('kbmp-categories-tree');
        $tree->setRootCategory($root->id)
            ->setInputName('kbmp_allowed_categories')
            ->setUseCheckBox(true)
            ->setUseSearch(false)
            ->setSelectedCategories((array) $category_array);

        $this->fields_form['input'][] = array(
            'type' => 'categories_select',
            'label' => $this->module->l('Categories Allowed'),
            'category_tree' => $tree->render(),
            'name' => 'kbmp_allowed_categories',
            'hint' => array(
                $this->module->l('Categories to be allowed to seller in which he/she can map his/her products.'),
                $this->module->l('If no category is selected that will mean that all the categories are allowed.')
            ),
            'desc' => $this->module->l(
                'If no category is selected that will mean that all the categories are allowed. '
                . 'In order to enable a category you will have to check all the parent categories '
                . 'otherwise the category will not be activated. '
                . 'Example- To enable `T-shirts` category, you will have to check all the parent categories '
                . 'i.e. Home, Women, Tops and ofcourse T-shirts.'
            )
        );


        parent::initContent();
        $this->context->smarty->assign(array(
            'title' => 'MarketPlace General Settings',
        ));
    }

    public function setMedia()
    {
        parent::setMedia();
        $this->context->controller->addJqueryUI('ui.widget');
        $this->context->controller->addJqueryPlugin('tagify');
    }

    public function renderForm()
    {
        $form = parent::renderForm();
        $tpl  = $this->context->smarty->createTemplate(
            _PS_MODULE_DIR_.$this->kb_module_name.'/views/templates/admin/setting.tpl'
        );
        $tpl->assign('form_fields', $form);
        return $tpl->fetch();
    }

    public function initProcess()
    {
        if (Tools::isSubmit('submitMarketPlaceConfiguration')) {
//            $this->display = 'edit';
//            print_r($this->tabAccess);
//            if ($this->tabAccess['edit'] === '1') {
                $this->action = 'MarketPlaceSetting';
//            } else {
//                $this->errors[] = Tools::displayError('You do not have permission to edit this.');
//            }
        }
    }

    public function processMarketPlaceSetting()
    {
        //Tools::safePostVars();
//	   d($_REQUEST);
        $mp_config = array();
        if (Tools::getIsset('kbmp_reset_setting') && Tools::getValue('kbmp_reset_setting')
            == 1) {
            $this->fields_value = KbGLobal::getDefaultSettingsFirstTime();
            $this->displayWarning(
                $this->l(
                    "Please click on 'Save' button to keep default settings (settings shown below), "
                    . "otherwise previously saved values will be kept."
                )
            );
            return $this->fields_value;
        } else {
            $default_settings = KbGLobal::getDefaultSettings();
            $this->getLanguages();
            foreach ($this->fields_form['input'] as $field) {
                $error = false;
                if (($field['name'] == 'kbmp_approval_request_limit') || ($field['name']
                    == 'kbmp_product_limit')) {
                    if (Tools::getValue($field['name']) < 0) {
                        $error          = true;
                        $label          = $field['label'];
                        $this->errors[] = Tools::displayError("Value of '$label' can not be negative.");
                    }
                }
                if (isset($field['lang']) && $field['lang']) {
                    $lang_data = $default_settings[$field['name']];
                    foreach ($this->_languages as $language) {
                        $lang_data[$language['id_lang']] = '';
                        if (Tools::getIsset($field['name'].'_'.$language['id_lang'])) {
                            $value                           = Tools::getValue($field['name'].'_'.$language['id_lang']);
                            $lang_data[$language['id_lang']] = Tools::htmlentitiesUTF8($value);
                        } else {
                            if ($field['name'] == 'kbmp_seller_order_email_template') {
                                $lang_data[$language['id_lang']] = KbEmail::getOrderEmailBaseTemplate();
                            } else {
                                $lang_data[$language['id_lang']] = '';
                            }
                        }
                    }
                    $mp_config[$field['name']] = $lang_data;
                } elseif (Tools::getIsset($field['name'])) {
                    if (isset($field['required']) && $field['required']) {
                        if (($value = Tools::getValue($field['name'])) == false && (string) $value
                            != '0') {
                            $error          = true;
                            $this->errors[] = sprintf(Tools::displayError('Field %s is required.'), $field['label']);
                        } elseif (isset($field['validation']) && !call_user_func(array(
                                "Validate", $field['validation']), Tools::getValue($field['name']))) {
                            $error          = true;
                            $this->errors[] = sprintf(Tools::displayError('Field %s is invalid.'), $field['label']);
                        }
                    } elseif (isset($field['validation']) && !call_user_func(array(
                            "Validate", $field['validation']), Tools::getValue($field['name']))) {
                        $error          = true;
                        $this->errors[] = sprintf(Tools::displayError('Field %s is invalid.'), $field['label']);
                    }
                    if (!$error) {
                        if ($field['type'] && isset($field['multiple']) && $field['multiple']) {
                            $mp_config[$field['name']] = Tools::getValue('selectItem'.$field['name']);
                        } else {
                            $mp_config[$field['name']] = Tools::getValue($field['name']);
                        }
                    }
                } else {
                    $mp_config[$field['name']] = $default_settings[$field['name']];
                }
            }
            if (Tools::getIsset('kbmp_allowed_categories')) {
                $mp_config['kbmp_allowed_categories'] = Tools::getValue('kbmp_allowed_categories');
            } else {
                $mp_config['kbmp_allowed_categories'] = array();
            }
            if (Tools::getIsset('kbmp_enable_seller_order_handling')) {
                Configuration::updateValue(
                    'KB_MP_SELLER_ORDER_HANDLING',
                    Tools::getValue('kbmp_enable_seller_order_handling')
                );
            }
            if (Tools::getIsset('kbmp_enable_seller_review')) {
                Configuration::updateValue('KB_MP_ENABLE_SELLER_REVIEW', Tools::getValue('kbmp_enable_seller_review'));
            }
        }

        if (!$this->errors || count($this->errors) == 0) {
            $this->confirmations[] = $this->_conf[6];
            Configuration::updateValue('KB_MARKETPLACE_CONFIG', serialize($mp_config));
            Hook::exec('actionMarketplaceSetting', array('controller' => $this, 'settings' => $mp_config));
        }
    }

    public function getFieldsValue($obj)
    {
        unset($obj);
        if (Tools::getIsset('kbmp_reset_setting') &&
            Tools::getValue('kbmp_reset_setting') == 1) {
            $this->fields_value = KbGLobal::getDefaultSettingsFirstTime();
            return $this->fields_value;
        } else {
            if (!Configuration::get('KB_MARKETPLACE_CONFIG') ||
                Configuration::get('KB_MARKETPLACE_CONFIG') == '') {
                $settings = KbGLobal::getDefaultSettings();
            } else {
                $settings = Tools::unSerialize(Configuration::get('KB_MARKETPLACE_CONFIG'));
//            print_r($settings);die;
                if (isset($settings['kbmp_seller_order_email_template']) &&
                    $settings['kbmp_seller_order_email_template'] == '0'
                ) {
                    foreach ($settings['kbmp_seller_order_email_template'] as $key => $template_lang) {
                        unset($template_lang);
                        $settings['kbmp_seller_order_email_template'][$key] = "<table>
                        <tbody>
                        <tr>
                        <td align='center' class='titleblock' style='padding: 7px 0;'>
                        <span size='2' face='Open-sans, sans-serif' color='#555454'
                        style='color: #555454; font-family: Open-sans, sans-serif; font-size: small;'>
                        <span class='title' style='font-weight: 500; font-size: 28px;
                        text-transform: uppercase; line-height: 33px;'>Hi {seller_name},</span><br />
                        <span class='subtitle'
                        style='font-weight: 500; font-size: 16px;
                        text-transform: uppercase; line-height: 25px;'>
                        A Customer has just placed an order for your products on {shop_name}!
                        </span>
                        </span></td>
                        </tr>
                        <tr>
                        <td class='space_footer' style='padding: 0!important;'> </td>
                        </tr>
                        <tr>
                        <td class='box' style='border: 1px solid #D6D4D4;
                        background-color: #f8f8f8; padding: 7px 14px;'>
                        <p data-html-only='1' style='border-bottom: 1px solid #D6D4D4;
                        margin: 3px 0 7px; text-transform: uppercase; font-weight: 500;
                        font-size: 18px; padding-bottom: 10px;'>Customer Information</p>
                        <span size='2' face='Open-sans, sans-serif' color='#555454' style='color: #555454;
                        font-family: Open-sans, sans-serif; font-size: small;'>
                        <span style='color: #777;'> <span style='color: #333;'>
                        <strong>Name:</strong></span> {firstname} {lastname}<br /> </span>
                        <span style='color: #777;'> <span style='color: #333;'>
                        <strong>Email:</strong></span> {email}<br /> </span> </span></td>
                        </tr>
                        <tr>
                        <td class='space_footer' style='padding: 0!important;'> </td>
                        </tr>
                        <tr>
                        <td class='box' style='border: 1px solid #D6D4D4;
                        background-color: #f8f8f8; padding: 7px 14px;'>
                        <p data-html-only='1' style='border-bottom: 1px solid #D6D4D4;
                        margin: 3px 0 7px; text-transform: uppercase; font-weight: 500; font-size: 18px;
                        padding-bottom: 10px;'>Order details</p>
                        <span size='2' face='Open-sans, sans-serif'
                        color='#555454' style='color: #555454;
                        font-family: Open-sans, sans-serif;
                        font-size: small;'><span style='color: #777;'>
                        <span style='color: #333;'><strong>Order:</strong></span>
                        {order_name} Placed on {date}<br /> </span> </span></td>
                        </tr>
                        <tr>
                        <td class='space_footer' style='padding: 0!important;'> </td>
                        </tr>
                        <tr>
                        <td>{products}</td>
                        </tr>
                        <tr>
                        <td class='space_footer' style='padding: 0!important;'> </td>
                        </tr>
                        <tr>
                        <td>
                        <table class='table' style='width: 100%;'>
                        <tbody>
                        <tr>
                        <td style='border: 1px solid #D6D4D4;
                        background-color: #f8f8f8; padding: 7px 14px;'>
                        <p data-html-only='1'
                        style='border-bottom: 1px solid #D6D4D4;
                        margin: 3px 0 7px; text-transform: uppercase;
                        font-weight: 500; font-size: 18px;
                        padding-bottom: 10px;'>Delivery address</p>
                        <span size='2' face='Open-sans, sans-serif' color='#555454' style='color: #555454;
                        font-family: Open-sans, sans-serif; font-size: small;'>
                        <span style='color: #777;'>{delivery_block_html}</span> </span></td>
                        <td width='20' class='space_address' style='padding: 7px 0;'> </td>
                        <td style='border: 1px solid #D6D4D4; background-color: #f8f8f8; padding: 7px 14px;'>
                        <p data-html-only='1' style='border-bottom: 1px solid #D6D4D4;
                        margin: 3px 0 7px; text-transform: uppercase; font-weight: 500;
                        font-size: 18px; padding-bottom: 10px;'>Billing address</p>
                        <span size='2' face='Open-sans, sans-serif' color='#555454'
                        style='color: #555454; font-family: Open-sans, sans-serif;
                        font-size: small;'>
                        <span style='color: #777;'>{invoice_block_html}</span> </span></td>
                        </tr>
                        </tbody>
                        </table>
                        </td>
                        </tr>
                        </tbody>
                        </table>";
                    }
                    $this->displayWarning(
                        $this->l("Please save the setting once, before using the module.")
                    );
                }
            }

            if (!isset($settings['kbmp_enable_seller_order_handling'])) {
                $settings['kbmp_enable_seller_order_handling'] = 1;
            }
            if (!isset($settings['kbmp_enable_free_shipping'])) {
                $settings['kbmp_enable_free_shipping'] = 0;
            }

            if (!isset($settings['kbmp_enable_seller_details'])) {
                $settings['kbmp_enable_seller_details'] = 0;
            }
            if (!isset($settings['kbmp_enable_seller_order_details'])) {
                $settings['kbmp_enable_seller_order_details'] = 0;
            }

            if (!isset($settings['kbmp_seller_agreement'])) {
                $settings['kbmp_seller_agreement'] = array();
            }

            if (!isset($settings['kbmp_seller_order_email_template'])) {
                $settings['kbmp_seller_order_email_template'] = array();
            }

            foreach ($this->fields_form[0]['form']['input'] as $fieldset) {
                if (isset($fieldset['lang']) && $fieldset['lang']) {
                    $lang_data = array();
                    $saved_data = array();
                    if (!empty($settings[$fieldset['name']])) {
                        $saved_data = $settings[$fieldset['name']];
                    }
                    foreach ($this->_languages as $language) {
                        $lang_data[$language['id_lang']] = '';
                        if (Tools::getIsset($fieldset['name']
                            . '_' . $language['id_lang'])) {
                            $lang_data[$language['id_lang']] =
                                Tools::getValue($fieldset['name'] . '_' . $language['id_lang']);
                        } elseif (isset($saved_data[$language['id_lang']])) {
                            $lang_data[$language['id_lang']] = Tools::htmlentitiesDecodeUTF8(
                                $saved_data[$language['id_lang']]
                            );
                        } else {
                            if ($fieldset['name'] == 'kbmp_seller_order_email_template') {
                                $lang_data[$language['id_lang']] =
                                    KbEmail::getOrderEmailBaseTemplate();
                            } else {
                                $lang_data[$language['id_lang']] = '';
                            }
                        }
                    }
                    $this->fields_value[$fieldset['name']] = $lang_data;
                } elseif (Tools::getIsset($fieldset['name'])) {
                    if ($fieldset['type'] && isset($fieldset['multiple']) && $fieldset['multiple']) {
                        $this->fields_value[$fieldset['name']] = Tools::getValue('selectItem' . $fieldset['name']);
                    } else {
                        $this->fields_value[$fieldset['name']] = Tools::getValue($fieldset['name']);
                    }
                } else {
                    if ($fieldset['type'] == 'select') {
                        if (isset($fieldset['multiple']) && $fieldset['multiple']) {
                            $this->fields_value[$fieldset['name']] =
                                (array) $settings[$fieldset['name']];
                        } else {
                            $this->fields_value[$fieldset['name']] = $settings[$fieldset['name']];
                        }
                    } else {
                        if (isset($settings[$fieldset['name']])) {
                            $this->fields_value[$fieldset['name']] = $settings[$fieldset['name']];
                        } else {
                            $this->fields_value[$fieldset['name']] = '';
                        }
                    }
                }
            }
            $this->fields_value['kbmp_allowed_categories'] = $settings['kbmp_allowed_categories'];
            return $this->fields_value;
        }
    }
}
