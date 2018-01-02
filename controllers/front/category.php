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

class KbmarketplaceCategoryModuleFrontController extends KbmarketplaceCoreModuleFrontController
{
    public $controller_name = 'category';

    const MIN_REASON_LENGTH = 30;
    const MAX_REASON_LENGTH = 300;

    public function __construct()
    {
        parent::__construct();
    }

    public function setMedia()
    {
        $this->addCSS($this->getKbModuleDir() . 'views/css/front/kb-forms.css');
        parent::setMedia();
    }

    public function postProcess()
    {
        parent::postProcess();
        if (Tools::isSubmit('ajax')) {
            $this->json = array();
            $renderhtml = false;
            if (Tools::isSubmit('method')) {
                switch (Tools::getValue('method')) {
                    case 'getSellerCategories':
                        $this->json = $this->getAjaxSellerCategoriesListHtml();
                        break;
                    case 'getRequestDetail':
                        $this->json = $this->getAjaxRequestDetail();
                        break;
                }
            }
            if (!$renderhtml) {
                echo Tools::jsonEncode($this->json);
            }
            die;
        } elseif (Tools::isSubmit('category_request_id')) {
            $this->saveNewCategoryRequest();
        }
    }

    public function initContent()
    {
        $categories = $this->getCategoryList($this->seller_obj->id, true);
        $req_categories = KbSellerCRequest::getRequestBySeller(
            $this->seller_info['id_seller'],
            0,
            false,
            $this->getPageStart(),
            $this->tbl_row_limit
        );
        foreach ($req_categories as $c) {
            foreach ($categories as $key => $ct) {
                if ($ct['id_category'] == $c['id_category']) {
                    unset($categories[$key]);
                }
            }
        }
        $request_category_id = (int)Tools::getValue('category_request_id', 0);
        if (Tools::getValue('category_request_reason', '') !=
            strip_tags(Tools::getValue('category_request_reason', ''))) {
            $request_category_reason = strip_tags(Tools::getValue('category_request_reason', ''));
        } else {
            $request_category_reason = Tools::getValue('category_request_reason', '');
        }
//        $request_category_reason = Tools::getValue('category_request_reason', '');

        Hook::exec('actionKbMarketPlaceAvailCatList', array('categories' => $categories));
        
        $this->context->smarty->assign('available_categories', $categories);
        $this->context->smarty->assign('min_reason_length', self::MIN_REASON_LENGTH);
        $this->context->smarty->assign('max_reason_length', self::MAX_REASON_LENGTH);
        $this->context->smarty->assign('request_category_id', $request_category_id);
        $this->context->smarty->assign('request_category_reason', $request_category_reason);

        $this->total_records = KbSellerCRequest::getRequestBySeller($this->seller_info['id_seller'], false, true);
        if ($this->total_records > 0) {
            $filter_cat_list = array();
            $tmp = $this->getCategoryList();
            foreach ($tmp as $cat) {
                $filter_cat_list[] = array('value' => $cat['id_category'], 'label' => $cat['name']);
            }

            $tmp = KbGlobal::getApporvalStatus();
            $approve_statuses = array();
            foreach ($tmp as $key => $val) {
                $approve_statuses[] = array('value' => $key, 'label' => $val);
            }

            $i = 0;
            $rating_array = array();
            while ($i <= KbGlobal::MAX_RATING) {
                $rating_array[] = array('value' => $i, 'label' => $i);
                $i++;
            }

            $this->filter_header = $this->module->l('Filter Your Search');
            $this->filter_id = 'seller_category_request_list';
            $this->filters = array(
                array(
                    'type' => 'select',
                    'placeholder' => $this->module->l('Select'),
                    'name' => 'id_category',
                    'label' => $this->module->l('Category'),
                    'values' => $filter_cat_list
                ),
                array(
                    'type' => 'select',
                    'placeholder' => $this->module->l('Select'),
                    'name' => 'approved',
                    'label' => $this->module->l('Status'),
                    'values' => $approve_statuses
                )
            );
            $this->filter_action_name = 'getSellerCategories';
            $this->context->smarty->assign('kbfilter', $this->renderKbListFilter());

            $this->table_id = $this->filter_id;
            $this->table_header = array(
                array('label' => $this->module->l('Date')),
                array('label' => $this->module->l('Category')),
                array('label' => $this->module->l('Status'))
            );

            $req_categories = KbSellerCRequest::getRequestBySeller(
                $this->seller_info['id_seller'],
                false,
                false,
                $this->getPageStart(),
                $this->tbl_row_limit
            );
            foreach ($req_categories as $ct) {
                $category = new Category($ct['id_category'], $this->seller_obj->id_default_lang);
                $parent_categories = $category->getParentsCategories($this->seller_obj->id_default_lang);
                $category_string = KbGlobal::makeParentToChildCategoryStr(
                    array_reverse($parent_categories),
                    $this->seller_obj->id_default_lang
                );
                $this->table_content[] = array(
                    array('value' => Tools::displayDate($ct['date_add'], null, false)),
                    array(
                        'link' => array(
                            'function' => 'getSellerRequestedCategoryDetail('
                            . $ct['id_seller_category_request'] . ')',
                            'title' => $this->module->l('Click to view detail')),
                        'value' => $category_string,
                        'class' => '',
                    ),
                    array('value' => KbGlobal::getApporvalStatus($ct['approved']))
                );
            }

            $this->list_row_callback = $this->filter_action_name;
        }

        $this->context->smarty->assign('kblist', $this->renderKbList());

        $this->setKbTemplate('seller/category_request.tpl');
        parent::initContent();
    }
    
