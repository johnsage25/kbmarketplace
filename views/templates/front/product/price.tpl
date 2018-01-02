<div class="kb-vspacer5"></div>
<div class="kb-panel outer-border kb_product_section">
    <div data-toggle="kb-product-form-price" class='kb-panel-header kb-panel-header-tab'>
        <h1>{$form_title|escape:'htmlall':'UTF-8'}</h1>
        <div class='kb-accordian-symbol kbexpand'><i class="kb-material-icons">&#xe145;</i></div>
        <div class='clearfix'></div>
    </div>
    <div id="kb-product-form-price" class='kb-panel-body'>
        <div class="kb-block kb-form">
            <ul class="kb-form-list">
                <li class="kb-form-l">
                    <div class="kb-form-label-block">
                        <span class="kblabel ">{l s='Wholesale Price' mod='kbmarketplace'}</span>
                    </div>
                    <div class="kb-form-field-block">
                        <div class="kb-labeled-inpfield">
                            {if isset($currency_prefix)}
                                <span class="inplbl">{$currency_prefix|escape:'htmlall':'UTF-8'}</span>
                            {/if}
                        <input type="text" class="kb-inpfield" validate="isPrice" name="wholesale_price" value="{$wholesale_price}" />
                            {if isset($currency_suffix)}
                                <span class="inplbl">{$currency_suffix|escape:'htmlall':'UTF-8'}</span>
                            {/if}
                        </div>
                    </div>
                </li>
                <li class="kb-form-r">
                    <div class="kb-form-label-block">
                        <span class="kblabel ">{l s='Retail Price' mod='kbmarketplace'}</span><em>*</em>
                    </div>
                    <div class="kb-form-field-block">
                        <div class="kb-labeled-inpfield">
                            {if isset($currency_prefix)}
                                <span class="inplbl">{$currency_prefix|escape:'htmlall':'UTF-8'}</span>
                            {/if}
                        <input id="price" type="text" class="kb-inpfield required" validate="isPrice" name="price" value="{$price}" />
                        {if isset($currency_suffix)}
                                <span class="inplbl">{$currency_suffix|escape:'htmlall':'UTF-8'}</span>
                            {/if}
                        </div>
                    </div>
                </li>
                <li class="kb-form-l">
                    <div class="kb-form-label-block">
                        <span class="kblabel ">{l s='Unit Price Per Quantity' mod='kbmarketplace'}</span>
                    </div>
                    <div class="kb-form-field-block">
                        <div class="kb-labeled-inpfield">
                            {if isset($currency_prefix)}
                                <span class="inplbl">{$currency_prefix|escape:'htmlall':'UTF-8'}</span>
                            {/if}
                        <input type="text" class="kb-inpfield" name="unit_price" validate="isPrice" value="{$unit_price}"  maxlength="14"/>
                            {if isset($currency_suffix)}
                                <span class="inplbl">{$currency_suffix|escape:'htmlall':'UTF-8'}</span>
                            {/if}
                        </div>
                    </div>
                </li>
                <li class="kb-form-r">
                    <div class="kb-form-label-block">
                        <span class="kblabel ">{l s='Special Price' mod='kbmarketplace'}</span>
                    </div>
                    <div class="kb-form-field-block">
                        <div class="kb-labeled-inpfield">
                            {if isset($currency_prefix)}
                                <span class="inplbl">{$currency_prefix|escape:'htmlall':'UTF-8'}</span>
                            {/if}
                        <input id="sp_reduction" type="text" class="kb-inpfield" name="sp_reduction" validate="isPrice" value="{$specific_price}" />
                        {if isset($currency_suffix)}
                                <span class="inplbl">{$currency_suffix|escape:'htmlall':'UTF-8'}</span>
                            {/if}
                    </div>
                </li>
                <li class="kb-form-l">
                    <div class="kb-form-label-block">
                        <span class="kblabel ">{l s='Special Price Start Date' mod='kbmarketplace'}</span>
                    </div>
                    <div class="kb-form-field-block">
                        <div class="kb-labeled-inpfield">
                            <span class="inplbl"><i class="kb-material-icons"></i></span>
                            <input id="sp_from_date" type="text" class="kb-inpfield datepicker" name="sp_from_date" validate="isDate" value="{$specific_price_from|escape:'htmlall':'UTF-8'}" />
                        </div>
                    </div>
                </li>
                <li class="kb-form-r">
                    <div class="kb-form-label-block">
                        <span class="kblabel ">{l s='Special Price End Date' mod='kbmarketplace'}</span>
                    </div>
                    <div class="kb-form-field-block">
                        <div class="kb-labeled-inpfield">
                            <span class="inplbl"><i class="kb-material-icons"></i></span>
                            <input id="sp_to" type="text" class="kb-inpfield datepicker" name="sp_to" validate="isDate" value="{$specific_price_to|escape:'htmlall':'UTF-8'}" />
                        </div>
                    </div>
                </li>
                <li class="kb-form-fwidth last-row">
                    <div class="kb-form-field-block">
                        <div class="kboption-inline kb-inpoption">
                            <input class="" type="checkbox" id="label_for_on_sale" name="on_sale" value="1" {if $on_sale}checked="checked"{/if}/> <label for="label_for_on_sale">{l s='Display the "on sale" icon on the product page, and in the text found within the product listing.' mod='kbmarketplace'}</label>    
                        </div>
                    </div>
                </li>
            </ul>
            {hook h="displayKbMarketPlacePForm" product_id=$id_product type=$product_type form="price"}
        </div>
    </div>
</div>
<script type="text/javascript">
    var kb_special_price_invalid = '{l s='Special price should not be greater than retail price' mod='kbmarketplace'}';
    var kb_invalid_sp_date_msg = '{l s='End date should not be greater than start date' mod='kbmarketplace'}';
</script>
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