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

class AdminKbProductApprovalListController extends AdminKbMarketplaceCoreController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'product';
        $this->className = 'Product';
        $this->identifier = 'id_product';
        $this->deleted = false;
        $this->lang = true;
        $this->display = 'list';
        $this->allow_export = true;
        $this->bulk_actions = array();
        $this->context = Context::getContext();
        $this->toolbar_title = 'Product Approval List';
        $this->imageType = 'jpg';

        parent::__construct();
        $alias_image = 'image_shop';

        $this->_select .= 'shop.name as shopname, cus.`firstname`, cus.`lastname`,';
        $this->_select .= 'MAX(' . pSQL($alias_image) . '.id_image) id_image, 
			cl.name `name_category`, 0 AS price_final, sav.`quantity` as sav_quantity, s2p.approved';

        $this->_join .= ' INNER JOIN ' . _DB_PREFIX_ . 'kb_mp_seller_product as s2p on (a.id_product = s2p.id_product) 
            INNER JOIN ' . _DB_PREFIX_ . 'kb_mp_seller as seller 
			ON (s2p.`id_seller` = seller.`id_seller`)';
        $this->_join .= ' INNER JOIN ' . _DB_PREFIX_ . 'customer as cus ON (seller.`id_customer` = cus.`id_customer`)';

        $id_shop = Shop::isFeatureActive() && Shop::getContext() == Shop::CONTEXT_SHOP
            ? (int)$this->context->shop->id : 'a.id_shop_default';

        $this->_join .= '
		INNER JOIN `' . _DB_PREFIX_ . 'product_shop` sa 
			ON (a.`id_product` = sa.`id_product` AND sa.id_shop = ' . $id_shop . ') 
		LEFT JOIN `' . _DB_PREFIX_ . 'image` i ON (i.`id_product` = a.`id_product`) 
		LEFT JOIN `' . _DB_PREFIX_ . 'stock_available` sav 
			ON (sav.`id_product` = a.`id_product` AND sav.`id_product_attribute` = 0 '
            . StockAvailable::addSqlShopRestriction(null, null, 'sav') . ') ';

        $this->_join .= '
		LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` cl ON (a.`id_category_default` = cl.`id_category` 
			AND b.`id_lang` = cl.`id_lang` AND cl.id_shop = ' . $id_shop . ') 
		LEFT JOIN `' . _DB_PREFIX_ . 'shop` shop ON (shop.id_shop = ' . $id_shop . ') 
		LEFT JOIN `' . _DB_PREFIX_ . 'image_shop` image_shop ON (image_shop.`id_image` = i.`id_image` 
			AND image_shop.`cover` = 1 AND image_shop.id_shop = ' . $id_shop . ')';

        $this->_where .= ' AND s2p.approved IN ( "' .KbGlobal::APPROVAL_WAITING. '","' .KbGlobal::DISSAPPROVED. '")';

        $this->_group = ' GROUP BY a.id_product';

        $this->fields_list = array();
        $this->fields_list['id_product'] = array(
            'title' => $this->module->l('ID'),
            'align' => 'center',
            'class' => 'fixed-width-xs',
            'type' => 'int',
            'order_key' => 'a.id_product'
        );
        $this->fields_list['image'] = array(
            'title' => $this->module->l('Image'),
            'align' => 'center',
            'image_id' => 'id_product',
            'image' => 'p',
            'orderby' => false,
            'filter' => false,
            'search' => false
        );
        $this->fields_list['name'] = array(
            'title' => $this->module->l('Name'),
            'filter_key' => 'b!name',
            'order_key' => 'b.name'
        );

        $this->fields_list['reference'] = array(
            'title' => $this->module->l('Reference'),
            'align' => 'left',
            'order_key' => 'a.reference'
        );

        $this->fields_list['firstname'] = array(
            'title' => $this->module->l('Seller First Name'),
            'align' => 'left',
            'filter_key' => 'cus!firstname',
            'order_key' => 'cus.firstname'
        );
        $this->fields_list['lastname'] = array(
            'title' => $this->module->l('Seller Last Name'),
            'align' => 'left',
            'filter_key' => 'cus!lastname',
            'order_key' => 'cus.lastname'
        );

        if (Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_SHOP) {
            $this->fields_list['shopname'] = array(
                'title' => $this->module->l('Default shop'),
                'filter_key' => 'shop!name',
                'order_key' => 'shop.name'
            );
        } else {
            $this->fields_list['name_category'] = array(
                'title' => $this->module->l('Category'),
                'filter_key' => 'cl!name',
                'order_key' => 'cl.name'
            );
        }

        if (Configuration::get('PS_STOCK_MANAGEMENT')) {
            $this->fields_list['sav_quantity'] = array(
                'title' => $this->module->l('Quantity'),
                'type' => 'int',
                'align' => 'text-right',
                'filter_key' => 'sav!quantity',
                'order_key' => 'sav.quantity',
                'orderby' => true
            );
        }

        $this->fields_list['active'] = array(
            'title' => $this->module->l('Active'),
            'callback' => 'sellerProductActive',
            'align' => 'text-center',
//            'type' => 'bool',
            'class' => 'fixed-width-sm',
            'orderby' => false,
            'order_key' => 'a.active'
        );

        $this->fields_list['approved'] = array(
            'title' => $this->module->l('Status'),
            'type' => 'select',
            'list' => $this->approval_statuses,
            'callback' => 'showApprovedStatus',
            'filter_type' => 'text',
            'filter_key' => 's2p!approved',
            'order_key' => 's2p.approved',
        );

        $this->addRowAction('viewapprovalproduct');
        $this->addRowAction('approve');
        $this->addRowAction('disapproveapprovalproduct');
    }
    
    public function sellerProductActive($id_row, $tr)
    {
        unset($id_row);
        if (!empty($tr)) {
            $seller_product = Db::getInstance()->getRow(
                'SELECT * FROM '._DB_PREFIX_.'kb_mp_seller_product_tracking'
                . ' WHERE id_product='. (int) $tr['id_product']
            );
            if (!empty($seller_product)) {
                return '<a class="list-action-enable action-enabled"'
                . ' href="javascript:void(0)" title="Enabled">'
                . '<i class="icon-check"></i></a>';
            } else {
                return '<a class="list-action-enable action-disabled"'
                . ' href="javascript:void(0)" title="Disable">'
                . '<i class="icon-remove"></i></a>';
            }
        }
    }
    
    public function initProcess()
    {
        parent::initProcess();
        if (Tools::getIsset('approve' . $this->table)) {
            $this->action = 'approveSellerProduct';
        } elseif (Tools::getIsset('dissapprove' . $this->table)) {
            $this->action = 'dissapproveSellerProduct';
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
        $this->content .= $this->getReasonPopUpHtml();
        parent::initContent();
    }

    public function initToolbar()
    {
        parent::initToolbar();
        unset($this->toolbar_btn['new']);
    }

    public function getList(
        $id_lang,
        $orderBy = null,
        $orderWay = null,
        $start = 0,
        $limit = null,
        $id_lang_shop = null
    ) {
        parent::getList($id_lang, $orderBy, $orderWay, $start, $limit, $id_lang_shop);
    }

    public function initPageHeaderToolbar()
    {
        parent::initPageHeaderToolbar();
    }

    public function processApproveSellerProduct()
    {
        if (Tools::getIsset($this->identifier)) {
            $seller_product_id =$this->getSellerProductIdByProductId(Tools::getValue($this->identifier));
            $seller_product = new KbSellerProduct($seller_product_id);
            

            $seller_obj = new KbSeller($seller_product->id_seller);
            $seller = $seller_obj->getSellerInfo();
            if ($seller['active'] == 1 && $seller['approved'] == KbGlobal::APPROVED) {
                $seller_product->approved = (string)KbGlobal::APPROVED;
                $seller_product->save();
                KbSellerProduct::trackAndUpdateApprovedProduct($seller['id_seller'], $seller_product->id_product);
            
                $product = new Product($seller_product->id_product, false, $seller['id_default_lang']);
                $custom_ssl_var = '';
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
                    '{{shop_title}}' => $seller['title'],
                    '{shop_url}' => $uri_path,
                    '{{seller_name}}' => $seller['seller_name'],
                    '{{seller_email}}' => $seller['email'],
                    '{{seller_contact}}' => $seller['phone_number'],
                    '{{product_name}}' => $product->name,
                    '{{product_sku}}' => $product->reference,
                    '{{product_price}}' => Tools::displayPrice($product->price),
                );

                $email = new KbEmail(
                    KbEmail::getTemplateIdByName('mp_product_approval_notification'),
                    $seller['id_default_lang']
                );

                $notification_emails = $seller_obj->getEmailIdForNotification();
                foreach ($notification_emails as $em) {
                    $email->send($em['email'], $em['title'], null, $template_vars);
                }

                $this->context->cookie->__set(
                    'kb_redirect_success',
                    $this->module->l('Product has been approved successfully.')
                );
            } else {
                $this->context->cookie->__set(
                    'kb_redirect_error',
                    $this->module->l('Before approving product, first approve or activate Seller Account.')
                );
            }
        }
        Tools::redirectAdmin($this->context->link->getAdminLink('AdminKbProductApprovalList'));
    }

    public function processDissapproveSellerProduct()
    {
        if (Tools::getIsset($this->identifier)) {
            $seller_product_id =$this->getSellerProductIdByProductId(Tools::getValue($this->identifier));
            $seller_product = new KbSellerProduct($seller_product_id);
            $seller_product->approved = (string)KbGlobal::DISSAPPROVED;
            $seller_product->save();

            $seller_obj = new KbSeller($seller_product->id_seller);
            $seller = $seller_obj->getSellerInfo();
            $product = new Product($seller_product->id_product, false, $seller['id_default_lang']);
            if (Tools::getValue('marketplace_reason_comment') !=
                strip_tags(Tools::getValue('marketplace_reason_comment'))) {
                $reason_comment = strip_tags(Tools::getValue('marketplace_reason_comment'));
            } else {
                $reason_comment = Tools::getValue('marketplace_reason_comment');
            }
            $template_vars = array(
                '{{shop_title}}' => $seller['title'],
                '{{seller_name}}' => $seller['seller_name'],
                '{{seller_email}}' => $seller['email'],
                '{{seller_contact}}' => $seller['phone_number'],
                '{{product_name}}' => $product->name,
                '{{product_sku}}' => $product->reference,
                '{{product_price}}' => Tools::displayPrice($product->price),
                '{{reason}}' => $reason_comment
            );

            $email = new KbEmail(
                KbEmail::getTemplateIdByName('mp_product_disapproval_notification'),
                $seller['id_default_lang']
            );
            $notification_emails = $seller_obj->getEmailIdForNotification();
            foreach ($notification_emails as $em) {
                $email->send($em['email'], $em['title'], null, $template_vars);
            }

            $reason_log = new KbReasonLog();
            $reason_log->reason_type = 3;
            $reason_log->id_seller = $seller_product->id_seller;
            $reason_log->id_seller_product = $seller_product_id;
            $reason_log->id_employee = $this->context->employee->id;
            $reason_log->comment = Tools::getValue('marketplace_reason_comment');
            $reason_log->save(true);

            $this->context->cookie->__set(
                'kb_redirect_success',
                $this->module->l('Product has been disapproved successfully.')
            );
        }
        Tools::redirectAdmin($this->context->link->getAdminLink('AdminKbProductApprovalList'));
    }

    public static function getSellerProductIdByProductId($id_product)
    {
        $sql = 'Select id_seller_product from ' . _DB_PREFIX_ . 'kb_mp_seller_product 
                where id_product = ' . (int)$id_product;

        return (int)DB::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
    }

    protected function processBulkStatusSelection($status)
    {
        unset($status);
        if (is_array($this->boxes) && !empty($this->boxes)) {
            $this->context->cookie->__set(
                'kb_redirect_error',
                $this->module->l('For Enable/Disable the product Go to Seller\'s Products tab')
            );
        }
        Tools::redirectAdmin($this->context->link->getAdminLink('AdminKbProductApprovalList'));
    }
}
