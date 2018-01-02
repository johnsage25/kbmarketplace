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

class KbmarketplaceDashboardModuleFrontController extends KbmarketplaceCoreModuleFrontController
{
    public $controller_name = 'dashboard';
    private $sale_graph_types = array('last_7_days', 'week', 'month', 'year');

    public function __construct()
    {
        parent::__construct();
    }

    public function setMedia()
    {
        parent::setMedia();

        $this->addJS($this->getKbModuleDir() . 'views/js/front/flot/jquery.flot.min.js');
        $this->addJS($this->getKbModuleDir() . 'views/js/front/flot/jquery.flot.tooltip.js');
        $this->addJS($this->getKbModuleDir() . 'views/js/front/flot/jquery.flot.axislabels.js');
        $this->addJS($this->getKbModuleDir() . 'views/js/front/flot/jquery.flot.orderBars.js');
        $this->addJS($this->getKbModuleDir() . 'views/js/front/flot/jquery.flot.tickRotor.js');
        $this->addJS($this->getKbModuleDir() . 'views/js/front/flot/kb_graph.js');
    }

    public function postProcess()
    {
        parent::postProcess();
        if (Tools::getIsset('ajax')) {
            if (Tools::getIsset('graph_type') && in_array(Tools::getValue('graph_type'), $this->sale_graph_types)) {
                $this->getGraphData();
            }
        }
    }
    
    public function getTemplateVarPage()
    {
        $page = parent::getTemplateVarPage();
        if (isset($page['meta']) && $this->seller_info) {
            $page_title = 'Dashboard';
            $page['meta']['title'] =  $page_title;
            $page['meta']['keywords'] = $this->seller_info['meta_keyword'];
            $page['meta']['description'] = $this->seller_info['meta_description'];
        }
        return $page;
    }

    public function initContent()
    {
        
        $total_revenue = Tools::displayPrice(
            KbSellerEarning::getTotalEarningInSellerOrders($this->seller_info['id_seller'])
        );
        $this->context->smarty->assign('total_revenue', $total_revenue);

        $total_earning = KbSellerEarning::getTotalSellerEarning($this->seller_info['id_seller']);
        $this->context->smarty->assign('total_earning', $total_earning);

        $total_orders = KbSellerEarning::getSellerTotalOrders($this->seller_info['id_seller']);
        $this->context->smarty->assign('total_orders', $total_orders);

        $total_sold_products = KbSellerEarning::getTotalSellerSoldProduct($this->seller_info['id_seller']);
        $this->context->smarty->assign('total_sold_products', $total_sold_products);


        //Sales Variation Reports
        $variation_report = array();
        //per day Variation
        $variation_report[] = array(
            'title' => $this->module->l('Today'),
            'curent_title' => $this->module->l('Today'),
            'prev_title' => $this->module->l('Yesterday'),
            'data' => KbSellerEarning::getPercentageVariationReports($this->seller_info['id_seller'], 'today')
        );

        //week Variation
        $variation_report[] = array(
            'title' => $this->module->l('This Week'),
            'curent_title' => $this->module->l('This Week'),
            'prev_title' => $this->module->l('Last Week'),
            'data' => KbSellerEarning::getPercentageVariationReports($this->seller_info['id_seller'], 'week')
        );

        //Month Variation
        $variation_report[] = array(
            'title' => $this->module->l('This Month'),
            'curent_title' => $this->module->l('This Month'),
            'prev_title' => $this->module->l('Last Month'),
            'data' => KbSellerEarning::getPercentageVariationReports($this->seller_info['id_seller'], 'month')
        );

        //Year Variation
        $variation_report[] = array(
            'title' => $this->module->l('This Year'),
            'curent_title' => $this->module->l('This Year'),
            'prev_title' => $this->module->l('Last Year'),
            'data' => KbSellerEarning::getPercentageVariationReports($this->seller_info['id_seller'], 'year')
        );

        $this->context->smarty->assign('sale_variation_report', $variation_report);

        //get Last 10 Orders
        $seller_orders = KbSellerEarning::getOrdersBySellerId($this->seller_info['id_seller'], false, 0, 10);

        $orders = array();

        if ($seller_orders && count($seller_orders) > 0) {
            foreach ($seller_orders as $so) {
                $order = new Order($so['id_order']);
                $currency = new Currency($order->id_currency);
                $customer = $order->getCustomer();
                $view_link = $this->context->link->getModuleLink(
                    $this->kb_module_name,
                    'order',
                    array('render_type' => 'view', 'id_order' => $order->id),
                    (bool)Configuration::get('PS_SSL_ENABLED')
                );
                $orders[] = array(
                    'view_link' => $view_link,
                    'reference' => Tools::strtoupper($order->getUniqReference()),
                    'order_date' => $order->date_add,
                    'customer_name' => $customer->firstname . ' ' . $customer->lastname,
                    'email' => $customer->email,
                    'qty' => $so['product_count'],
                    'status' => KbSellerEarning::getOrderState(
                        $order->getCurrentState(),
                        $this->context->language->id
                    ),
                    'total' => Tools::displayPrice($so['total_earning'], $currency)
                );
            }
        }
        $this->context->smarty->assign('orders', $orders);

        $this->setKbTemplate('dashboard.tpl');
        parent::initContent();
    }

    protected function getGraphData()
    {
        $columns = 'IF(SUM(total_earning) IS NULL,0,SUM(total_earning)) as total_revenue, 
				IF(SUM(seller_earning) IS NULL,0,SUM(seller_earning)) as seller_revenue, 
				IF(SUM(admin_earning) IS NULL,0,SUM(admin_earning)) as admin_revenue, 
				IF(SUM(product_count) IS NULL,0,SUM(product_count)) as ordered_qty, 
				IF(COUNT(id_order) IS NULL,0,COUNT(id_order)) as total_order';
        $set = array(
            'total_revenue' => 0,
            'seller_revenue' => 0,
            'admin_revenue' => 0,
            'ordered_qty' => 0,
            'total_order' => 0,
            'formatted_total_revenue' => 'NA',
            'formatted_seller_revenue' => 'NA',
            'formatted_admin_revenue' => 'NA'
        );
        $stats = array();
        switch (Tools::getValue('graph_type')) {
            case 'last_7_days':
                $start = date('Y-m-d', strtotime('today -6 days'));
                $end = date('Y-m-d', strtotime('today'));
                while ($start <= $end) {
                    $sql = 'Select ' . $columns . ' from ' . _DB_PREFIX_ . 'kb_mp_seller_earning 
                    where id_seller = ' . (int)$this->seller_obj->id
                        . ' AND is_canceled = "0"'
                        . ' AND DATE(date_add) = "' . pSQL($start) . '"';
                    $result = $set;
                    $result = DB::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
                    $result['formatted_total_revenue'] = Tools::displayPrice(
                        $result['total_revenue'],
                        $this->seller_currency
                    );
                    $result['formatted_seller_revenue'] = Tools::displayPrice(
                        $result['seller_revenue'],
                        $this->seller_currency
                    );
                    $result['formatted_admin_revenue'] = Tools::displayPrice(
                        $result['admin_revenue'],
                        $this->seller_currency
                    );
                    $result['xaxis'] = Tools::displayDate($start, $this->seller_obj->id_default_lang, false);
                    $stats[] = $result;
                    $start = date('Y-m-d', strtotime('1 day', strtotime($start)));
                }
                break;
            case 'week':
                break;
            case 'month':
                break;
            case 'year':
                break;
        }
        echo Tools::jsonEncode($stats);
        die;
    }
}
