<div class="kb-vspacer5"></div>
<div class="kb-panel outer-border kb_product_section">
    <div data-toggle="kb-product-form-features" class='kb-panel-header kb-panel-header-tab'>
        <h1>{$form_title|escape:'htmlall':'UTF-8'}</h1>
        <div class='kb-accordian-symbol kbexpand'><i class="kb-material-icons">&#xe145;</i></div>
        <div class='clearfix'></div>
    </div>
    <div id="kb-product-form-features" class='kb-panel-body'>
        {if $id_product eq 0}
            <div class="kbalert kbalert-warning pack-empty-warning">
                <i class="kb-material-icons" style="margin-right:0;"></i> {l s='Please save this product, before adding feature(s).' mod='kbmarketplace'}
            </div>
        {else}
           <div class="kb-block kb-form">
               {if count($features)}
            <ul class="kb-form-list">
                {foreach $features as $feature}
                <li class="kb-form-fwidth">
                    <div class="kb-form-label-block">
                        <span class="kblabel ">{$feature['name']|escape:'htmlall':'UTF-8'}</span>
                    </div>
                    <div class="kb-form-field-block">
                        <select id="kb-product-{$feature['id_feature']|intval}-feature" name="feature_{$feature['id_feature']|intval}_value" class="kb-inpselect">
                            <option value="0" >---</option>
                            {foreach $feature['value'] as $value}
                                <option value="{$value['id_feature_value']|intval}" {if isset($product_features[$feature['id_feature']]) && $product_features[$feature['id_feature']] ==  $value['id_feature_value']} selected='selected' {/if}>{$value['value']|escape:'htmlall':'UTF-8'}</option>
                            {/foreach}
                        </select>
                    </div>
                </li>
                {/foreach}
            {/if}
        {/if}
        {hook h="displayKbMarketPlacePForm" product_id=$id_product type=$product_type form="features"}
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