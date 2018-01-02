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

class AdminKbSellerCRequestController extends AdminKbMarketplaceCoreController
{
    public function __construct()
    {
        parent::__construct();
        $this->bootstrap = true;
        $this->table = 'kb_mp_seller_category_request';
        $this->className = 'KbSellerCRequest';
        $this->identifier = 'id_seller_category_request';
        $this->lang = false;
        $this->display = 'list';
        $this->allow_export = true;
//        $this->_use_found_rows = false;
//        $this->explicitSelect = true;
        $this->context = Context::getContext();
        $this->toolbar_title = 'Seller Category Request';

        $this->_select = ' CONCAT(LEFT(sn.`firstname`, 1), \'. \', sn.`lastname`) AS `seller_name`, 
			sn.email, cl.name as cname';

        $this->_join .= '
			INNER JOIN ' . _DB_PREFIX_ . 'kb_mp_seller as sr on (a.id_seller = sr.id_seller) 
			INNER JOIN `' . _DB_PREFIX_ . 'customer` sn ON (sr.`id_customer` = sn.`id_customer`) 
			INNER JOIN ' . _DB_PREFIX_ . 'category_lang as cl on (a.id_category = cl.id_category 
			AND cl.id_lang = ' . (int)$this->context->language->id . ' AND sn.id_lang=cl.id_lang)';

        $this->_where .= ' AND a.approved IN ( "'
                . (int) KbGlobal::APPROVAL_WAITING . '","' . (int) KbGlobal::DISSAPPROVED
                . '") GROUP BY a.id_seller_category_request';
//        $this->_group = 'a.id_seller_category_request';
        $this->_orderBy = 'a.id_seller_category_request';
        $this->_orderWay = 'DESC';
        

        $this->fields_list = array(
            'id_seller_category_request' => array(
                'title' => $this->module->l('ID'),
                'align' => 'text-center',
                'filter_key' => 'a!id_seller_category_request',
                'class' => 'fixed-width-xs'
            ),
            'seller_name' => array(
                'title' => $this->module->l('Seller'),
                'havingFilter' => true,
                'filter_key' => 'seller_name',
                'order_key' => 'seller_name',
            ),
            'email' => array(
                'title' => $this->module->l('Email'),
                'filter_key' => 'sn!email',
                'order_key' => 'email',
            ),
            'cname' => array(
                'title' => $this->module->l('Category'),
                'havingFilter' => true,
                'filter_key' => 'cl!name',
                'order_key' => 'cname',
            ),
            'comment' => array(
                'title' => $this->module->l('Comment'),
                'havingFilter' => false,
                'class' => 'comment_col_w',
                'maxlength' => 100
            ),
            'approved' => array(
                'title' => $this->module->l('Status'),
//                'havingFilter' => true,
                'type' => 'select',
                'list' => $this->approval_statuses,
                'callback' => 'showApprovedStatus',
                'filter_type' => 'text',
                'filter_key' => 'a!approved',
                'order_key' => 'a.approved'
            ),
            'date_add' => array(
                'title' => $this->module->l('Request Date'),
                'filter_key' => 'a!date_add',
                'havingFilter' => true,
            )
        );

        $this->addRowAction('viewmodal');
        $this->addRowAction('approve');
        $this->addRowAction('disapprovescategoryrequest');
    }

    public function initProcess()
    {
        parent::initProcess();
        if (Tools::getIsset('approve' . $this->table)) {
            $this->action = 'approveRequest';
        } elseif (Tools::getIsset('dissapprove' . $this->table)) {
            $this->action = 'dissapproveRequest';
        }
    }

    public function postProcess()
    {
        parent::postProcess();
    }

    public function setMedia()
    {
        parent::setMedia();
        $this->addJqueryPlugin('fancybox');
    }

    public function initContent()
    {
        $tpl = $this->custom_smarty->createTemplate('ajax_view_popup.tpl');

        $this->content .= $tpl->fetch();
        $this->content .= $this->getReasonPopUpHtml();
        parent::initContent();
    }

    public function initToolbar()
    {
        parent::initToolbar();
        unset($this->toolbar_btn['new']);
    }

    public function processKbAjaxView()
    {
        $this->render_ajax_html = true;
        $id_requested_category = (int)Tools::getValue('id_seller_category_request');

        $req_obj = new $this->className($id_requested_category);

        $tpl = $this->custom_smarty->createTemplate('view_category_request.tpl');

        $seller_obj = new KbSeller($req_obj->id_seller);

        $seller_info = $seller_obj->getSellerInfo();

        $category = new Category($req_obj->id_category, $this->context->language->id);
        $parent_categories = $category->getParentsCategories($this->context->language->id);
        $category_string = KbGlobal::makeParentToChildCategoryStr(
            array_reverse($parent_categories),
            $this->context->language->id
        );

        $tpl->assign(array(
            'seller_name' => $seller_info['seller_name'],
            'seller_shop' => $seller_info['title'],
            'req_category' => $category_string,
            'comment' => Tools::safeOutput($req_obj->comment, true),
            'req_cat_heading' => $this->module->l('Requested Category'),
            'comment_heading' => $this->module->l('Comment')
        ));

        return $tpl->fetch();
    }

