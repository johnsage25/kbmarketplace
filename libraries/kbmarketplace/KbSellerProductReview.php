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

class KbSellerProductReview extends ObjectModel
{
    public $id;
    public $id_seller;
    public $id_shop;
    public $id_customer;
    public $id_lang;
    public $id_product;
    public $id_product_comment;
    public $date_add;
    public $date_upd;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'kb_mp_seller_product_review',
        'primary' => 'id_seller_product_review',
        'fields' => array(
            'id_seller' => array('type' => self::TYPE_INT, 'validate' => 'isNullOrUnsignedId', 'copy_post' => false),
            'id_customer' => array('type' => self::TYPE_INT, 'validate' => 'isNullOrUnsignedId', 'copy_post' => false),
            'id_shop' => array('type' => self::TYPE_INT, 'validate' => 'isNullOrUnsignedId', 'copy_post' => false),
            'id_lang' => array('type' => self::TYPE_INT, 'validate' => 'isNullOrUnsignedId', 'copy_post' => false),
            'id_product' => array('type' => self::TYPE_INT, 'validate' => 'isNullOrUnsignedId', 'copy_post' => false),
            'id_product_comment' => array('type' => self::TYPE_INT, 'validate' => 'isNullOrUnsignedId',
                'required' => true, 'copy_post' => false),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false)
        )
    );

    protected $webserviceParameters = array(
        'objectsNodeName' => 'kbsellerproductreviews',
        'objectNodeName' => 'kbsellerproductreview',
        'fields' => array(
            'id_seller' => array('xlink_resource' => 'kbsellers'),
            'id_customer' => array('xlink_resource' => 'customers'),
            'id_product' => array('xlink_resource' => 'products'),
            'id_shop' => array('xlink_resource' => 'shops'),
            'id_lang' => array('xlink_resource' => 'languages'),
            'comment_title' => array('getter' => 'getCommentTitleWs'),
        )
    );

    public function __construct($id = null)
    {
        parent::__construct($id);
    }

    public static function getRowByComment($id_product_comment)
    {
        $sql = 'Select * from ' . _DB_PREFIX_ . 'kb_mp_seller_product_review 
			Where id_product_comment = ' . (int)$id_product_comment;
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
    }

    public static function getReviewsBySeller(
        $id_seller,
        $id_lang = null,
        $filter_string = null,
        $only_count = false,
        $start = null,
        $limit = null
    ) {
        $sql = 'Select {{COLUMN}} from ' . _DB_PREFIX_ . 'kb_mp_seller_product_review as spr 
			INNER JOIN ' . _DB_PREFIX_ . 'kb_mp_seller as sr 
			ON (spr.`id_seller` = sr.`id_seller` AND sr.id_seller = ' . (int)$id_seller . ') 
			INNER JOIN ' . _DB_PREFIX_ . 'product_comment as pc on (spr.id_product_comment = pc.id_product_comment) 
			LEFT JOIN `' . _DB_PREFIX_ . 'customer` c ON (pc.`id_customer` = c.`id_customer`) 
			LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl 
			ON (pl.`id_product` = pc.`id_product` 
				AND spr.id_lang = pl.`id_lang`' . Shop::addSqlRestrictionOnLang('pl')
            . ') Where 1' . ((!empty($id_lang)) ? ' AND spr.id_lang = ' . (int)$id_lang : '');

        if (!empty($filter_string)) {
            $sql .= $filter_string;
        }

        if ($only_count) {
            $sql = str_replace('{{COLUMN}}', 'COUNT(spr.id_seller_product_review) as total', $sql);
            return DB::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
        } else {
            $cols = 'spr.*, IF(c.id_customer, CONCAT(c.`firstname`, \' \',  c.`lastname`), pc.customer_name) 
			customer_name, pc.`title`, pc.`content`, pc.`grade`, pl.`name`, pc.`validate`, 
			((pc.`grade`/' . (int)KbGlobal::MAX_RATING . ')*100) as rating_percent';
            $sql = str_replace('{{COLUMN}}', $cols, $sql);

            $sql .= ' ORDER BY spr.date_add DESC';

            if ((int)$start >= 0 && (int)$limit > 0) {
                $sql .= ' LIMIT ' . (int)$start . ', ' . (int)$limit;
            } elseif ((int)$limit > 0) {
                $sql .= ' LIMIT ' . (int)$limit;
            }

            return DB::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        }
    }

    public function getMenuBadgeHtml($id_seller)
    {
        return self::getReviewsBySeller($id_seller, null, null, true);
    }

    public function getCommentTitleWs()
    {
        $data = array();
        try {
            $sql = 'Select * from ' . _DB_PREFIX_ . 'product_comment 
                Where id_product_comment = '.(int)$this->id_product_comment;
            $data = DB::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);    
        } catch (Exception $e) {
            $data['error'] = $e->getMessage();
        }
        return Tools::jsonEncode($data);
    }
}
