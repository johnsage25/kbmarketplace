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

class KbPerProductSellerShipping extends ObjectModel
{
     public static function issellersProductsInCart()
    {
        $products = Context::getContext()->cart->getProducts();
        if ($products) {
            foreach ($products as $product) {
                $check_product_query = 'SELECT COUNT(*) as row from ' . _DB_PREFIX_ . 'kb_mp_seller_product 
                                where id_product = ' . (int) $product['id_product'];
                if ((bool) DB::getInstance(_PS_USE_SQL_SLAVE_)->getValue($check_product_query)) {
                    return true;
                }
            }
        }
        return false;
    }


    public static function getCartProductsWithCarriers()
    {
        $avilable_carriers_per_product = array();
        $lang_id = Context::getContext()->language->id;
        $shop_id = Context::getContext()->shop->id_shop_group;
        if (isset(Context::getContext()->cart->id_address_delivery) && Context::getContext()->cart->id_address_delivery) {
            $id_zone = Address::getZoneById((int)Context::getContext()->cart->id_address_delivery);
        } else {
            $id_zone = false;
        }
        
        foreach (Context::getContext()->cart->getProducts() as $key => $product) {
            if (isset($product['id_address_delivery'])) {
                $avilable_carriers_per_product[$product['id_product']]['product'] = array('cart_quantity' => $product['cart_quantity'], 'id_address_delivery' => $product['id_address_delivery']);
            } else {
                $avilable_carriers_per_product[$product['id_product']]['product'] = array('cart_quantity' => $product['cart_quantity']);
            }
            $shippings = KbPerProductSellerShipping::getCarriersForProduct($product);
            $avilable_carriers_per_product[$product['id_product']]['shipping'] = KbPerProductSellerShipping::replaceKeyWithId($shippings, 'id_carrier');
        }
        return $avilable_carriers_per_product;
    }

    public static function  issellerProduct($id_product)
    {
        $sql = 'SELECT COUNT(*) as row from ' . _DB_PREFIX_ . 'kb_mp_seller_product 
			where id_product = ' . (int) $id_product;
        return (bool) DB::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
    }

