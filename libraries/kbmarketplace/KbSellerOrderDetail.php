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

class KbSellerOrderDetail extends ObjectModel
{
    public $id;
    public $id_seller;
    public $id_order;
    public $id_shop;
    public $id_category;
    public $id_product;
    public $id_order_detail;
    public $commission_percent;
    public $total_earning;
    public $seller_earning;
    public $admin_earning;
    public $unit_price;
    public $qty;
    public $is_consider;
    public $is_canceled;
    public $date_add;
    public $date_upd;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'kb_mp_seller_order_detail',
        'primary' => 'id_seller_order_detail',
        'fields' => array(
            'id_seller' => array('type' => self::TYPE_INT, 'validate' => 'isNullOrUnsignedId', 'required' => true),
            'id_shop' => array('type' => self::TYPE_INT, 'validate' => 'isNullOrUnsignedId'),
            'id_order' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_category' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_product' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_order_detail' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'commission_percent' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPercentage',
                'required' => true),
            'total_earning' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true),
            'seller_earning' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true),
            'admin_earning' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true),
            'unit_price' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true),
            'qty' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'is_consider' => array('type' => self::TYPE_STRING, 'values' => array('0', '1'), 'default' => '1'),
            'is_canceled' => array('type' => self::TYPE_STRING, 'values' => array('0', '1'), 'default' => '0'),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate')
        )
    );

    protected $webserviceParameters = array(
        'objectsNodeName' => 'kbsellerorderdetails',
        'objectNodeName' => 'kbsellerorderdetail',
        'fields' => array(
            'id_seller' => array('xlink_resource' => 'kbsellers'),
            'id_order' => array('xlink_resource' => 'orders'),
            'id_shop' => array('xlink_resource' => 'shops'),
            'id_category' => array('xlink_resource' => 'categories'),
            'id_product' => array('xlink_resource' => 'products'),
            'id_order_detail' => array('xlink_resource' => 'order_details'),
        )
    );

    public function __construct($id = null)
    {
        parent::__construct($id);
    }

    public static function getDetailByOrderId($id_order)
    {
        $sql = 'Select * from ' . _DB_PREFIX_ . 'kb_mp_seller_order_detail where id_order = ' . (int)$id_order;

        return DB::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    }

    public static function getDetailBySellerId($id_seller)
    {
        $sql = 'Select * from ' . _DB_PREFIX_ . 'kb_mp_seller_order_detail where id_seller = ' . (int)$id_seller;

        return DB::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    }

    public static function getDetailBySellerAndOrderId($id_seller, $id_order)
    {
        $sql = 'Select * from ' . _DB_PREFIX_ . 'kb_mp_seller_order_detail where id_seller = ' . (int)$id_seller
            . ' AND id_order = ' . (int)$id_order;

        return DB::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    }

    public static function getDetailByOrderItemId($id_order_detail)
    {
        $sql = 'Select * from ' . _DB_PREFIX_ . 'kb_mp_seller_order_detail 
			where id_order_detail = ' . (int)$id_order_detail;

        return DB::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
    }
}
