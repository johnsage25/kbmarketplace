<div class="kb-content">
    {if !isset($permission_error)}
    <div class="kb-content-header">
        <h1>{$product_form_heading}</h1>
        <div class="clearfix"></div>
    </div>
    {/if}
    {if $step eq 1}
        {include file=$product_template_dir|cat:"product_type.tpl"}
    {elseif $step eq 2}
        {if !isset($permission_error)}
        <form id="kb-product-form" action="{$form_submit_url nofilter}" method="post" enctype="multipart/form-data">
            <input type="hidden" name="productformkey" value="{$formkey}" />
            <input type="hidden" name="id_product" value="{$id_product|intval}" />
            <div id="kb-product-form-global-msg" class="kbalert kbalert-danger" style="display:none;"><i class="icon-exclamation-sign"></i></div>
            {if $id_product > 0}
            <div class="kbbtn-group kb-tright">
                <a href="{$duplicate_link nofilter}" id="kb_product_duplicate_btn" class="btn-sm btn-info" title="{l s='click to clone this product' mod='kbmarketplace'}"><i class="kb-material-icons"></i>{l s='Duplicate' mod='kbmarketplace'}</a>
                <a href="javascript:void(0)" onclick='{$delete_link_js nofilter}' class="btn-sm btn-danger" title="{l s='click to delete this product' mod='kbmarketplace'}"><i class="kb-material-icons"></i>{l s='Delete' mod='kbmarketplace'}</a>
            </div>
            {/if}
            <div class="kbalert kbalert-info">
                <i class="kb-material-icons"></i>{l s='Fields marked with (*) are mandatory fields.' mod='kbmarketplace'}
            </div>
            {if count($tabs_display) > 0}
                {foreach $tabs_display as $tab_form}
                    {$tab_form nofilter}
                {/foreach}
            {/if}
            {hook h="displayKbMarketPlacePForm" product_id=$id_product type=$product_type form="parentform"}
            <div class='kb-vspacer5'></div>
            <br>
            <input id="kb_submission_type" type="hidden" name="submitType" value="save" />
            <input type="hidden" id="kb_product_type" name="type_product" value="{$product_type|intval}" />
            <a href="javascript:void(0)" class='btn-sm btn-info' id="submit_product_form_butn" onclick="submitProductForm('savenstay')">{l s='Save and Stay' mod='kbmarketplace'}</a>
            <a href="javascript:void(0)" class='btn-sm btn-success' id="submit_product_form_butn" onclick="submitProductForm('save')">{l s='Save' mod='kbmarketplace'}</a>
        </form>
        <script>
            var kb_id_product = {$id_product|intval};
            var kb_editor_lang = '{$editor_lang}';
            var kb_default_lang = {$default_lang|intval};
            var kb_form_validation_error = '{l s='Please fill the detail with valid values.' mod='kbmarketplace'}';
            var kb_img_format = [];

            {foreach $kb_img_frmats as $for}
                kb_img_format.push('{$for|escape:'htmlall':'UTF-8'}');
            {/foreach}
                
            var kb_product_types = [];
            var kb_product_type_simple = {$type_simple|intval};
            var kb_product_type_pack = {$type_pack|intval};
            var kb_product_type_virtual = {$type_virtual|intval};
            kb_product_types.push(kb_product_type_simple, kb_product_type_pack, kb_product_type_virtual);    
        </script>
        {/if}
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