    public static function getCarriersByCondition()
    {

        $available_carriers = KbPerProductSellerShipping::getCartProductsWithCarriers();
        // Get id zone
        if (isset(Context::getContext()->cart->id_address_delivery) // Be carefull, id_address_delivery is not usefull one 1.5
            && Context::getContext()->cart->id_address_delivery
            && Customer::customerHasAddress(Context::getContext()->cart->id_customer, Context::getContext()->cart->id_address_delivery
        )) {
            $id_zone = Address::getZoneById((int)Context::getContext()->cart->id_address_delivery);
        } else {
            $default_country = Context::getContext()->country;
            if (!Validate::isLoadedObject($default_country)) {
                $default_country = new Country(Configuration::get('PS_COUNTRY_DEFAULT'), Configuration::get('PS_LANG_DEFAULT'));
            }
            $id_zone = (int)$default_country->id_zone;
        }

        $selected_carrier = array();
        foreach ($available_carriers as $pid => $carriers) {
            $product = new Product((int)$pid);
            if ($product->getType() == Product::PTYPE_VIRTUAL) {
                unset($available_carriers[$pid]);
                continue;
            }
            foreach($carriers['shipping'] as $key => $carrier) {
                if (isset(Context::getContext()->cookie->kb_selected_carrier)) {
                    $selected_carrier = Tools::unSerialize(Context::getContext()->cookie->kb_selected_carrier);
                    if (isset($selected_carrier[$pid]) && $carrier['id_carrier'] == $selected_carrier[$pid]) {
                        $available_carriers[$pid]['shipping'][$key]['selected_carrier'] = 1;
                    }
                } else {
                    if (isset($carrier['is_default_carrier']) && $carrier['is_default_carrier'] == 1) {
                        $selected_carrier[$pid] = $carrier['id_carrier'];
                        $available_carriers[$pid]['shipping'][$key]['selected_carrier'] = 1;
                    }
                }
                if ($carrier['is_free'] == 1) {
                    $available_carriers[$pid]['shipping'][$key]['is_price'] = 0;
                } else {
                    $carrier_obj = new Carrier($carrier['id_carrier']);
                    $shipping_method = $carrier_obj->getShippingMethod();
                    // Get only carriers that are compliant with shipping method
                    if (($shipping_method == Carrier::SHIPPING_METHOD_WEIGHT && $carrier_obj->getMaxDeliveryPriceByWeight((int)$id_zone) === false)
                        || ($shipping_method == Carrier::SHIPPING_METHOD_PRICE && $carrier_obj->getMaxDeliveryPriceByPrice((int)$id_zone) === false)) {
                            unset($available_carriers[$pid]['shipping'][$key]);
                            continue;
                    }
                    $total_product_weight = (float)$product->weight * (int)$carriers['product']['cart_quantity'];
                    $total_product_price = (float)$product->price * (int)$carriers['product']['cart_quantity'];
                    
                    if (Configuration::get('PS_TAX_ADDRESS_TYPE') == 'id_address_invoice') {
                        $address_id = (int)Context::getContext()->cart->id_address_delivery;
                    } elseif (isset($carriers['product']['id_address_delivery'])) {
                        $address_id = (int)$carriers['product']['id_address_delivery'];
                    } else {
                        $address_id = null;
                    }
                    if (!Address::addressExists($address_id)) {
                        $address_id = null;
                    }
                     // Select carrier tax
                    if (!Tax::excludeTaxeOption()) {
                        $address = Address::initialize((int)$address_id);

                        if (Configuration::get('PS_ATCP_SHIPWRAP')) {
                            // With PS_ATCP_SHIPWRAP, pre-tax price is deduced
                            // from post tax price, so no $carrier_tax here
                            // even though it sounds weird.
                            $carrier_tax = 0;
                        } else {
                            $carrier_tax = $carrier_obj->getTaxesRate($address);
                        }
                    }
                    
                    // Start with shipping cost at 0
                    $shipping_cost = 0;
                    
                    $configuration = Configuration::getMultiple(array(
                        'PS_SHIPPING_FREE_PRICE',
                        'PS_SHIPPING_HANDLING',
                        'PS_SHIPPING_METHOD',
                        'PS_SHIPPING_FREE_WEIGHT'
                    ));

                     // Free fees
                    $free_fees_price = 0;
                    if (isset($configuration['PS_SHIPPING_FREE_PRICE'])) {
                        $free_fees_price = Tools::convertPrice((float)$configuration['PS_SHIPPING_FREE_PRICE'], Currency::getCurrencyInstance((int)Context::getContext()->cart->id_currency));
                    }

                    if ($total_product_price >= (float)($free_fees_price) && (float)($free_fees_price) > 0) {
                        $shipping_cost = 0;
                        $available_carriers[$pid]['shipping'][$key]['is_price'] = 0;
                        continue;
                    }

                    if (isset($configuration['PS_SHIPPING_FREE_WEIGHT'])
                        && $total_product_weight >= (float)$configuration['PS_SHIPPING_FREE_WEIGHT']
                        && (float)$configuration['PS_SHIPPING_FREE_WEIGHT'] > 0) {
                        $shipping_cost = 0;
                        $available_carriers[$pid]['shipping'][$key]['is_price'] = 0;
                        continue;
                    }
                    // If out-of-range behavior carrier is set on "Desactivate carrier"
                    if ($carrier['range_behavior']) {
                        $check_delivery_price_by_weight = Carrier::checkDeliveryPriceByWeight($carrier['id_carrier'], $total_product_weight, (int)$id_zone);

                        $check_delivery_price_by_price = Carrier::checkDeliveryPriceByPrice($carrier['id_carrier'], $total_product_price, (int)$id_zone, (int)Context::getContext()->cart->id_currency);

                        // Get only carriers that have a range compatible with cart
                        if (($shipping_method == Carrier::SHIPPING_METHOD_WEIGHT && !$check_delivery_price_by_weight)
                        || ($shipping_method == Carrier::SHIPPING_METHOD_PRICE && !$check_delivery_price_by_price)) {
                            unset($available_carriers[$pid]['shipping'][$key]);
                            continue;
                        } else {
                            if ($shipping_method == Carrier::SHIPPING_METHOD_WEIGHT) {
                                $shipping_cost += $carrier_obj->getDeliveryPriceByWeight($total_product_weight, $id_zone);
                            } else { // by price
                                $shipping_cost += $carrier_obj->getDeliveryPriceByPrice($total_product_price, $id_zone, (int)Context::getContext()->cart->id_currency);
                            }
                        }
                    } else {
                        if ($shipping_method == Carrier::SHIPPING_METHOD_WEIGHT) {
                            $shipping_cost += $carrier_obj->getDeliveryPriceByWeight($total_product_weight, (int)$id_zone);
                        } else { // by price
                            $shipping_cost += $carrier_obj->getDeliveryPriceByPrice($total_product_price, (int)$id_zone, (int)Context::getContext()->cart->id_currency);
                        }
                    }                    
                    // Adding handling charges
                    if (isset($configuration['PS_SHIPPING_HANDLING']) && $carrier_obj->shipping_handling) {
                        $shipping_cost += (float)$configuration['PS_SHIPPING_HANDLING'];
                    }

                    // Additional Shipping Cost per product
                    $shipping_cost += $product->additional_shipping_cost * (int)$carriers['product']['cart_quantity'];

                    $shipping_cost = Tools::convertPrice($shipping_cost, Currency::getCurrencyInstance((int)Context::getContext()->cart->id_currency));
                    

//                    if (Configuration::get('PS_ATCP_SHIPWRAP')) {
//                        if (!$use_tax) {
//                            // With PS_ATCP_SHIPWRAP, we deduce the pre-tax price from the post-tax
//                                // price. This is on purpose and required in Germany.
//                                $shipping_cost /= (1 + $this->getAverageProductsTaxRate());
//                        }
//                    } else {
//                        // Apply tax
//                        if ($use_tax && isset($carrier_tax)) {
//                            $shipping_cost *= 1 + ($carrier_tax / 100);
//                        }
//                    }

                    // Apply tax
                    if (isset($carrier_tax)) {
                        $shipping_cost *= 1 + ($carrier_tax / 100);
                    }
                    $shipping_cost = (float)Tools::ps_round((float)$shipping_cost, 2);
                   
                    $available_carriers[$pid]['shipping'][$key]['is_price'] = 1;
                    $available_carriers[$pid]['shipping'][$key]['carrier_price'] = $shipping_cost;
                    $available_carriers[$pid]['shipping'][$key]['display_carrier_price'] = Tools::displayPrice($shipping_cost, (int)Context::getContext()->cart->id_currency);
                }
            }  
        }
        Context::getContext()->cookie->kb_selected_carrier = serialize($selected_carrier);
        return $available_carriers;
    }

