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

class KbmarketplaceCoreModuleFrontController extends ModuleFrontController
{
    protected $kb_module_name = 'kbmarketplace';
    protected $kbtemplate = 'not_found_page.tpl';
    protected $seller_image_path;
    //Show error message on same page
    protected $Kberrors = array();
    //Show success/confirmation message on same page
    protected $Kbconfirmation = array();

    const LONG_TEXT_CHARACTER_LIMIT = 200;

    public $img_size_limit = 5000;
    public $imageType = 'jpg';
    public $img_formats = array('jpeg', 'png', 'jpg', 'gif');

    /*
     * array containing filter parameters
     * array(
     * 	array(
     * 		'type'		//required, [text, select]
     * 		'name'		//required, name of the field
     * 		'class'		//optional, additional class
     * 		'default'	//optional, value to be set as default or selected 
     * 		'label'		//optional, label for field
     * 		'placeholder'	//optional, place holder for blank field
     * 		'is_required'	//optional, field is required or not
     * 		'validate'	//optional, validation type, 
     * 					possible values [isInt, isPrice, isDate, isEmail, isPhoneNumber, isGenericName]
     * 	),
     * 	.....
     * )
     */
    protected $filters = array();

    /*
     * Filter block heading
     */
    protected $filter_header;

    /*
     * Filter block unique id
     */
    protected $filter_id = '';

    /*
     * Filter action name on which 
     * system send ajax request
     */
    protected $filter_action_name = '';


    /*
     * Multiaction Form Parameters
     * array(
     * 		'multiaction_values' => array(), // required, array(array('label' => '', 'value' => ''),..)
     * 		'has_status_dropdown' => false, //default false; if true, require statuses values
     * 		'status_dropdown_values' => array(), //required, if has_status_dropdown is true; 
     * 												array(array('label' => '', 'value' => ''),..)
     * 		'show_status_on_multiaction_value' => '', //required, value through which identify status value type
     * 		'multiaction_related_to_table' => '', //required, id of the tabl; should be same as $table_id
     * 		'has_reason_popup' => false, //optional, default false; 
     * 										if true, display popup for taking reason for particular action
     * 		'reason_popup_open_for' => array(), //Optional set values for which popup will open
     * 		'submit_action' => '', //require url
     * 		'submit_function' => '', //name of the function for handling click event
     * 	)
     */
    protected $kb_multiaction_params = array(
        'multiaction_values' => array(),
        'has_status_dropdown' => false,
        'status_dropdown_values' => array(),
        'show_status_on_multiaction_value' => null,
        'multiaction_related_to_table' => '',
        'has_reason_popup' => false,
        'submit_action' => '',
        'submit_function' => 'kbMultiactionFormSubmit'
    );

    /*
     * List Id
     * Keep this id same as $filter id to show result
     * according to search criteria
     */
    protected $table_id = '';
    protected $table_header = array();
    protected $table_sorted_column;


    /*
     * array(
     * 		// 1 row value
     * 		array(
     * 			// 1 cell value
     * 			array(
     * 				'link' => array('href' => '', 'function' => '', 'title' => '', 'target' = '') 
     * 										//optional, display cell value as link
     * 				'value' => '#1234' // required, value to be displayed in cell
     * 				'class' => '' // optional, add additional class on cell value,
     * 				'align' => 'right' //optional, default is left
     * 			),
     * 			.....
     * 		),
     * 		......
     * 	)
     * 
     */
    protected $table_content = array();
    protected $table_enable_multiaction = false;
    protected $table_row_actions = array();
    protected $total_records;
    protected $page_start = 1;
    protected $tbl_row_limit = 20;
    protected $list_row_callback = '';
    protected $seller_obj;
    protected $seller_info;
    protected $seller_currency;

