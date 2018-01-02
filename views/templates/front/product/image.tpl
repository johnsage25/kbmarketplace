<div class="kb-vspacer5"></div>
<div class="kb-panel outer-border kb_product_section">
    <div data-toggle="kb-product-form-image" class='kb-panel-header kb-panel-header-tab'>
        <h1>{$form_title|escape:'htmlall':'UTF-8'}</h1>
        <div class='kb-accordian-symbol kbexpand'><i class="kb-material-icons">&#xe145;</i></div>
        <div class='clearfix'></div>
    </div>
    <div id="kb-product-form-image" class='kb-panel-body'>
        {if $id_product eq 0}
            <div class="kbalert kbalert-warning pack-empty-warning">
                <i class="kb-material-icons" style="margin-right:0;"></i> {l s='Please save this product, before adding image(s).' mod='kbmarketplace'}
            </div>
        {else}
            <div id="img-upload-error" class="kbalert" style="display: none; margin-bottom:10px;"></div>
            <div class="kbalert kbalert-warning pack-empty-warning" style="display: block; margin:0;">
                <i class="kb-material-icons" style="margin-right:0;"></i> {l s='You can upload image of maximum size' mod='kbmarketplace'}: {$max_image_size|escape:'htmlall':'UTF-8'} MB<br/>
            </div>
            <div class="kb-block kb-form">
                <ul class="kb-form-list">
                    <li class="kb-form-fwidth">
                        <div class="kb-form-label-block">
                            <span class="kblabel ">{l s='New Image Caption' mod='kbmarketplace'}</span>
                        </div>
                        <div class="kb-form-field-block">
                            <input type="text" class="kb-inpfield" name="legend" value="{$product_name}" />
                        </div>
                    </li>
                    <li id='pro-img-upload-section' class="kb-form-fwidth pro-img-form inerit-h">
                        <div class="kb-form-label-inblock">
                            <span class="kblabel ">{l s='Add new Image to this product' mod='kbmarketplace'}</span>
                        </div>
                        <div class="kb-form-field-inblock">
                                <input id="product-img-uploader-btn" type="file" name="file[]" style="display:none;" multiple="multiple"/>
                                <a id="product-img-uploader" href="javascript:void(0)" class="btn-sm btn-info" ><i class="kb-material-icons"></i><span>{l s='Add Image' mod='kbmarketplace'}..</span></a>
                                <div id="upload-error" class="kb-validation-error" style="display:none;"></div>
                        </div>
                        <div class="kb-vspacer5"></div>
                        <div id="img-upload-info-block" class="kb-form-field-block img-uploder-block" style="display:none;">
                            <div id="img-upload-info-block-container"></div>
                            <div class="uploaded-img-action">
                                <div id="uploading-progress" class="input-loader" style="float:left; display:none;"></div>
                                <a id='star-upload-btn' href="javascript:void(0)" class="btn-sm btn-success" ><i class="kb-material-icons"></i><span>{l s='Upload' mod='kbmarketplace'}</span></a>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </li>
                    <li class="kb-form-fwidth last-row" style='overflow-y: auto'>
                        <table class="kb-table-list">
                            <thead>
                                <tr class="heading-row">
                                    <th style="width:90px;">{l s='Image' mod='kbmarketplace'}</th>
                                    <th style="width:60%;">{l s='Caption' mod='kbmarketplace'}</th>
                                    <th width="60">{l s='Position' mod='kbmarketplace'}</th>
                                    <th width="60">{l s='Cover' mod='kbmarketplace'}</th>
                                    <th width="100"></th>
                                </tr>
                            </thead>
                            <tbody id="product-images">
                                {if count($images) == 0}
                                    <tr><td colspan="5" class="kb-tcenter kb-empty-table">{l s='Images not found for this product.' mod='kbmarketplace'}</td></tr>
                                {/if}
                            </tbody>
                        </table>
                        <table class="kb-table-list" style="display:none;">
                            <tbody id="lineType">
                                <tr id="image_id">                        
                                    <td class="kb-tcenter">
                                        <input type="hidden" class="img-caption" name="product_img[image_id][id]" value="image_id" />
                                        <a class="img-clickable-tag" href="#" ><img src="" class="pro-img-smallprev"/></a>
                                    </td>
                                    <td class="kb-tcenter"><input type="text" class="img-caption" name="product_img[image_id][legend_{$default_lang|intval}]" value="image_legend" /></td>
                                    <td class="kb-tcenter"><input type="text" class="img-position" name="product_img[image_id][position]" value="image_position" /></td>
                                    <td class="kb-tcenter"><input type="radio" name="product_img_default_cover" value="image_id" /></td>
                                    <td class="kb-tcenter"><button type="button" class="btn-sm btn-danger-outline" data-id="image_id" onclick="deleteImage(this)"><i class="kb-material-icons"></i></button></td>
                                </tr>    
                            </tbody>
                        </table>
                        <input type="hidden" id="kb_product_image_new_line_path" value="{$smarty.const._THEME_PROD_DIR_ nofilter}image_path.jpg" />
                    </li>
                </ul>
            </div>
        {/if}
        {hook h="displayKbMarketPlacePForm" product_id=$id_product type=$product_type form="image"}
    </div>
</div>
<script type="text/javascript">
    var kb_product_default_category = {$id_default_category|intval};
    var kb_max_img_upload_size = {$max_image_size|intval};
    var kb_img_format_error = '{l s='Image format is not supported' mod='kbmarketplace'}';
    var kb_img_save_error = '{l s='Before adding images, you must have to save this product.' mod='kbmarketplace'}';
    var kb_no_image_msg = '{l s='Images not found for this product.' mod='kbmarketplace'}';
    
    $(document).ready(function(){ 
        {if count($images) > 0}
            {foreach from=$images item=image}
                    displayNewLineImage({$image->id|intval}, "{$image->getExistingImgPath() nofilter}", {$image->position|intval}, {$image->cover|escape:'htmlall':'UTF-8'}, "{$image->legend[$default_lang]|escape:'htmlall':'UTF-8'}");
            {/foreach}
        {/if}
    });
    
    
    
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