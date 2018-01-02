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

class KbSellerEarning extends ObjectModel
{
    public $id;
    public $id_seller;
    public $id_shop;
    public $id_order;
    public $product_count;
    public $total_earning;
    public $seller_earning;
    public $admin_earning;
    public $is_canceled;
    public $can_handle_order;
    public $date_add;
    public $date_upd;

    const REPORT_FORMAT_DAILY = 1;
    const REPORT_FORMAT_WEEKLY = 2;
    const REPORT_FORMAT_MONTHLY = 3;
    const REPORT_FORMAT_YEARLY = 4;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'kb_mp_seller_earning',
        'primary' => 'id_seller_earning',
        'fields' => array(
            'id_seller' => array('type' => self::TYPE_INT, 'validate' => 'isNullOrUnsignedId', 'copy_post' => false),
            'id_shop' => array('type' => self::TYPE_INT, 'validate' => 'isNullOrUnsignedId', 'copy_post' => false),
            'id_order' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'copy_post' => false),
            'product_count' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'total_earning' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true),
            'seller_earning' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true),
            'admin_earning' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true),
            'is_canceled' => array('type' => self::TYPE_STRING, 'values' => array('0', '1'), 'default' => '0'),
            'can_handle_order' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'default' => 0),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false)
        )
    );

    protected $webserviceParameters = array(
        'objectsNodeName' => 'kbsellerearnings',
        'objectNodeName' => 'kbsellerearning',
        'fields' => array(
            'id_seller' => array('xlink_resource' => 'kbsellers'),
            'id_order' => array('xlink_resource' => 'orders'),
            'id_shop' => array('xlink_resource' => 'shops')
        ),
        'associations' => array(
            'kbsellerorderdetails' => array('getter' => 'getSellerOrderDetailWs', 'resource' => 'kbsellerorderdetail'),
        )
    );

    public function __construct($id = null)
    {
        parent::__construct($id);
    }

    public static function getEarningByOrder($id_order)
    {
        $sql = 'Select * from ' . _DB_PREFIX_ . 'kb_mp_seller_earning where id_order = ' . (int)$id_order;

        return DB::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    }

    public static function getEarningBySeller($id_seller)
    {
        $sql = 'Select * from ' . _DB_PREFIX_ . 'kb_mp_seller_earning where id_seller = ' . (int)$id_seller;

        return DB::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    }

    public static function getEarningBySellerAndOrder($id_seller, $id_order)
    {
        $sql = 'Select * from ' . _DB_PREFIX_ . 'kb_mp_seller_earning 
			where id_seller = ' . (int)$id_seller . ' AND id_order = ' . (int)$id_order;

        return DB::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
    }

    public static function getOrdersBySellerId(
        $id_seller,
        $only_count = false,
        $start = 0,
        $limit = 0,
        $filter_string = null
    ) {
        $sql = 'Select {{COLUMN}} from ' . _DB_PREFIX_ . 'kb_mp_seller_earning as e 
			INNER JOIN ' . _DB_PREFIX_ . 'orders as o on (e.id_order = o.id_order) 
			INNER JOIN ' . _DB_PREFIX_ . 'customer as c on(o.id_customer = c.id_customer) 
			Where e.id_seller = ' . (int)$id_seller;

        if (!empty($filter_string)) {
            $sql .= pSQL($filter_string);
        }

        if ($only_count) {
            $sql = str_replace(
                '{{COLUMN}}',
                'IF(COUNT(e.id_seller_earning) IS NULL,0,COUNT(e.id_seller_earning)) as total',
                $sql
            );
            return DB::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
        } else {
            $col_string = 'e.*';
            $sql = str_replace('{{COLUMN}}', $col_string, $sql);

            $sql .= ' ORDER BY e.id_seller_earning DESC';

            if ((int)$start >= 0 && (int)$limit > 0) {
                $sql .= ' LIMIT ' . (int)$start . ', ' . (int)$limit;
            } elseif ((int)$limit > 0) {
                $sql .= ' LIMIT ' . (int)$limit;
            }

            return DB::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        }
    }

    public static function getTotalEarningInSellerOrders($id_seller, $include_cancel_order = false)
    {
        $sql = 'Select IF(SUM(total_earning) IS NULL,0,SUM(total_earning)) as total 
			from ' . _DB_PREFIX_ . 'kb_mp_seller_earning 
			where id_seller = ' . (int)$id_seller;

        if (!$include_cancel_order) {
            $sql .= ' AND is_canceled = "0"';
        }
        return DB::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
    }

    public static function getTotalSellerEarning($id_seller, $include_cancel_order = false, $custom_filter = '')
    {
        $sql = 'Select IF(SUM(seller_earning) IS NULL,0,SUM(seller_earning)) as total 
			from ' . _DB_PREFIX_ . 'kb_mp_seller_earning 
			where id_seller = ' . (int)$id_seller;

        if (!$include_cancel_order) {
            $sql .= ' AND is_canceled = "0"';
        }

        if (!empty($custom_filter)) {
            $sql .= $custom_filter;
        }

        return DB::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
    }

    public static function getTotalSellerSoldProduct($id_seller, $include_cancel_order = false, $custom_filter = '')
    {
        $sql = 'Select IF(SUM(product_count) IS NULL,0,SUM(product_count)) as total 
			from ' . _DB_PREFIX_ . 'kb_mp_seller_earning 
			where id_seller = ' . (int)$id_seller;

        if (!$include_cancel_order) {
            $sql .= ' AND is_canceled = "0"';
        }

        if (!empty($custom_filter)) {
            $sql .= pSQL($custom_filter);
        }

        return DB::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
    }

    public static function getSellerPendingOrders($id_seller, $custom_filter = '')
    {
        $final_statuses = array(
            Configuration::get('PS_OS_ERROR'),
            Configuration::get('PS_OS_CANCELED')
        );

        $results = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT * FROM `' . _DB_PREFIX_ . 'order_state` WHERE delivery = 1');
        if ($results && count($results) > 0) {
            foreach ($results as $del) {
                $final_statuses = pSQL(array_merge($final_statuses, array($del['id_order_state'])));
            }
        }

        $sql = 'Select IF(COUNT(e.id_seller_earning) IS NULL,0,COUNT(e.id_seller_earning)) as total 
			from ' . _DB_PREFIX_ . 'kb_mp_seller_earning as e 
			INNER JOIN ' . _DB_PREFIX_ . 'orders as o on (e.id_order = o.id_order) 
			where e.id_seller = ' . (int)$id_seller
            . ' AND o.current_state NOT IN (' . pSQL(implode(',', $final_statuses)) . ')';

        if (!empty($custom_filter)) {
            $sql .= pSQL($custom_filter);
        }

        return DB::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
    }

    public static function getSellerTotalOrders($id_seller, $not_include_cancel_order = false, $custom_filter = '')
    {
        $sql = 'Select IF(COUNT(e.id_seller_earning) IS NULL,0,COUNT(e.id_seller_earning)) as total 
			from ' . _DB_PREFIX_ . 'kb_mp_seller_earning as e 
			INNER JOIN ' . _DB_PREFIX_ . 'orders as o on (e.id_order = o.id_order) 
			where e.id_seller = ' . (int)$id_seller;

        if ($not_include_cancel_order) {
            $sql .= ' AND is_canceled = "0"';
        }

        if (!empty($custom_filter)) {
            $sql .= pSQL($custom_filter);
        }

        return DB::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
    }

    public static function getSellerDeliveredOrders($id_seller, $custom_filter = '')
    {
        $sql = 'Select IF(COUNT(e.id_seller_earning) IS NULL,0,COUNT(e.id_seller_earning)) as total 
			from ' . _DB_PREFIX_ . 'kb_mp_seller_earning as e 
			INNER JOIN ' . _DB_PREFIX_ . 'orders as o on (e.id_order = o.id_order) 
			where e.id_seller = ' . (int)$id_seller . ' AND o.current_state = ' . (int)OrderState::FLAG_DELIVERY;

        if (!empty($custom_filter)) {
            $sql .= pSQL($custom_filter);
        }

        return DB::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
    }

    public static function getSellerCanceledOrders($id_seller, $custom_filter = '')
    {
        $final_statuses = array(
            Configuration::get('PS_OS_ERROR'),
            Configuration::get('PS_OS_CANCELED')
        );
        $sql = 'Select IF(COUNT(e.id_seller_earning) IS NULL,0,COUNT(e.id_seller_earning)) as total 
			from ' . _DB_PREFIX_ . 'kb_mp_seller_earning as e 
			INNER JOIN ' . _DB_PREFIX_ . 'orders as o on (e.id_order = o.id_order) 
			where e.id_seller = ' . (int)$id_seller
            . ' AND o.current_state IN (' . pSQL(implode(',', $final_statuses)) . ')';

        if (!empty($custom_filter)) {
            $sql .= pSQL($custom_filter);
        }

        return DB::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
    }

    public static function isSellerOrder($id_seller, $id_order)
    {
        $sql = 'Select IF(COUNT(id_seller_earning) IS NULL,0,COUNT(id_seller_earning)) as total 
			from ' . _DB_PREFIX_ . 'kb_mp_seller_earning 
			where id_seller = ' . (int)$id_seller . ' AND id_order = ' . (int)$id_order;

        return (bool)DB::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
    }

    public static function getOrderState($id_order_state, $id_lang)
    {
        $sql = 'Select o.id_order_state, o.color, osl.name from ' . _DB_PREFIX_ . 'order_state as o 
			INNER JOIN ' . _DB_PREFIX_ . 'order_state_lang as osl on (o.id_order_state = osl.id_order_state) 
			where o.id_order_state = ' . (int)$id_order_state . ' AND osl.id_lang = ' . (int)$id_lang;

        return DB::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
    }

    public static function getPercentageVariationReports($id_seller, $period)
    {
        $reportData = array();

        switch ($period) {
            case 'today':
                $today = date('Y-m-d', strtotime('today'));
                $yesterday = date('Y-m-d', strtotime('-1 day'));
                $reportData = self::getFormattedReportData($id_seller, $today, $today, $yesterday, $yesterday);
                break;
            case 'week':
                $current_week_start_date = date('Y-m-d', strtotime('this week'));
                $current_week_end_date = date('Y-m-d', strtotime('this week +6 days'));
                $last_week_start_date = date('Y-m-d', strtotime('last week'));
                $last_week_end_date = date('Y-m-d', strtotime('last week +6 days'));
                $reportData = self::getFormattedReportData(
                    $id_seller,
                    $current_week_start_date,
                    $current_week_end_date,
                    $last_week_start_date,
                    $last_week_end_date
                );
                break;
            case 'month':
                $current_month_start_date = date('Y-m-d', strtotime('first day of this month'));
                $current_month_end_date = date('Y-m-d', strtotime('last day of this month'));
                $last_month_start_date = date('Y-m-d', strtotime('first day of last month'));
                $last_month_end_date = date('Y-m-d', strtotime('last day of last month'));
                $reportData = self::getFormattedReportData(
                    $id_seller,
                    $current_month_start_date,
                    $current_month_end_date,
                    $last_month_start_date,
                    $last_month_end_date
                );
                break;
            case 'year':
                $current_year = date('Y', strtotime('this year'));
                $last_year = date('Y', strtotime('last year'));
                $current_year_start_date = date('Y-m-d', strtotime('first day of january ' . $current_year . ''));
                $current_year_end_date = date('Y-m-d', strtotime('last day of december ' . $current_year . ''));
                $last_year_start_date = date('Y-m-d', strtotime('first day of january ' . $last_year . ''));
                $last_year_end_date = date('Y-m-d', strtotime('last day of december ' . $last_year . ''));
                $reportData = self::getFormattedReportData(
                    $id_seller,
                    $current_year_start_date,
                    $current_year_end_date,
                    $last_year_start_date,
                    $last_year_end_date
                );
                break;
        }
        return $reportData;
    }

    public static function calculateDifferencePercentage($old_val, $new_val)
    {
        $difference = ($new_val - $old_val);
        $difference_percentage = 0;
        if ($old_val == 0 || $new_val == 0) {
            $difference_percentage = round(($difference * 100), 2);
        } elseif ($old_val > 0 && $new_val > 0) {
            $difference_percentage = round(((float)($difference / $old_val) * 100), 2);
        }

        return $difference_percentage;
    }

    public static function getFormattedReportData(
        $id_seller,
        $first_start_date,
        $first_end_date,
        $second_start_date,
        $second_end_date
    ) {
        $reportData = array();

        //Get Order Improvement
        $start_date_filter = ' AND (DATE(e.date_add) >= "' . pSQL($first_start_date) . '" 
			AND DATE(e.date_add) <= "' . pSQL($first_end_date) . '")';
        $current = self::getSellerTotalOrders($id_seller, false, $start_date_filter);

        $end_date_filter = ' AND (DATE(e.date_add) >= "' . pSQL($second_start_date) . '" 
			AND DATE(e.date_add) <= "' . pSQL($second_end_date) . '")';
        $prev = self::getSellerTotalOrders($id_seller, false, $end_date_filter);

        $reportData['order']['current'] = $current;
        $reportData['order']['previous'] = $prev;
        $reportData['order']['diff'] = ($current - $prev);
        $reportData['order']['diff_percent'] = self::calculateDifferencePercentage($prev, $current);


        //Get Revenue Improvement
        $start_date_filter = ' AND (DATE(date_add) >= "' . pSQL($first_start_date) . '" 
			AND DATE(date_add) <= "' . pSQL($first_end_date) . '")';
        $current = self::getTotalSellerEarning($id_seller, true, $start_date_filter);

        $end_date_filter = ' AND (DATE(date_add) >= "' . pSQL($second_start_date) . '" 
			AND DATE(date_add) <= "' . pSQL($second_end_date) . '")';
        $prev = self::getTotalSellerEarning($id_seller, true, $end_date_filter);

        $reportData['revenue']['current'] = $current;
        $reportData['revenue']['previous'] = $prev;
        $reportData['revenue']['diff'] = ($current - $prev);
        $reportData['revenue']['diff_percent'] = self::calculateDifferencePercentage($prev, $current);


        //Get Qty Ordered Improvement
        $start_date_filter = ' AND (DATE(date_add) >= "' . pSQL($first_start_date) . '" 
			AND DATE(date_add) <= "' . pSQL($first_end_date) . '")';
        $current = self::getTotalSellerSoldProduct($id_seller, true, $start_date_filter);

        $end_date_filter = ' AND (DATE(date_add) >= "' . pSQL($second_start_date) . '" 
			AND DATE(date_add) <= "' . pSQL($second_end_date) . '")';
        $prev = self::getTotalSellerSoldProduct($id_seller, true, $end_date_filter);

        $reportData['ordered_qty']['current'] = $current;
        $reportData['ordered_qty']['previous'] = $prev;
        $reportData['ordered_qty']['diff'] = ($current - $prev);
        $reportData['ordered_qty']['diff_percent'] = self::calculateDifferencePercentage($prev, $current);

        return $reportData;
    }

    public function getMenuBadgeHtml($id_seller)
    {
        return self::getSellerTotalOrders($id_seller);
    }

    public function getSellerOrderDetailWs()
    {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            'SELECT `id_seller_order_detail` as id
            FROM `'._DB_PREFIX_.'kb_mp_seller_order_detail` 
            WHERE `id_seller` = '.(int)$this->id_seller.' AND id_order = '.(int)$this->id_order
        );
        return $result;
    }
    
    public static function isSellerCanHandleOrder($id_seller, $id_order)
    {
        $result = self::getEarningBySellerAndOrder($id_seller, $id_order);
        if (!empty($result) && $result['can_handle_order'] == 1) {
            return true;
        } else {
            return false;
        }
    }
}
