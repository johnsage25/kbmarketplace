<div class="multiaction-group">
    <form action="{$kb_multiaction_params['submit_action']|escape:'htmlall':'UTF-8'}" method="post" id="{$kb_multiaction_params['multiaction_related_to_table']|escape:'htmlall':'UTF-8'}_multiaction_form" >
    <span class="multiaction-label">{l s='Action' mod='kbmarketplace'}: </span>
    <div class="multiaction-field">
        <select id="{$kb_multiaction_params['multiaction_related_to_table']|escape:'htmlall':'UTF-8'}-multiaction-type" name="mutiaction_type" class="" {if $kb_multiaction_params['has_status_dropdown']}onchange="showMultiactionStatusList(this, '{$kb_multiaction_params['multiaction_related_to_table']|escape:'htmlall':'UTF-8'}')"{/if}>
            <option value="">{l s='Select action' mod='kbmarketplace'}</option>
            {if count($kb_multiaction_params['multiaction_values']) > 0}
                {foreach $kb_multiaction_params['multiaction_values'] as $val}
                    <option value="{$val['value']|escape:'htmlall':'UTF-8'}">{$val['label']|escape:'htmlall':'UTF-8'}</option>
                {/foreach}
            {/if}
        </select>
    </div>
    {if $kb_multiaction_params['has_status_dropdown']}
    <div id="{$kb_multiaction_params['multiaction_related_to_table']|escape:'htmlall':'UTF-8'}-status-list" class="multiaction-field" style="display:none;">
        <select name="mutiaction_status_list" class="">
            <option value="">{l s='Select status' mod='kbmarketplace'}</option>
            {if count($kb_multiaction_params['status_dropdown_values']) > 0}
                {foreach $kb_multiaction_params['status_dropdown_values'] as $val}
                    <option value="{$val['value']|escape:'htmlall':'UTF-8'}">{$val['label']|escape:'htmlall':'UTF-8'}</option>
                {/foreach}
            {/if}
        </select>
    </div>
    <input type="hidden" name="status_dropdown_value" class="{$kb_multiaction_params['multiaction_related_to_table']|escape:'htmlall':'UTF-8'}_status_drop_value" value="{$kb_multiaction_params['show_status_on_multiaction_value']|escape:'htmlall':'UTF-8'}" />
    {/if}
    {if isset($kb_multiaction_params['carrier_id'])}
    <input type="hidden" name="id_carrier" value="{$kb_multiaction_params['carrier_id']|escape:'htmlall':'UTF-8'}" />
    {/if}
    <input type="hidden" name="selected_table_item_ids" class="{$kb_multiaction_params['multiaction_related_to_table']|escape:'htmlall':'UTF-8'}_selected_items" value="" />
    <a href="javascript:void(0)" onclick="{$kb_multiaction_params['submit_function']|escape:'htmlall':'UTF-8'}('{$kb_multiaction_params['multiaction_related_to_table']|escape:'htmlall':'UTF-8'}')" class="kbbtn btn-info" title="{l s='submit action' mod='kbmarketplace'}">{l s='Submit' mod='kbmarketplace'}</a>
    <div class="clearfix"></div>
    {if $kb_multiaction_params['has_reason_popup']}
        <div id="{$kb_multiaction_params['multiaction_related_to_table']|escape:'htmlall':'UTF-8'}_reason_modal" style="display:none;">
            <div class="kb-overlay"></div>
            <div class="kb-modal">
                <div class="kb-modal-header">
                    <h1 id='kb_combination_form_title'>{l s='WHY YOU WANT DO THIS?' mod='kbmarketplace'}</h1>
                    <span class="kb-modal-close" data-modal="{$kb_multiaction_params['multiaction_related_to_table']|escape:'htmlall':'UTF-8'}_reason_modal">X</span>
                </div>
                <div class="kb-modal-content kb-form">
                    <ul class="kb-form-list">
                        <li class="kb-form-fwidth">
                            <div class="kb-form-label-block">
                                <span class="kblabel">{l s='Provide Your Reason' mod='kbmarketplace'}</span>
                            </div>
                            <div class="kb-form-field-block">
                                <textarea rows="5" class="kb-inptexarea multiaction_reason" name="reason"></textarea>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="kb-modal-footer">
                    <button type="button" class="kbbtn-big kbbtn-success" data-id="0" onclick="submitMultiactionReason('{$kb_multiaction_params['multiaction_related_to_table']|escape:'htmlall':'UTF-8'}')">{l s='Submit' mod='kbmarketplace'}</button>
                </div>
            </div>
        </div>
    {/if}
    </form>
</div>
<script>
    function showMultiactionStatusList(e, block_id){
	var kb_multiaction_status_value = $('input[type="hidden"].'+block_id+'_status_drop_value').val();
	if($(e).val() == kb_multiaction_status_value)
	{
	    $('#'+block_id+'-status-list').show();
	}else{
	    $('#'+block_id+'-status-list').hide();
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