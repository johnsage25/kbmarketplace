<div class="kb-vspacer5"></div>
<div class="kb-panel outer-border kb_product_section">
    <div data-toggle="kb-product-form-qty" class='kb-panel-header kb-panel-header-tab'>
        <h1>{$form_title|escape:'htmlall':'UTF-8'}</h1>
        <div class='kb-accordian-symbol kbexpand'><i class="kb-material-icons">&#xe145;</i></div>
        <div class='clearfix'></div>
    </div>
    <div id="kb-product-form-qty" class='kb-panel-body'>
        <div class="kb-block kb-form">
            <ul class="kb-form-list">
                <li class="kb-form-l">
                    <div class="kb-form-label-block">
                        <span class="kblabel ">{l s='Quantity' mod='kbmarketplace'}</span>
                    </div>
                    <div class="kb-form-field-block">
                        <input type="text" id="kb_product_quantity" class="kb-inpfield {if $has_attributes > 0}disabled{/if}" validate="isInt" name="qty_0" value="{if $qty > 0} {$qty|escape:'htmlall':'UTF-8'} {else}0{/if}" {if $has_attributes > 0}readonly="true"{/if}/>
                    </div>
                </li>
                <li class="kb-form-r">
                    <div class="kb-form-label-block">   
                        <span class="kblabel "><i class="kb-material-icons" data-toggle="tooltip" data-placement="top" title="{l s='Action when product is out of stock' mod='kbmarketplace'}"></i>{l s='Out of Stock' mod='kbmarketplace'}</span>
                    </div>
                    <div class="kb-form-field-block">
                        <select name="out_of_stock" class="kb-inpselect">
                            <option value="0" {if $out_of_stock == 0} selected="selected" {/if}>{l s='Deny orders' mod='kbmarketplace'}</option>
                            <option value="1" {if $out_of_stock == 1} selected="selected" {/if}>{l s='Allow orders' mod='kbmarketplace'}</option>
                            <option value="2" {if $out_of_stock == 2} selected="selected" {/if}>{l s='Default' mod='kbmarketplace'}: {if $order_out_of_stock == 1}{l s='Allow orders' mod='kbmarketplace'}{else}{l s='Deny orders' mod='kbmarketplace'}{/if}</option>
                        </select>
                    </div>
                </li>
                <li class="kb-form-l">
                    <div class="kb-form-label-block">
                        <span class="kblabel "><i class="kb-material-icons" data-toggle="tooltip" data-placement="top" title="{l s='The minimum quantity to buy this product (set to 1 to disable this feature)' mod='kbmarketplace'}"></i>{l s='Minimum Quantity' mod='kbmarketplace'}</span><em>*</em>
                    </div>
                    <div class="kb-form-field-block">
                        <input type="text" class="kb-inpfield" name="minimal_quantity" id="minimal_quantity_input" validate="isInt" maxlength="6" value="{if $minimal_quantity > 0}{$minimal_quantity|escape:'htmlall':'UTF-8'}{else}1{/if}" />
                    </div>
                </li>
                <li class="kb-form-r">
                    <div class="kb-form-label-block">
                        <span class="kblabel "><i class="kb-material-icons" data-toggle="tooltip" data-placement="top" title="{l s='The next date of availability for this product when it is out of stock.' mod='kbmarketplace'}"></i>{l s='Available Date' mod='kbmarketplace'}</span>
                    </div>
                    <div class="kb-form-field-block">
                        <div class="kb-labeled-inpfield">
                            <span class="inplbl"><i class="kb-material-icons"></i></span>
                            <input type="text" class="kb-inpfield datepicker" validate="isDate" name="available_date" value="{$available_date|escape:'htmlall':'UTF-8'}" />
                        </div>
                        
                    </div>
                </li>
                <li class="kb-form-l">
                    <div class="kb-form-label-block">
                        <span class="kblabel ">{l s='Displayed text when in-stock' mod='kbmarketplace'}</span>
                    </div>
                    <div class="kb-form-field-block">
                        <input type="text" class="kb-inpfield" name="available_now_{$default_lang|intval}" value="{$available_now|escape:'htmlall':'UTF-8'}" />
                    </div>
                </li>
                <li class="kb-form-r">
                    <div class="kb-form-label-block">
                        <span class="kblabel ">{l s='Displayed text when backordering is allowed' mod='kbmarketplace'}</span>
                    </div>
                    <div class="kb-form-field-block">
                        <input type="text" class="kb-inpfield" name="available_later_{$default_lang|intval}" value="{$available_later|escape:'htmlall':'UTF-8'}" />
                    </div>
                </li>
                {hook h="displayKbMarketPlacePForm" product_id=$id_product type=$product_type form="quantity"}
            </ul>
        </div>
    </div>
</div>
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