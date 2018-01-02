{if is_array($filter_params) && count($filter_params) > 0}
<div id="filter-block-{$filter_id|escape:'htmlall':'UTF-8'}" class="kb-filter-container">
    <div data-toggle="{$filter_id|escape:'htmlall':'UTF-8'}_filter" class="kb-filter-header kb-panel-header-tab">
        {$filter_header|escape:'htmlall':'UTF-8'}
        <div class="kb-accordian-symbol kbexpand"></div>
    </div>
    <div id="{$filter_id|escape:'htmlall':'UTF-8'}_filter" class="kb-form kb-filter-block">
        <ul>
            {foreach $filter_params as $filter}
                {if $filter['type'] == 'select'}
                    {if is_array($filter['values']) && count($filter['values']) > 0}
                        <li>
                            <div class="kb-form-label-block">
                                <span class="kblabel">{$filter['label']|escape:'htmlall':'UTF-8'}:{if isset($filter['is_required']) && $filter['is_required'] eq true}<em>*</em>{/if}</span>
                            </div>
                            <div class="kb-form-field-block">
                                <select name="{$filter['name']|escape:'htmlall':'UTF-8'}" class="kb-inpselect {if isset($filter['class'])}{$filter['class']|escape:'htmlall':'UTF-8'}{/if} {if isset($filter['is_required']) && $filter['is_required'] eq true}required{/if}" {if isset($filter['validate']) && $filter['validate'] neq null}validate="{$filter['validate']|escape:'htmlall':'UTF-8'}"{/if}>
                                    {if isset($filter['placeholder']) && $filter['placeholder'] neq ''}
                                        <option value=''>{$filter['placeholder']|escape:'htmlall':'UTF-8'}</option>
                                    {/if}
                                    {foreach $filter['values'] as $val}
                                        <option {if isset($filter['default']) && $filter['default'] == $val['value']}selected="selected"{/if} value='{$val['value']|escape:'htmlall':'UTF-8'}'>{$val['label']|escape:'htmlall':'UTF-8'}</option>
                                    {/foreach}
                                </select>
                            </div>
                        </li>     
                    {/if}
                {elseif $filter['type'] == 'text'}
                    <li>
                        <div class="kb-form-label-block">
                            <span class="kblabel">{$filter['label']|escape:'htmlall':'UTF-8'}:{if isset($filter['is_required']) && $filter['is_required'] eq true}<em>*</em>{/if}</span>
                        </div>
                        <div class="kb-form-field-block">
                            <input 
                                {if isset($filter['placeholder']) && $filter['placeholder'] neq ''}placeholder="{$filter['placeholder']|escape:'htmlall':'UTF-8'}"{/if}
                                value="{if isset($filter['default']) && $filter['default'] neq ''}{$filter['default']|escape:'htmlall':'UTF-8'}{/if}" 
                                type="text" 
                                name="{$filter['name']|escape:'htmlall':'UTF-8'}" class="kb-inpfield {if isset($filter['class'])}{$filter['class']|escape:'htmlall':'UTF-8'}{/if} {if isset($filter['is_required']) && $filter['is_required'] eq true}required{/if}" {if isset($filter['validate']) && $filter['validate'] neq null}validate="{$filter['validate']|escape:'htmlall':'UTF-8'}"{/if} />
                        </div>
                    </li>
                {elseif $filter['type'] == 'hidden'}
                    <input type="hidden"  name="{$filter['name']|escape:'htmlall':'UTF-8'}" value="{if isset($filter['default']) && $filter['default'] neq ''}{$filter['default']|escape:'htmlall':'UTF-8'}{/if}"
                {/if}
                
            {/foreach}
        </ul>
        <div class="kb-filter-action-btn">
            <input id='kb_filter_action_{$filter_id|escape:'htmlall':'UTF-8'}' type='hidden' name='kb_filter_action_{$filter_id|escape:'htmlall':'UTF-8'}' value='{$filter_action_name|escape:'htmlall':'UTF-8'}' />
            <button type="button" class="kbbtn kbbtn-success" onclick="KbFilterList('{$filter_id|escape:'htmlall':'UTF-8'}')">{l s='Search' mod='kbmarketplace'}</button>
            <button type="button" class="kbbtn btn-warning" onclick="resetKbFilters('{$filter_id|escape:'htmlall':'UTF-8'}')">{l s='Reset' mod='kbmarketplace'}</button>
            <div id="uploading-progress" class="input-loader" style="vertical-align: middle; display:none;"></div>
        </div>
    </div>
</div>
{/if}
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