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

class CartRule extends CartRuleCore
{
    public function checkValidity(
        Context $context,
        $already_in_cart = false,
        $display_error = true,
        $check_carrier = true
    ) {
        
        $is_valid = parent::checkValidity($context, $already_in_cart, $display_error, $check_carrier);
        if ($is_valid) {
            $products = $context->cart->getProducts();
            $issellersProductsInCart = false;
            if ($products) {
                foreach ($products as $product) {
                    $check_product_query = 'SELECT COUNT(*) as row from ' . _DB_PREFIX_ . 'kb_mp_seller_product 
                        where id_product = ' . (int) $product['id_product'];
                    if ((bool) DB::getInstance(_PS_USE_SQL_SLAVE_)->getValue($check_product_query)) {
                        $issellersProductsInCart = true;
                    }
                }
            }
            $kbmpsettings = Tools::unSerialize(Configuration::get('KB_MARKETPLACE_CONFIG'));
            if ($issellersProductsInCart
                && $this->free_shipping
                && !$kbmpsettings['kbmp_enable_free_shipping']
            ) {
                $is_valid (!$display_error)
                    ? false
                    : Tools::displayError('This voucher cannot be used with this 
                        order as this order contains the seller products');
            }
        }
        
        return $is_valid;
    }
}
