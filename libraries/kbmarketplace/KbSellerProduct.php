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

class KbSellerProduct extends ObjectModel
{
    public $id;
    public $id_seller;
    public $id_shop;
    public $id_product;
    public $approved;
    public $deleted;
    public $date_add;
    public $date_upd;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'kb_mp_seller_product',
        'primary' => 'id_seller_product',
        'multilang' => false,
        'fields' => array(
            'id_seller' => array('type' => self::TYPE_INT, 'validate' => 'isNullOrUnsignedId', 'copy_post' => false),
            'id_shop' => array('type' => self::TYPE_INT, 'validate' => 'isNullOrUnsignedId', 'copy_post' => false),
            'id_product' => array('type' => self::TYPE_INT, 'validate' => 'isNullOrUnsignedId', 'copy_post' => false),
            'approved' => array('type' => self::TYPE_STRING, 'validate' => 'isString',
                'values' => array('0', '1', '2'), 'default' => '0'),
            'deleted' => array('type' => self::TYPE_STRING, 'validate' => 'isString',
                'values' => array('0', '1'), 'default' => '0'),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false)
        )
    );

    protected $webserviceParameters = array(
        'objectsNodeName' => 'kbsellerproducts',
        'objectNodeName' => 'kbsellerproduct',
        'fields' => array(
            'id_seller' => array('xlink_resource' => 'kbsellers'),
            'id_product' => array('xlink_resource' => 'products'),
            'id_shop' => array('xlink_resource' => 'shops'),
            'approved_text' => array('getter' => 'getApprovedTextWs'),
        )
    );

    public function __construct($id = null)
    {
        parent::__construct($id);
    }

    /*
     * Check seller product
     */

    public static function isSellerProduct($id_seller, $id_product)
    {
        $sql = 'SELECT COUNT(*) as row from ' . _DB_PREFIX_ . 'kb_mp_seller_product 
			where id_seller = ' . (int)$id_seller . ' AND id_product = ' . (int)$id_product;
        return (bool)DB::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
    }

    public static function getLoadedObject($id_seller, $id_product)
    {
        $sql = 'SELECT id_seller_product from ' . _DB_PREFIX_ . 'kb_mp_seller_product 
			where id_seller = ' . (int)$id_seller . ' AND id_product = ' . (int)$id_product;
        return (new KbSellerProduct((int)DB::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql)));
    }

    /*
     * get seller products
     */

    public static function getSellerProducts(
    $id_seller, $only_count = false, $start = null, $limit = null, $orderby = null, $orderway
    = null, $approved = null, $deleted = null, $id_shop = null, $custom_filter = '', $id_lang
    = null
    )
    {
        if ((int) $id_lang == 0) {
            $id_lang = Context::getContext()->language->id;
        }
        $context = new Context();
        $sql     = 'SELECT {{COLUMN}} from '._DB_PREFIX_.'kb_mp_seller_product as sp
            INNER JOIN '._DB_PREFIX_.'product as p on (sp.id_product = p.id_product)
            INNER JOIN '._DB_PREFIX_.'product_lang as pl on (p.id_product = pl.id_product AND pl.id_lang = '.(int) $id_lang.
                ' AND pl.id_shop='.(int) $context->getContext()->shop->id.')
            where sp.id_seller = '.(int) $id_seller;

        if ($id_shop === null) {
            
        } else {
            $sql .= ' AND sp.id_shop = '.(int) $id_shop;
        }

        if ($approved === null) {
            
        } else {
            $sql .= ' AND sp.approved = "'.(int) $approved.'"';
        }

        if ($deleted === null) {
            
        } else {
            $sql .= ' AND sp.deleted = "'.pSQL($deleted).'"';
        }

//        return $sql;
        if ($custom_filter != '') {
            $sql .= pSQL($custom_filter);
        }
        if ($only_count) {
            $sql = str_replace('{{COLUMN}}', 'COUNT(*) as total', $sql);
            return DB::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
        } else {
            $sql = str_replace('{{COLUMN}}', 'sp.*', $sql);

            if ($orderby != null && $orderway != null) {
                $sql .= ' ORDER BY '.pSQL($orderby).' '.pSQL($orderway);
            } else {
                $sql .= ' ORDER BY sp.id_product DESC';
            }

            if ((int) $start >= 0 && (int) $limit > 0) {
                $sql .= ' LIMIT '.(int) $start.', '.(int) $limit;
            } elseif ((int) $limit > 0) {
                $sql .= ' LIMIT '.(int) $limit;
            }

            return DB::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        }
    }

    /*
     * Fetch seller products with product details
     */

    public static function getProductsWithDetails(
        $id_seller,
        $id_lang = null,
        $filters = array(),
        $only_count = false,
        $start = null,
        $limit = null,
        $orderby = null,
        $orderway = null
    ) {
        $alias_where = 'p';
        if (version_compare(_PS_VERSION_, '1.5', '>')) {
            $alias_where = 'product_shop';
        }

        $columns = 'p.*, product_shop.*, product_shop.id_category_default, pl.*, 
			MAX(image_shop.`id_image`) id_image, il.legend, m.name manufacturer_name, 
			MAX(product_attribute_shop.id_product_attribute) id_product_attribute, 
			DATEDIFF(product_shop.`date_add`, DATE_SUB(NOW(), INTERVAL 20 DAY)) > 0 AS new, stock.out_of_stock, 
			IFNULL(stock.quantity, 0) as quantity';

        $query = 'SELECT {{COLUMNS}} FROM `' . _DB_PREFIX_ . 'product` p 
			INNER JOIN ' . _DB_PREFIX_ . 'kb_mp_seller_product as s2p 
			on (p.id_product = s2p.id_product AND s2p.approved = "' . (int)KbGlobal::APPROVED . '") ';

        if (isset($filters['id_category']) && (int)$filters['id_category'] > 0) {
            $query .= 'INNER JOIN `' . _DB_PREFIX_ . 'category_product` cp ON p.`id_product` = cp.`id_product` 
			LEFT JOIN ' . _DB_PREFIX_ . 'category c ON (c.id_category = cp.id_category) ';
        }


        $query .= Shop::addSqlAssociation('product', 'p')
            . Product::sqlStock('p', null, false, Context::getContext()->shop) . ' 
			LEFT JOIN ' . _DB_PREFIX_ . 'product_lang pl 
			ON (pl.id_product = p.id_product' . Shop::addSqlRestrictionOnLang('pl')
            . ' AND pl.id_lang = ' . (int)$id_lang . ') 
			LEFT JOIN `' . _DB_PREFIX_ . 'image` i ON (i.`id_product` = p.`id_product`) 
			' . Shop::addSqlAssociation('image', 'i', false, 'image_shop.cover=1') . ' 
			LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il ON (image_shop.`id_image` = il.`id_image` 
			AND il.`id_lang` = ' . (int)$id_lang . ') 
			LEFT JOIN ' . _DB_PREFIX_ . 'manufacturer m ON (m.id_manufacturer = p.id_manufacturer) 
			LEFT JOIN ' . _DB_PREFIX_ . 'product_attribute pa ON (p.id_product = pa.id_product) 
			' . Shop::addSqlAssociation('product_attribute', 'pa', false, 'product_attribute_shop.`default_on` = 1')
            . ' WHERE s2p.id_seller = ' . (int)$id_seller . ' AND ' . pSQL($alias_where) . '.`active` = 1 
			AND ' . pSQL($alias_where) . '.`visibility` IN ("both", "catalog") ';

        $filter_conditions = '';
        if (isset($filters['id_category']) && (int)$filters['id_category'] > 0) {
            $filter_conditions .= ' AND c.id_category = ' . (int)$filters['id_category'] . ' AND c.active = 1';
        }

        $query .= pSQL($filter_conditions);

        if (is_array($filters) && count($filters) > 0) {
            foreach ($filters as $key => $filter) {
                if ($key != 'id_category') {
                    $filter_conditions .= ' AND ' . pSQL($filter);
                }
            }
        }

        $query .= ' GROUP BY ' . pSQL($alias_where) . '.id_product';

        if ($only_count) {
            $query = str_replace('{{COLUMNS}}', 'COUNT(p.id_product) as total', $query);
            $rows = DB::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
            return count($rows);
        } else {
            $query = str_replace('{{COLUMNS}}', $columns, $query);
            if ($orderby != null && $orderway != null) {
                $query .= ' ORDER BY ' . pSQL($orderby) . ' ' . pSQL($orderway);
            } else {
                $query .= ' ORDER BY ' . pSQL($alias_where) . '.id_product DESC';
            }

            if ((int)$start > 0 && (int)$limit > 0) {
                $query .= ' LIMIT ' . (int)$start . ',' . (int)$limit;
            } elseif ((int)$limit > 0) {
                $query .= ' LIMIT ' . (int)$limit;
            }

            return DB::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
        }
    }

    public static function getSellerByProductId($id_product)
    {
        $sql = 'Select id_seller from ' . _DB_PREFIX_ . 'kb_mp_seller_product where id_product = ' . (int)$id_product;

        $id_seller = DB::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);

        if ($id_seller && (int)$id_seller > 0) {
            $object = new KbSeller($id_seller);

            return $object->getSellerInfo();
        } else {
            return array();
        }
    }

    public static function getSellerIdByProductId($id_product)
    {
        $sql = 'Select id_seller from ' . _DB_PREFIX_ . 'kb_mp_seller_product where id_product = ' . (int)$id_product;

        return (int)DB::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
    }

    public static function isApprovedProduct($id_seller, $id_product)
    {
        $sql = 'Select id_seller_product from ' . _DB_PREFIX_ . 'kb_mp_seller_product 
			where id_seller = ' . (int)$id_seller
            . ' AND id_product = ' . (int)$id_product . ' AND approved = "' . (int)KbGlobal::APPROVED . '"';

        return (bool)DB::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
    }

    public function getMenuBadgeHtml($id_seller)
    {
        return self::getSellerProducts($id_seller, true);
    }

    public function getApprovedTextWs()
    {
        return KbGlobal::getApporvalStatus($this->approved);
    }

    public static function trackAndUpdateProduct($id_seller, $seller_status = 0)
    {
        if ($id_seller > 0) {
            if ($seller_status == 0) {
                $query = 'SELECT p.id_product FROM ' . _DB_PREFIX_ . 'product p 
					INNER JOIN ' . _DB_PREFIX_ . 'kb_mp_seller_product as s2p on (p.id_product = s2p.id_product) 
					WHERE s2p.id_seller = ' . (int)$id_seller . ' AND p.active = 1';

                $results = DB::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
                if ($results && count($results) > 0) {
                    foreach ($results as $p) {
                        $where = 'id_seller = ' . (int)$id_seller . ' AND id_product = ' . (int)$p['id_product'];
                        Db::getInstance(_PS_USE_SQL_SLAVE_)->delete(
                            'kb_mp_seller_product_tracking',
                            pSQL($where)
                        );
                        Db::getInstance(_PS_USE_SQL_SLAVE_)->insert(
                            'kb_mp_seller_product_tracking',
                            array(
                                'id_seller' => (int)$id_seller,
                                'id_product' => (int)$p['id_product'],
                                'date_add' => pSQL(date('Y-m-d H:i:s'))
                            )
                        );

                        $product = new Product($p['id_product']);
                        $product->active = 1;
                        $product->toggleStatus();
                    }
                }
            } elseif ($seller_status == 1) {
                $query = 'SELECT * FROM ' . _DB_PREFIX_ . 'kb_mp_seller_product_tracking 
					WHERE id_seller = ' . (int)$id_seller;

                $results = DB::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
                if ($results && count($results) > 0) {
                    foreach ($results as $p) {
                        $where = 'id_seller = ' . (int)$id_seller . ' AND id_product = ' . (int)$p['id_product'];
                        Db::getInstance(_PS_USE_SQL_SLAVE_)->delete(
                            'kb_mp_seller_product_tracking',
                            pSQL($where)
                        );

                        $product = new Product($p['id_product']);
                        $product->active = 0;
                        $product->toggleStatus();
                    }
                }
            }
        }
    }

    public static function trackAndUpdateApprovedProduct($id_seller, $product_id = 0)
    {
        if ($id_seller > 0) {
            if ($product_id > 0) {
                $query = 'SELECT * FROM ' . _DB_PREFIX_ . 'kb_mp_seller_product_tracking 
					WHERE id_seller = ' . (int)$id_seller.' AND id_product = '.(int)$product_id;

                $results = DB::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
                if ($results && count($results) > 0) {
                    foreach ($results as $p) {
                        $where = 'id_seller = ' . (int)$id_seller . ' AND id_product = ' . (int)$p['id_product'];
                        Db::getInstance(_PS_USE_SQL_SLAVE_)->delete(
                            'kb_mp_seller_product_tracking',
                            pSQL($where)
                        );

                        $product = new Product($p['id_product']);
                        $product->active = 0;
                        $product->toggleStatus();
                    }
                }
            }
        }
    }
}
