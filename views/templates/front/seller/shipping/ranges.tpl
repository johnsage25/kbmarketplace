<div id="zone_ranges" class="kb-form-field-block" style='overflow-y: auto;'>
<table id="zones_table" class="kb-table-list">
    <tbody id="shipping_ranges_body">
        <tr class="range_inf">
            <td class="range_type"></td>
            <td class="range_symbol">>=</td>
            {foreach from=$ranges key=r item=range}
            <td>
                <div class="kb-labeled-inpfield input-group">
                    <span class="inplbl weight_unit" style="display:{if $carrier->shipping_method == $shipping_method_weight}table-cell{else}none{/if};">{$ps_weight_unit nofilter}</span>
                    <span class="inplbl price_unit" style="display:{if $carrier->shipping_method == $shipping_method_price}table-cell{else}none{/if};">{$ps_currency_unit nofilter}</span>
                    <input class="kb-inpfield" validate="isPrice" name="range_inf[{$range.id_range|intval}]" type="text" value="{number_format($range.delimiter1, 6,'.','')}" />
                </div>
            </td>
            {foreachelse}
            <td>
                <div class="kb-labeled-inpfield input-group">
                    <span class="inplbl weight_unit" style="display:{if $carrier->shipping_method == $shipping_method_weight}table-cell{else}none{/if};">{$ps_weight_unit nofilter}</span>
                    <span class="inplbl price_unit" style="display:{if $carrier->shipping_method == $shipping_method_price}table-cell{else}none{/if};">{$ps_currency_unit nofilter}</span>
                    <input class="kb-inpfield" validate="isPrice" name="range_inf[0]" type="text" value="0" />
                </div>
            </td>
            {/foreach}
        </tr>
        <tr class="range_sup">
            <td class="range_type"></td>
            <td class="range_symbol"><</td>
            {foreach from=$ranges key=r item=range}
            <td class="range_data">
                <div class="kb-labeled-inpfield input-group">
                    <span class="inplbl weight_unit" style="display:{if $carrier->shipping_method == $shipping_method_weight}table-cell{else}none{/if};">{$ps_weight_unit nofilter}</span>
                    <span class="inplbl price_unit" style="display:{if $carrier->shipping_method == $shipping_method_price}table-cell{else}none{/if};">{$ps_currency_unit nofilter}</span>
                    <input class="kb-inpfield" name="range_sup[{$range.id_range|intval}]" type="text" value="{if isset($change_ranges) && $range.id_range == 0} {else}{number_format($range.delimiter2, 6,'.','')}{/if}" autocomplete="off"/>
                </div>
            </td>
            {foreachelse}
            <td class="range_data">
                <div class="kb-labeled-inpfield input-group">
                    <span class="inplbl weight_unit" style="display:{if $carrier->shipping_method == $shipping_method_weight}table-cell{else}none{/if};">{$ps_weight_unit nofilter}</span>
                    <span class="inplbl price_unit" style="display:{if $carrier->shipping_method == $shipping_method_price}table-cell{else}none{/if};">{$ps_currency_unit nofilter}</span>
                    <input class="kb-inpfield" name="range_sup[0]" type="text" value="0" autocomplete="off" />
                </div>
            </td>
            {/foreach}
        </tr>
        <tr class="fees_all">
            <td>
                <span class="fees_all" {if $ranges|count == 0}style="display:none" {/if}>All</span>
            </td>
            <td style="">
                <input type="checkbox" onclick="checkAllZones(this);" class="form-control">
            </td>
            {foreach from=$ranges key=r item=range}
            <td class=" {if $range.id_range != 0} validated {/if}">
                <div class="kb-labeled-inpfield input-group">
                    <span class="inplbl currency_sign"{if $range.id_range == 0} style="display:none" {/if}>{$ps_currency_unit nofilter}</span>
                    <input class="kb-inpfield" type="text" autocomplete="off" {if $range.id_range == 0} style="display:none"{/if} />
                </div>
            </td>
            {foreachelse}
            <td class="">
                <div class="kb-labeled-inpfield input-group">
                    <span class="inplbl" style="display:none">{$ps_currency_unit nofilter}</span>
                    <input class="kb-inpfield" type="text" autocomplete="off" style="display:none" />
                </div>
            </td>
            {/foreach}
        </tr>
        {foreach from=$zones key=i item=zone}
            <tr class="fees" data-zoneid="{$zone.id_zone|intval}">
                <td>
                    <label for="zone_{$zone.id_zone|intval}">{$zone.name}</label>
                </td>
                <td class="zone">
                    <input class="form-control input_zone" id="zone_{$zone.id_zone|intval}" name="zone_{$zone.id_zone|intval}" value="1" type="checkbox" {if isset($fields_value['zones'][$zone.id_zone]) && $fields_value['zones'][$zone.id_zone]} checked="checked"{/if}/>
                </td>
                {foreach from=$ranges key=r item=range}
                <td>
                    <div class="kb-labeled-inpfield input-group">
                        <span class="inplbl" >{$ps_currency_unit}</span>
                        <input class="kb-inpfield" name="fees[{$zone.id_zone|intval}][{$range.id_range|intval}]" type="text"
                        {if !isset($fields_value['zones'][$zone.id_zone]) || (isset($fields_value['zones'][$zone.id_zone]) && !$fields_value['zones'][$zone.id_zone])} disabled="disabled"{/if}

                        {if isset($price_by_range[$range.id_range][$zone.id_zone]) && $price_by_range[$range.id_range][$zone.id_zone] && isset($fields_value['zones'][$zone.id_zone]) && $fields_value['zones'][$zone.id_zone]} value="{number_format($price_by_range[$range.id_range][$zone.id_zone], 6,'.','')}" {else} value="" {/if} />
                    </div>
                </td>
                {/foreach}
            </tr>
        {/foreach}
        <tr class="delete_range">
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            {foreach from=$ranges name=ranges key=r item=range}
                {if $smarty.foreach.ranges.first}
                    <td>&nbsp;</td>
                {else}
                    <td>
                        <button class="btn btn-default">{l s='Delete' mod='kbmarketplace'}</button>
                    </td>
                {/if}
            {/foreach}
        </tr>
    </tbody>
</table>
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