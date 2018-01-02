<div class="kb-panel outer-border kb_product_section">
    <div data-toggle="kb-product-form-information" class='kb-panel-header kb-panel-header-tab'>
        <h1>{$form_title|escape:'htmlall':'UTF-8'}</h1>
        <div class='kb-accordian-symbol kbexpand'><i class="kb-material-icons">&#xe145;</i></div>
        <div class='clearfix'></div>
    </div>
    <div id="kb-product-form-information" class='kb-panel-body'>
        <div class="kb-block kb-form">
            <ul class="kb-form-list">
                <li class="kb-form-l">
                    <div class="kb-form-label-block">
                        <span class="kblabel ">{l s='Name' mod='kbmarketplace'}</span><em>*</em>
                    </div>
                    <div class="kb-form-field-block">
                        <input type="text" class="kb-inpfield required" validate="isGenericName" name="name_{$default_lang|intval}" lang-id="{$default_lang|intval}" value="{$name|escape:'htmlall':'UTF-8'}" onkeyup="updateLinkRewrite(this)" />
                    </div>
                </li>
                <li class="kb-form-r">
                    <div class="kb-form-label-block">
                        <span class="kblabel ">{l s='Reference Code' mod='kbmarketplace'}</span><em>*</em>
                    </div>
                    <div class="kb-form-field-block">
                        <input type="text" class="kb-inpfield required" validate="isGenericName" name="reference" value="{$reference|escape:'htmlall':'UTF-8'}" />
                    </div>
                </li>
                <li class="kb-form-l">
                    <div class="kb-form-label-block">
                        <span class="kblabel ">{l s='EAN-13 or JAN barcode' mod='kbmarketplace'}</span>
                    </div>
                    <div class="kb-form-field-block">
                        <input type="text" class="kb-inpfield" name="ean13" value="{$ean13|escape:'htmlall':'UTF-8'}" maxlength="13"/>
                    </div>
                </li>
                <li class="kb-form-r">
                    <div class="kb-form-label-block">
                        <span class="kblabel ">{l s='UPC barcode' mod='kbmarketplace'}</span>
                    </div>
                    <div class="kb-form-field-block">
                        <input type="text" class="kb-inpfield" name="upc" value="{$upc|escape:'htmlall':'UTF-8'}" maxlength="12" />
                    </div>
                </li>
                <li class="kb-form-l">
                    <div class="kb-form-label-block">
                        <span class="kblabel ">{l s='Active' mod='kbmarketplace'}</span>
                    </div>
                    <div class="kb-form-field-block">
                        <select name="active" class="kb-inpselect">
                            {if !empty($seller_product)}
                                <option value="1" selected="selected">{l s='Yes' mod='kbmarketplace'}</option>
{*                                <option value="0"> {l s='No' mod='kbmarketplace'}</option>*}
                            {else}
                                
                                <option value="0" {if $active eq 0}selected="selected"{/if}>{l s='No' mod='kbmarketplace'}</option>
                                <option value="1" {if $active eq 1}selected="selected"{/if}>{l s='Yes' mod='kbmarketplace'}</option>
                            {/if}
                        </select>
                    </div>
                </li>
                <li class="kb-form-r">
                    <div class="kb-form-label-block">
                        <span class="kblabel ">{l s='Visibility' mod='kbmarketplace'}</span>
                    </div>
                    <div class="kb-form-field-block">
                        <select name="visibility" class="kb-inpselect">
                            <option value="both" {if $visibility eq 'both'}selected="selected"{/if}>{l s='Everywhere' mod='kbmarketplace'}</option>
                            <option value="catalog" {if $visibility eq 'catalog'}selected="selected"{/if}>{l s='Catalog only' mod='kbmarketplace'}</option>
                            <option value="search" {if $visibility eq 'search'}selected="selected"{/if}>{l s='Search only' mod='kbmarketplace'}</option>
                            <option value="none" {if $visibility eq 'none'}selected="selected"{/if}>{l s='Nowhere' mod='kbmarketplace'}</option>
                        </select>
                    </div>
                </li>
                <li class="kb-form-l">
                    <div class="kb-form-label-block">
                        <span class="kblabel ">{l s='Condition' mod='kbmarketplace'}</span>
                    </div>
                    <div class="kb-form-field-block">
                        <select name="condition" class="kb-inpselect">
                            <option value="new" {if $condition eq 'new'}selected="selected"{/if}>{l s='New' mod='kbmarketplace'}</option>
                            <option value="used" {if $condition eq 'used'}selected="selected"{/if}>{l s='Used' mod='kbmarketplace'}</option>
                            <option value="refurbished" {if $condition eq 'refurbished'}selected="selected"{/if}>{l s='Refurbished' mod='kbmarketplace'}</option>
                        </select>
                    </div>
                </li>
                <li class="kb-form-r">
                    <div class="kb-form-label-block">
                        <span class="kblabel ">{l s='Manufacturer' mod='kbmarketplace'}</span>
                    </div>
                    <div class="kb-form-field-block">
                        <select name="id_manufacturer" class="kb-inpselect">
                            <option value="0">{l s='Select Manufacturer' mod='kbmarketplace'}</option>
                            {foreach $manufacturers as $manu}
                                <option value="{$manu['id_manufacturer']|intval}" {if $manu['id_manufacturer'] == $id_manufacturer}selected="selected"{/if} >{$manu['name']|escape:'htmlall':'UTF-8'}</option>
                            {/foreach}
                        </select>
                    </div>
                </li>
                <li class="kb-form-fwidth">
                    <div class="kb-form-label-block">
                        <span class="kblabel ">{l s='Options' mod='kbmarketplace'}</span>
                    </div>
                    <div class="kb-form-field-block">
                        <div class="kboption-inline kb-inpoption">
                            <input class="" type="checkbox" id="label_for_available_order" name="available_for_order" value="1" {if $id_product > 0}{if $available_for_order}checked="checked"{/if}{else}checked="checked"{/if} /> <label for="label_for_available_order">{l s='Available for order' mod='kbmarketplace'}</label>    
                        </div>
                        <div class="kboption-inline kb-inpoption">
                            <input class="" type="checkbox" id="label_for_show_price" name="show_price" value="1"  {if $show_price}checked="checked"{/if}  /> <label  for="label_for_show_price">{l s='Show price' mod='kbmarketplace'}</label>    
                        </div>
                        <div class="kboption-inline kb-inpoption" style="margin-bottom:0;">
                            <input class="" type="checkbox" id="label_for_online_only" name="online_only" value="1" {if $online_only}checked="checked"{/if} /> <label  for="label_for_online_only">{l s='Online only (not sold in your retail store)' mod='kbmarketplace'}</label>    
                        </div>
                    </div>
                </li>
                <li class="kb-form-fwidth">
                    <div class="kb-form-label-block">
                        <span class="kblabel ">{l s='Short Description' mod='kbmarketplace'}</span>
                    </div>
                    <div class="kb-form-field-block">
                        <textarea name="description_short_{$default_lang|intval}" rows="5" class="kb-inptexarea autoload_rte">{$description_short nofilter}</textarea>
                        <span class="counter" data-max="{if $short_description_limit}{$short_description_limit|intval}{else}none{/if}"></span>
                    </div>
                </li>
                <li class="kb-form-fwidth">
                    <div class="kb-form-label-block">
                        <span class="kblabel ">{l s='Description' mod='kbmarketplace'}</span>
                    </div>
                    <div class="kb-form-field-block">
                        <textarea name="description_{$default_lang|intval}" rows="5" class="kb-inptexarea autoload_rte">{$description nofilter}</textarea>
                    </div>
                </li>
                <li class="kb-form-fwidth last-row">
                    <div class="kb-form-label-block">
                        <span class="kblabel ">{l s='Tags' mod='kbmarketplace'}</span>
                    </div>
                    <div class="kb-form-field-block">
                        <input type="text"  validate="isTagList" name="tags_{$default_lang|intval}" class="kb-inpfield" value="{$tags|htmlentitiesUTF8}">
                    </div>
                    <span class='help-block'>{l s='Each tag has to be followed by a comma. The following characters are forbidden' mod='kbmarketplace'}: !<;>;?=+#"°{}_$%.</span>
                </li>
                {hook h="displayKbMarketPlacePForm" product_id=$id_product type=$product_type form="information"}
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