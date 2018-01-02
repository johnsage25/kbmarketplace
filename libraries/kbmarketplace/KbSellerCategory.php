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

class KbSellerCategory extends ObjectModel
{
    public $id;
    public $id_seller;
    public $id_category;
    public $id_shop;
    public $date_add;
    public $date_upd;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'kb_mp_seller_category',
        'primary' => 'id_seller_category',
        'fields' => array(
            'id_seller' => array('type' => self::TYPE_INT, 'validate' => 'isNullOrUnsignedId', 'copy_post' => false),
            'id_shop' => array('type' => self::TYPE_INT, 'validate' => 'isNullOrUnsignedId', 'copy_post' => false),
            'id_category' => array('type' => self::TYPE_INT, 'validate' => 'isNullOrUnsignedId', 'copy_post' => false),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
        )
    );

    protected $webserviceParameters = array(
        'objectsNodeName' => 'kbsellercategories',
        'objectNodeName' => 'kbsellercategory',
        'fields' => array(
            'id_seller' => array('xlink_resource' => 'kbsellers'),
            'id_category' => array('xlink_resource' => 'categories'),
            'id_shop' => array('xlink_resource' => 'shops')
        ),
        'associations' => array(
            'kbsellers' => array('getter' => 'getSellersWs', 'resource' => 'kbseller'),
        )
    );

    public function __construct($id = null)
    {
        parent::__construct($id);
    }

    public static function getCategoriesBySeller($id_seller, $only_count = false, $start = null, $limit = null)
    {
        $sql = 'select {{COLUMNS}} from ' . _DB_PREFIX_ . 'kb_mp_seller_category 
			WHERE id_seller = ' . (int)$id_seller;

        if ($only_count === true) {
            $sql = Tools::str_replace_once('{{COLUMNS}}', 'count(id_category) as total', $sql);
            return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
        } else {
            $sql = Tools::str_replace_once('{{COLUMNS}}', 'id_category', $sql);

            $sql .= ' ORDER BY date_add DESC';

            if (!empty($start) && !empty($limit)) {
                $sql .= ' LIMIT ' . (int)$start . ', ' . (int)$limit;
            } elseif (!empty($limit)) {
                $sql .= ' LIMIT ' . (int)$limit;
            }

            $results = DB::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
            $categories = array();
            foreach ($results as $c) {
                $categories[] = (int)$c['id_category'];
            }

            return $categories;
        }
    }

    public static function getRowIdBySellerAndCategory($id_seller, $id_category)
    {
        $sql = 'select id_seller_category from ' . _DB_PREFIX_ . 'kb_mp_seller_category 
			WHERE id_seller = ' . (int)$id_seller . ' AND id_category = ' . (int)$id_category;

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
    }

    public static function getDisabledCategoryProducts($id_seller, $id_category)
    {
        $sql = 'select * from ' . _DB_PREFIX_ . 'kb_mp_seller_category_tracking 
			WHERE id_seller = ' . (int)$id_seller . ' AND id_category = ' . (int)$id_category;

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    }

    public static function deleteAllBySeller($id_seller)
    {
        $where = 'id_seller = ' . (int)$id_seller;
        Db::getInstance(_PS_USE_SQL_SLAVE_)->delete(
            'kb_mp_seller_category',
            pSQL($where)
        );
    }

    public static function deleteCategoryBySeller($id_seller, $id_category)
    {
        $where = 'id_seller = ' . (int)$id_seller . ' AND id_category = ' . (int)$id_category;
        Db::getInstance(_PS_USE_SQL_SLAVE_)->delete(
            'kb_mp_seller_category',
            pSQL($where)
        );
    }

    public function getSellersWs()
    {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            'SELECT `id_seller` as id
            FROM `'._DB_PREFIX_.'kb_mp_seller_category` 
            WHERE `id_category` = '.(int)$this->id_category
        );
        return $result;
    }

    public static function trackAndUpdateCategories($id_seller, $categories)
    {
        $new_categories = $categories;
        $prev_categories = self::getCategoriesBySeller($id_seller);

        $newly_comes = array_diff($new_categories, $prev_categories);

        if (count($newly_comes) > 0) {
            foreach ($newly_comes as $id_cat) {
                $tracked_products = self::getDisabledCategoryProducts($id_seller, $id_cat);
                if (count($tracked_products) > 0) {
                    foreach ($tracked_products as $row) {
                        $product = new Product($row['id_product']);
                        $product->updateCategories(array($id_cat));

                        $where = 'id_seller = ' . (int)$id_seller
                            . ' AND id_category = ' . (int)$id_cat . ' AND id_product = ' . (int)$row['id_product'];

                        Db::getInstance(_PS_USE_SQL_SLAVE_)->delete(
                            'kb_mp_seller_category_tracking',
                            pSQL($where)
                        );
                    }
                }
            }
        }

        $deleted_categories = array_diff($prev_categories, $new_categories);

        if (count($deleted_categories) > 0) {
            $sql = 'Select id_product from ' . _DB_PREFIX_ . 'kb_mp_seller_product 
				where id_seller = ' . (int)$id_seller;
            $products = DB::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
            if (count($products) > 0) {
                foreach ($deleted_categories as $id_cat) {
                    foreach ($products as $prod) {
                        $sql = 'Select id_product from ' . _DB_PREFIX_ . 'category_product 
							where id_product = ' . (int)$prod['id_product'] . ' AND id_category = ' . (int)$id_cat;
                        if ((bool)DB::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql)) {
                            $p_obj = new Product($prod['id_product']);
                            Db::getInstance(_PS_USE_SQL_SLAVE_)->insert(
                                'kb_mp_seller_category_tracking',
                                array(
                                    'id_seller' => (int)$id_seller,
                                    'id_category' => (int)$id_cat,
                                    'id_product' => (int)$prod['id_product'],
                                    'date_add' => pSQL(date('Y-m-d H:i:s'))
                                )
                            );
                            $p_obj->deleteCategory($id_cat);
                        }
                    }
                }
            }
        }
    }
}
