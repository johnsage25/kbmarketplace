<ul class="kb-form-list">
    <li class="kb-form-fwidth">
        <div class="kb-form-label-block">
            <span class="kblabel">{l s='Pay to the order of (name)' mod='kbmarketplace'}</span><em>*</em>
        </div>
        <div class="kb-form-field-block">
            <input type="hidden" name="payment_info[check][name][label]" value="Owner Name">
            <input data-tab="paymentinfo" type="text" class="kb-inpfield required"  name="payment_info[check][name][value]" value="{$name|escape:'htmlall':'UTF-8'}">
        </div>
    </li>
    <li class="kb-form-fwidth">
        <div class="kb-form-label-block">
            <span class="kblabel">{l s='Address' mod='kbmarketplace'}</span><em>*</em>
        </div>
        <div class="kb-form-field-block">
            <input type="hidden" name="payment_info[check][address][label]" value="Address">
            <textarea data-tab="paymentinfo"  rows="5" class="kb-inptexarea required"  name="payment_info[check][address][value]">{$address|escape:'htmlall':'UTF-8'}</textarea>
            <p class="form-inp-help">{l s='Address where the cheque should be sent to.' mod='kbmarketplace'}</p>
        </div>
    </li>
    <li class="kb-form-fwidth">
        <div class="kb-form-label-block">
            <span class="kblabel">{l s='Additional Information' mod='kbmarketplace'}</span>
        </div>
        <div class="kb-form-field-block">
            <input type="hidden" name="payment_info[check][add_info][label]" value="Additional Information">
            <textarea data-tab="paymentinfo" rows="5" class="kb-inptexarea"  name="payment_info[check][add_info][value]" >{$add_info|escape:'htmlall':'UTF-8'}</textarea>
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