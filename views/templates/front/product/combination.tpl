<div class="kb-vspacer5"></div>
<div class="kb-panel outer-border kb_product_section">
    <div data-toggle="kb-product-form-combination" class='kb-panel-header kb-panel-header-tab'>
        <h1>{$form_title|escape:'htmlall':'UTF-8'}</h1>
        <div class='kb-accordian-symbol kbexpand'><i class="kb-material-icons">&#xe145;</i></div>
        <div class='clearfix'></div>
    </div>
    <div id="kb-product-form-combination" class='kb-panel-body'>
        {if $id_product eq 0}
            <div class="kbalert kbalert-warning pack-empty-warning">
                <i class="kb-material-icons" style="margin-right:0"></i> {l s='Before creating combination, you must have to save this product.' mod='kbmarketplace'}
            </div>
        {else}
            <div class="kb-block kb-form">
                <ul class="kb-form-list">
                    <li class="kb-form-fwidth last-row" style='min-height:inherit;'>
                        <div class="kb-form-field-inblock" style="float:right;">
                            <a href="javascript:void(0)" class="btn-sm btn-info" onclick="editCombination(0);"><i class="kb-material-icons"></i><span>{l s='Add New Combination' mod='kbmarketplace'}</span></a>
                        </div>
                        <div class="clearfix"></div>
                    </li>
                    <li class="kb-form-fwidth last-row" style='overflow-y: auto'>
                        <div id="combination-section-msg" class="kbalert kbalert-info" style="display:{if count($attributes) > 0}block{else}none{/if};"><i class="kb-material-icons" style="font-size: 20px;"></i>{l s='Blue row indicates the default combination.' mod='kbmarketplace'}</div>
                        <table class="kb-table-list">
                            <thead>
                                <tr class="heading-row">
                                    <th>{l s='Combination' mod='kbmarketplace'}</th>
                                    <th>{l s='Reference' mod='kbmarketplace'}</th>
                                    <th>{l s='EAN' mod='kbmarketplace'}</th>
                                    <th>{l s='UPC' mod='kbmarketplace'}</th>
                                    <th width="50">{l s='Qty' mod='kbmarketplace'}</th>
                                    <th width="70">{l s='Default' mod='kbmarketplace'}</th>
                                    <th width="100"></th>
                                </tr>
                            </thead>
                            <tbody id="kb_product_combination_list">
                                {if count($attributes) == 0}
                                    <tr><td colspan="7" class="kb-empty-table kb-tcenter">{l s='No Combination available for this product.' mod='kbmarketplace'}</td></tr>
                                {/if}
                            </tbody>
                        </table>
                        <table class="kb-table-list" style="display:none;">
                            <tbody id="new_combination_template">
                                <tr id="id_product_attribute">                        
                                    <td>combination</td>
                                    <td>reference</td>
                                    <td>ean13</td>
                                    <td>upc</td>
                                    <td class="kb-tright">{l s='stock_available' mod='kbmarketplace'}</td>
                                    <td class="kb-tcenter de_com_col">{l s='is_default' mod='kbmarketplace'}</td>
                                    <td class="kb-tcenter">
                                        <a href="javascript:void(0)" class="btn-sm" onclick="editCombination(id_product_attribute);" title="{l s='Click to edit' mod='kbmarketplace'}"><i class="kb-material-icons"></i></a>
                                        <a href="javascript:void(0)" class="btn-sm" onclick="deleteCombination(id_product_attribute);" title="{l s='Click to delete' mod='kbmarketplace'}"><i class="kb-material-icons"></i></a>
                                    </td>
                                </tr>    
                            </tbody>
                        </table>
                    </li>
                </ul>
            </div>
        {/if}
    </div>
</div>
<script type="text/javascript">
    var kb_no_combination_msg = '{l s='No Combination available for this product.' mod='kbmarketplace'}';
    var kb_new_combination_title = '{l s='Add New Combination' mod='kbmarketplace'}';
    var kb_edit_combination_title = '{l s='Edit Combination' mod='kbmarketplace'}';
    
    $(document).ready(function(){ 
        {if count($attributes) > 0}
            {foreach $attributes as $attribute}
                displayCombinationRow({$attribute['id_product_attribute']|intval},
                    "{$attribute['attribute_designation']|escape:'htmlall':'UTF-8'}",
                    "{$attribute['reference']|escape:'htmlall':'UTF-8'}",
                    "{$attribute['ean13']|escape:'htmlall':'UTF-8'}",
                    "{$attribute['upc']|escape:'htmlall':'UTF-8'}",
                    {$attribute['stock_available']|intval},
                    {$attribute['default_on']|intval});
            {/foreach}
        {/if}
    });
