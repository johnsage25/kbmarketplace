<div class="kb-vspacer5"></div>
<div class="kb-panel outer-border kb_product_section">
    <div data-toggle="kb-product-form-virtual" class='kb-panel-header kb-panel-header-tab'>
        <h1>{$form_title|escape:'htmlall':'UTF-8'}</h1>
        <div class='kb-accordian-symbol kbexpand'><i class="kb-material-icons">&#xe145;</i></div>
        <div class='clearfix'></div>
    </div>
    <div id="kb-product-form-virtual" class='kb-panel-body'>
        <div id="virtual-section-msg" class="kbalert" style="display: none; margin-bottom:10px;"></div>
        <div class="kb-block kb-form">
            <input type="hidden" id="virtual_product_filename" name="virtual_product_filename" value="{$virtual_product_filename|escape:'htmlall':'UTF-8'}" />
            <ul class="kb-form-list">
                <li class="kb-form-fwidth is_virtual_good" style="display:none;">
                    <div class="kb-form-field-inblock">
                        <div class="kboption-inline kb-inpoption">
                            <input class="" type="checkbox" id="is_virtual_good" name="is_virtual_good" value="true" {if $is_virtual && $download_active}checked="checked"{/if} /> <label  for="is_virtual_good">{l s='Is this a virtual product' mod='kbmarketplace'}?</label>    
                        </div>
                    </div>
                </li>
                <li class="kb-form-fwidth inerit-h">
                    <div class="kb-form-label-inblock">
                        <span class="kblabel ">{l s='Does this product have an associated file' mod='kbmarketplace'}?</span>
                    </div>
                    <div class="kb-form-field-inblock">
                        <div class="kboption-inline kb-inpoption">
                            <input class="" type="radio" id="label_for_has_downloade_file_yes" name="is_virtual_file" value="1" {if $product_downloaded} checked="checked"{/if} /> <label for="label_for_has_downloade_file_yes">{l s='Yes' mod='kbmarketplace'}</label>    
                        </div>
                        <div class="kboption-inline kb-inpoption">
                            <input class="" type="radio" id="label_for_has_downloade_file_no" name="is_virtual_file" value="0" {if !$product_downloaded} checked="checked"{/if} /> <label  for="label_for_has_downloade_file_no">{l s='No' mod='kbmarketplace'}</label>    
                        </div>
                    </div>
                </li>
                <li class="kb-form-fwidth inerit-h last-row virtual_good virtual_file_product">
                    {if $download_product_file_missing}
                        <div class="kbalert kbalert-danger"><i class="icon-exclamation-sign"></i>
                            {$download_product_file_missing|escape:'htmlall':'UTF-8'} :<br/>
                            <strong>{l s='Server file name : %s' sprintf=array($virtual_product_filename) mod='kbmarketplace'}</strong>
                        </div>
                    {/if}
                    {if !$download_dir_writable}
                        <div class="kbalert kbalert-info"><i class="kb-material-icons"></i>
                            {l s='Your download repository is not writable.' mod='kbmarketplace'}
                        </div>
                    {/if}
                </li>
                {* Don't display file form if the product has combinations *}
                {if empty($product->cache_default_attribute)}
                <li id="virtual-upload-section" class="kb-form-fwidth virtual_good virtual_file_product" style="display:none;">
                    <div class="kb-form-label-block">
                        <span class="kblabel ">{l s='File' mod='kbmarketplace'}</span>
                    </div>
                    <div class="kb-form-field-block">
                        <div class="kb-labeled-inpfield">
                            <input id="virtual_product_file_uploader" type="file" name="virtual_product_file_uploader" style="display:none;" />
                            <input type="text" id="virtual_product_file_uploader-name" class="kb-inpfield lsyd" name="filename" readonly="true" />
                            <span class="inplbl-btn rsyd" style="border-top-right-radius: 0;border-bottom-right-radius: 0;">
                                <a id="virtual-file-upload-trigger" style="padding-top:5px;padding-bottom:9px;border-radius:0;" href="javascript:void(0)" class="btn-sm btn-warning" ><i class="kb-material-icons"></i><span>{l s='Upload File..' mod='kbmarketplace'}</span></a>
                            </span>
                        </div>
                        <div id="virtual-upload-error" class="kb-validation-error" style="display:none;"></div>
                    </div>
                </li>
                <li class="{if $is_file}kb-form-l{else}kb-form-fwidth{/if} virtual_good virtual_file_product">
                    {if $download_id}
                            <input type="hidden" id="virtual_product_id" name="virtual_product_id" value="{$download_id|intval}" />
                    {/if}
                    <div class="kb-form-label-block">
                        <span class="kblabel "><i class="kb-material-icons" data-toggle="tooltip" data-placement="top" title="{l s='The full filename with its extension (e.g. Book.pdf)' mod='kbmarketplace'}"></i>{l s='File Name' mod='kbmarketplace'}<em>*</em></span>
                    </div>
                    <div class="kb-form-field-block">
                        <input class="kb-inpfield" type="text" id="virtual_product_name" name="virtual_product_name" value="{$display_filename|escape:'htmlall':'UTF-8'}" />
                    </div>
                </li>
                {if $is_file}
                    <li class="kb-form-r virtual_good virtual_file_product">
                        <div class="kb-form-label-block">
                            <span class="kblabel ">&nbsp;<!--{l s='Link to the file' mod='kbmarketplace'}--></span>
                        </div>
                        <div class="kb-form-field-block">
                            <div class="uploaded-img-action">
                                <a href="javascript:void(0)" class="btn-sm btn-danger" onclick="deleteVirtualFile()"><i class="kb-material-icons"></i><span>{l s='Delete this file' mod='kbmarketplace'}</span></a>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </li>
                {/if}
                <li class="kb-form-l virtual_good virtual_file_product">
                    <div class="kb-form-label-block">
                        <span class="kblabel "><i class="kb-material-icons" data-toggle="tooltip" data-placement="top" title="{l s='Number of downloads allowed per customer. Set to 0 for unlimited downloads.' mod='kbmarketplace'}"></i>{l s='No. of Downloads' mod='kbmarketplace'}</span>
                    </div>
                    <div class="kb-form-field-block">
                        <input type="text" class="kb-inpfield" validate="isInt" id="virtual_product_nb_downloable" name="virtual_product_nb_downloable" value="{$nb_downloadable|intval}" size="6" />
                    </div>
                </li>
                <li class="kb-form-r virtual_good virtual_file_product">
                    <div class="kb-form-label-block">
                        <span class="kblabel "><i class="kb-material-icons" data-toggle="tooltip" data-placement="top" title="{l s='If set, the file will not be downloadable after this date. Leave blank if you do not wish to attach an expiration date.' mod='kbmarketplace'}"></i>{l s='Expiration Date' mod='kbmarketplace'}</span>
                    </div>
                    <div class="kb-form-field-block">
                        <input type="text" class="kb-inpfield datepicker" validate="isDate" id="virtual_product_expiration_date" name="virtual_product_expiration_date" value="{$date_expiration|escape:'htmlall':'UTF-8'}" size="11" maxlength="10" autocomplete="off" />
                    </div>
                </li>
                <li class="kb-form-fwidth virtual_good virtual_file_product">
                    <div class="kb-form-label-block">
                        <span class="kblabel "><i class="kb-material-icons" data-toggle="tooltip" data-placement="top" title="{l s='Number of days this file can be accessed by customers. Set to zero for unlimited access.)' mod='kbmarketplace'}"></i>{l s='No. of Days' mod='kbmarketplace'}</span>
                    </div>
                    <div class="kb-form-field-block inpfwidth" style="width:47.4%">
                        <input type="text" class="kb-inpfield" validate="isInt" name="virtual_product_nb_days" value="{if !$nb_days_accessible}0{else}{$nb_days_accessible|intval}{/if}" size="4" />
                    </div>
                </li>
                {else}
                    <li class="kb-form-fwidth virtual_good virtual_file_product last-row">
                        <div class="kbalert kbalert-info"><i class="kb-material-icons"></i>
                            {l s='You cannot edit your file here because you used combinations. Please edit this file in the Combinations tab.' mod='kbmarketplace'}
                        </div>
                        {if isset($error_product_download)}{$error_product_download|escape:'htmlall':'UTF-8'}{/if}
                    </li>
                {/if}
                {hook h="displayKbMarketPlacePForm" product_id=$id_product type=$product_type form="virtual"}
            </ul>
        </div>
    </div>
</div>
<script type="text/javascript">
	var newLabel = "{l s='New label' mod='kbmarketplace'}";
	var choose_language = "{l s='Choose language:' mod='kbmarketplace'}";
	var required = "{l s='Required' mod='kbmarketplace'}";
	var customizationUploadableFileNumber = '{$uploadable_files|intval}';
	var customizationTextFieldNumber = '{$text_fields|intval}';
	var uploadableFileLabel = 0;
	var textFieldLabel = 0;
        
        $(document).ready(function(){
            {if !$download_id || !$download_active}
                    $('.virtual_good').hide();
            {/if}
                
            $('input[name="is_virtual_file"]').on('change', function(){
                if($(this).val() == 1)
                    $('.virtual_file_product').show();
                else
                    $('.virtual_file_product').hide();
            });
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