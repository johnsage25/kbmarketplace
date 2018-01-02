<ul class="kb-form-list">
    <li class="kb-form-fwidth">
        <div class="kb-form-label-block">
            <span class="kblabel">{l s='Paypal Id' mod='kbmarketplace'}</span><em>*</em>
        </div>
        <div class="kb-form-field-block">
            <input type="hidden" name="payment_info[kbpaypal][paypal_id][label]" value="Paypal Id">
            <input data-tab="paymentinfo" type="text" class="kb-inpfield required"  validate="isEmail" name="payment_info[kbpaypal][paypal_id][value]" value="{$paypal_id|escape:'htmlall':'UTF-8'}">
        </div>
    </li>
    <li class="kb-form-fwidth">
        <div class="kb-form-label-block">
            <span class="kblabel">{l s='Additional Information' mod='kbmarketplace'}</span>
        </div>
        <div class="kb-form-field-block">
            <input type="hidden" name="payment_info[kbpaypal][add_info][label]" value="Additional Information">
            <textarea data-tab="paymentinfo" rows="5" class="kb-inptexarea"  name="payment_info[kbpaypal][add_info][value]" >{$add_info|escape:'htmlall':'UTF-8'}</textarea>
        </div>
    </li>
</ul>
{*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer tohttp://www.prestashop.com for more information.
* We offer the best and most useful modules PrestaShop and modifications for your online store.
*
* @category  PrestaShop Module
* @author    knowband.com <support@knowband.com>
* @copyright 2016 knowband
* @license   see file: LICENSE.txt
*}