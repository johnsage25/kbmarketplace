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

class KbSellerCRequest extends ObjectModel
{
    public $id;
    public $id_seller;
    public $id_category;
    public $id_shop;
    public $id_lang;
    public $comment;
    public $approved;
    public $date_add;
    public $date_upd;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'kb_mp_seller_category_request',
        'primary' => 'id_seller_category_request',
        'fields' => array(
            'id_seller' => array('type' => self::TYPE_INT, 'validate' => 'isNullOrUnsignedId', 'copy_post' => false),
            'id_shop' => array('type' => self::TYPE_INT, 'validate' => 'isNullOrUnsignedId', 'copy_post' => false),
            'id_lang' => array('type' => self::TYPE_INT, 'validate' => 'isNullOrUnsignedId', 'copy_post' => false),
            'id_category' => array('type' => self::TYPE_INT, 'validate' => 'isNullOrUnsignedId', 'copy_post' => false),
            'comment' => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'required' => true),
            'approved' => array('type' => self::TYPE_STRING, 'validate' => 'isString',
                'values' => array('0', '1', '2'), 'default' => '0'),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
        )
    );

    protected $webserviceParameters = array(
        'objectsNodeName' => 'kbsellercrequests',
        'objectNodeName' => 'kbsellercrequest',
        'fields' => array(
            'id_seller' => array('xlink_resource' => 'kbsellers'),
            'id_category' => array('xlink_resource' => 'categories'),
            'id_shop' => array('xlink_resource' => 'shops'),
            'id_lang' => array('xlink_resource' => 'languages'),
            'approved_text' => array('getter' => 'getApprovedTextWs'),
        )
    );

    public function __construct($id = null)
    {
        parent::__construct($id);
    }

    public function getApprovedTextWs()
    {
        return KbGlobal::getApporvalStatus($this->approved);
    }

    public static function getRequestBySeller(
        $id_seller,
        $approve,
        $only_count = false,
        $start = null,
        $limit = null,
        $filter_string = ''
    ) {
        $sql = 'Select {{COLUMN}} from ' . _DB_PREFIX_ . 'kb_mp_seller_category_request  
			Where id_seller = ' . (int)$id_seller;

        if ($approve !== false) {
            $sql .= ' AND approved = "' . pSQL($approve) . '"';
        }

        if (!empty($filter_string)) {
            $sql .= pSQL($filter_string);
        }

        if ($only_count) {
            $sql = str_replace('{{COLUMN}}', 'COUNT(id_seller_category_request) as total', $sql);
            return DB::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
        } else {
            $sql = str_replace('{{COLUMN}}', '*', $sql);

            $sql .= ' ORDER BY date_add DESC';

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
        return self::getRequestBySeller($id_seller, KbGlobal::APPROVAL_WAITING, true);
    }
}
