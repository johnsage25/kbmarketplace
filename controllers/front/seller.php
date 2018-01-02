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

class KbmarketplaceSellerModuleFrontController extends KbmarketplaceCoreModuleFrontController
{
    public $controller_name = 'seller';
    private $seller;
    public $logo_size = array('width' => 150, 'height' => '150');
    public $banner_size = array('width' => 250, 'height' => 180);

    public function __construct()
    {
        parent::__construct();
        $this->seller = new KbSeller(
            KbSeller::getSellerByCustomerId($this->context->customer->id),
            $this->context->language->id
        );
    }

    public function setMedia()
    {
        parent::setMedia();
        $this->addCSS($this->getKbModuleDir() . 'views/css/front/kb-forms.css');
        $this->addJS($this->getKbModuleDir() . 'libraries/tinymce/tinymce.min.js');
    }

    public function postProcess()
    {
        parent::postProcess();
        if (Tools::isSubmit('ajax')) {
            $this->json = array();
            if (Tools::isSubmit('method')) {
                switch (Tools::getValue('method')) {
                    case 'checkBusniessEmail':
                        if (!KbSeller::isBusinessEmailExist(
                            Tools::getValue('bemail', ''),
                            Tools::getValue('id_seller', 0)
                        )
                        ) {
                            $this->json = array('msg' => '');
                        } else {
                            $this->json = array('msg' => $this->module->l(
                                'Already exist for another seller'
                            ));
                        }
                        break;
                    case 'getSelectedPaymentContent':
                        if ($content = $this->getSelectedPaymentContent(Tools::getValue('payment_name'))) {
                            $this->json = array('content' => $content);
                        } else {
                            $this->json = array('content' => '');
                        }
                        break;
                }
            }

            echo Tools::jsonEncode($this->json);
            die;
        } else {
            if (Tools::isSubmit('updateSellerProfile')) {
                if (Tools::getValue('updateSellerProfile')
                    != Tools::encrypt($this->controller_name . $this->seller->id)) {
                    $this->Kberrors[] = $this->module->l('Token Mismatch');
                } else {
                    $this->seller->title = trim(Tools::getValue('seller_title'));
                    $this->seller->phone_number = trim(Tools::getValue('seller_phone_number'));
                    $this->seller->business_email = trim(Tools::getValue('seller_business_email'));
                    $this->seller->notification_type = (string)Tools::getValue('seller_notification_type');
                    $this->seller->address = trim(Tools::getValue('seller_address'));
                    $this->seller->state = Tools::getValue('seller_state', null);
                    $this->seller->id_country = Tools::getValue('seller_country', 0);
                    $this->seller->fb_link = trim(Tools::getValue('seller_fb_link'));
                    $this->seller->gplus_link = trim(Tools::getValue('seller_gplus_link'));
                    $this->seller->twit_link = trim(Tools::getValue('seller_twit_link'));
                    $this->seller->description = trim(Tools::getValue('seller_description'));
                    $this->seller->meta_keyword = trim(Tools::getValue('seller_meta_keywords'));
                    $this->seller->meta_description = trim(Tools::getValue('seller_meta_description'));
                    $this->seller->profile_url = trim(Tools::getValue('seller_profile_url'));
                    $this->seller->return_policy = trim(Tools::getValue('seller_return_policy'));
                    $this->seller->shipping_policy = trim(Tools::getValue('seller_shipping_policy'));

                    $seller_img_path = _PS_IMG_DIR_ . KbSeller::SELLER_PROFILE_IMG_PATH . $this->seller->id . '/';
                    if (!Tools::file_exists_no_cache(_PS_IMG_DIR_ . $this->module->name . '/')) {
                        @mkdir(_PS_IMG_DIR_ . $this->module->name . '/', 0777);
                    }

                    if (!Tools::file_exists_no_cache(_PS_IMG_DIR_ . KbSeller::SELLER_PROFILE_IMG_PATH)) {
                        @mkdir(_PS_IMG_DIR_ . KbSeller::SELLER_PROFILE_IMG_PATH, 0777);
                    }

                    if ((isset($_FILES['seller_logo'])
                        && $_FILES['seller_logo']['size'] > 0) || Tools::getValue('seller_logo_update')) {
                        if ((int)Tools::getValue('seller_logo_update') > 0) {
                            $this->deleteImage($seller_img_path . $this->seller->id . '-logo' . $this->imageType);
                        }
                        if (isset($_FILES['seller_logo']) && $_FILES['seller_logo']['size'] > 0
                            && $this->uploadImage(
                                'seller_logo',
                                $seller_img_path,
                                $this->seller->id . '-logo',
                                false
                            )
                        ) {
                            $this->seller->logo = $this->seller->id . '-logo.' . $this->imageType;
                        } else {
                            $this->seller->logo = null;
                        }
                    }

                    if ((isset($_FILES['seller_banner']) && $_FILES['seller_banner']['size'] > 0)
                        || Tools::getValue('seller_banner_update')) {
                        if ((int)Tools::getValue('seller_banner_update') > 0) {
                            $this->deleteImage($seller_img_path . $this->seller->id . '-banner' . $this->imageType);
                        }
                        if (isset($_FILES['seller_banner']) && $_FILES['seller_banner']['size'] > 0
                            && $this->uploadImage(
                                'seller_banner',
                                $seller_img_path,
                                $this->seller->id . '-banner',
                                false
                            )
                        ) {
                            $this->seller->banner = $this->seller->id . '-banner.' . $this->imageType;
                        } else {
                            $this->seller->banner = null;
                        }
                    }

                    $payment_option_data = array();
                    if (Tools::isSubmit('seller_payment_option')) {
                        if (Tools::getValue('seller_payment_option') != '') {
                            $payment_option_name = Tools::getValue('seller_payment_option');
                            $payment_option_data['name'] = $payment_option_name;
                            if ($payment_info = Tools::getValue('payment_info', array())) {
                                if (isset($payment_info[$payment_option_name])) {
                                    $file = $this->getKbModuleDir() . 'classes/payment/'.$payment_option_name.'.php';
                                    if (Tools::file_exists_no_cache($file) && is_file($file)) {
                                        $name = basename($file, '.php');
                                        require_once $this->getKbModuleDir() . 'classes/payment/'.$name.'.php';
                                        if (method_exists(get_class(new $name()), "getPaymentContent")) {
                                            $payment_option_data['data'] = $payment_info[$payment_option_name];
                                            $this->seller->payment_info = serialize($payment_option_data);
                                        } else {
                                            $this->Kberrors[] = sprintf(
                                                $this->module->l('getPaymentContent() function not found in %s class'),
                                                $payment_option_name
                                            );
                                        }
                                    }
                                }
                            }
                        }
                    }

                    $payment_info = $this->seller->payment_info;
                    $validate_fields = $this->seller->validateController();
                    if (!empty($validate_fields)) {
                        $this->Kberrors = array_merge($this->Kberrors, $validate_fields);
                    } else {
                        $this->seller->payment_info = $payment_info;
                        if ($this->seller->save(true)) {
                            Hook::exec('actionKbMarketPlaceUpdateSeller', array('object' => $this->seller));
                            if (!empty($this->Kberrors)) {
                                $msg = $this->module->l(
                                    'Not all the fields saved due to following reason:'
                                );
                            } else {
                                $msg = $this->module->l(
                                    'Your profile has been updated successfully.'
                                );
                            }
                            $this->Kbconfirmation = array_merge(
                                array($msg),
                                $this->Kberrors
                            );
                            $this->context->cookie->__set(
                                'redirect_success',
                                implode('####', $this->Kbconfirmation)
                            );
                            Tools::redirect(
                                $this->context->link->getModuleLink(
                                    $this->kb_module_name,
                                    $this->controller_name,
                                    array(),
                                    (bool)Configuration::get('PS_SSL_ENABLED')
                                )
                            );
                        }
                    }
                }
            }
        }
    }