    public static function replaceKeyWithId($shipping_array, $index)
    {
        $temp_array = array();
        if (count($shipping_array)) {
            foreach ($shipping_array as $key => $data) {
                $temp_array[$data[$index]] = $shipping_array[$key];
            }
        }
        return $temp_array;
    }

    public static function getTotalPerProductShippingCost()
    {
        $total_carriers = KbPerProductSellerShipping::getCarriersByCondition();
        $total_cost = 0;
        foreach($total_carriers as $pid => $carriers_list) {
            $product = new Product((int)$pid);
            if ($product->is_virtual) {
                continue;
            } else {
                if(isset(Context::getContext()->cookie->kb_selected_carrier)) {
                    $selected_shipping = Tools::unSerialize(Context::getContext()->cookie->kb_selected_carrier);
                }
                foreach ($carriers_list['shipping'] as $carrier) {
                    if (isset($selected_shipping[$pid]) && $carrier['id_carrier'] == $selected_shipping[$pid] && $carrier['is_price'] == 1) {
                        $total_cost += (float)$carrier['carrier_price'];
                    }
                }
            }
        }
        return $total_cost;
    }

    public static function getCarriersForProduct($product)
    {
        $available_carrier = array();
        $lang_id = Context::getContext()->language->id;
        $shop_id = Context::getContext()->shop->id_shop_group;
        if (isset(Context::getContext()->cart->id_address_delivery) && Context::getContext()->cart->id_address_delivery) {
            $id_zone = Address::getZoneById((int)Context::getContext()->cart->id_address_delivery);
        } else {
            $id_zone = false;
        }
        $product_obj = new Product((int)$product['id_product']);
        $selected_carrier = $product_obj->getCarriers();
        if (count($selected_carrier)) {
            foreach($selected_carrier as $key => $carrier) {
                $carrier_obj = new Carrier((int)$carrier['id_carrier']);
                $selected_carrier[$key]['delay'] = $carrier_obj->delay[$lang_id];
            }
            $selected_carrier[0]['is_default_shipping'] = 1;
            return $selected_carrier;
        } else {
            if (KbPerProductSellerShipping::issellerProduct($product['id_product'])) {
                $seller = KbSellerProduct::getSellerByProductId((int) $product['id_product']);
                if ($seller) {
                    $available_carrier = KbSellerShipping::getShippingForProducts($lang_id, $seller['id_seller'], false, true, false, $id_zone, Carrier::PS_CARRIERS_ONLY, true);
                }
            } else {
                $available_carrier = KbSellerShipping::getShippingForProducts($lang_id, 0, true, true, false, $id_zone, Carrier::PS_CARRIERS_ONLY, true);
            }
            if ($available_carrier) {
                $available_carrier[0]['is_default_shipping'] = 1;
            }
            return $available_carrier;
        }
    }