    public function __construct()
    {
        $this->context = Context::getContext();
        if (Configuration::get('KB_MARKETPLACE') === false
            || Configuration::get('KB_MARKETPLACE') == 0) {
            Tools::redirect(
                $this->context->link->getPageLink(
                    'index',
                    (bool)Configuration::get('PS_SSL_ENABLED')
                )
            );
        }

        if (!$this->context->customer->logged) {
            Tools::redirect(
                $this->context->link->getPageLink(
                    'my-account',
                    (bool)Configuration::get('PS_SSL_ENABLED')
                )
            );
        } else {
            $this->seller_obj = new KbSeller(KbSeller::getSellerByCustomerId((int)$this->context->customer->id));
            if (!$this->seller_obj->isSeller()) {
                Tools::redirect(
                    $this->context->link->getPageLink(
                        'my-account',
                        (bool)Configuration::get('PS_SSL_ENABLED')
                    )
                );
            }

            $this->seller_info = $this->seller_obj->getSellerInfo();
            $this->seller_info['settings'] = array();
            $global_settings = Tools::unserialize(Configuration::get('KB_MARKETPLACE_CONFIG'));
            $seller_setting = new KbSellerSetting($this->seller_info['id_seller']);
            if ($global_settings && count($global_settings) > 0) {
                foreach ($global_settings as $set_key => $set_val) {
                    if (isset($seller_setting->{$set_key}) && !$seller_setting->{$set_key}['global']) {
                        $this->seller_info['settings'][$set_key] = $seller_setting->{$set_key}['main'];
                    } else {
                        $this->seller_info['settings'][$set_key] = $set_val;
                    }
                }
            } elseif ($seller_setting && count($seller_setting) > 0) {
                foreach ($seller_setting->setting as $set_key => $set_val) {
//                    $this->seller_info['settings'][$set_key] = $seller_setting->{$set_key}['main'];
                     $this->seller_info['settings'][$set_key] = $seller_setting->setting{$set_key}['main'];
                }
            }

            $this->setKbTemplate($this->kbtemplate);
        }
        parent::__construct();

        $this->seller_image_path = KbGlobal::getBaseLink((bool)Configuration::get('PS_SSL_ENABLED'))
            . 'img/' . KbSeller::SELLER_PROFILE_IMG_PATH;
        $this->context->smarty->assign(
            'kb_image_path',
            KbGlobal::getBaseLink((bool)Configuration::get('PS_SSL_ENABLED'))
            . 'modules/' . $this->kb_module_name . '/views/img/'
        );
        $this->context->smarty->assign(
            'kb_current_request',
            $this->context->link->getModuleLink(
                $this->kb_module_name,
                $this->controller_name,
                array(),
                (bool)Configuration::get('PS_SSL_ENABLED')
            )
        );
        $this->context->smarty->assign('kb_image_size_limit', $this->img_size_limit);
        $this->context->smarty->assign('kb_module_name', $this->kb_module_name);
        
        $currency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'), $this->seller_info['id_default_lang']);
        $this->seller_currency = $currency;
        $this->context->smarty->assign('kb_seller_currency', $this->seller_currency);
    }

    public function setMedia()
    {
        parent::setMedia();
        $system_js_path = _PS_ROOT_DIR_ . '/js/jquery/jquery-' . _PS_JQUERY_VERSION_.'.min.js';
        if (Tools::file_exists_no_cache($system_js_path)) {
            $this->registerJavascript(
                'corejquery',
                'js/jquery/jquery-'._PS_JQUERY_VERSION_.'.min.js',
                array('position' => 'head', 'priority' => 0)
            );
        } else {
            $system_js_path = 'modules/'. $this->kb_module_name . '/views/js/front/jquery-1.11.0.min.js';
            $this->registerJavascript('corejquery', $system_js_path, array('position' => 'head', 'priority' => 0));
        }
        $this->addJS($this->getKbModuleDir() . 'views/js/front/jquery-migrate-1.4.1.js');
        $this->addCSS($this->getKbModuleDir() . 'views/css/front/kblayout.css');
        $this->addJS($this->getKbModuleDir() . 'views/js/front/kbtooltip.js');
        $this->addJS($this->getKbModuleDir() . 'views/js/front/kb-common.js');
        $this->addJS($this->getKbModuleDir() . 'views/js/front/kb-list.js');
        $this->context->controller->addJqueryPlugin('alerts');
        $this->context->controller->addJqueryUI('ui.datepicker');
        $this->context->controller->addJqueryPlugin('fancybox');
    }

