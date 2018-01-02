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

require_once 'KbSellerCategory.php';

class KbSellerSetting extends ObjectModel
{
    public $id;
    public $id_seller;
    public $id_shop;
    public $key;
    public $value;
    public $use_global;
    public $date_add;
    public $date_upd;
    public $setting = array();

    const TABLE = 'kb_mp_seller_config';

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'kb_mp_seller_config',
        'primary' => 'id_seller_config',
        'fields' => array(
            'id_seller' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId',
                'required' => true, 'copy_post' => false),
            'id_shop' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId',
                'required' => true, 'copy_post' => false),
            'key' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true),
            'value' => array('type' => self::TYPE_STRING, 'required' => true),
            'use_global' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName',
                'values' => array('0', '1')),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
        )
    );

    public function __construct($id_seller = null, $use_default_load = false, $id = null)
    {
        if (!$use_default_load) {
            $this->id_seller = (int)$id_seller;
            if ($this->id_seller > 0) {
                $sql = 'Select * from ' . _DB_PREFIX_ . pSQL(self::TABLE) . ' where id_seller = ' . (int)$this->id_seller;
                $results = DB::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
                if (count($results) > 0) {
                    $this->setting = self::cleanSettings($results);
                    $this->id_shop = $results[0]['id_shop'];
                }
            }
        } else {
            parent::__construct($id);
        }
    }

    public static function getSellerDefaultSetting($key = null)
    {
        $settings = array(
            'kbmp_default_commission' => array('main' => 15, 'global' => 1),
            'kbmp_product_limit' => array('main' => 20, 'global' => 0),
            'kbmp_new_product_approval_required' => array('main' => 1, 'global' => 1),
            'kbmp_enable_seller_review' => array('main' => 1, 'global' => 1),
            'kbmp_seller_review_approval_required' => array('main' => 1, 'global' => 1),
            'kbmp_email_on_new_order' => array('main' => 1, 'global' => 1)
        );

        if (!empty($key)) {
            return $settings[$key];
        } else {
            return $settings;
        }
    }

    public static function settingFromGlobalWoutGlobal()
    {
        return array('kbmp_product_limit');
    }

    public function saveSettings()
    {
        $sql = 'delete from ' . _DB_PREFIX_ . pSQL(self::TABLE) . ' where id_seller = ' . (int)$this->id_seller;
        if (DB::getInstance(_PS_USE_SQL_SLAVE_)->execute($sql)) {
            foreach ($this->setting as $key => $set) {
                $setting = new KbSellerSetting(null, true);
                $setting->id_seller = $this->id_seller;
                $setting->id_shop = $this->id_shop;
                $setting->key = $key;
                if (isset($set['main'])) {
                    $setting->value = $set['main'];
                } else {
                    $setting->value = $set['global'];
                }
                if (isset($set['global']) && $set['global'] == 1) {
                    $setting->use_global = '1';
                } else {
                    $setting->use_global = '0';
                }

                if ($key == 'kbmp_product_limit') {
                    $setting->use_global = '0';
                }

                $setting->save();
            }
        } else {
            return false;
        }
    }

    public static function saveSettingForNewSeller($seller_obj)
    {
        $settings = self::getSellerDefaultSetting();

        if (!Configuration::get('KB_MARKETPLACE_CONFIG') || Configuration::get('KB_MARKETPLACE_CONFIG') == '') {
            $global_settings = KbGLobal::getDefaultSettings();
        } else {
            $global_settings = Tools::unserialize(Configuration::get('KB_MARKETPLACE_CONFIG'));
        }

        $sql = 'delete from ' . _DB_PREFIX_ . pSQL(self::TABLE) . ' where id_seller = ' . (int)$seller_obj->id;
        if (DB::getInstance(_PS_USE_SQL_SLAVE_)->execute($sql)) {
            foreach ($settings as $key => $set) {
                $setting = new KbSellerSetting(null, true);
                $setting->id_seller = $seller_obj->id;
                $setting->id_shop = $seller_obj->id_shop;
                $setting->key = $key;
                $setting->value = $set['main'];
                if (isset($set['global']) && $set['global'] == 1) {
                    $setting->use_global = '1';
                    if (isset($global_settings[$key])) {
                        $setting->value = $global_settings[$key];
                    }
                } else {
                    $setting->use_global = '0';
                    if (in_array($key, self::settingFromGlobalWoutGlobal())) {
                        if (isset($global_settings[$key])) {
                            $setting->value = $global_settings[$key];
                        }
                    }
                }

                $setting->save();
            }
        }
    }

    public static function cleanSettings($data = array())
    {
        $tmp = array();
        foreach ($data as $set) {
            $values = $set['value'];

            $tmp[$set['key']] = array(
                'main' => $values,
                'global' => $set['use_global']
            );
        }
        return $tmp;
    }

    public function setSettings($settings = array())
    {
        foreach ($settings as $key => $val) {
            if (isset($val['global']) && $val['global'] == 1) {
                $settings[$key]['global'] = '1';
            } else {
                $settings[$key]['global'] = '0';
            }

            if ($key == 'kbmp_product_limit') {
                $settings[$key]['global'] = '0';
            }
        }
        $this->setting = $settings;
    }

    public function setShop($id_shop = null)
    {
        $this->id_shop = $id_shop;
    }

    public static function getRowBySellerAndKey($id_seller, $key)
    {
        $sql = 'Select s.* from ' . _DB_PREFIX_ . 'kb_mp_seller_config as s 
			WHERE s.id_seller = ' . (int)$id_seller . ' AND s.key = "' . pSQL($key) . '"';

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
    }

    public function getSettings()
    {
        foreach (self::getSellerDefaultSetting() as $key => $setting) {
            if (!isset($this->setting[$key])) {
                $this->setting[$key] = $setting;
            }
        }

        return $this->setting;
    }

    public static function getSellerSettingByKey($id_seller, $key, $id_shop = null)
    {
        $sql = 'Select * from ' . _DB_PREFIX_ . pSQL(self::TABLE)
            . ' as s Where s.id_seller = ' . (int)$id_seller
            . ' AND s.key = "' . pSQL($key) . '"';

        if (!empty($id_shop)) {
            $sql .= ' AND s.id_shop = ' . (int)$id_shop;
        }

        $row = DB::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);

        if (!$row || empty($row)) {
            $row = self::getSellerDefaultSetting($key);
        } else {
            if ($row['key'] == 'kbmp_product_limit') {
                $row = array('main' => $row['value'], 'global' => 0);
            } else {
                $row = array('main' => $row['value'], 'global' => $row['use_global']);
            }
        }


        $value = null;
        if ($row['global'] == 1) {
            $global_settings = Tools::unserialize(Configuration::get('KB_MARKETPLACE_CONFIG'));
            if ($global_settings && count($global_settings) > 0 && isset($global_settings[$key])) {
                if ($key != 'kbmp_allowed_categories') {
                    $value = $global_settings[$key];
                }
            } else {
                $tmp = self::getSellerDefaultSetting($key);
                if ($key != 'kbmp_allowed_categories') {
                    $value = $tmp['main'];
                }
            }
        } else {
            if ($key != 'kbmp_allowed_categories') {
                $value = $row['main'];
            }
        }
        return $value;
    }

    public static function assignCategoryGlobalToSeller($seller)
    {
        $settings = Tools::unserialize(Configuration::get('KB_MARKETPLACE_CONFIG'));
        $categories_to_be_map = array();
        if (!empty($settings['kbmp_allowed_categories'])) {
            $categories_to_be_map = (array)$settings['kbmp_allowed_categories'];
        } else {
            $sql = 'select c.id_category from ' . _DB_PREFIX_ . 'category c 
				INNER JOIN ' . _DB_PREFIX_ . 'category_shop cs on (c.id_category = cs.id_category) 
				WHERE cs.id_shop = ' . (int)$seller->id_shop;
            if ($cats = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql)) {
                foreach ($cats as $c) {
                    $categories_to_be_map[] = $c['id_category'];
                }
            }
        }

        if (is_array($categories_to_be_map) && count($categories_to_be_map) > 0) {
            foreach ($categories_to_be_map as $id_category) {
                $row_id = (int)KbSellerCategory::getRowIdBySellerAndCategory($seller->id, $id_category);

                $seller_cat = new KbSellerCategory($row_id);
                $seller_cat->id_seller = (int)$seller->id;
                $seller_cat->id_shop = (int)$seller->id_shop;
                $seller_cat->id_category = $id_category;
                $seller_cat->save();
            }
        }
    }

    public static function assignCategoryToSeller($seller, $categories)
    {
        if (is_array($categories) && count($categories) > 0) {
            KbSellerCategory::deleteAllBySeller($seller->id);
            foreach ($categories as $id_category) {
                $row_id = (int)KbSellerCategory::getRowIdBySellerAndCategory($seller->id, $id_category);

                $seller_cat = new KbSellerCategory($row_id);
                $seller_cat->id_seller = (int)$seller->id;
                $seller_cat->id_shop = (int)$seller->id_shop;
                $seller_cat->id_category = $id_category;
                $seller_cat->save();
            }
        }
    }
}
