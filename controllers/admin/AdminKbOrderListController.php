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

class AdminKbOrderListController extends AdminKbMarketplaceCoreController
{
    protected $statuses_array = array();

    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'kb_mp_seller_earning';
        $this->className = 'KbSellerEarning';
        $this->identifier = 'id_order';
//        $this->identifier = 'id_seller_earning';
        $this->lang = false;
        $this->display = 'list';
        $this->allow_export = true;
        $this->context = Context::getContext();
        $this->toolbar_title = 'Seller\'s Orders';

        parent::__construct();
        $this->_select = 'o.*, CONCAT(LEFT(c.`firstname`, 1), \'. \', c.`lastname`) AS `customer`,
		osl.`name` AS `osname`, os.`color`, country_lang.name as cname, IF(o.valid, 1, 0) badge_success';

        $this->_join .= ' INNER JOIN ' . _DB_PREFIX_ . 'orders o on (a.id_order = o.id_order) ';

        if (Tools::getIsset('id_seller') && Tools::getValue('id_seller') > 0) {
            $this->_join .= ' INNER JOIN ' . _DB_PREFIX_ . 'kb_mp_seller as sl 
				ON (a.`id_seller` = sl.`id_seller` AND sl.id_seller = ' . (int)Tools::getValue('id_seller') . ')';
        } else {
            $this->_select .=', sn.`email`';
            $this->_join .= ' INNER JOIN ' . _DB_PREFIX_ . 'kb_mp_seller as sl on (a.id_seller = sl.id_seller)';
        }

        $this->_join .= '
		INNER JOIN `' . _DB_PREFIX_ . 'customer` c ON (o.`id_customer` = c.`id_customer`) 
		INNER JOIN `' . _DB_PREFIX_ . 'customer` sn ON (sl.`id_customer` = sn.`id_customer`) 
		INNER JOIN `' . _DB_PREFIX_ . 'address` address ON address.id_address = o.id_address_delivery 
		INNER JOIN `' . _DB_PREFIX_ . 'country` country ON address.id_country = country.id_country 
		INNER JOIN `' . _DB_PREFIX_ . 'country_lang` country_lang ON (country.`id_country` = country_lang.`id_country` 
			AND country_lang.`id_lang` = ' . (int)$this->context->language->id . ')
		LEFT JOIN `' . _DB_PREFIX_ . 'order_state` os ON (os.`id_order_state` = o.`current_state`) 
		LEFT JOIN `' . _DB_PREFIX_ . 'order_state_lang` osl ON (os.`id_order_state` = osl.`id_order_state` 
			AND osl.`id_lang` = ' . (int)$this->context->language->id . ')';
        $this->_orderBy = 'o.id_order';
        $this->_orderWay = 'DESC';

        $statuses = OrderState::getOrderStates((int)$this->context->language->id);
        foreach ($statuses as $status) {
            $this->statuses_array[$status['id_order_state']] = $status['name'];
        }

        $this->fields_list = array(
            'id_order' => array(
                'title' => $this->module->l('ID'),
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
//                'filter_key' => 'o!id_order',
//                'havingFilter' => true,
            ),
            'reference' => array(
                'title' => $this->module->l('Reference')
            ),
            'email' => array(
                'title' => $this->module->l('Seller Email'),
                'havingFilter' => true,
                'filter_key' => 'sn!email'
            ),
            'customer' => array(
                'title' => $this->module->l('Customer'),
                'havingFilter' => true,
            ),
        );

        if (Configuration::get('PS_B2B_ENABLE')) {
            $this->fields_list = array_merge($this->fields_list, array(
                'company' => array(
                    'title' => $this->module->l('Company'),
                    'filter_key' => 'c!company'
                ),
            ));
        }

        $this->fields_list = array_merge($this->fields_list, array(
            'total_paid_tax_incl' => array(
                'title' => $this->module->l('Total'),
                'align' => 'text-right',
                'type' => 'price',
                'currency' => true,
                'callback' => 'setOrderCurrency',
                'badge_success' => true
            ),
            'payment' => array(
                'title' => $this->module->l('Payment')
            ),
            'osname' => array(
                'title' => $this->module->l('Status'),
                'type' => 'select',
                'color' => 'color',
                'list' => $this->statuses_array,
                'filter_key' => 'os!id_order_state',
                'filter_type' => 'int',
                'order_key' => 'osname'
            ),
            'date_add' => array(
                'title' => $this->module->l('Date'),
                'align' => 'text-right',
                'type' => 'datetime',
                'filter_key' => 'o!date_add'
            )
        ));

        if (Country::isCurrentlyUsed('country', true)) {
            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
			SELECT DISTINCT c.id_country, cl.`name` 
			FROM `' . _DB_PREFIX_ . 'orders` o 
			' . Shop::addSqlAssociation('orders', 'o') . ' 
			INNER JOIN `' . _DB_PREFIX_ . 'address` a ON a.id_address = o.id_address_delivery 
			INNER JOIN `' . _DB_PREFIX_ . 'country` c ON a.id_country = c.id_country 
			INNER JOIN `' . _DB_PREFIX_ . 'country_lang` cl ON (c.`id_country` = cl.`id_country` 
				AND cl.`id_lang` = ' . (int)$this->context->language->id . ') 
			ORDER BY cl.name ASC');

            $country_array = array();
            foreach ($result as $row) {
                $country_array[$row['id_country']] = $row['name'];
            }

            $part1 = array_slice($this->fields_list, 0, 3);
            $part2 = array_slice($this->fields_list, 3);
            $part1['cname'] = array(
                'title' => $this->module->l('Delivery'),
                'type' => 'select',
                'list' => $country_array,
                'filter_key' => 'country!id_country',
                'filter_type' => 'int',
                'order_key' => 'cname'
            );
            $this->fields_list = array_merge($part1, $part2);
        }

        $this->shopLinkType = 'shop';
        $this->shopShareDatas = Shop::SHARE_ORDER;

        $this->addRowAction('vieworder');
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

    public static function setOrderCurrency($echo, $tr)
    {
        return Tools::displayPrice($echo, (int)$tr['id_currency']);
    }

    /**
     * Display link to view/edit order
     */
    public function displayViewOrderLink($token = null, $id = 0, $name = null)
    {
        unset($token);
        unset($name);
        $tpl = $this->custom_smarty->createTemplate('list_action.tpl');
//        $row = new KbSellerEarning($id);
//        print_r($row);die;
        $tpl->assign(array(
            'separate_tab' => true,
            'href' => $this->context->link->getAdminLink('AdminOrders') . '&id_order=' . $id. '&vieworder',
            'action' => 'View',
            'icon' => 'icon-search-plus'
        ));

        return $tpl->fetch();
    }
}
