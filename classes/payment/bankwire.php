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

class Bankwire
{

    public static function getPaymentContent()
    {
        $template_path = _PS_MODULE_DIR_ . 'kbmarketplace/views/templates/front/seller/payment/';
        $smarty = Context::getContext()->smarty;
        $owner_name = '';
        $details = '';
        $address = '';
        $add_info = '';
        if ($data = Bankwire::getPaymentData()) {
            $owner_name = $data['owner_name']['value'];
            $details = $data['details']['value'];
            $address = $data['address']['value'];
            $add_info = $data['add_info']['value'];
        }
        $smarty->assign(
            array(
                'owner_name' => $owner_name,
                'details' => $details,
                'address' => $address,
                'add_info' => $add_info
            )
        );
        return $smarty->fetch($template_path . 'bankwire.tpl');
    }

    public static function getPaymentData()
    {
        $seller = new KbSeller(
            KbSeller::getSellerByCustomerId(Context::getContext()->customer->id),
            Context::getContext()->customer->id_lang
        );
        $payment_info = Tools::unSerialize($seller->payment_info);
        if (isset($payment_info['name']) && $payment_info['name'] == 'bankwire') {
            return $payment_info['data'];
        } else {
            return false;
        }
    }
}