    public function postProcess()
    {
        parent::postProcess();

        if (Tools::getIsset('request_for_approve')) {
            $this->sendRequestToAgainApproveAccount();
        }


        if (Tools::isSubmit('ajax')) {
            header('Content-Type: application/json', true);
        }

        if (Tools::getIsset('kb_page_start') && (int)Tools::getValue('kb_page_start') > 0) {
            $this->page_start = Tools::getValue('kb_page_start');
        }
    }

    public function initContent()
    {
        parent::initContent();
        $this->context->smarty->assign(array(
            'HOOK_LEFT_COLUMN' => null,
            'HOOK_RIGHT_COLUMN' => null
        ));

        $this->context->smarty->assign(array(
            'HOOK_KBLEFT_COLUMN' => Hook::exec('displayKBLeftColumn'),
            'HOOK_KBRIGHT_COLUMN' => Hook::exec('displayKBRightColumn'),
        ));

        if ($this->kbtemplate) {
            $template = $this->context->smarty->fetch($this->kbtemplate);
        } else {
            $template = '';
        }

        if (isset($this->context->cookie->redirect_error)) {
            $prev_page_msgs = explode('####', $this->context->cookie->redirect_error);

            foreach ($prev_page_msgs as $e) {
                $this->Kberrors[] = $e;
            }
            unset($this->context->cookie->redirect_error);
        }

        if (isset($this->context->cookie->redirect_success)) {
            $prev_page_msgs = explode('####', $this->context->cookie->redirect_success);

            foreach ($prev_page_msgs as $e) {
                $this->Kbconfirmation[] = $e;
            }
            unset($this->context->cookie->redirect_success);
        }

        if ($this->seller_obj->approved == KbGlobal::DISSAPPROVED && $this->seller_obj->approval_request_limit > 0) {
            $request_approve_param = array('request_for_approve' => true);
            $link_to_request_approve = $this->context->link->getModuleLink(
                $this->kb_module_name,
                $this->controller_name,
                $request_approve_param,
                (bool)Configuration::get('PS_SSL_ENABLED')
            );
            $this->context->smarty->assign('approval_link', $link_to_request_approve);
        } elseif ($this->seller_obj->approved == KbGlobal::DISSAPPROVED
            && $this->seller_obj->approval_request_limit == 0) {
            $this->context->smarty->assign('account_dissaproved', 1);
        } elseif ($this->seller_obj->approved == KbGlobal::APPROVAL_WAITING) {
            $this->context->smarty->assign('waiting_for_approval', 1);
        } elseif ($this->seller_obj->active != 1) {
            $this->context->smarty->assign('account_disabled', 1);
        }

        $this->context->smarty->assign('mobile_device', $this->context->getMobileDevice());
        $this->context->smarty->assign('kb_errors', $this->Kberrors);
        $this->context->smarty->assign('kb_confirmation', $this->Kbconfirmation);
        $this->context->smarty->assign('kb_layout_dir', $this->getKbTemplateDir() . 'layouts/');
        $this->context->smarty->assign('TEMPLATE', $template);

        //ajax request and validation errors
        $this->context->smarty->assign(
            'ajax_error',
            $this->module->l('Technical Error: Contact to support')
        );
        $this->context->smarty->assign('required_field_error', $this->module->l('Required Field'));
        $this->context->smarty->assign('invalid_field_error', $this->module->l('Invalid value'));


        $layout = $this->getKbLayout();
        if ($layout) {
            $this->setTemplate($layout);
        } else {
            Tools::displayAsDeprecated(
                'Market Place layout file is missing from "'
                . $this->getKbTemplateDir() . '" directory.'
            );
        }
    }

