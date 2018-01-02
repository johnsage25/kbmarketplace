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

class KbSellerShipping extends ObjectModel
{
    public $id;
    public $id_seller;
    public $id_carrier;
    public $id_reference;
    public $is_default_shipping;
    public $date_add;
    public $date_upd;

    const DEFAULT_SHIPPING_NAME = 'Default Free Shipping';

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'kb_mp_seller_shipping',
        'primary' => 'id_seller_shipping',
        'fields' => array(
            'id_seller' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
            'id_carrier' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
            'id_reference' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
            'is_default_shipping' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'default' => 0),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false)
        )
    );

    protected $webserviceParameters = array(
        'objectsNodeName' => 'kbsellershippings',
        'objectNodeName' => 'kbsellershipping',
        'fields' => array(
            'id_seller' => array('xlink_resource' => 'kbsellers'),
            'id_carrier' => array('xlink_resource' => 'carriers')
        )
    );

    public function __construct($id = null)
    {
        parent::__construct($id);
    }

    public function createAndAssignFreeShipping($seller_obj)
    {
        $seller_info = $seller_obj->getSellerInfo();
        $languages = Language::getLanguages();
        $carrier = new Carrier();
        $definition = ObjectModel::getDefinition($carrier);
        $carrier->name = self::DEFAULT_SHIPPING_NAME . ' - ' . $seller_info['seller_name'];
        foreach ($languages as $lang) {
            $carrier->delay[$lang['id_lang']] = 'Deliver in minimum time';
        }
        $carrier->active = 1;
        $carrier->deleted = 0;
        $carrier->shipping_handling = 0;
        $carrier->shipping_external = 0;
        $carrier->range_behavior = 0;
        $carrier->is_module = 0;
        $carrier->is_free = 1;
        $carrier->shipping_method = Carrier::SHIPPING_METHOD_PRICE;
        if ($carrier->add()) {
            self::mapWithPaymentModules($carrier->id);
            $this->id_seller = $seller_obj->id;
            $this->id_carrier = $carrier->id;
            $this->id_reference = $carrier->id;
            $this->is_default_shipping = 1;
            $this->save();

            $carrier->setGroups(self::getCustomerGroups());
            $zones = Zone::getZones(false);
            foreach ($zones as $zone) {
                $carrier->addZone((int)$zone['id_zone']);
            }

            $shop_mapping = array(
                array(
                    $definition['primary'] => (int)$carrier->id,
                    'id_shop' => (int)$seller_obj->id_shop
                )
            );

            Db::getInstance()->insert(pSQL($definition['table']) . '_shop', $shop_mapping, false, true, (int) Db::INSERT_IGNORE);
            $carrier->setTaxRulesGroup(0);
        }
    }

    public static function getCustomerGroups()
    {
        $groups = Group::getGroups(Context::getContext()->language->id);
        $arr = array();
        foreach ($groups as $g) {
            $arr[] = $g['id_group'];
        }
        return $arr;
    }

    public function getMenuBadgeHtml($id_seller)
    {
        return self::getSellerShippings($id_seller, Context::getContext()->language->id, true);
    }

    public static function getSellerShippings(
        $id_seller,
        $id_lang,
        $only_count = false,
        $start = null,
        $limit = null,
        $orderby = null,
        $orderway = null,
        $custom_filter = ''
    ) {
        $context = new Context();
        $sql = 'SELECT {{COLUMN}} from ' . _DB_PREFIX_ . 'carrier as c 
            INNER JOIN ' . _DB_PREFIX_ . 'carrier_lang as cl 
            on (c.id_carrier = cl.id_carrier AND cl.id_lang = ' . (int)$id_lang .
            ' AND cl.id_shop='.(int) $context->getContext()->cart->id_shop.') 
            INNER JOIN ' . _DB_PREFIX_ . 'kb_mp_seller_shipping as s on (c.id_carrier = s.id_carrier)
            where s.id_seller = ' . (int)$id_seller . ' AND c.deleted = 0';

        if (!empty($custom_filter)) {
            $sql .= $custom_filter;
        }
        if ($only_count) {
            $sql = str_replace('{{COLUMN}}', 'COUNT(*) as total', $sql);
            return DB::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
        } else {
            $sql = str_replace('{{COLUMN}}', 'c.*, cl.delay, s.id_seller, s.is_default_shipping', $sql);

            if ($orderby != null && $orderway != null) {
                $sql .= ' ORDER BY ' . pSQL($orderby) . ' ' . pSQL($orderway);
            } else {
                $sql .= ' ORDER BY c.id_carrier DESC';
            }

            if ((int)$start >= 0 && (int)$limit > 0) {
                $sql .= ' LIMIT ' . (int)$start . ', ' . (int)$limit;
            }

            return DB::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        }
    }

    public static function getShippingForProducts(
        $id_lang,
        $id_seller = 0,
        $only_system_shipping = false,
        $active = false,
        $delete = false,
        $id_zone = false,
        $modules_filters = Carrier::PS_CARRIERS_ONLY,
        $for_admin = false
    ) {
        $sql = '
		SELECT c.*, cl.delay, s.id_seller 
		FROM `' . _DB_PREFIX_ . 'carrier` c '.(($only_system_shipping === true) ? 'LEFT JOIN ' : 'INNER JOIN ')
        . _DB_PREFIX_ . 'kb_mp_seller_shipping as s 
        on (c.id_carrier = s.id_carrier'
        .((!$for_admin) ? ' AND s.is_default_shipping = 0 ' : '')
        .(((int)$id_seller > 0) ? ' AND s.id_seller = '.(int)$id_seller : '').') 
		LEFT JOIN `' . _DB_PREFIX_ . 'carrier_lang` cl 
        ON (c.`id_carrier` = cl.`id_carrier` 
        AND cl.`id_lang` = ' . (int)$id_lang . Shop::addSqlRestrictionOnLang('cl') . ') 
		LEFT JOIN `' . _DB_PREFIX_ . 'carrier_zone` cz ON (cz.`id_carrier` = c.`id_carrier`)'
        . ($id_zone ? 'LEFT JOIN `' . _DB_PREFIX_ . 'zone` z ON (z.`id_zone` = ' . (int)$id_zone . ')' : '') . '
		' . Shop::addSqlAssociation('carrier', 'c') . '
		WHERE c.`deleted` = ' . ($delete ? '1' : '0')
        .(($only_system_shipping === true) ? ' AND s.id_seller IS NULL' : '');
        if ($active) {
            $sql .= ' AND c.`active` = 1 ';
        }
        if ($id_zone) {
            $sql .= ' AND cz.`id_zone` = ' . (int)$id_zone . ' AND z.`active` = 1 ';
        }

        switch ($modules_filters) {
            case 1:
                $sql .= ' AND c.is_module = 0 ';
                break;
            case 2:
                $sql .= ' AND c.is_module = 1 ';
                break;
            case 3:
                $sql .= ' AND c.is_module = 1 AND c.need_range = 1 ';
                break;
            case 4:
                $sql .= ' AND (c.is_module = 0 OR c.need_range = 1) ';
                break;
        }
        $sql .= ' GROUP BY c.`id_carrier` ORDER BY c.`position` ASC';

        $cache_id = 'Carrier::getCarriers_' . md5($sql);
        if (!Cache::isStored($cache_id)) {
            $carriers = Db::getInstance()->executeS($sql);
            Cache::store($cache_id, $carriers);
        } else {
            $carriers = Cache::retrieve($cache_id);
        }

        foreach ($carriers as $key => $carrier) {
            if ($carrier['name'] == '0') {
                $carriers[$key]['name'] = str_replace(array('#', ';'), '', Configuration::get('PS_SHOP_NAME'));
            }
        }
        return $carriers;
    }

    public static function isAssociateWidSeller($id_reference)
    {
        return (bool)DB::getInstance(_PS_USE_SQL_SLAVE_)->getValue(
            'Select * from ' . _DB_PREFIX_ . 'kb_mp_seller_shipping 
            WHERE id_reference = ' . (int)$id_reference
        );
    }

    public static function isSellerShipping($id_seller, $id_carrier)
    {
        return (bool)DB::getInstance(_PS_USE_SQL_SLAVE_)->getValue(
            'Select * from ' . _DB_PREFIX_ . 'kb_mp_seller_shipping 
            WHERE id_seller = ' . (int)$id_seller . ' AND id_carrier = ' . (int)$id_carrier
        );
    }

    public static function isSellerShippingByReference($id_seller, $id_reference)
    {
        return (bool)DB::getInstance(_PS_USE_SQL_SLAVE_)->getValue(
            'Select * from ' . _DB_PREFIX_ . 'kb_mp_seller_shipping 
            WHERE id_seller = ' . (int)$id_seller . ' AND id_reference = ' . (int)$id_reference
        );
    }

    public static function getIdByReference($id_reference, $id_seller = 0)
    {
        $sql = 'Select id_seller_shipping from ' . _DB_PREFIX_ . 'kb_mp_seller_shipping 
            WHERE id_reference = ' . (int)$id_reference;
        if ((int)$id_seller > 0) {
            $sql .= ' AND id_seller = '.(int)$id_seller;
        }
        return DB::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
    }

    public static function getDefaultShippingId($id_seller)
    {
        return DB::getInstance(_PS_USE_SQL_SLAVE_)->getValue(
            'Select id_reference from ' . _DB_PREFIX_ . 'kb_mp_seller_shipping 
            WHERE id_seller = ' . (int)$id_seller . ' AND is_default_shipping = 1'
        );
    }

    public static function getSellerDetailByShippingId($id_carrier)
    {
        $sql = 'Select CONCAT(cus.`firstname`, \' \', cus.`lastname`) 
			AS `seller_name`, cus.`email`, s.*, sl.* 
			FROM ' . _DB_PREFIX_ . 'kb_mp_seller_shipping as sc 
            INNER JOIN ' . _DB_PREFIX_ . 'kb_mp_seller as s on (sc.id_seller = s.id_seller) 
			INNER JOIN ' . _DB_PREFIX_ . 'customer cus 
			ON (s.`id_customer` = cus.`id_customer`) 
			INNER JOIN ' . _DB_PREFIX_ . 'kb_mp_seller_lang as sl 
			on (s.id_seller = sl.id_seller AND sl.id_lang = s.id_default_lang) 
			WHERE sc.id_carrier = ' . (int)$id_carrier;

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
        if ($result) {
            $result['title'] = (!empty($result['title'])) ? $result['title'] : 'Not Mentioned';
            return $result;
        } else {
            return array();
        }
    }
    
    public static function mapWithPaymentModules($id_reference)
    {   
        $hook_payment = 'Payment';
        if (Db::getInstance()->getValue('SELECT `id_hook` FROM `'._DB_PREFIX_.'hook` WHERE `name` = \'paymentOptions\'')) {
            $hook_payment = 'paymentOptions';
        }

        $list = Shop::getContextListShopID();
        
        $sql = 'SELECT DISTINCT m.`id_module`, h.`id_hook`, m.`name`, hm.`position`
        FROM `'._DB_PREFIX_.'module` m 
        LEFT JOIN `'._DB_PREFIX_.'hook_module` hm ON hm.`id_module` = m.`id_module`
        LEFT JOIN `'._DB_PREFIX_.'hook` h ON hm.`id_hook` = h.`id_hook`
        WHERE h.`name` = \''.pSQL($hook_payment).'\' 
        AND (SELECT COUNT(*) FROM '._DB_PREFIX_.'module_shop ms WHERE ms.id_module = m.id_module AND ms.id_shop IN('.implode(', ', (int) $list).')) = '.count( (int) $list).'
        AND hm.id_shop IN('.implode(', ', (int) $list).') 
        GROUP BY hm.id_hook, hm.id_module
        ORDER BY hm.`position`, m.`name` DESC';
        
        $payment_modules = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        
        if ($payment_modules && count($payment_modules) > 0) {
            $shopId = Context::getContext()->shop->id;
            foreach ($payment_modules as $module) {
                $sql = 'Select COUNT(*) as total from '._DB_PREFIX_.'module_'.bqSQL('carrier').' 
                    where id_module = '.(int)$module['id_module']
                    .' AND id_shop = '.(int)$shopId.' AND id_reference = '.(int)$id_reference;
                if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql)) {
                    Db::getInstance(_PS_USE_SQL_SLAVE_)->execute('
                        INSERT INTO `'._DB_PREFIX_.'module_'.bqSQL('carrier').'`
                        (`id_module`, `id_shop`, `id_'.bqSQL('reference').'`)
                        VALUES ('.(int) $module['id_module'].','.(int) $shopId.','.(int) $id_reference.')'
                    );
                }        
            }
        }
        
        
    }
    
    public static function getSimpleCarriersForProduct($product, $only_admin = false)
    {
        $carriers = array();
        
        if ($only_admin) {
            $sql = '
                SELECT c.id_carrier FROM `' . _DB_PREFIX_ . 'carrier` c 
                LEFT JOIN '. _DB_PREFIX_ . 'kb_mp_seller_shipping as s 
                on (c.id_carrier = s.id_carrier)
                WHERE c.`deleted` = 0 AND c.`active` = 1 AND s.id_seller IS NULL';
            $carriers = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
            return $carriers;
        }
        
        if (!is_object($product)) {
            if (is_array($product) && isset($product['id_product'])) {
                $product = new Product($product['id_product']);
            } elseif ((int)$product) {
                $product = new Product($product);
            } else {
                return array();
            }
        }
        $context = Context::getContext();
        $seller = KbSellerProduct::getSellerByProductId($product->id);
        $product_carriers = array();
        if (!empty($seller) && $seller['id_seller'] > 0) {
            $product_carriers = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
                SELECT c.*
                FROM `'._DB_PREFIX_.'product_carrier` pc
                INNER JOIN `'._DB_PREFIX_.'carrier` c
                    ON (c.`id_reference` = pc.`id_carrier_reference` AND c.`active` = 1 AND c.`deleted` = 0)
                WHERE pc.`id_product` = '.(int)$product->id.'
                    AND pc.`id_shop` = '.(int)$context->shop->id);
            
            if (!empty($product_carriers)) {
                $carriers = $product_carriers;
            } else {
                $sql = '
                    SELECT c.id_carrier FROM `' . _DB_PREFIX_ . 'carrier` c 
                    INNER JOIN '. _DB_PREFIX_ . 'kb_mp_seller_shipping as s 
                    on (c.id_carrier = s.id_carrier AND s.is_default_shipping = 0 AND s.id_seller = '.(int)$seller['id_seller'].')
                    WHERE c.`deleted` = 0 AND c.`active` = 1';

                $carriers = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
                if (empty($carriers)) {
                    $carriers = array(
                        array('id_carrier' => self::getDefaultShippingId($seller['id_seller']))
                    );
                } 
            }
        } else {
            $sql = '
                SELECT c.id_carrier FROM `' . _DB_PREFIX_ . 'carrier` c 
                LEFT JOIN '. _DB_PREFIX_ . 'kb_mp_seller_shipping as s 
                on (c.id_carrier = s.id_carrier)
                WHERE c.`deleted` = 0 AND c.`active` = 1 AND s.id_seller IS NULL';
            $carriers = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        }
        return $carriers;
    }
}
