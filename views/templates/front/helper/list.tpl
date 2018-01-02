{if is_array($table_content) && count($table_content) > 0}
<div class="kb-panel">
    <div id="{$table_id}-panel-body" class='kb-panel-table-body' style="overflow-x:auto; overflow-y:hidden; position:relative;">
        <table class="kb-table-list">
            <thead>
                <tr class="heading-row">
                    {if $table_enable_multiaction}
                        <th class="kb-tcenter" width="60px">
                            <input type="checkbox" class="kb_list_row_checkbox"  onclick="multiactionCheck(this);"/>
{*                            <a href="javascript:void(0)" onclick="checkAll();"><span>Check All</span></a>/<a href="javascript:void(0)" onclick="uncheckAll();"><span>UnCheck All</span></a>*}
                        </th>   
                    {/if}
                    {foreach $table_header as $kb_tb_h}
                        <th {if isset($kb_tb_h['width'])}width="{$kb_tb_h['width']|intval}px"{/if}>{$kb_tb_h['label']}</th>
                    {/foreach}
                </tr>
            </thead>
            <tbody id="{$table_id}_body">
                {foreach $table_content as $product_id => $row}
                <tr>
                    {if $table_enable_multiaction}
                        <td class="kb-tcenter">
                            <input type="checkbox" class="kb_list_row_checkbox" name="row_item_id[]" value="{$product_id|intval}" title="" />
                        </td>
                    {/if}
                    {foreach $row as $cell}
                        <td 
                            class="{if isset($cell['class'])}{$cell['class']}{/if} {if isset($cell['align'])}{$cell['align']}{/if}"
                        >
                            {if isset($cell['input'])}
                                {if $cell['input']['type'] == 'checkbox'}
                                    
                                {elseif $cell['input']['type'] == 'radio'}
                                    <input type="radio" name="{$cell['input']['name']}" value="{$cell['value']}" {if isset($cell['input']['title'])}title="{$cell['input']['title']}"{/if} />
                                {elseif $cell['input']['type'] == 'text'}
                                    <input type="text" name="{$cell['input']['name']}" value="{$cell['value']}" {if isset($cell['input']['title'])}title="{$cell['input']['title']}"{/if} />
                                {elseif $cell['input']['type'] == 'textarea'}
                                    <textarea name="{$cell['input']['name']}" {if isset($cell['input']['title'])}title="{$cell['input']['title']}"{/if}>{$cell['value'] nofilter}</textarea>
                                {elseif $cell['input']['type'] == 'action'}
                                    {if isset($cell['actions']) && count($cell['actions']) > 0}
                                        {foreach $cell['actions'] as $action}
                                            {if isset($action['type']) && $action['type'] eq 'delete'}
                                                <a 
                                                    class="kb_list_action {if isset($action['class']) && $action['class'] neq ''}{$action['class']}{/if}" 
                                                    href="javascript:void(0)"
                                                    data-href="{if isset($action['href'])}{$action['href'] nofilter}{else}{/if}"
                                                    title="{if isset($action['title'])}{$action['title']}{/if}" 
                                                    onclick="actionDeleteConfirmation(this);" 
                                                    {if isset($action['target']) && $action['target'] neq ''}target="{$action['target']}"{/if}
                                                >{l s='Delete' mod='kbmarketplace'}</a>
                                            {elseif isset($action['type']) && $action['type'] eq 'edit'}
                                                <a 
                                                    class="kb_list_action {if isset($action['class']) && $action['class'] neq ''}{$action['class']}{/if}" 
                                                    href="{if isset($action['href'])}{$action['href'] nofilter}{else}javascript:void(0){/if}" 
                                                    title="{if isset($action['title'])}{$action['title']}{/if}" 
                                                    {if isset($action['function'])}onclick="{$action['function']}"{/if} 
                                                    {if isset($action['target']) && $action['target'] neq ''}target="{$action['target']}"{/if}
                                                >{l s='Edit' mod='kbmarketplace'}</a>
                                            {elseif isset($action['type']) && $action['type'] eq 'view'}
                                                <a 
                                                    class="kb_list_action {if isset($action['class']) && $action['class'] neq ''}{$action['class']}{/if}" 
                                                    href="{if isset($action['href'])}{$action['href'] nofilter}{else}javascript:void(0){/if}" 
                                                    title="{if isset($action['title'])}{$action['title']}{/if}"
                                                    {if isset($action['function'])}onclick="{$action['function']}"{/if} 
                                                    {if isset($action['target']) && $action['target'] neq ''}target="{$action['target']}"{/if}
                                                >{l s='View' mod='kbmarketplace'}</a>
                                            {elseif isset($action['type']) && $action['type'] eq 'extra'}
                                                <a 
                                                    class="kb_list_action {if isset($action['class']) && $action['class'] neq ''}{$action['class']}{/if}" 
                                                    href="{if isset($action['href'])}{$action['href'] nofilter}{else}javascript:void(0){/if}" 
                                                    title="{if isset($action['title'])}{$action['title']}{/if}"
                                                    {if isset($action['function'])}onclick="{$action['function']}"{/if} 
                                                    {if isset($action['target']) && $action['target'] neq ''}target="{$action['target']}"{/if}
                                                >{if isset($action['label'])}{$action['label']}{else}{l s='Extra' mod='kbmarketplace'}{/if}</a>
                                            {/if}
                                        {/foreach}
                                    {else}
                                        --
                                    {/if}
                                {/if}
                            {elseif isset($cell['link'])}
                                <a 
                                    href="{if isset($cell['link']['href'])}{$cell['link']['href'] nofilter}{else}javascript:void(0){/if}" 
                                    title="{if isset($cell['link']['title'])}{$cell['link']['title']}{/if}"
                                    {if isset($cell['link']['function'])}onclick="{$cell['link']['function']}"{/if} 
                                    {if isset($cell['link']['target']) && $cell['link']['target'] neq ''}target="{$cell['link']['target']}"{/if}
                                >{$cell['value'] nofilter}</a>
                                {elseif isset($cell['actions'])}
                                    {foreach $cell['actions'] as $action}
                                        <a href='{if isset($action['href'])}{$action['href'] nofilter}{else}javascript:void(0){/if}'
                                            title="{if isset($action['title'])}{$action['title']}{/if}" 
                                            {if isset($action['function'])}onclick="{$action['function']}"{/if} 
                                            {if isset($action['target']) && $action['target'] neq ''}target="{$action['target']}"{/if}
                                            class='btn btn-default kb-multiaction-link'><i class='kb-material-icons kb-multiaction-icon' >{if isset($action['icon-class'])}{$action['icon-class'] nofilter}{/if}</i>
                                        </a>
                                    {/foreach}
                                    
                            {else}
                                {$cell['value'] nofilter}
                            {/if}
                        </td>
                    {/foreach}
                </tr>
                {/foreach}
            </tbody>
        </table>
        <div class="kb-paginator-block">
            {if $kb_pagination}
                {$kb_pagination nofilter}
            {/if}
            <div class="clearfix"></div>
        </div>
        <div class="clearfix"></div>
        <img id="kb-list-loader" src="{$kb_image_path nofilter}loader128.gif" />
    </div>
</div>
{else}
    <div class="kb-panel">
        <div class="kbalert kbalert-warning" style="display: block; margin:0;">
            {l s='List is empty' mod='kbmarketplace'}
        </div>
    </div>
{/if}
<style>
    .kb-multiaction-link {
        border: none !important;
        padding: 2px 6px !important;
    }
</style>
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