    protected function getFormatedAddress(Address $the_address, $line_sep, $fields_style = array())
    {
        return AddressFormat::generateAddress($the_address, array('avoid' => array()), $line_sep, ' ', $fields_style);
    }

    protected function setKbTemplate($template)
    {
        if (!$path = $this->getKbTemplatePath($template)) {
            throw new PrestaShopException("Template '$template' not found");
        }

        $this->kbtemplate = $path;
    }

    public function getKbTemplatePath($template)
    {
        if (Tools::file_exists_cache($this->getKbTemplateDir() . $template)) {
            return $this->getKbTemplateDir() . $template;
        }

        return false;
    }

    public function getKbLayout()
    {
        $layout = false;

        if (!$layout && file_exists($this->getKbTemplateDir() . 'layout.tpl')) {
            $layout = 'module:'.$this->kb_module_name.'/views/templates/front/layout.tpl';
        }

        return $layout;
    }

    protected function getKbTemplateDir()
    {
        return $this->getKbModuleDir() . 'views/templates/front/';
    }

    protected function getKbModuleDir()
    {
        return _PS_MODULE_DIR_ . $this->kb_module_name . '/';
    }

    public function renderKbListFilter()
    {
        $this->context->smarty->assign(array(
            'filter_header' => $this->filter_header,
            'filter_id' => $this->filter_id,
            'filter_params' => $this->filters,
            'filter_action_name' => $this->filter_action_name
        ));

        return $this->context->smarty->fetch($this->getKbTemplateDir() . 'helper/filter.tpl');
    }

    public function renderKbList()
    {
        $this->context->smarty->assign(array(
            'table_header' => $this->table_header,
            'table_id' => $this->table_id,
            'table_content' => $this->table_content,
            'table_enable_multiaction' => $this->table_enable_multiaction
        ));

        $this->context->smarty->assign(
            'kb_pagination',
            $this->generatePaginator(
                $this->page_start,
                $this->total_records,
                $this->getTotalPages(),
                $this->list_row_callback
            )
        );

        return $this->context->smarty->fetch($this->getKbTemplateDir() . 'helper/list.tpl');
    }

    public function renderKbMultiAction()
    {
        $this->context->smarty->assign(array(
            'kb_multiaction_params' => $this->kb_multiaction_params
        ));

        return $this->context->smarty->fetch($this->getKbTemplateDir() . 'helper/multiaction.tpl');
    }

    public function getPageStart()
    {
        return (($this->page_start - 1) * $this->tbl_row_limit);
    }

    public function getTotalPages()
    {
        return ceil((int)$this->total_records / $this->tbl_row_limit);
    }

