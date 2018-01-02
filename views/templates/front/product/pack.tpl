<div class="kb-vspacer5"></div>
<div class="kb-panel outer-border kb_product_section">
    <div data-toggle="kb-product-form-package" class='kb-panel-header kb-panel-header-tab'>
        <h1>{$form_title|escape:'htmlall':'UTF-8'}</h1>
        <div class='kb-accordian-symbol kbexpand'><i class="kb-material-icons">&#xe145;</i></div>
        <div class='clearfix'></div>
    </div>
    <div id="kb-product-form-package" class='kb-panel-body'>
        <div class="kb-block kb-form">
            <ul class="kb-form-list">
                <li class="kb-form-fwidth">
                    <div class="kb-form-label-inblock">
                        <span class="kblabel "><i class="kb-material-icons" data-toggle="tooltip" data-placement="top" title="{l s='Start by typing the first letters of the product name, then select the product from the drop-down list.' mod='kbmarketplace'}"></i>{l s='Add Product in your Pack' mod='kbmarketplace'}</span>
                    </div>
                    <div class="kb-form-field-inblock pakcage_pro_search">
                        <div class="kb-form-field-inblock kb-search-box">
                            <input type="text" id="curPackItemName" name="curPackItemName" class="kb-form-control" />    
                        </div>
                        <div class="kb-form-field-inblock package_pro_wfield">
                            <div class="kb-labeled-inpfield">
                                <span class="inplbl">X</span>
                                <input id="curPackItemQty" type="text" class="kb-inpfield" name="curPackItemQty" value="1" style="height:28px; width:80px;" />
                            </div>
                        </div>
                        <div class="kb-form-field-inblock package_pro_add_btn">
                            <a id="add_pack_item" href="javascript:void(0)" class="btn-sm btn-warning"><i class="kb-material-icons"></i>{l s='Add' mod='kbmarketplace'}</a>
                        </div>
                    </div>
                </li>
                <li class="kb-form-fwidth last-row">
                    <div id="package-empty-msg" class="kbalert kbalert-warning pack-empty-warning" {if $pack_items|@count != 0}style="display:none"{/if}>{l s='This pack is empty. You must add at least one product item.' mod='kbmarketplace'}</div>
                    <ul id="divPackItems" class="list-unstyled">
                        {foreach $pack_items as $pack_item}
                            <li class="product-pack-item packpro-prev-pack" data-product-name="{if isset($curPackItemName)}{$curPackItemName|escape:'htmlall':'UTF-8'}{/if}" data-product-qty="{$pack_item.pack_quantity|intval}" data-product-id="{$pack_item.id|intval}">
                                <img class="packpro-prev-pack-img" src="{$pack_item.image|escape:'htmlall':'UTF-8'}"/>
                                <span class="packpro-prev-pack-title">{$pack_item.name|escape:'htmlall':'UTF-8'}</span>
                                <span class="packpro-prev-pack-ref">{l s='REF' mod='kbmarketplace'}: {$pack_item.reference|escape:'htmlall':'UTF-8'}</span>
                                <span class="packpro-prev-pack-quantity"><span class="text-muted">x</span> {$pack_item.pack_quantity|intval}</span>
                                <a href="javascript:void(0)" class="btn-sm btn-tertiary delPackItem packpro-prev-pack-action" data-delete="{$pack_item.id|intval}" ><i class="kb-material-icons"></i></a>
                            </li>
                        {/foreach}
                    </ul>
                    <div class="clearfix"></div>
                </li>
            </ul>
            {hook h="displayKbMarketPlacePForm" product_id=$id_product type=$product_type form="pack"}
        </div>
        <input type="hidden" name="inputPackItems" id="inputPackItems" value="{$input_pack_items|escape:'htmlall':'UTF-8'}" placeholder="inputs"/>
        <input type="hidden" name="namePackItems" id="namePackItems" value="{$input_namepack_items|escape:'htmlall':'UTF-8'}" placeholder="name"/>
    </div>
    <script type="text/javascript">
        var product_search_url = '{$search_product_url|escape:'htmlall':'UTF-8'}';
        var kb_package_product_ref_lbl = '{l s='REF' mod='kbmarketplace'}';
    </script>
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