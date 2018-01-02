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
{if isset($seller_footer_link_arr) && count($seller_footer_link_arr) > 0}
    {foreach $seller_footer_link_arr as $sl}
        {if isset($sl['confirm'])}
            <li>
                <a href="javascript:void(0)" title="{$sl.label}" rel="nofollow" onclick="if(confirm('{$sl.confirm.message}')){ location.href= '{$sl.href nofilter}' }">
                  {$sl.label}
                </a>
            </li>
            
        {else}
            <li>
                <a href="{$sl.href nofilter}" title="{$sl.label}" rel="nofollow">
                  {$sl.label}
                </a>
            </li>
        {/if}
    {/foreach}
{/if}