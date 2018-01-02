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

class KbReasonLog extends ObjectModel
{
    public $id;
    public $reason_type;
    public $id_seller;
    public $id_seller_product;
    public $id_seller_product_review;
    public $id_seller_review;
    public $id_seller_category_request;
    public $id_employee;
    public $comment;
    public $date_add;
    public $date_upd;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'kb_mp_reasons',
        'primary' => 'id_reason',
        'fields' => array(
            'reason_type' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'id_seller' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_seller_product' => array('type' => self::TYPE_NOTHING, 'validate' => 'isNullOrUnsignedId'),
            'id_seller_product_review' => array('type' => self::TYPE_NOTHING, 'validate' => 'isNullOrUnsignedId'),
            'id_seller_review' => array('type' => self::TYPE_NOTHING, 'validate' => 'isNullOrUnsignedId'),
            'id_seller_category_request' => array('type' => self::TYPE_NOTHING, 'validate' => 'isNullOrUnsignedId'),
            'id_employee' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'default' => null),
            'comment' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName'),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate')
        )
    );
    
    protected $webserviceParameters = array(
        'objectsNodeName' => 'kbreasonlogs',
        'objectNodeName' => 'kbreasonlog',
        'fields' => array(
            'id_seller' => array('xlink_resource' => 'kbsellers'),
            'id_seller_product' => array('xlink_resource' => 'kbsellerproducts'),
            'id_seller_product_review' => array('xlink_resource' => 'kbsellerproductreviews'),
            'id_seller_review' => array('xlink_resource' => 'kbsellerreviews'),
            'id_seller_category_request' => array('xlink_resource' => 'kbsellercrequests')
        )
    );


    /*
     * Reason types, please do not change ids of any reason type
     */
    private $reason_types = array(
        1 => array('key' => 'seller_dissapproved', 'label' => 'Seller Account Disapproved'),
        2 => array('key' => 'product_del_by_sellet', 'label' => 'Product Deletion by Seller'),
        3 => array('key' => 'product_dissaproved_by_admin', 'label' => 'Product Disapproved by Admin'),
        4 => array('key' => 'review_dissaproved_by_admin', 'label' => 'Review Disapproved by Admin'),
        5 => array('key' => 'category_dissaproved_by_admin', 'label' => 'Category Disapproved by Admin'),
        6 => array('key' => 'review_delete_by_admin', 'label' => 'Seller Review Deleted by Admin'),
    );

    public function __construct($id = null)
    {
        parent::__construct($id);
    }

    public static function getReasonOfCategoryDissapproval($id_seller_category_request)
    {
        $sql = 'Select * from ' . _DB_PREFIX_ . 'kb_mp_reasons 
			where id_seller_category_request = ' . (int)$id_seller_category_request;

        return DB::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
    }
}
