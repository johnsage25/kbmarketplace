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

class KbSellerMenu extends ObjectModel
{
    public $id;
    public $module_name;
    public $controller_name;
    public $position;
    public $icon;
    public $css_class;
    public $show_badge;
    public $badge_class;
    public $label;
    public $title;
    public $date_add;
    public $date_upd;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'kb_mp_seller_menu',
        'primary' => 'id_seller_menu',
        'multilang' => true,
        'fields' => array(
            'module_name' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true),
            'controller_name' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true),
            'position' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'icon' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName'),
            'css_class' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName'),
            'show_badge' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'default' => true),
            'badge_class' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName'),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
            /* Lang fields */
            'label' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName',
                'required' => true),
            'title' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName')
        )
    );

    protected $webserviceParameters = array(
        'objectsNodeName' => 'kbsellermenus',
        'objectNodeName' => 'kbsellermenu'
    );

    public function __construct($id = null, $id_lang = null)
    {
        parent::__construct($id, $id_lang);
    }

    public static function getAllMenus($id_lang)
    {
        $sql = 'Select sm.*, sml.label, sml.title from ' . _DB_PREFIX_ . 'kb_mp_seller_menu as sm 
			LEFT JOIN ' . _DB_PREFIX_ . 'kb_mp_seller_menu_lang as sml on (sm.id_seller_menu = sml.id_seller_menu) 
			WHERE sml.id_lang = ' . (int)$id_lang;

        $results = DB::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        if (count($results) > 0) {
            $columns = array();
            foreach ($results as $key => $row) {
                $columns[$key] = $row['position'];
            }

            array_multisort($columns, SORT_ASC, $results);

            return $results;
        }

        return array();
    }

    public static function getMenusByModule($module_name, $id_lang)
    {
        $sql = 'Select sm.*, sml.label, sml.title from ' . _DB_PREFIX_ . 'kb_mp_seller_menu as sm 
			LEFT JOIN ' . _DB_PREFIX_ . 'kb_mp_seller_menu_lang as sml on (sm.id_seller_menu = sml.id_seller_menu) 
			WHERE sm.module_name = "' . pSQL($module_name) . '" AND sml.id_lang = ' . (int)$id_lang;

        return DB::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    }

    public static function getMenuIdByModuleAndController($module_name, $controller_name)
    {
        $sql = 'Select id_seller_menu from ' . _DB_PREFIX_ . 'kb_mp_seller_menu 
			WHERE module_name = "' . pSQL($module_name) . '" AND controller_name = "' . pSQL($controller_name) . '"';

        return DB::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
    }

    public static function getMenusByModuleAndController($module_name, $controller_name, $id_lang)
    {
        $sql = 'Select sm.*, sml.label, sml.title from ' . _DB_PREFIX_ . 'kb_mp_seller_menu as sm 
			LEFT JOIN ' . _DB_PREFIX_ . 'kb_mp_seller_menu_lang as sml on (sm.id_seller_menu = sml.id_seller_menu) 
			WHERE sm.module_name = "' . pSQL($module_name) . '"
			 AND sm.controller_name = "' . pSQL($controller_name) . '" AND sml.id_lang = ' . (int)$id_lang;

        return DB::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
    }

    public static function getNextPosition()
    {
        $sql = 'Select (MAX(position) + 1) as next from ' . _DB_PREFIX_ . 'kb_mp_seller_menu';

        return DB::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
    }
}
