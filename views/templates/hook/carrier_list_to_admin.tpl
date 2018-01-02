<script type='text/javascript'>
    var kb_carrier_list_html = '';
    {foreach $kb_avail_carrier_list as $c}
        {if !isset($c.selected) || !$c.selected}
            kb_carrier_list_html += '<option value="{$c.id_reference|intval}">{$c.name|escape:'htmlall':'UTF-8'}</option>';
        {/if}
    {/foreach}
    $(document).ready(function(){
        var kb_html_carr_render = false;
            if($('#availableCarriers').length){
                $('#availableCarriers').html(kb_carrier_list_html);
                kb_html_carr_render = true;
            }else{
                setTimeout(kbAppendCarrierList, 1000);
            }
    });
    
    function kbAppendCarrierList(){
        if($('#availableCarriers').length){
            $('#availableCarriers').html(kb_carrier_list_html);
            kb_html_carr_render = true;
        }else{
            setTimeout(kbAppendCarrierList, 1000);
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