    public function generatePaginator($current_page, $total_records, $total_pages, $ajaxcallfn = '')
    {
        $summary_txt = '';
        $pagination = '';
        if ($total_pages > 0 && $total_pages != 1 && $current_page <= $total_pages) {
            $summary_align = 'kb-pagination-left';
            $pagination_align = 'kb-pagination-right';
            if (Configuration::get('KBMP_FRONT_PAGINATION_ALIGN') == 'left') {
                $summary_align = 'kb-pagination-right';
                $pagination_align = 'kb-pagination-left';
            }
            $record_start = $current_page;
            $record_end = (int)$this->tbl_row_limit;
            if ($current_page > 1) {
                $record_start = (($current_page - 1) * (int)$this->tbl_row_limit) + 1;
                if ($current_page == $total_pages) {
                    $record_end = $total_records;
                } else {
                    $record_end = $current_page * (int)$this->tbl_row_limit;
                }
            }

            $summary_txt = '<div class="' . $summary_align . ' kb-paginate-summary">
				Showing ' . $record_start . ' to ' . $record_end . ' of '
                . $total_records . ' (' . $total_pages . ' pages)</div>';

            $pagination .= '<div class="' . $pagination_align . '"><ul class="kb-pagination">';

            $ajax_call_function = '';
            if ($ajaxcallfn != '') {
                $ajax_call_function .= $ajaxcallfn . '(\'' . $this->table_id . '\', {page_number});';
            }

            $right_links = $current_page + 3;
            $previous = $current_page - 3; //previous link
            $first_link = true; //boolean var to decide our first link

            if ($current_page > 1) {
                $previous_link = ($previous == 0) ? 1 : $previous;
                $pagination .= '<li class="first"><a href="javascript:void(0)" data-page="1" 
					onclick="' . str_replace('{page_number}', 1, $ajax_call_function) . '" 
					title="First">&laquo;</a></li>'; //first link
                $pagination .= '<li><a href="javascript:void(0)" data-page="' . $previous_link . '" 
					onclick="' . str_replace('{page_number}', $previous_link, $ajax_call_function) . '" 
					title="Previous">&lt;</a></li>'; //previous link
                for ($i = ($current_page - 2); $i < $current_page; $i++) {
                    if ($i > 0) {
                        $pagination .= '<li><a href="javascript:void(0)" data-page="' . $i . '" 
						onclick="' . str_replace('{page_number}', $i, $ajax_call_function) . '" 
						title="Page' . $i . '">' . $i . '</a></li>';
                    }
                }
                $first_link = false; //set first link to false
            }

            if ($first_link) {
                $pagination .= '<li class="first active">' . $current_page . '</li>';
            } elseif ($current_page == $total_pages) {
                $pagination .= '<li class="last active">' . $current_page . '</li>';
            } else {
                $pagination .= '<li class="active">' . $current_page . '</li>';
            }

            for ($i = $current_page + 1; $i < $right_links; $i++) {
                if ($i <= $total_pages) {
                    $pagination .= '<li><a href="javascript:void(0)" data-page="' . $i . '" 
					onclick="' . str_replace('{page_number}', $i, $ajax_call_function) . '" 
					title="Page ' . $i . '">' . $i . '</a></li>';
                }
            }
            if ($current_page < $total_pages) {
                $next_link = ($i > $total_pages) ? $total_pages : $i;
                $pagination .= '<li><a href="javascript:void(0)" data-page="' . $next_link . '" 
					onclick="' . str_replace('{page_number}', $next_link, $ajax_call_function) . '" 
					title="Next">&gt;</a></li>'; //next link
                $pagination .= '<li class="last"><a href="javascript:void(0)" data-page="' . $total_pages . '" 
					onclick="' . str_replace('{page_number}', $total_pages, $ajax_call_function) . '" 
					title="Last">&raquo;</a></li>'; //last link
            }

            $pagination .= '</div></ul>';
            return $summary_txt . $pagination;
        }
        return '';
    }

    public function clipLongText(
        $text = '',
        $read_more_link = '',
        $length = self::LONG_TEXT_CHARACTER_LIMIT,
        $show_dots = true
    ) {
        if (Tools::strlen($text) > $length) {
            $text = Tools::safeOutput($text, false);
            $text = Tools::substr($text, 0, $length);
            if ($show_dots) {
                $text .= '...';
            }

            if ($read_more_link != '') {
                $text = $text . $read_more_link;
            }
        }
        return $text;
    }

    protected function getCategoryList($id_seller = 0, $return_unassigned = false)
    {
        $categories = array();
        $root_category = Category::getRootCategories();
        $all = Category::getSimpleCategories($this->context->language->id);
        $seller_categories = array();
        if ($id_seller > 0) {
            $seller_categories = KbSellerCategory::getCategoriesBySeller($id_seller);
        }
        foreach ($all as $c) {
            if ($root_category[0]['id_category'] != $c['id_category']) {
                $include_cat = false;
                if ($id_seller > 0) {
                    if ($return_unassigned && !in_array($c['id_category'], $seller_categories)) {
                        $include_cat = true;
                    } elseif (!$return_unassigned && in_array($c['id_category'], $seller_categories)) {
                        $include_cat = true;
                    }
                } else {
                    $include_cat = true;
                }

                if ($include_cat) {
                    $tmp = new Category($c['id_category'], $this->context->language->id, $this->context->shop->id);
                    $parents = $tmp->getParentsCategories();

                    $parents = array_reverse($parents);
                    $str = '';
                    foreach ($parents as $p) {
                        $str .= ' >> ' . $p['name'];
                    }

                    $categories[] = array(
                        'id_category' => $c['id_category'],
                        'name' => ltrim($str, ' >> ')
                    );
                }
            }
        }

        return $categories;
    }
    