    public function getTemplateVarPage()
    {
        $page = parent::getTemplateVarPage();
        if (isset($page['meta']) && $this->seller_info) {
            $page_title = 'New Category Request';
            $page['meta']['title'] =  $page_title;
            $page['meta']['keywords'] = $this->seller_info['meta_keyword'];
            $page['meta']['description'] = $this->seller_info['meta_description'];
        }
        return $page;
    }
    
    protected function saveNewCategoryRequest()
    {
        $id_category_request = Tools::getValue('category_request_id', 0);

        $cat_obj = new Category($id_category_request);
        if ($cat_obj != '' && Validate::isLoadedObject($cat_obj)) {
            $request_obj = new KbSellerCRequest();
            $request_obj->id_seller = $this->seller_obj->id;
            $request_obj->id_shop = $this->seller_obj->id_shop;
            $request_obj->id_lang = $this->seller_obj->id_default_lang;
            $request_obj->id_category = $id_category_request;
            if (Tools::getValue('category_request_reason', '') !=
                strip_tags(Tools::getValue('category_request_reason', ''))) {
                $category_request_reason = strip_tags(Tools::getValue('category_request_reason', ''));
                $request_obj->comment = strip_tags(Tools::getValue('category_request_reason', ''));
            } else {
                $category_request_reason = Tools::getValue('category_request_reason');
                $request_obj->comment = Tools::getValue('category_request_reason');
            }
//            $request_obj->comment = Tools::getValue('category_request_reason', '');
            $request_obj->approved = "0";

            if ($request_obj->save()) {
                $this->context->cookie->__set(
                    'redirect_success',
                    $this->module->l(
                        'Your request is successfully sent to admin. Wait for his approval.'
                    )
                );

                $category = new Category($id_category_request, $this->seller_obj->id_default_lang);
                $parent_categories = $category->getParentsCategories($this->seller_obj->id_default_lang);
                $category_string = KbGlobal::makeParentToChildCategoryStr(
                    array_reverse($parent_categories),
                    $this->seller_obj->id_default_lang
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

                //send email to Admin
                $template_vars = array(
                    '{{shop_title}}' => $this->seller_info['title'],
                    '{{seller_name}}' => $this->seller_info['seller_name'],
                    '{{seller_email}}' => $this->seller_info['email'],
                    '{{requested_category}}' => $category_string,
                    '{{reason}}' => $category_request_reason,
                    '{{seller_contact}}' => $this->seller_info['phone_number'],
                    '{shop_url}' => $uri_path,
                );
                $email = new KbEmail(
                    KbEmail::getTemplateIdByName('mp_category_request_notification_admin'),
                    $this->seller_obj->id_default_lang
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
                    $this->module->l('Error occurred while sending your request to admin.')
                );
            }

            Tools::redirect($this->context->link->getModuleLink(
                $this->kb_module_name,
                $this->controller_name,
                array(),
                (bool)Configuration::get('PS_SSL_ENABLED')
            ));
        } else {
            $this->Kberrors[] = $this->module->l('Requested category is not valid');
        }
        Hook::exec('actionKbMarketPlaceCRequestAfterSave');
    }

    protected function getAjaxSellerCategoriesListHtml()
    {
        $json = array();

        $custom_filter = '';

        if (Tools::getIsset('id_category') && Tools::getValue('id_category') != '') {
            $custom_filter .= ' AND id_category = ' . (int)Tools::getValue('id_category');
        }

        $approved = false;
        if (Tools::getIsset('approved') && Tools::getValue('approved') != '') {
            $approved = pSQL(Tools::getValue('approved'));
        }

        $this->total_records = KbSellerCRequest::getRequestBySeller(
            $this->seller_info['id_seller'],
            $approved,
            true,
            null,
            null,
            $custom_filter
        );
        if ($this->total_records > 0) {
            if (Tools::getIsset('start') && (int)Tools::getValue('start') > 0) {
                $this->page_start = (int)Tools::getValue('start');
            }

            $this->table_id = 'seller_category_request_list';

            $req_categories = KbSellerCRequest::getRequestBySeller(
                $this->seller_info['id_seller'],
                $approved,
                false,
                $this->getPageStart(),
                $this->tbl_row_limit,
                $custom_filter
            );

            $row_html = '';
            foreach ($req_categories as $ct) {
                $category = new Category($ct['id_category'], $this->seller_obj->id_default_lang);
                $parent_categories = $category->getParentsCategories($this->seller_obj->id_default_lang);
                $category_string = KbGlobal::makeParentToChildCategoryStr(
                    array_reverse($parent_categories),
                    $this->seller_obj->id_default_lang
                );
                $row_html .= '<tr>
								<td>' . Tools::displayDate($ct['date_add'], null, false) . '</td>
								<td>
									<a href="javascript:void(0)" title="' . $this->module->l('Click to view detail')
                    . '" onclick="getSellerRequestedCategoryDetail('
                    . $ct['id_seller_category_request'] . ')">' . $category_string . '</a>
								</td>
								<td>' . KbGlobal::getApporvalStatus($ct['approved']) . '</td>
						</tr>';
            }

            $this->list_row_callback = 'getSellerCategories';
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

    protected function getAjaxRequestDetail()
    {
        $json = array();
        $id_requested_category = (int)Tools::getValue('id_request', 0);
        if ($id_requested_category > 0) {
            $req_cat = new KbSellerCRequest($id_requested_category);

            $category = new Category($req_cat->id_category, $this->seller_obj->id_default_lang);
            $parent_categories = $category->getParentsCategories($this->seller_obj->id_default_lang);
            $category_string = KbGlobal::makeParentToChildCategoryStr(
                array_reverse($parent_categories),
                $this->seller_obj->id_default_lang
            );

            $data = array(
                'category_name' => $category_string,
                'posted_on' => Tools::displayDate($req_cat->date_add, null, false),
                'status' => KbGlobal::getApporvalStatus($req_cat->approved),
                'seller_comment' => Tools::safeOutput($req_cat->comment)
            );

            if ($req_cat->approved == KbGlobal::DISSAPPROVED) {
                $row = KbReasonLog::getReasonOfCategoryDissapproval($id_requested_category);
                if (count($row) > 0) {
                    $data = array_merge($data, array('admin_comment' => Tools::safeOutput($row['comment'])));
                }
            }
            
            Hook::exec('displayKbMarketPlaceCRequest', array('object' => $req_cat, 'data' => $data));

            $json['status'] = true;
            $json['data'] = $data;
        } else {
            $json['status'] = false;
            $json['msg'] = $this->module->l('No Data Found');
        }
        return $json;
    }
}