    public function getTemplateVarPage()
    {
        $page = parent::getTemplateVarPage();
        if (isset($page['meta']) && $this->seller_info) {
            $page_title = 'Seller Profile';
            $page['meta']['title'] = $page_title;
            $page['meta']['keywords'] = $this->seller_info['meta_keyword'];
            $page['meta']['description'] = $this->seller_info['meta_description'];
        }
        return $page;
    }
    
    public function initContent()
    {
        
        if (Validate::isEmail($this->seller->payment_info)) {
            $seller_payment_data = array();
            $seller_payment_data['name'] = 'kbpaypal';
            $seller_payment_data['data']['paypal_id']['label'] = $this->module->l('Payment Info');
            $seller_payment_data['data']['paypal_id']['value'] = $this->seller->payment_info;
            $seller_payment_data['data']['add_info']['label'] = $this->module->l(
                'Additional Information'
            );
            $seller_payment_data['data']['add_info']['value'] = '';
            $this->seller->payment_info = serialize($seller_payment_data);
            $this->seller->save(true);
        }

        $base_link = KbGlobal::getBaseLink((bool)Configuration::get('PS_SSL_ENABLED'));
        $profile_default_image_path = $base_link . 'modules/' . $this->module->name . '/' . 'views/img/';
        $seller_img_path = _PS_IMG_DIR_ . KbSeller::SELLER_PROFILE_IMG_PATH . $this->seller->id . '/';
        if (empty($this->seller->logo) || !Tools::file_exists_no_cache($seller_img_path . $this->seller->logo)) {
            $this->seller->logo = $profile_default_image_path . KbGlobal::SELLER_DEFAULT_LOGO;
        } else {
            $this->seller->logo = $this->seller_image_path . $this->seller->id . '/' . $this->seller->logo;
        }

        if (empty($this->seller->banner) || !Tools::file_exists_no_cache($seller_img_path . $this->seller->banner)) {
            $this->seller->banner = $profile_default_image_path . KbGlobal::SELLER_DEFAULT_BANNER;
        } else {
            $this->seller->banner = $this->seller_image_path . $this->seller->id . '/' . $this->seller->banner;
        }

        $editor_lang_path = $this->getKbModuleDir() . 'libraries/tinymce/langs/';
        $editor_lang_code = Language::getIsoById($this->context->language->id);
        if (file_exists($editor_lang_path.$editor_lang_code.'.js')) {
            $editor_lang = $editor_lang_code;
        } elseif (file_exists($editor_lang_path.Language::getIsoById($this->seller->id_default_lang).'.js')) {
            $editor_lang = Language::getIsoById($this->seller->id_default_lang);
        } else {
            $editor_lang = 'en';
        }

        $this->context->smarty->assign('editor_lang', $editor_lang);
        $this->context->smarty->assign('seller', (array)$this->seller);
        $this->context->smarty->assign('payment_info', Tools::unSerialize($this->seller->payment_info));

        $tmp = Country::getCountries($this->seller->id_default_lang, false, false, false);
        $country_array = array();
        foreach ($tmp as $row) {
            $country_array[$row['id_country']] = $row['country'];
        }

        $this->context->smarty->assign('countries', $country_array);

        if ((int)$this->seller->id_country > 0) {
            $seller_country = (int)$this->seller->id_country;
        } elseif (Tools::getIsset('seller_country') && (int)Tools::getValue('seller_country') > 0) {
            $seller_country = (int)Tools::getValue('seller_country');
        } else {
            $seller_country = Configuration::get('PS_COUNTRY_DEFAULT');
        }

        $this->context->smarty->assign('seller_country', $seller_country);
        $this->context->smarty->assign('kb_id_seller', $this->seller->id);
        $this->context->smarty->assign('seller_form_key', Tools::encrypt($this->controller_name . $this->seller->id));
        $this->context->smarty->assign('seller_default_logo', KbGlobal::SELLER_DEFAULT_LOGO);
        $this->context->smarty->assign('seller_default_banner', KbGlobal::SELLER_DEFAULT_BANNER);
        $this->context->smarty->assign('kb_img_frmats', $this->img_formats);
        $this->context->smarty->assign(
            'kb_validation_error',
            $this->module->l('Please provide mandatory information with valid values.')
        );
        $this->context->smarty->assign(
            'kb_img_size_error',
            sprintf(
                $this->module->l('Size should not be greater than %d MB'),
                (float)($this->img_size_limit / 1000)
            )
        );
        $this->context->smarty->assign(
            'kb_img_type_error',
            $this->module->l('Image format is not supproted')
        );
        $this->context->smarty->assign('time', strtotime("now"));
        $avilable_payment_file = array();

        foreach (glob($this->getKbModuleDir() . 'classes/payment/*.php') as $file) {
            if (Tools::file_exists_no_cache($file) && is_file($file)) {
                $name = basename($file, '.php');
                if ($name != 'index') {
                    require_once $this->getKbModuleDir() . 'classes/payment/'.$name.'.php';
                    if (method_exists(get_class(new $name()), "getPaymentContent")) {
                        $avilable_payment_file[$name] =  $this->getPaymentMethodname($name);
                    }
                }
            }
        }

        $this->context->smarty->assign('curent_shop_url', $this->context->shop->getBaseURL());
        $this->context->smarty->assign('available_payment_file', $avilable_payment_file);
        $this->setKbTemplate('seller/profile_form.tpl');

        parent::initContent();
    }

    public function getSelectedPaymentContent($payment_method)
    {
        $file = $this->getKbModuleDir() . 'classes/payment/'.$payment_method.'.php';
        if (Tools::file_exists_no_cache($file) && is_file($file)) {
            $name = basename($file, '.php');
            require_once $this->getKbModuleDir() . 'classes/payment/'.$name.'.php';
            if (method_exists(get_class(new $name()), "getPaymentContent")) {
                $avilable_payment_file = array();
                $avilable_payment_file[$name] =  $this->getPaymentMethodname($name);
                return($name::getPaymentContent());
            } else {
                $msg = $this->module->l('Not able to get content of this payment method');
                return $msg;
            }
        } else {
            return false;
        }
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
