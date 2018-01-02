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

class Carrier extends CarrierCore
{
    /*
    * module: kbmarketplace
    * date: 2016-10-01 15:18:49
    * version: 1.0.4
    */
    public static function getAvailableCarrierList(
        Product $product,
        $id_warehouse,
        $id_address_delivery = null,
        $id_shop = null,
        $cart = null,
        &$error = array()
    ) {
        static $ps_country_default = null;
        if ($ps_country_default === null) {
            $ps_country_default = Configuration::get('PS_COUNTRY_DEFAULT');
        }
        if (is_null($id_shop)) {
            $id_shop = Context::getContext()->shop->id;
        }
        if (is_null($cart)) {
            $cart = Context::getContext()->cart;
        }
        if (is_null($error) || !is_array($error)) {
            $error = array();
        }
        $id_address = (int)((!is_null($id_address_delivery) && $id_address_delivery != 0)
            ? $id_address_delivery
            :  $cart->id_address_delivery);
        if ($id_address) {
            $id_zone = Address::getZoneById($id_address);
            if (!Address::isCountryActiveById($id_address)) {
                return array();
            }
        } else {
            $country = new Country($ps_country_default);
            $id_zone = $country->id_zone;
        }
        $cache_id = 'Carrier::getAvailableCarrierList_'.(int)$product->id.'-'.(int)$id_shop;
        if (!Cache::isStored($cache_id)) {
            $query = new DbQuery();
            $query->select('id_carrier');
            $query->from('product_carrier', 'pc');
            $query->innerJoin(
                'carrier',
                'c',
                'c.id_reference = pc.id_carrier_reference AND c.deleted = 0 AND c.active = 1'
            );
            $query->where('pc.id_product = '.(int)$product->id);
            $query->where('pc.id_shop = '.(int)$id_shop);
            $carriers_for_product = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
            Cache::store($cache_id, $carriers_for_product);
        } else {
            $carriers_for_product = Cache::retrieve($cache_id);
        }

        $context = Context::getContext();
        if (empty($carriers_for_product)) {
            if (isset($context->cookie->kbsellerhandleorder)
            && $context->cookie->kbsellerhandleorder == 1) {
                $carriers_for_product = KbSellerShipping::getSimpleCarriersForProduct($product);
            } else {
                $carriers_for_product = KbSellerShipping::getSimpleCarriersForProduct($product, true);
            }
        }

        $carrier_list = array();
        if (!empty($carriers_for_product)) {
            foreach ($carriers_for_product as $carrier) { //check if the linked carriers are available in current zone
                if (Carrier::checkCarrierZone($carrier['id_carrier'], $id_zone)) {
                    $carrier_list[$carrier['id_carrier']] = $carrier['id_carrier'];
                }
            }
            if (empty($carrier_list)) {
                return array();
            }//no linked carrier are available for this zone
        }
        if ($id_warehouse) {
            $warehouse = new Warehouse($id_warehouse);
            $warehouse_carrier_list = $warehouse->getCarriers();
        }
        $available_carrier_list = array();
        $cache_id = 'Carrier::getAvailableCarrierList_getCarriersForOrder_'.(int)$id_zone.'-'.(int)$cart->id;
        if (!Cache::isStored($cache_id)) {
            $customer = new Customer($cart->id_customer);
            $carrier_error = array();
            $carriers = Carrier::getCarriersForOrder($id_zone, $customer->getGroups(), $cart, $carrier_error);
            Cache::store($cache_id, array($carriers, $carrier_error));
        } else {
            list($carriers, $carrier_error) = Cache::retrieve($cache_id);
        }
        $error = array_merge($error, $carrier_error);
        foreach ($carriers as $carrier) {
            $available_carrier_list[$carrier['id_carrier']] = $carrier['id_carrier'];
        }
        if ($carrier_list) {
            $carrier_list = array_intersect($available_carrier_list, $carrier_list);
        } else {
            $carrier_list = $available_carrier_list;
        }
        if (isset($warehouse_carrier_list)) {
            $carrier_list = array_intersect($carrier_list, $warehouse_carrier_list);
        }
        $cart_quantity = 0;
        $cart_weight = 0;
        foreach ($cart->getProducts(false, false) as $cart_product) {
            if ($cart_product['id_product'] == $product->id) {
                $cart_quantity += $cart_product['cart_quantity'];
            }
            if ($cart_product['weight_attribute'] > 0) {
                $cart_weight += ($cart_product['weight_attribute'] * $cart_product['cart_quantity']);
            } else {
                $cart_weight += ($cart_product['weight'] * $cart_product['cart_quantity']);
            }
        }
        if ($product->width > 0
            || $product->height > 0
            || $product->depth > 0
            || $product->weight > 0
            || $cart_weight > 0
        ) {
            foreach ($carrier_list as $key => $id_carrier) {
                $carrier = new Carrier($id_carrier);
                $carrier_sizes = array((int)$carrier->max_width, (int)$carrier->max_height, (int)$carrier->max_depth);
                $product_sizes = array((int)$product->width, (int)$product->height, (int)$product->depth);
                rsort($carrier_sizes, SORT_NUMERIC);
                rsort($product_sizes, SORT_NUMERIC);
                if (($carrier_sizes[0] > 0 && $carrier_sizes[0] < $product_sizes[0])
                    || ($carrier_sizes[1] > 0 && $carrier_sizes[1] < $product_sizes[1])
                    || ($carrier_sizes[2] > 0 && $carrier_sizes[2] < $product_sizes[2])) {
                    $error[$carrier->id] = Carrier::SHIPPING_SIZE_EXCEPTION;
                    unset($carrier_list[$key]);
                }
                if ($carrier->max_weight > 0
                    && ($carrier->max_weight < $product->weight * $cart_quantity
                    || $carrier->max_weight < $cart_weight)
                ) {
                    $error[$carrier->id] = Carrier::SHIPPING_WEIGHT_EXCEPTION;
                    unset($carrier_list[$key]);
                }
            }
        }
        return $carrier_list;
    }
}
