<div class="kb-vspacer5"></div>
<div class="kb-panel outer-border kb_product_section">
    <div data-toggle="kb-product-form-suppliers" class='kb-panel-header kb-panel-header-tab'>
        <h1>{$form_title|escape:'htmlall':'UTF-8'}</h1>
        <div class='kb-accordian-symbol kbexpand'></div>
        <div class='clearfix'></div>
    </div>
    <div id="kb-product-form-suppliers" class='kb-panel-body'>
        {if $id_product eq 0}
            <div class="kbalert kbalert-warning pack-empty-warning">
                <i class="icon-exclamation-sign" style="font-size:12px; margin-right:5px;"></i> {l s='You must save this product before managing suppliers.' mod='kbmarketplace'}
            </div>
        {else}
        <div class="kb-block kb-form">
            <ul class="kb-form-list">
                <li class="kb-form-fwidth last-row">
                    <div class="kb-form-label-block">
                    <span class="kblabel " title="{l s='List of the suppliers' mod='kbmarketplace'}">{l s='Suppliers' mod='kbmarketplace'}</span>
                    </div>
                    <div class="kb-form-field-block">
                        <select id="selectbox_product_suppliers" name="id_suppliers[]" class="kb-inpselect selectbox_suppliers" multiple="multiple" data-id='kb_mp_product_default_supplier'>
                            {if $suppliers > 0}
                            {foreach $suppliers as $supplier}
                            <option class="customize_options" value="{$supplier['id_supplier']|intval}" {if $supplier['is_selected'] == true}selected="selected"{/if}>{$supplier['name']|escape:'html':'UTF-8'}</option>
                            {/foreach}
                            {else}
                            <option class="customize_options" value='0'>{l s='Select' mod='kbmarketplace'}</option>
                            {/if}
                        </select>
                    </div>
                </li>
                <li class="kb-form-fwidth last-row">
                    <div class="kb-form-label-block">
                        <span class="kblabel " title="{l s='Select default supplier' mod='kbmarketplace'}">{l s='Default Supplier' mod='kbmarketplace'}</span>
                    </div>
                    <div class="kb-form-field-block">
                        <select id="kb_mp_product_default_supplier" name="default_supplier" class="kb-inpselect">
                            {if $suppliers > 0}
                            {foreach $suppliers as $supplier}
                            {if $supplier['is_selected'] == true}
                            <option class="customize_options" value="{$supplier['id_supplier']|intval}" {if $supplier['id_supplier'] == $default_supplier}selected="selected"{/if}>{$supplier['name']|escape:'htmlall':'UTF-8'}</option>
                            {/if}
                            {/foreach}
                            {else}
                            <option class="customize_options" value='0'>{l s='Select' mod='kbmarketplace'}</option>
                            {/if}
                        </select>
                    </div>
                </li>
            </ul>
    </div>
    {/if}
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