</script>
<div id="kb-combination-modal-form" style="display:none;">
    <div class="kb-overlay"></div>
    <div id="combination-loader" class="kb-modal loading-block"><div class="loader128"></div></div>
    <div id="combination-form-content" class="kb-modal" style="display:none">
        <div class="kb-modal-header">
            <h1 id='kb_combination_form_title'>{l s='Add New Combination' mod='kbmarketplace'}</h1>
            <span class="kb-modal-close" data-modal="kb-combination-modal-form">X</span>
        </div>
        <div class="kb-modal-content">
            <div id="new-combination-form-msg" class="kbalert"></div>
            <div id="new-combination-form" class="new_cmbination_form kb-form" style="padding:0;">
                <ul class="kb-form-list">
                    <li class="kb-form-fwidth">
                        <div class="kb-form-field-block kb-mbtm10">
                            <div class="form-lbl-indis kb-tright">
                                <span class="kblabel">{l s='Attribute' mod='kbmarketplace'}:</span>
                            </div>
                            <div class="form-field-indis cobmination-field">
                                <select name="attribute_group" id="attribute_group" onchange="populate_attrs();" class="kb-inpselect">
                                    {if isset($attributes_groups)}
                                            {foreach from=$attributes_groups key=k item=attribute_group}
                                            <option value="{$attribute_group.id_attribute_group|intval}">{$attribute_group.name|escape:'htmlall':'UTF-8'}&nbsp;&nbsp;</option>
                                            {/foreach}
                                    {/if}
                                </select>
                            </div>    
                        </div>
                        <div class="kb-form-field-block kb-mbtm10">
                            <div class="form-lbl-indis kb-tright">
                                <span class="kblabel">{l s='Value' mod='kbmarketplace'}:</span>
                            </div>
                            <div class="form-field-indis cobmination-field">
                                <select name="attribute" id="attribute" class="kb-inpselect">
                                    <option value="0">-</option>
                                </select>
                            </div>    
                        </div>
                        <div class="kb-form-field-block">
                            <div class="form-lbl-indis">
                                <span class="kblabel">&nbsp;</span>
                            </div>
                            <div class="form-field-indis">
                                <button type="button" class="btn btn-info" onclick="add_attr();"><i class="kb-material-icons" ></i> {l s='Add' mod='kbmarketplace'}</button>
                            </div>
                        </div>
                    </li>
                    <li class="kb-form-fwidth">
                        <div class="kb-form-field-block kb-mbtm10">
                            <div class="form-lbl-indis">
                                <span class="kblabel">{l s='Combination' mod='kbmarketplace'}</span>
                            </div>
                            <div class="form-field-block">
                                <select id="product_att_list" name="attribute_combination_list[]" multiple="multiple" class="kb-inpselect" style="height:100px;overflow: auto;"></select>
                            </div>    
                        </div>
                        <div class="kb-form-field-block">
                            <button type="button" class="btn btn-danger" onclick="del_attr()"><i class="kb-material-icons"></i> {l s='Delete' mod='kbmarketplace'}</button>
                        </div>
                    </li>
                    <li class="kb-form-fwidth">
                        <div class="kb-form-label-block">
                            <span class="kblabel">{l s='Reference' mod='kbmarketplace'}</span>
                        </div>
                        <div class="kb-form-field-block">
                            <input type="text" class="kb-inpfield" id="attribute_reference" name="attribute_reference" value="" />
                        </div>
                    </li>
                    <li class="kb-form-fwidth">
                        <div class="kb-form-label-block">
                            <span class="kblabel">{l s='EAN-13 or JAN barcode' mod='kbmarketplace'}</span>
                        </div>
                        <div class="kb-form-field-block">
                            <input maxlength="13" type="text" class="kb-inpfield" id="attribute_ean13" name="attribute_ean13" value="" />
                        </div>
                    </li>
                    <li class="kb-form-fwidth">
                        <div class="kb-form-label-block">
                            <span class="kblabel">{l s='UPC barcode' mod='kbmarketplace'}</span>
                        </div>
                        <div class="kb-form-field-block">
                            <input maxlength="12" type="text" class="kb-inpfield" id="attribute_upc" name="attribute_upc" value="" />
                        </div>
                    </li>
                    <li class="kb-form-fwidth">
                        <div class="kb-form-label-block">
                            <span class="kblabel">{l s='Quantity' mod='kbmarketplace'}</span>
                        </div>
                        <div class="kb-form-field-block">
                            <input type="text" class="kb-inpfield" validate="isInt" id="attribute_stock_available" name="attribute_stock_available" value="0" />
                        </div>
                    </li>
                    <li class="kb-form-fwidth" style="border-bottom:0;">
                        <div class="kb-form-label-block">
                            <span class="kblabel"><i class="kb-material-icons" data-toggle="tooltip" data-placement="top" title="{l s='The next date of availability for this product when it is out of stock.' mod='kbmarketplace'}"></i>{l s='Available Date' mod='kbmarketplace'}</span>
                        </div>
                        <div class="kb-form-field-block">
                            <input type="text" class="kb-inpfield datepicker" validate="isDate" id="available_date_attribute" name="available_date_attribute" value="" />
                        </div>
                    </li>
                    <li class="kb-form-fwidth" style="border-bottom:0;">
                        <div class="kb-form-field-block">
                            <ul id="combination-imgs-list" class="combination-imgs-list">
                                {if $images|count}
                                {foreach from=$images key=k item=image}
                                <li id="combination-form-image_{$image.id_image|intval}">
                                        <input type="checkbox" name="id_image_attr[]" value="{$image.id_image|intval}" id="id_image_attr_{$image.id_image|intval}" />
                                        <label for="id_image_attr_{$image.id_image|intval}">
                                                <img class="img-thumbnail" src="{$smarty.const._THEME_PROD_DIR_|escape:'htmlall':'UTF-8'}{$image.obj->getExistingImgPath()|escape:'htmlall':'UTF-8'}-{$imageType|escape:'htmlall':'UTF-8'}.jpg" alt="{$image.legend|escape:'htmlall':'UTF-8'}" title="{$image.legend|escape:'htmlall':'UTF-8'}" />
                                        </label>
                                </li>
                                {/foreach}
                                {/if}
                            </ul>
                        </div>
                    </li>
                    <li class="kb-form-fwidth" style="border-bottom:0;">
                        <div class="kb-form-field-block">
                            <label for="attribute_default">
                                    <input type="checkbox" name="attribute_default" id="attribute_default" value="1" />
                                    {l s='Make this combination the default combination for this product.' mod='kbmarketplace'}
                            </label>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
        <div class="kb-modal-footer">
            <button id="combination-submit" type="button" class="btn-sm btn-success" data-id="0" onclick="saveCombination()">{l s='Submit' mod='kbmarketplace'}</button>
            <div id="combination-updating-progress" class="input-loader" style="display: none; vertical-align: middle;"></div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var msg_combination_1 = "{l s='Please choose an attribute.' mod='kbmarketplace'}";
    var msg_combination_2 = "{l s='Please choose a value.' mod='kbmarketplace'}";
    var msg_combination_3 = "{l s='You can only add one combination per attribute type.' mod='kbmarketplace'}";
    var msg_combination_4 = "{l s='Before creating combination, you must have to save this product.' mod='kbmarketplace'}";
    var msg_combination_5 = "{l s='Requested combination not found.' mod='kbmarketplace'}";
    var msg_combination_6 = "{l s='Atleast one attribute required.' mod='kbmarketplace'}";
    var attrs = new Array();
    attrs[0] = new Array(0, "---");
    {foreach from=$attributeJs key=idgrp item=group}
            attrs[{$idgrp|intval}] = new Array(0
            , '---'
            {foreach from=$group key=idattr item=attrname}
                    , {$idattr|intval}, '{$attrname|trim}'
            {/foreach}
            );
    {/foreach}
    
    $(document).ready(function(){
            populate_attrs();
    });
    
    function updateCombinationFormImage(id_image, path, legend){
        if($('#combination-imgs-list').length)
        {
            if($('#combination-imgs-list #combination-form-image_'+id_image).length){
                $('#combination-imgs-list #combination-form-image_'+id_image).remove();
            }
            var img_hmtl = '';
            img_hmtl += '<li id="combination-form-image_'+id_image+'">';
            img_hmtl += '<div class="checker" id="uniform-id_image_attr_'+id_image+'"><span class="unchecked"><input type="checkbox" name="id_image_attr[]" value="'+id_image+'" id="id_image_attr_'+id_image+'"></span></div>';
            img_hmtl += '<label for="id_image_attr_'+id_image+'"><img class="img-thumbnail" src="{$smarty.const._THEME_PROD_DIR_|escape:'htmlall':'UTF-8'}'+path+'-cart_default.jpg" alt="'+legend+'" title="'+legend+'"></label>';
            img_hmtl += '</li>';
            $('#combination-imgs-list').append(img_hmtl);
        }
    }
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