    public function processApproveRequest()
    {
        if (Tools::getIsset($this->identifier)) {
            $object = new $this->className(Tools::getValue($this->identifier));
            $object->approved = (string)KbGlobal::APPROVED;

            if ($object->save()) {
                $sell_obj = new KbSeller($object->id_seller);
                $seller_info = $sell_obj->getSellerInfo();

                $tracked_products = KbSellerCategory::getDisabledCategoryProducts(
                    $object->id_seller,
                    $object->id_category
                );
                if (count($tracked_products) > 0) {
                    foreach ($tracked_products as $row) {
                        $product = new Product($row['id_product']);
                        $product->updateCategories(array($object->id_category));

                        $where = 'id_seller = ' . (int)$object->id_seller . ' AND id_category = '
                            . (int)$object->id_category . ' AND id_product = ' . (int)$row['id_product'];

                        Db::getInstance(_PS_USE_SQL_SLAVE_)->delete(
                            'kb_mp_seller_category_tracking',
                            pSQL($where)
                        );
                    }
                }

                $row_id = (int)KbSellerCategory::getRowIdBySellerAndCategory($object->id_seller, $object->id_category);

                $seller_cat = new KbSellerCategory($row_id);
                $seller_cat->id_seller = (int)$sell_obj->id;
                $seller_cat->id_shop = (int)$sell_obj->id_shop;
                $seller_cat->id_category = $object->id_category;

                if ($seller_cat->save()) {
                    $this->context->cookie->__set(
                        'kb_redirect_success',
                        $this->module->l('Category has been successfully approved for requested seller.')
                    );
                    $category = new Category($object->id_category, $sell_obj->id_default_lang);
                    $parent_categories = $category->getParentsCategories($sell_obj->id_default_lang);
                    $category_string = KbGlobal::makeParentToChildCategoryStr(
                        array_reverse($parent_categories),
                        $sell_obj->id_default_lang
                    );

                    //send email to Seller
                    $template_vars = array(
                        '{{seller_name}}' => $seller_info['seller_name'],
                        '{{shop_title}}' => $seller_info['title'],
                        '{{seller_email}}' => $seller_info['email'],
                        '{{seller_contact}}' => $sell_obj->phone_number,
                        '{{requested_category}}' => $category_string,
                    );
                    $email = new KbEmail(
                        KbEmail::getTemplateIdByName('mp_category_request_approved'),
                        $sell_obj->id_default_lang
                    );
                    $notification_emails = $sell_obj->getEmailIdForNotification();
                    foreach ($notification_emails as $em) {
                        $email->send($em['email'], $em['title'], null, $template_vars);
                    }
                } else {
                    $this->context->cookie->__set(
                        'kb_redirect_success',
                        $this->module->l('Category has been approved but not mapped with requested seller.')
                    );
                }
            } else {
                $this->context->cookie->__set(
                    'kb_redirect_error',
                    $this->module->l('Error occurred while approving category')
                );
            }
        }
        Tools::redirectAdmin($this->context->link->getAdminLink('AdminKbSellerCRequest'));
    }

    public function processDissapproveRequest()
    {
        if (Tools::getIsset($this->identifier)) {
            $object = new $this->className(Tools::getValue($this->identifier));
            $object->approved = (string)KbGlobal::DISSAPPROVED;
//            print_r(Tools::getAllValues());die;
            if ($object->save()) {
                //send email to Seller
                $sell_obj = new KbSeller($object->id_seller);
                $seller_info = $sell_obj->getSellerInfo();

                $category = new Category($object->id_category, $sell_obj->id_default_lang);
                $parent_categories = $category->getParentsCategories($sell_obj->id_default_lang);
                $category_string = KbGlobal::makeParentToChildCategoryStr(
                    array_reverse($parent_categories),
                    $sell_obj->id_default_lang
                );
                if (Tools::getValue('marketplace_reason_comment', '') !=
                    strip_tags(Tools::getValue('marketplace_reason_comment', ''))) {
                    $reason_comment = strip_tags(Tools::getValue('marketplace_reason_comment', ''));
                } else {
                    $reason_comment = Tools::getValue('marketplace_reason_comment', '');
                }
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
                    '{{seller_name}}' => $seller_info['seller_name'],
                    '{{shop_title}}' => $seller_info['title'],
                    '{{seller_email}}' => $seller_info['email'],
                    '{{seller_contact}}' => $sell_obj->phone_number,
                    '{{requested_category}}' => $category_string,
                    '{{comment}}' => $reason_comment,
                    '{shop_url}' => $uri_path
                );
                $email = new KbEmail(
                    KbEmail::getTemplateIdByName('mp_category_request_disapproved'),
                    $sell_obj->id_default_lang
                );
                $notification_emails = $sell_obj->getEmailIdForNotification();
                foreach ($notification_emails as $em) {
                    $email->send($em['email'], $em['title'], null, $template_vars);
                }

                $reason_log = new KbReasonLog();
                $reason_log->reason_type = 5;
                $reason_log->id_seller = $object->id_seller;
                $reason_log->id_seller_category_request = $object->id;
                $reason_log->id_employee = $this->context->employee->id;
                $reason_log->comment = $reason_comment;
                $reason_log->save(true);

                $this->context->cookie->__set(
                    'kb_redirect_success',
                    $this->module->l('Category has been successfully Disapproved.')
                );
            } else {
                $this->context->cookie->__set(
                    'kb_redirect_error',
                    $this->module->l('Error occurred while disapproving category')
                );
            }
        }
        Tools::redirectAdmin($this->context->link->getAdminLink('AdminKbSellerCRequest'));
    }
}
