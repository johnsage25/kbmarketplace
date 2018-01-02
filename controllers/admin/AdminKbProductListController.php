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

class AdminKbProductListController extends AdminKbMarketplaceCoreController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'kb_mp_seller_product';
        $this->className = 'KbSellerProduct';
        $this->identifier = 'id_seller_product';
        $this->deleted = false;
        $this->lang = false;
        $this->display = 'list';
        $this->bulk_actions = array();
        $this->allow_export = true;
        $this->context = Context::getContext();
        $this->toolbar_title = 'Product List';
        $this->imageType = 'jpg';

        parent::__construct();
        $alias_image = 'image_shop';

        $this->_select .= 'shop.name as shopname, cus.`firstname`, cus.`lastname`,';
        $this->_select .= 'b.name, p.id_product, p.reference, MAX(' . $alias_image . '.id_image) id_image, 
			cl.name `name_category`, p.`price`, 0 AS price_final, sav.`quantity` as sav_quantity, p.`active`';

        if (Tools::getIsset('id_seller') && Tools::getValue('id_seller') > 0) {
            $this->_join .= ' INNER JOIN ' . _DB_PREFIX_ . 'kb_mp_seller as seller 
				ON (a.`id_seller` = seller.`id_seller` 
				AND seller.id_seller = ' . (int)Tools::getValue('id_seller') . ')';
            $this->_join .= ' INNER JOIN ' . _DB_PREFIX_ . 'customer as cus ON (c.`id_customer` = a.`id_customer`)';
        } else {
            $this->_join .= ' INNER JOIN ' . _DB_PREFIX_ . 'kb_mp_seller as seller 
				ON (a.`id_seller` = seller.`id_seller`)';
            $this->_join .= ' INNER JOIN ' . _DB_PREFIX_ . 'customer as cus 
				ON (seller.`id_customer` = cus.`id_customer`)';
        }

        $id_shop = Shop::isFeatureActive() && Shop::getContext() == Shop::CONTEXT_SHOP
            ? (int)$this->context->shop->id : 'p.id_shop_default';

        $this->_join .= '
		INNER JOIN ' . _DB_PREFIX_ . 'product as p on (a.id_product = p.id_product) 
		INNER JOIN `' . _DB_PREFIX_ . 'product_shop` sa ON (p.`id_product` = sa.`id_product` 
			AND sa.id_shop = ' . $id_shop . ') 
		INNER JOIN `' . _DB_PREFIX_ . 'product_lang` b ON (p.`id_product` = b.`id_product` 
			AND sa.id_shop = ' . $id_shop . ' AND b.id_lang = ' . (int)$this->context->language->id . ') 
		LEFT JOIN `' . _DB_PREFIX_ . 'image` i ON (i.`id_product` = p.`id_product`) 
		LEFT JOIN `' . _DB_PREFIX_ . 'stock_available` sav ON (sav.`id_product` = p.`id_product` 
			AND sav.`id_product_attribute` = 0' . StockAvailable::addSqlShopRestriction(null, null, 'sav') . ') ';

        $this->_join .= '
		LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` cl ON (p.`id_category_default` = cl.`id_category` 
			AND b.`id_lang` = cl.`id_lang` AND cl.id_shop = ' . $id_shop . ') 
		LEFT JOIN `' . _DB_PREFIX_ . 'shop` shop ON (shop.id_shop = ' . $id_shop . ') 
		LEFT JOIN `' . _DB_PREFIX_ . 'image_shop` image_shop ON (image_shop.`id_image` = i.`id_image` 
			AND image_shop.`cover` = 1 AND image_shop.id_shop = ' . $id_shop . ')';

        $this->_where .= 'AND a.approved = "' . (int) KbGlobal::APPROVED . '"';

        $this->_group = ' GROUP BY p.id_product';

        $this->fields_list = array();
        $this->fields_list['id_product'] = array(
            'title' => $this->module->l('ID'),
            'align' => 'center',
            'class' => 'fixed-width-xs',
            'type' => 'int',
            'order_key' => 'p.id_product'
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
            'order_key' => 'p.reference'
        );

        if (Tools::getIsset('id_seller') && Tools::getValue('id_seller') > 0) {
            //nothing to do
        } else {
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
        }

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
            'active' => 'status',
            'filter_key' => 'p!active',
            'order_key' => 'p.active',
            'align' => 'text-center',
            'type' => 'bool',
            'class' => 'fixed-width-sm',
            'orderby' => false
        );

        $this->addRowAction('viewproduct');
        $this->addRowAction('delete');
    }

    public function initProcess()
    {
        parent::initProcess();
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

    public function processStatus()
    {
        $errors = '';
        $seller = new KbSeller($this->object->id_seller);
        if (!$seller->isApprovedSeller() || $seller->active == 0) {
            $this->context->cookie->__set(
                'kb_redirect_error',
                $this->module->l('You can\'t change the status as seller of this product is not active.')
            );
        } else {
            $object = new Product($this->object->id_product);
            if (!Validate::isLoadedObject($object)) {
                $errors = $this->module->l('Not able to load object');
            }
            if (($error = $object->validateFields(false, true)) !== true) {
                $errors = $error;
            }
            if ($errors == '') {
                if ($object->toggleStatus()) {
                    $this->context->cookie->__set(
                        'kb_redirect_success',
                        $this->module->l('Status successfully updated.')
                    );
                } else {
                    $this->context->cookie->__set(
                        'kb_redirect_error',
                        $this->module->l('Error occured while updating status.')
                    );
                }
            } else {
                $this->context->cookie->__set('kb_redirect_error', $errors);
            }
        }
        Tools::redirectAdmin($this->context->link->getAdminLink('AdminKbProductList'));
    }

    /**
     * Toggle status of multiple items
     *
     * @return boolean true if succcess
     */
    protected function processBulkStatusSelection($status)
    {
        if (is_array($this->boxes) && !empty($this->boxes)) {
            $not_update_count = 0;
            foreach ($this->boxes as $id) {
                $s2p = new $this->className((int)$id);
                if ((int)$s2p->id_product > 0) {
                    $object = new Product((int)$s2p->id_product);
                    $object->setFieldsToUpdate(array('active' => true));
                    $object->active = (int)$status;
                    if (!$object->update()) {
                        $not_update_count++;
                    }
                }
            }

            if ($not_update_count > 0) {
                $this->context->cookie->__set(
                    'kb_redirect_success',
                    sprintf(
                        $this->module->l('<b>%s</b> product(s) has been updated out of <b>%s</b> product(s)'),
                        (count($this->boxes) - $not_update_count),
                        count($this->boxes)
                    )
                );
            } else {
                $this->context->cookie->__set(
                    'kb_redirect_success',
                    $this->module->l('Selected item(s) updated successfully.')
                );
            }
        } else {
            $this->context->cookie->__set(
                'kb_redirect_error',
                $this->module->l('Atleast one item should be selected to perform action.')
            );
        }

        Tools::redirectAdmin($this->context->link->getAdminLink('AdminKbProductList'));
    }
    
//    public function displayDeleteLink($token = null, $id = 0, $name = null) {
//        parent::displayDeleteLink($token, $id, $name);
//    }

    public function processDelete()
    {
        if (Tools::getIsset($this->identifier)) {
            $error = '';
            $seller_product = new $this->className(Tools::getValue($this->identifier));
            $product = new Product($seller_product->id_product);

            $seller_obj = new KbSeller($seller_product->id_seller);
            $seller = $seller_obj->getSellerInfo();
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
                '{{product_name}}' => $product->name[$seller['id_default_lang']],
                '{{product_sku}}' => $product->reference,
                '{{product_price}}' => Tools::displayPrice($product->price),
                '{{reason}}' => $reason_comment
            );

            if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT') && $product->advanced_stock_management) {
                $stock_manager = StockManagerFactory::getManager();
                $physical_quantity = $stock_manager->getProductPhysicalQuantities($product->id, 0);
                $real_quantity = $stock_manager->getProductRealQuantities($product->id, 0);
                if ($physical_quantity > 0 || $real_quantity > $physical_quantity) {
                    $error = $this->module->l('You cannot delete this product because there\'s physical stock left.');
                }
            }

            if ($error == '') {
                if (!$product->delete()) {
                    $error = $this->module->l('An error occurred during deletion.');
                } else {
                    $seller_product->delete();
                    $email = new KbEmail(
                        KbEmail::getTemplateIdByName('mp_product_delete_notification'),
                        $seller['id_default_lang']
                    );
                    $notification_emails = $seller_obj->getEmailIdForNotification();
                    foreach ($notification_emails as $em) {
                        $email->send($em['email'], $em['title'], null, $template_vars);
                    }
                }
            }

            if ($error == '') {
                $this->context->cookie->__set(
                    'kb_redirect_success',
                    $this->module->l('Product has been deleted from store.')
                );
            } else {
                $this->context->cookie->__set('kb_redirect_error', $error);
            }
        }
        Tools::redirectAdmin($this->context->link->getAdminLink('AdminKbProductList'));
    }
}