    public static function getSellerPackageList()
    {
        $carriers = KbPerProductSellerShipping::getCarriersByCondition();
        $cart_products = KbPerProductSellerShipping::replaceKeyWithId(Context::getContext()->cart->getProducts(), 'id_product');
        $sellers = KbPerProductSellerShipping::getSellersInCart();
        $selected_carrier = Tools::unSerialize(Context::getContext()->cookie->kb_selected_carrier);
        $seller_package_list = array();
        foreach($sellers as $sid){
            foreach ($selected_carrier as $product_id => $carrier_id) {
                if (KbSellerShipping::isSellerShipping((int)$sid, (int)$carrier_id)) {
                    $seller_package_list[$sid][$carrier_id]['product_list'][] = $cart_products[$product_id];
                    unset($selected_carrier[$product_id]);
                    unset($cart_products[$product_id]);
                }
            }
        }
        if (isset($sellers[0]) && count($selected_carrier)) {
            foreach ($selected_carrier as $product_id => $carrier_id) {
                $seller_package_list[0][$carrier_id]['product_list'][] = $cart_products[$product_id];
                unset($selected_carrier[$product_id]);
                unset($cart_products[$product_id]);
            }
        }

        if (count($cart_products)) {
            foreach ($cart_products as $product) {
                if (isset($product['is_virtual']) && $product['is_virtual'] == 1) {
                    $seller_id = KbSellerProduct::getSellerIdByProductId((int)$product['id_product']);
                    $seller_package_list[$seller_id][0]['product_list'][] = $product;
                }
            }
        }

        return $seller_package_list;
    }

    public static function getSellersInCart()
    {
        $cart_products = Context::getContext()->cart->getProducts();
        $seller_ids = array();
        $seller_ids_temp = array();
       
        foreach ($cart_products as $product) {
            $seller_ids_temp[] = KbSellerProduct::getSellerIdByProductId((int)$product['id_product']);
        }
        asort($seller_ids_temp);
        array_unique($seller_ids_temp);
        foreach ($seller_ids_temp as $sid) {
            $seller_ids[$sid] = $sid;
        }
        return $seller_ids;
    }

     public static function getCartProducts()
    {
        $cart_summary = Context::getContext()->cart->getSummaryDetails();
        $gift_products = $cart_summary['gift_products'];
        foreach($gift_products as $gift_product) {
            $cart_summary['products'][] = $gift_product;
        }
        foreach ($cart_summary['products'] as $key => $product) {
            $image = Image::getCover($product['id_product']);
            $link = new LINK();
            if ($image) {
                $cart_summary['products'][$key]['image_url'] = 'http://' . $link->getImageLink($product['link_rewrite'], $image['id_image'], 'small_default');
            } else {
                $cart_summary['products'][$key]['image_url'] = 'http://' . $link->getImageLink($product['link_rewrite'], $product['id_image'], 'small_default');
            }
        }
        return $cart_summary['products'];
    }
}
