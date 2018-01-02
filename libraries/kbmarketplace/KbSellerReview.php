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

class KbSellerReview extends ObjectModel
{
    public $id;
    public $id_seller;
    public $id_customer;
    public $id_shop;
    public $id_lang;
    public $title;
    public $comment;
    public $rating;
    public $approved;
    public $date_add;
    public $date_upd;
    public $seller;
    public $customer;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'kb_mp_seller_review',
        'primary' => 'id_seller_review',
        'fields' => array(
            'id_seller' => array('type' => self::TYPE_INT, 'validate' => 'isNullOrUnsignedId', 'copy_post' => false),
            'id_customer' => array('type' => self::TYPE_INT, 'validate' => 'isNullOrUnsignedId', 'copy_post' => false),
            'id_shop' => array('type' => self::TYPE_INT, 'validate' => 'isNullOrUnsignedId', 'copy_post' => false),
            'id_lang' => array('type' => self::TYPE_INT, 'validate' => 'isNullOrUnsignedId', 'copy_post' => false),
            'title' => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'required' => true),
            'comment' => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml'),
            'rating' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'default' => 0),
            'approved' => array('type' => self::TYPE_STRING, 'validate' => 'isString',
                'values' => array('0', '1', '2'), 'default' => '0'),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
        )
    );

    protected $webserviceParameters = array(
        'objectsNodeName' => 'kbsellerreviews',
        'objectNodeName' => 'kbsellerreview',
        'fields' => array(
            'id_seller' => array('xlink_resource' => 'kbsellers'),
            'id_customer' => array('xlink_resource' => 'customers'),
            'id_shop' => array('xlink_resource' => 'shops'),
            'id_lang' => array('xlink_resource' => 'languages'),
            'approved_text' => array('getter' => 'getApprovedTextWs'),
        )
    );

    public function __construct($id = null)
    {
        parent::__construct($id);
        if ($id > 0) {
            $seller = new KbSeller($this->id_seller);
            $seller_info = $seller->getSellerInfo();
            $this->seller = array(
                'seller_name' => $seller_info['seller_name'],
                'email' => $seller_info['email'],
                'title' => $seller_info['title'],
                'id_default_lang' => $seller_info['id_default_lang']
            );

            $customer = new Customer($this->id_customer);
            $this->customer = array(
                'name' => $customer->firstname . ' ' . $customer->lastname,
                'email' => $customer->email,
                'id_lang' => $customer->id_lang
            );
        } else {
            $this->seller = array(
                'seller_name' => '',
                'email' => '',
                'title' => '',
            );
            $this->customer = array(
                'name' => '',
                'email' => ''
            );
        }
    }

    public static function getReviewsBySellerId(
        $id_seller,
        $id_lang = null,
        $approved_status = false,
        $only_count = false,
        $rating = false,
        $start = null,
        $limit = null,
        $filter_string = null
    ) {
        $sql = 'Select {{COLUMN}} from ' . _DB_PREFIX_ . 'kb_mp_seller_review as sr 
			INNER JOIN ' . _DB_PREFIX_ . 'customer as c on (sr.id_customer = c.id_customer) 
			Where sr.id_seller = ' . (int)$id_seller;

        if (!empty($id_lang)) {
            $sql .= ' AND sr.id_lang  = "' . (int)$id_lang . '"';
        }

        if ($approved_status !== false) {
            $sql .= ' AND sr.approved  = "' . pSQL($approved_status) . '"';
        }

        if ($rating !== false) {
            $sql .= ' AND sr.rating  = ' . (int)$rating;
        }

        if (!empty($filter_string)) {
            $sql .= pSQL($filter_string);
        }

        if ($only_count) {
            $sql = str_replace('{{COLUMN}}', 'COUNT(sr.id_seller_review) as total', $sql);
            return DB::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
        } else {
            $col_string = 'sr.*, ((sr.rating/' .(int) KbGlobal::MAX_RATING . ')*100) as rating_percent, 
				c.firstname, c.lastname ';
            $sql = str_replace('{{COLUMN}}', $col_string, $sql);

            $sql .= ' ORDER BY sr.date_add DESC';

            if ((int)$start >= 0 && (int)$limit > 0) {
                $sql .= ' LIMIT ' . (int)$start . ', ' . (int)$limit;
            } elseif ((int)$limit > 0) {
                $sql .= ' LIMIT ' . (int)$limit;
            }

            return DB::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        }
    }

    public static function getSellerIdByReview($id_seller_review)
    {
        return DB::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            'Select * from ' . _DB_PREFIX_ . 'kb_mp_seller_review 
			where id_seller_review = ' . (int)$id_seller_review
        );
    }

    public static function getSellerRating($id_seller, $approve_status = false)
    {
        $sql = 'Select * from ' . _DB_PREFIX_ . 'kb_mp_seller_review where id_seller = ' . (int)$id_seller;

        if ($approve_status !== false) {
            $sql .= ' AND approved  = "' . pSQL($approve_status) . '"';
        } else {
            $sql .= ' AND approved  = "' . (int)KbGlobal::APPROVED . '"';
        }

        $results = DB::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        if ($results && count($results) > 0) {
            $total_points = 0;
            foreach ($results as $rev) {
                $total_points += (int)$rev['rating'];
            }

            return (float)($total_points / count($results));
        } else {
            return 0;
        }
    }

    public function getMenuBadgeHtml($id_seller)
    {
        return self::getReviewsBySellerId($id_seller, null, false, true);
    }

    public function getApprovedTextWs()
    {
        return KbGlobal::getApporvalStatus($this->approved);
    }
}
