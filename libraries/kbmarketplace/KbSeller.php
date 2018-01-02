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

class KbSeller extends ObjectModel
{
    public $id;
    public $id_customer;
    public $id_shop;
    public $id_default_lang;
    public $approved;
    public $active;
    public $product_limit_wout_approval;
    public $approval_request_limit;
    public $phone_number;
    public $business_email;
    public $notification_type;
    public $logo;
    public $banner;
    public $address;
    public $state;
    public $id_country;
    public $payment_info;
    public $fb_link;
    public $gplus_link;
    public $twit_link;
    public $deleted;
    public $title;
    public $description;
    public $meta_keyword;
    public $meta_description;
    public $profile_url;
    public $return_policy;
    public $shipping_policy;
    public $date_add;
    public $date_upd;

    const TABLE_NAME = 'kb_mp_seller';
    const NOTIFICATION_BOTH = 0;
    const NOTIFICATION_PRIMARY = 1;
    const NOTIFICATION_BUSINESS = 2;

    const SELLER_PROFILE_IMG_PATH = 'kbmarketplace/sellers/';

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'kb_mp_seller',
        'primary' => 'id_seller',
        'multilang' => true,
        'fields' => array(
            'id_customer' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isNullOrUnsignedId'
            ),
            'id_shop' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isNullOrUnsignedId'
            ),
            'id_default_lang' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isNullOrUnsignedId'
            ),
            'approved' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isInt'
            ),
            'active' => array(
                'type' => self::TYPE_BOOL,
                'validate' => 'isBool'
            ),
            'product_limit_wout_approval' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isInt', 'default' => 0
            ),
            'approval_request_limit' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isInt', 'default' => 0
            ),
            'phone_number' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isPhoneNumber'
            ),
            'business_email' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isEmail', 'size' => 128
            ),
            'notification_type' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isString', 'values' => array('0', '1', '2')
            ),
            'logo' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isGenericName'
            ),
            'banner' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isGenericName'
            ),
            'address' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isAddress'
            ),
            'state' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isGenericName',
                'default' => null
            ),
            'id_country' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isNullOrUnsignedId'
            ),
            'payment_info' => array(
                'type' => self::TYPE_HTML,
                'default' => null
            ),
            'fb_link' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isUrl', 'default' => null
            ),
            'gplus_link' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isUrl', 'default' => null
            ),
            'twit_link' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isUrl', 'default' => null
            ),
            'deleted' => array(
                'type' => self::TYPE_BOOL,
                'validate' => 'isBool'
            ),
            'date_add' => array(
                'type' => self::TYPE_DATE,
                'validate' => 'isDate', 'copy_post' => false
            ),
            'date_upd' => array(
                'type' => self::TYPE_DATE,
                'validate' => 'isDate', 'copy_post' => false
            ),
            'title' => array(
                'type' => self::TYPE_STRING,
                'lang' => true, 'validate' => 'isGenericName'
            ),
            'description' => array(
                'type' => self::TYPE_HTML,
                'lang' => true, 'validate' => 'isCleanHtml'
            ),
            'meta_keyword' => array(
                'type' => self::TYPE_STRING,
                'lang' => true, 'validate' => 'isGenericName'
            ),
            'meta_description' => array(
                'type' => self::TYPE_STRING,
                'lang' => true, 'validate' => 'isGenericName'
            ),
            'profile_url' => array(
                'type' => self::TYPE_STRING,
                'lang' => true, 'validate' => 'isLinkRewrite'
            ),
            'return_policy' => array(
                'type' => self::TYPE_HTML,
                'lang' => true, 'validate' => 'isCleanHtml'
            ),
            'shipping_policy' => array(
                'type' => self::TYPE_HTML,
                'lang' => true, 'validate' => 'isCleanHtml'
            )
        ),
        'associations' => array(
            'customer' =>   array('type' => self::HAS_ONE, 'field' => 'id_customer', 'object' => 'Customer'),
            'language' =>   array('type' => self::HAS_ONE, 'field' => 'id_default_lang', 'object' => 'Language'),
            'shop' =>   array('type' => self::HAS_ONE, 'field' => 'id_seller_shop', 'object' => 'Shop'),
            'kbsellerproducts' => array('type' => self::HAS_MANY, 'field' => 'id_seller', 'object' => 'KbSellerProduct', 'association' => 'kb_mp_seller_product'),
            'kbsellercategories' => array('type' => self::HAS_MANY, 'field' => 'id_seller_category', 'object' => 'KbSellerCategory', 'association' => 'kb_mp_seller_category'),
        )
    );

    protected $webserviceParameters = array(
        'objectMethods' => array(
            'add' => 'addWs',
            'update' => 'updateWs'
        ),
        'objectNodeName' => 'seller',
        'objectsNodeName' => 'kbsellers',
        'fields' => array(
            'id_customer' => array('xlink_resource' => 'customers'),
            'id_seller_shop' => array('xlink_resource' => 'shops'),
            'id_default_lang' => array('xlink_resource' => 'languages'),
            'approved_text' => array('getter' => 'getApprovedTextWs'),
            'logo' => array('getter' => 'getLogoWs'),
            'banner' => array('getter' => 'getBannerWs'),
            'id_country' => array('xlink_resource' => 'countries'),
            'rating' => array('getter' => 'getRatingWs'),
            'total_review' => array('getter' => 'getReviewCountWs'),
        ),
        'associations' => array(
            'kbsellerproducts' => array('getter' => 'getProductsWs', 'resource' => 'kbsellerproduct'),
            'kbsellercrequests' => array('getter' => 'getRequestedCategoriesWs', 'resource' => 'kbsellercrequest'),
            'categories' => array(
                'getter' => 'getCategoriesWs',
                'resource' => 'category'
            )
        ),
    );

    public function __construct($id = null, $id_lang = null)
    {
        parent::__construct($id, $id_lang);
    }

    public function getSellerInfo()
    {
        $sql = 'Select CONCAT(c.`firstname`, \' \', c.`lastname`) 
			AS `seller_name`, c.`email`, s.*, sl.* 
			FROM ' . _DB_PREFIX_ . 'kb_mp_seller as s 
			INNER JOIN ' . _DB_PREFIX_ . 'customer c 
			ON (s.`id_customer` = c.`id_customer`) 
			INNER JOIN ' . _DB_PREFIX_ . 'kb_mp_seller_lang as sl 
			on (s.id_seller = sl.id_seller AND sl.id_lang = s.id_default_lang) 
			WHERE s.id_seller = ' . (int)$this->id;

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
        if ($result) {
            $result['title'] = (!empty($result['title']))
                ? $result['title'] : Translate::getModuleTranslation(null, 'Not Mentioned', 'kbmarketplace');
            return $result;
        } else {
            return array();
        }
    }

    public function getBusinessEmail()
    {
        if (!empty($this->business_email) && Validate::isEmail($this->business_email)) {
            return $this->business_email;
        } else {
            $info = $this->getSellerInfo();
            return $info['email'];
        }
    }

    public function getEmailIdForNotification()
    {
        $seller_info = $this->getSellerInfo();
        $emails = array(array(
                'title' => $seller_info['seller_name'],
                'email' => $seller_info['email']
        ));
        if ($this->notification_type == self::NOTIFICATION_BOTH
            && Validate::isEmail($this->business_email)) {
            $emails[] = array(
                'title' => $seller_info['title'],
                'email' => $this->business_email
            );
        } elseif ($this->notification_type == self::NOTIFICATION_BUSINESS
            && Validate::isEmail($this->business_email)) {
            $emails = array(array(
                    'title' => $seller_info['title'],
                    'email' => $this->business_email
            ));
        }

        return $emails;
    }

    public static function getSellers(
        $only_count = false,
        $include_review_count = true,
        $start = null,
        $limit = null,
        $orderby = null,
        $orderway = null,
        $approved = false,
        $active = false,
        $id_lang = null,
        $id_shop = null
    ) {
        $columns = 'CONCAT(c.`firstname`, \'. \', c.`lastname`) AS `name`, 
			c.`email`, s.*, sl.*';
        $sql = 'Select {{COLUMN}} FROM ' . _DB_PREFIX_ . 'kb_mp_seller as s 
			INNER JOIN ' . _DB_PREFIX_ . 'customer c 
			ON (s.`id_customer` = c.`id_customer`) 
			INNER JOIN ' . _DB_PREFIX_ . 'kb_mp_seller_lang as sl 
			on (s.id_seller = sl.id_seller 
			AND sl.id_lang = ' . (($id_lang) ? (int)$id_lang : 's.id_default_lang') . ')';

        if ($only_count === false && $include_review_count) {
            $sql .= ' LEFT JOIN ' . _DB_PREFIX_ . 'kb_mp_seller_review as rev 
				on (s.id_seller = rev.id_seller AND rev.approved = "' . (int)KbGlobal::APPROVED . '")';
            $columns .= ', SUM(IF(rev.rating IS NOT NULL, rev.rating, 0)) 
				as rating, COUNT(rev.id_seller_review) as total_review';
        }

        $sql .= ' WHERE 1';
        if ($approved === true) {
            $sql .= ' AND s.approved = ' . (int)KbGlobal::APPROVED;
        }

        if ($active === true) {
            $sql .= ' AND s.active = ' . (int)KbGlobal::ENABLE;
        }

        if ($id_shop) {
            $sql .= ' AND s.id_shop = ' . (int)$id_shop;
        } else {
            $sql .= ' AND s.id_shop = ' . (int)Context::getContext()->shop->id;
        }

        if ($only_count) {
            $sql = str_replace('{{COLUMN}}', 'COUNT(*) as total', $sql);
            return DB::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
        } else {
            $sql .= ' GROUP BY s.id_seller';

            if ($orderby != null && $orderway != null) {
                $sql .= ' ORDER BY ' . pSQL($orderby) . ' ' . pSQL($orderway);
            } else {
                $sql .= ' ORDER BY s.id_seller DESC';
            }

            if ((int)$start > 0 && (int)$limit > 0) {
                $sql .= ' LIMIT ' . (int)$start . ', ' . (int)$limit;
            } elseif ((int)$limit > 0) {
                $sql .= ' LIMIT ' . (int)$limit;
            }

            $sql = str_replace('{{COLUMN}}', $columns, $sql);

            return DB::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        }
    }

    public function isSeller()
    {
        if ($this->id != '' && $this->id > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function registerNewCustomer($id_customer = 0, $approve = KbGlobal::APPROVED, $active = KbGlobal::DISABLE)
    {
        $customer = new Customer($id_customer);

        if ($customer->id > 0) {
            $this->id_customer = $customer->id;
            $this->id_shop = $customer->id_shop;
            $this->id_default_lang = $customer->id_lang;
            $this->approved = $approve;
            $this->active = $active;
            $this->deleted = '0';
            if ($this->save(true)) {
                return $this->id;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public static function getSellerByCustomerId($id_customer = 0)
    {
        if ($id_customer > 0) {
            $sql = 'select id_seller from ' . _DB_PREFIX_ . pSQL(self::TABLE_NAME)
                . ' where id_customer = ' . (int)$id_customer;
            return DB::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
        } else {
            return 0;
        }
    }

    public function isApprovedSeller()
    {
        if ($this->approved == KbGlobal::APPROVED) {
            return true;
        } else {
            return false;
        }
    }

    public static function isBusinessEmailExist($email, $id_seller = 0)
    {
        $sql = 'Select COUNT(id_seller) as total from ' . _DB_PREFIX_ . 'kb_mp_seller 
			WHERE business_email = "' . pSQL($email) . '"';

        if ((int)$id_seller > 0) {
            $sql .= ' AND id_seller != ' . (int)$id_seller;
        }

        return (bool)DB::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
    }

    public function addWs($autodate = true, $null_values = false)
    {
        $success = parent::add($autodate, $null_values);
        Hook::exec('actionKbMarketPlaceUpdateSeller', array('object' => $this));
        return $success;
    }

    public function updateWs($null_values = false)
    {
        $success = parent::update($null_values);
        Hook::exec('actionKbMarketPlaceUpdateSeller', array('object' => $this));
        return $success;
    }

    public function getLogoWs()
    {
        $seller_image_path = KbGlobal::getBaseLink((bool)Configuration::get('PS_SSL_ENABLED'))
            . 'modules/kbmarketplace/views/img/seller_media/';
        $seller_img_dir = _PS_MODULE_DIR_ . 'kbmarketplace/views/img/seller_media/' . $this->id . '/';
        if (empty($this->logo) || !Tools::file_exists_no_cache($seller_img_dir . $this->logo)) {
            return $seller_image_path . KbGlobal::SELLER_DEFAULT_LOGO;
        } else {
            return $seller_image_path . $this->id . '/' . $this->logo;
        }
    }

    public function getBannerWs()
    {
        $seller_image_path = KbGlobal::getBaseLink((bool)Configuration::get('PS_SSL_ENABLED'))
            . 'modules/kbmarketplace/views/img/seller_media/';
        $seller_img_dir = _PS_MODULE_DIR_ . 'kbmarketplace/views/img/seller_media/' . $this->id . '/';
        if (empty($this->banner) || !Tools::file_exists_no_cache($seller_img_dir . $this->banner)) {
            return $seller_image_path . KbGlobal::SELLER_DEFAULT_BANNER;
        } else {
            return $seller_image_path . $this->id . '/' . $this->banner;
        }
    }

    public function getProductsWs()
    {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            'SELECT `id_seller_product` as id
            FROM `'._DB_PREFIX_.'kb_mp_seller_product` 
            WHERE `id_seller` = '.(int)$this->id
        );
        return $result;
    }

    public function getRatingWs()
    {
        return KbGlobal::convertRatingIntoPercent(KbSellerReview::getSellerRating((int)$this->id));
    }

    public function getReviewCountWs()
    {
        return KbSellerReview::getSellerRating((int)$this->id);
    }

    public function getCategoriesWs()
    {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            'SELECT `id_category` as id
            FROM `'._DB_PREFIX_.'kb_mp_seller_category` 
            WHERE `id_seller` = '.(int)$this->id
        );
        return $result;
    }

    public function getRequestedCategoriesWs()
    {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            'SELECT `id_seller_category_request` as id
            FROM `'._DB_PREFIX_.'kb_mp_seller_category_request` 
            WHERE `id_seller` = '.(int)$this->id
        );
        return $result;
    }

    public function getApprovedTextWs()
    {
        return KbGlobal::getApporvalStatus($this->approved);
    }

    public static function getAllSellers($id_shop = null)
    {
        $sql = 'Select id_seller, id_default_lang, id_customer from ' . _DB_PREFIX_ . 'kb_mp_seller WHERE 1';

        if ((int)$id_shop > 0) {
            $sql .= ' AND id_shop = ' . (int)$id_shop;
        }

        return DB::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    }
}
