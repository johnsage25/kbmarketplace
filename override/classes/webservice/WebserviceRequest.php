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

class WebserviceRequest extends WebserviceRequestCore
{

    public static function getResources()
    {
        $resources = parent::getResources();
        
        $resources = array_merge(
            $resources,
            array(
                'kbsellers' => array('description' => 'Sellers', 'class' => 'KbSeller'),
                'kbsellerproducts' => array('description' => 'Seller Products', 'class' => 'KbSellerProduct'),
                'kbsellercategories' => array('description' => 'Seller Categories', 'class' => 'KbSellerCategory'),
                'kbsellercrequests' => array(
                    'description' => 'Seller Category Request',
                    'class' => 'KbSellerCRequest'
                ),
                'kbsellerearnings' => array(
                    'description' => 'Seller Orders',
                    'class' => 'KbSellerEarning',
                    'forbidden_method' => array('PUT', 'POST', 'DELETE')
                ),
                'kbsellerorderdetails' => array(
                    'description' => 'Seller Order Details',
                    'class' => 'KbSellerOrderDetail',
                    'forbidden_method' => array('PUT', 'POST', 'DELETE')
                ),
                'kbsellerproductreviews' => array(
                    'description' => 'Seller Product Comment',
                    'class' => 'KbSellerProductReview',
                    'forbidden_method' => array('PUT', 'POST', 'DELETE')
                ),
                'kbsellerreviews' => array('description' => 'Seller Reviews', 'class' => 'KbSellerReview'),
                'kbsellermenus' => array('description' => 'Seller Menus', 'class' => 'KbSellerMenu'),
                'kbsellershippings' => array('description' => 'Seller Shipping', 'class' => 'KbSellerShipping'),
                'kbsellertransactions' => array(
                    'description' => 'Seller Transactions',
                    'class' => 'KbSellerTransaction',
                    'forbidden_method' => array('PUT', 'POST', 'DELETE')
                )
            )
        );
        
        ksort($resources);
        return $resources;
    }
}
