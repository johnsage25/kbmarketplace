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

{if isset($kb_displaynav1_links) && count($kb_displaynav1_links) > 0}
    <style>
        #_desktop_contact_link, #kb_displaynav1_links_container{
            display: inline-table;
        }
        
        .display_nav1_link{
            display: table-cell;
            padding: 0 5px;
        }
    </style>
    <div id="kb_displaynav1_links_container">
        {foreach $kb_displaynav1_links as $sl}
            <div class="display_nav1_link">
            {if isset($sl['confirm'])}
                <a href="javascript:void(0)" title="{$sl.title}" rel="nofollow" onclick="if(confirm('{$sl.confirm.message}')){ location.href= '{$sl.href nofilter}' }">
                    {$sl.label}
                </a>
            {else}
                <a href="{$sl.href nofilter}" title="{$sl.title}" rel="nofollow">
                    {$sl.label}
                </a>
            {/if}
            </div>
        {/foreach}    
    </div>
{/if}
{if isset($cart_url) && $cart_url !=''}
    <input type="hidden" id="allow_free_shipping" value="{$allow_free_shipping}"/>
    <input type="hidden" id="cart_rule_url" value="{$cart_url}"/>
{/if}