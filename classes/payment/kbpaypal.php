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

class Kbpaypal
{
    public static function getPaymentContent()
    {
        $template_path = _PS_MODULE_DIR_ . 'kbmarketplace/views/templates/front/seller/payment/';
        $smarty = Context::getContext()->smarty;
        $paypal_id = '';
        $add_info = '';
        if ($data = Kbpaypal::getPaymentData()) {
            $paypal_id = $data['paypal_id']['value'];
            $add_info = $data['add_info']['value'];
        }
        $smarty->assign(
            array(
                'paypal_id' => $paypal_id,
                'add_info' => $add_info,
            )
        );

        return $smarty->fetch($template_path . 'paypal.tpl');
    }

    public static function getTranslatedText($text)
    {
        return Translate::getModuleTranslation('kbmarketplace', $text, 'kbbankwire');
    }

    public static function getPaymentData()
    {
        $seller = new KbSeller(
            KbSeller::getSellerByCustomerId(Context::getContext()->customer->id),
            Context::getContext()->customer->id_lang
        );
        $payment_info = Tools::unSerialize($seller->payment_info);
        if (isset($payment_info['name']) && $payment_info['name'] == 'kbpaypal') {
            return $payment_info['data'];
        } else {
            return false;
        }
    }
}
