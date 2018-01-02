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

class AdminKbCommissionController extends AdminKbMarketplaceCoreController
{
    public function __construct()
    {
        $this->bootstrap = true;
        if (Tools::getIsset('commsion_view_type') && Tools::getValue('commsion_view_type') == 1) {
            $this->table = 'kb_mp_seller_order_detail';
            $this->className = 'KbSellerOrderDetail';
            $this->identifier = 'id_order';
        } else {
            $this->table = 'kb_mp_seller_earning';
            $this->className = 'KbSellerEarning';
            $this->identifier = 'id_order';
        }
        $this->lang = false;
        $this->display = 'list';
        $this->allow_export = true;
        $this->context = Context::getContext();

        parent::__construct();

        $this->_join .= ' INNER JOIN ' . _DB_PREFIX_ . 'kb_mp_seller as sl on (a.id_seller = sl.id_seller) 
			INNER JOIN `' . _DB_PREFIX_ . 'customer` c ON (sl.`id_customer` = c.`id_customer`)';

        if (Tools::getIsset('commsion_view_type') && Tools::getValue('commsion_view_type') == 1) {
            $this->fields_list = array(
                'id_category' => array(
                    'title' => $this->module->l('ID'),
                    'align' => 'text-center',
                    'class' => 'fixed-width-xs'
                ),
                'cname' => array(
                    'title' => $this->module->l('Category'),
                    'havingFilter' => true,
                ),
                'total_product_count' => array(
                    'title' => $this->module->l('Quantity Ordered'),
                    'align' => 'text-right',
                    'type' => 'int'
                ),
                'total_total_earning' => array(
                    'title' => $this->module->l('Total Earning'),
                    'align' => 'text-right',
                    'type' => 'price',
                    'currency' => true,
                    'callback' => 'setCurrency'
                ),
                'total_admin_earning' => array(
                    'title' => $this->module->l('Your Commission'),
                    'align' => 'text-right',
                    'type' => 'price',
                    'currency' => true,
                    'callback' => 'setCurrency'
                ),
                'total_seller_earning' => array(
                    'title' => $this->module->l('Seller Earning'),
                    'align' => 'text-right',
                    'type' => 'price',
                    'currency' => true,
                    'callback' => 'setCurrency'
                )
            );

            $this->toolbar_title = $this->module->l('Category Wise') . ' ' . $this->module->l('Admin Commissions');
            $this->_select = 'cl.name as cname, o.id_currency, SUM(a.total_earning) as total_total_earning, 
				SUM(a.admin_earning) as total_admin_earning, SUM(a.seller_earning) as total_seller_earning, 
				COUNT(a.qty) as total_product_count, 
				CONCAT(LEFT(c.`firstname`, 1), \'. \', c.`lastname`) AS `seller_name`, c.email';
            $this->_join .= ' INNER JOIN ' . _DB_PREFIX_ . 'orders as o on (a.id_order = o.id_order) 
				INNER JOIN ' . _DB_PREFIX_ . 'category_lang as cl 
				on (a.id_category = cl.id_category AND cl.id_lang = ' . (int)$this->context->language->id . ')';
            $this->_where .= ' AND a.is_canceled = "0"';
            if (Tools::getIsset('commsion_by_category') && (int)Tools::getValue('commsion_by_category') > 0) {
                $this->_where .= ' AND a.id_category = ' . (int)Tools::getValue('commsion_by_category');
            } else {
                $this->_where .= ' AND a.is_consider = "1"';
            }
            $this->_orderBy .= 'total_admin_earning';
            $this->_orderWay .= 'DESC';
            $this->_group .= 'GROUP by a.id_category';
        } else {
            $this->fields_list = array(
                'id_order' => array(
                    'title' => $this->module->l('ID'),
                    'align' => 'text-center',
                    'class' => 'fixed-width-xs'
                ),
                'reference' => array(
                    'title' => $this->module->l('Reference')
                ),
                'seller_name' => array(
                    'title' => $this->module->l('Seller'),
                    'havingFilter' => true,
                ),
                'email' => array(
                    'title' => $this->module->l('Email'),
                    'havingFilter' => true,
                ),
                'product_count' => array(
                    'title' => $this->module->l('Quantity Ordered'),
                    'align' => 'text-right',
                    'type' => 'int'
                ),
                'total_earning' => array(
                    'title' => $this->module->l('Total Earning'),
                    'align' => 'text-right',
                    'type' => 'price',
                    'currency' => true,
                    'callback' => 'setCurrency'
                ),
                'admin_earning' => array(
                    'title' => $this->module->l('Your Commission'),
                    'align' => 'text-right',
                    'type' => 'price',
                    'currency' => true,
                    'callback' => 'setCurrency'
                ),
                'seller_earning' => array(
                    'title' => $this->module->l('Seller Earning'),
                    'align' => 'text-right',
                    'type' => 'price',
                    'currency' => true,
                    'callback' => 'setCurrency'
                ),
                'date_add' => array(
                    'title' => $this->module->l('Date'),
                    'align' => 'text-right',
                    'type' => 'datetime',
                    'filter_key' => 'a!date_add'
                )
            );

            $this->toolbar_title = $this->module->l('Order Wise') . ' ' . $this->module->l('Admin Commissions');
            $this->_select = 'o.reference, o.id_currency, 
				CONCAT(LEFT(c.`firstname`, 1), \'. \', c.`lastname`) AS `seller_name`, c.email';
            $this->_join .= ' INNER JOIN ' . _DB_PREFIX_ . 'orders as o on (a.id_order = o.id_order)';
            $this->_where = ' AND a.is_canceled = "0"';
            $this->_orderBy = 'a.id_order';
            $this->_orderWay = 'DESC';
        }

        $this->addRowAction('');
        $this->page_header_toolbar_title = $this->toolbar_title;
    }

    public function postProcess()
    {
        parent::postProcess();
    }

    public function setMedia()
    {
        parent::setMedia();
    }

    public function init()
    {
        parent::init();
        if (Tools::getIsset('commsion_view_type') && Tools::getValue('commsion_view_type') == 1) {
            self::$currentIndex .= '&commsion_view_type=' . (int)Tools::getValue('commsion_view_type');
            if (Tools::getIsset('commsion_by_category')) {
                self::$currentIndex .= '&commsion_by_category=' . (int)Tools::getValue('commsion_by_category');
            }
        } else {
            self::$currentIndex .= '&commsion_view_type=0';
        }

        $this->context->smarty->assign(array(
            'current' => self::$currentIndex
        ));
    }

    public function initContent()
    {
        $view_type = array(
            array(
                'id_type' => 0,
                'name' => $this->module->l('Order Wise')
            ),
            array(
                'id_type' => 1,
                'name' => $this->module->l('Category Wise')),
        );

        $fields_options = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->module->l('Commission View Type'),
                    'icon' => 'icon-cogs'
                ),
                'input' => array(
                    array(
                        'type' => 'select',
                        'label' => $this->module->l('Select Type'),
                        'name' => 'commsion_view_type',
                        'onchange' => 'changeCommsionView(this)',
                        'options' => array(
                            'query' => $view_type,
                            'id' => 'id_type',
                            'name' => 'name'
                        ),
                        'class' => '',
                        'col' => '6',
                    )
                )
            )
        );

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $field_value = array();
        $field_value['commsion_view_type'] = 0;
        if (Tools::getIsset('commsion_view_type') && Tools::getValue('commsion_view_type') == 1) {
            $field_value['commsion_view_type'] = 1;
            $categories = KbGlobal::getCategories();
            $fields_options['form']['input'][] = array(
                'type' => 'select',
                'label' => $this->module->l('Select Category'),
                'name' => 'commsion_by_category',
                'onchange' => 'changeCommsionView(this)',
                'options' => array(
                    'default' => array('value' => 0, 'label' => $this->module->l('All Categories')),
                    'query' => $categories,
                    'id' => 'id_category',
                    'name' => 'name'
                ),
                'class' => '',
                'col' => '6',
            );
            $field_value['commsion_by_category'] = 0;
            if (Tools::getIsset('commsion_by_category') && (int)Tools::getValue('commsion_by_category') > 0) {
                $field_value['commsion_by_category'] = (int)Tools::getValue('commsion_by_category');
            }
        }

        $helper->tpl_vars = array('fields_value' => $field_value);

        $helper->default_form_language = $this->context->language->id;

        $helper->currentIndex = $this->context->link->getAdminLink('AdminKbCommission');
        $helper->submit_action = 'submitCommisionViewType';

        $tpl = $this->custom_smarty->createTemplate('kb_extra_content.tpl');
        $tpl->assign('extra_content', $helper->generateForm(array($fields_options)));
        $this->content .= $tpl->fetch();
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

    public static function setCurrency($echo, $tr)
    {
        return Tools::displayPrice($echo, (int)$tr['id_currency']);
    }

    public function setCommisionPercent($echo, $tr)
    {
        unset($echo);
        return Tools::ps_round($tr['commission_percent'], 2) . '%';
    }
}
