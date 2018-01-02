<div class="kb-vspacer5"></div>
<div class="kb-panel outer-border kb_product_section">
    <div data-toggle="kb-product-form-shipping" class='kb-panel-header kb-panel-header-tab'>
        <h1>{$form_title|escape:'htmlall':'UTF-8'}</h1>
        <input type="hidden" name="shipping_tab" value="1" />
        <div class='kb-accordian-symbol kbexpand'><i class="kb-material-icons">&#xe145;</i></div>
        <div class='clearfix'></div>
    </div>
    <div id="kb-product-form-shipping" class='kb-panel-body'>
        <div class="kb-block kb-form">
            <ul class="kb-form-list">
                <li class="kb-form-l">
                    <div class="kb-form-label-block">
                        <span class="kblabel ">{l s='Package Width' mod='kbmarketplace'}</span>
                    </div>
                    <div class="kb-form-field-block">
                        <div class="kb-labeled-inpfield">
                            <span class="inplbl">{$ps_dimension_unit|escape:'htmlall':'UTF-8'}</span>
                            <input type="text" class="kb-inpfield" validate="isPrice" name="width" value="{$width|escape:'htmlall':'UTF-8'}" maxlength="14" />
                        </div>
                    </div>
                </li>
                <li class="kb-form-r">
                    <div class="kb-form-label-block">
                        <span class="kblabel ">{l s='Package Height' mod='kbmarketplace'}</span>
                    </div>
                    <div class="kb-form-field-block">
                        <div class="kb-labeled-inpfield">
                            <span class="inplbl">{$ps_dimension_unit|escape:'htmlall':'UTF-8'}</span>
                            <input type="text" class="kb-inpfield" validate="isPrice" name="height" value="{$height|escape:'htmlall':'UTF-8'}" maxlength="14" />
                        </div>
                    </div>
                </li>
                <li class="kb-form-l">
                    <div class="kb-form-label-block">
                        <span class="kblabel ">{l s='Package Depth' mod='kbmarketplace'}</span>
                    </div>
                    <div class="kb-form-field-block">
                        <div class="kb-labeled-inpfield">
                            <span class="inplbl">{$ps_dimension_unit|escape:'htmlall':'UTF-8'}</span>
                            <input type="text" class="kb-inpfield" validate="isPrice" name="depth" value="{$depth|escape:'htmlall':'UTF-8'}" maxlength="14" />
                        </div>
                    </div>
                </li>
                <li class="kb-form-r">
                    <div class="kb-form-label-block">
                        <span class="kblabel ">{l s='Package Weight' mod='kbmarketplace'}</span>
                    </div>
                    <div class="kb-form-field-block">
                        <div class="kb-labeled-inpfield">
                            <span class="inplbl">{$ps_weight_unit|escape:'htmlall':'UTF-8'}</span>
                            <input type="text" class="kb-inpfield" validate="isPrice" name="weight" value="{$weight|escape:'htmlall':'UTF-8'}" maxlength="14" />
                        </div>
                    </div>
                </li>
                <li class="kb-form-fwidth">
                    <div class="kb-form-label-block">
                        <span class="kblabel ">{l s='Shipping Cost (for a single item)' mod='kbmarketplace'}</span>
                    </div>
                    <div class="kb-form-field-block inpfwidth" style="width:47.4%">
                        <input type="text" class="kb-inpfield" validate="isPrice" name="additional_shipping_cost" value="{$additional_shipping_cost|escape:'htmlall':'UTF-8'}" />
                    </div>
                </li>
                <li class="kb-form-l">
                    <div class="kb-form-label-block">
                        <span class="kblabel ">{l s='Available Shippings' mod='kbmarketplace'}</span>
                    </div>
                    <div class="kb-form-field-block">
                        <select id="availableShipping" name="availableShipping" multiple="multiple" class="kb-inpselect ship-multiselect">
                            {foreach $carrier_list as $carrier}
                                {if !isset($carrier.selected) || !$carrier.selected}
                                    <option value="{$carrier.id_reference|escape:'htmlall':'UTF-8'}">{$carrier.name|escape:'htmlall':'UTF-8'}</option>
                                {/if}
                            {/foreach}
                        </select>
                        <a href="javascript:void(0)" id="addShipping" class="btn-sm btn-tertiary-outline btn-block text-center">Add <i class="kb-material-icons"></i></a>
                    </div>
                </li>
                <li class="kb-form-r">
                    <div class="kb-form-label-block">
                        <span class="kblabel ">{l s='Selected Shippings' mod='kbmarketplace'}</span>
                    </div>
                    <div class="kb-form-field-block">
                        <select id="selectedShipping" name="selectedShipping[]" multiple="multiple" class="kb-inpselect ship-multiselect">
                            {foreach $carrier_list as $carrier}
                                {if isset($carrier.selected) && $carrier.selected}
                                    <option value="{$carrier.id_reference|escape:'htmlall':'UTF-8'}">{$carrier.name|escape:'htmlall':'UTF-8'}</option>
                                {/if}
                            {/foreach}
                        </select>
                        <a href="javascript:void(0)" id="removeShipping" class="btn-sm btn-tertiary-outline btn-block text-center"><i class="kb-material-icons"></i> {l s='Remove' mod='kbmarketplace'}</a>
                    </div>
                </li>
                <li id="shipping-selection-msg" class="kb-form-fwidth" style="display:{if $product_has_shipping}none{else}block{/if};">
                    <div class="kbalert kbalert-warning"><i class="kb-material-icons" ></i>{l s='If no shipping selected, then product will be available for sale with free shipping.' mod='kbmarketplace'}</div>
                </li>
                {hook h="displayKbMarketPlacePForm" product_id=$id_product type=$product_type form="shipping"}
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