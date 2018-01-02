<ul class="kb-form-list">
    <li class="kb-form-fwidth">
        <div class="kb-form-label-block">
            <span class="kblabel">{l s='Card Holder Name' mod='kbmarketplace'}</span>
        </div>
        <div class="kb-form-field-block">
            <input type="hidden" name="payment_info[creditcard][card_holder_name][label]" value="Card Holder Name">
            <input data-tab="paymentinfo" type="text" class="kb-inpfield"  name="payment_info[creditcard][card_holder_name][value]" value="{$card_holder_name|escape:'htmlall':'UTF-8'}">
        </div>
    </li>
    <li class="kb-form-fwidth">
        <div class="kb-form-label-block">
            <span class="kblabel">{l s='Card Number' mod='kbmarketplace'}</span><em>*</em>
        </div>
        <div class="kb-form-field-block">
            <input type="hidden" name="payment_info[creditcard][card_number][label]" value="Card Number">
            <input data-tab="paymentinfo" type="text" class="kb-inpfield required"  validate="isInt" name="payment_info[creditcard][card_number][value]" value="{$card_number|escape:'htmlall':'UTF-8'}">
        </div>
    </li>
    <li class="kb-form-fwidth">
        <div class="kb-form-label-block">
            <span class="kblabel">{l s='Bank Details' mod='kbmarketplace'}</span>
        </div>
        <div class="kb-form-field-block">
            <input type="hidden" name="payment_info[creditcard][details][label]" value="Bank Ifsc Code">
            <textarea data-tab="paymentinfo" rows="5" class="kb-inptexarea"  name="payment_info[creditcard][details][value]" >{$bank_details|escape:'htmlall':'UTF-8'}</textarea>
        </div>
    </li>
    <li class="kb-form-fwidth">
        <div class="kb-form-label-block">
            <span class="kblabel">{l s='Additional Information' mod='kbmarketplace'}</span>
        </div>
        <div class="kb-form-field-block">
            <input type="hidden" name="payment_info[creditcard][add_info][label]" value="Additional Information">
            <textarea data-tab="paymentinfo" rows="5" class="kb-inptexarea"  name="payment_info[creditcard][add_info][value]" >{$add_info|escape:'htmlall':'UTF-8'}</textarea>
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