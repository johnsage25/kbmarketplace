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

class Creditcard
{
    public static function getPaymentContent()
    {
        $template_path = _PS_MODULE_DIR_ . 'kbmarketplace/views/templates/front/seller/payment/';
        $smarty = Context::getContext()->smarty;
        $card_holder_name = '';
        $card_number = '';
        $add_info = '';
        $bank_details = '';
        if ($data = Creditcard::getPaymentData()) {
            $card_holder_name = $data['card_holder_name']['value'];
            $card_number = $data['card_number']['value'];
            $bank_details = $data['details']['value'];
            $add_info = $data['add_info']['value'];
        }
        $smarty->assign(
            array(
                'card_holder_name' => $card_holder_name,
                'card_number' => $card_number,
                'add_info' => $add_info,
                'bank_details' => $bank_details
            )
        );
        return $smarty->fetch($template_path . 'credit_card.tpl');
    }

    public static function getPaymentData()
    {
        $seller = new KbSeller(
            KbSeller::getSellerByCustomerId(Context::getContext()->customer->id),
            Context::getContext()->customer->id_lang
        );
        $payment_info = Tools::unSerialize($seller->payment_info);
        if (isset($payment_info['name']) && $payment_info['name'] == 'creditcard') {
            return $payment_info['data'];
        } else {
            return false;
        }
    }
}