    protected function getFieldValue($obj, $key, $id_lang = null, $default_value = false)
    {
        $default_value = false;
        if ($id_lang) {
            if (isset($obj->id) && $obj->id) {
                if (is_array($obj->{$key}) && isset($obj->{$key}[$id_lang])) {
                    $default_value = $obj->{$key}[$id_lang];
                } elseif (isset($obj->{$key})) {
                    $default_value = $obj->{$key};
                }
            }
        } else {
            if (isset($obj->id) && $obj->id) {
                if (isset($obj->{$key})) {
                    $default_value = $obj->{$key};
                }
            }
        }

        return Tools::getValue($key . ($id_lang ? '_' . $id_lang : ''), $default_value);
    }

    protected function uploadImage($name, $dir, $img_name, $ext = false, $width = null, $height = null)
    {
        if (isset($_FILES[$name]['tmp_name']) && !empty($_FILES[$name]['tmp_name'])) {
            $errors = array();
            // Check image validity
            $max_size = isset($this->img_size_limit) ? $this->img_size_limit : 0;
            $max_size = $max_size * 1024;
            if ($error = ImageManager::validateUpload($_FILES[$name], Tools::getMaxUploadSize($max_size))) {
                $errors[] = $error;
            }

            $tmp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS');
            if (!$tmp_name) {
                return false;
            }

            if (!move_uploaded_file($_FILES[$name]['tmp_name'], $tmp_name)) {
                return false;
            }

            // Evaluate the memory required to resize the image: if it's too much, you can't resize it.
            if (!ImageManager::checkImageMemoryLimit($tmp_name)) {
                $errors[] = $this->module->l('Due to memory limit restrictions, this image cannot be loaded. 
					Please contact to support');
            }

            if (!Tools::file_exists_no_cache($dir)) {
                @mkdir($dir, 0777);
            }

            // Copy new image
            if (empty($errors) && !ImageManager::resize(
                $tmp_name,
                $dir . $img_name . '.' . $this->imageType,
                (int)$width,
                (int)$height,
                ($ext ? $ext : $this->imageType)
            )
            ) {
                $errors[] = $this->module->l('An error occurred while uploading the image');
            }

            if (count($errors)) {
                $this->Kberrors = array_merge($this->Kberrors, $errors);
                return false;
            }

            unlink($tmp_name);
        }
        return true;
    }

    public function deleteImage($image_url = null)
    {
        if (file_exists($image_url) && !unlink($image_url)) {
            return false;
        }
        return true;
    }

    protected function cleanMetaKeywords($keywords)
    {
        if (!empty($keywords) && $keywords != '') {
            $out = array();
            $words = explode(',', $keywords);
            foreach ($words as $word_item) {
                $word_item = trim($word_item);
                if (!empty($word_item) && $word_item != '') {
                    $out[] = $word_item;
                }
            }
            return ((count($out) > 0) ? implode(',', $out) : '');
        } else {
            return '';
        }
    }

    protected function getAllReportFormat()
    {
        return array(
            KbSellerEarning::REPORT_FORMAT_DAILY => $this->module->l('Daily'),
            KbSellerEarning::REPORT_FORMAT_WEEKLY => $this->module->l('Weekly'),
            KbSellerEarning::REPORT_FORMAT_MONTHLY => $this->module->l('Monthly'),
            KbSellerEarning::REPORT_FORMAT_YEARLY => $this->module->l('Yearly')
        );
    }

    protected function sendRequestToAgainApproveAccount()
    {
        $seller = new KbSeller($this->seller_obj->id);
        $seller->approved = KbGlobal::APPROVAL_WAITING;
        if ($seller->approval_request_limit >= 0) {
            $seller->approval_request_limit = $seller->approval_request_limit - 1;
            if ($seller->save(true)) {
                $this->context->cookie->__set(
                    'redirect_success',
                    $this->module->l('Your request has been sent to admin. Please wait for approval.')
                );
                if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) &&
                    $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
                    $custom_ssl_var = 1;
                }
                if ((bool) Configuration::get('PS_SSL_ENABLED') && $custom_ssl_var == 1) {
                    $uri_path = _PS_BASE_URL_SSL_ . __PS_BASE_URI__;
                } else {
                    $uri_path = _PS_BASE_URL_ . __PS_BASE_URI__;
                }
                $template_vars = array(
                    '{{shop_title}}' => $this->seller_info['title'],
                    '{{seller_name}}' => $this->seller_info['seller_name'],
                    '{{seller_email}}' => $this->seller_info['email'],
                    '{{seller_contact}}' => $this->seller_info['phone_number'],
                    '{shop_url}' => $uri_path,
                );

                $email = new KbEmail(
                    KbEmail::getTemplateIdByName('mp_seller_account_approval_after_disapprove'),
                    $this->context->language->id
                );
                $email->send(
                    Configuration::get('PS_SHOP_EMAIL'),
                    Configuration::get('PS_SHOP_NAME'),
                    null,
                    $template_vars
                );
            } else {
                $this->context->cookie->__set(
                    'redirect_error',
                    $this->module->l('Error occurred while sending request to admin.')
                );
            }
        }
        Tools::redirect($this->context->link->getModuleLink(
            $this->kb_module_name,
            $this->controller_name,
            array(),
            (bool)Configuration::get('PS_SSL_ENABLED')
        ));
    }

    public function generatePDF($object, $template)
    {
        $pdf = new PDF($object, $template, Context::getContext()->smarty);
        $pdf->render();
        // We want to be sure that displaying PDF is the last thing this controller will do
        exit;
    }

    protected function getProductType($product)
    {
        if ($product->getType() == Product::PTYPE_PACK) {
            return $this->module->l('Packed');
        } elseif ($product->getType() == Product::PTYPE_VIRTUAL) {
            return $this->module->l('Downloadable');
        }
        return $this->module->l('Simple');
    }

    public function getTranslatedText($text, $class)
    {
        return Translate::getModuleTranslation($this->module, $text, $class);
    }
    
    protected function registerAdminSmartyPlugins()
    {
        $plugins = array(
            array('name' => 'toolsConvertPrice', 'params' => 'toolsConvertPrice'),
            array('name' => 'convertPrice', 'params' => array('Product', 'convertPrice')),
            array('name' => 'convertPriceWithCurrency', 'params' => array('Product', 'convertPriceWithCurrency')),
            array('name' => 'displayWtPrice', 'params' => array('Product', 'displayWtPrice')),
            array('name' => 'displayWtPriceWithCurrency', 'params' => array('Product', 'displayWtPriceWithCurrency')),
            array('name' => 'displayPrice', 'params' => array('Tools', 'displayPriceSmarty')),
            array('name' => 'convertAndFormatPrice', 'params' => array('Product', 'convertAndFormatPrice')),
            array('name' => 'displayAddressDetail', 'params' => array('AddressFormat', 'generateAddressSmarty')),
            array('name' => 'getWidthSize', 'params' => array('Image', 'getWidth')),
            array('name' => 'getHeightSize', 'params' => array('Image', 'getHeight'))
        );
        foreach ($plugins as $plugin) {
            smartyRegisterFunction($this->context->smarty, 'function', $plugin['name'], $plugin['params']);
        }
    }
}
