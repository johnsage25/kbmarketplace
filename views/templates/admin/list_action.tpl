<a 
    {if isset($display_popup) && $display_popup} href="#marketplace-reason-modal" class="marketplace-reason-modal" data-url="{$href|escape:'htmlall':'UTF-8'}" {else}href="{$href|escape:'htmlall':'UTF-8'}"{/if}
    title="{$action|escape:'htmlall':'UTF-8'}" 
    {if isset($display_popup) && $display_popup}
        {if isset($popup_show) && $popup_show}
        onclick="disapproveWithConfirmation(this)"
            {else}
              onclick="deleteWithConfirmation(this)"  
                {/if}
    
    {/if}
    {if isset($display_confirm_popup) && $display_confirm_popup} onclick="approveWithConfirmation(this)" {/if}
    {if isset($separate_tab) && $separate_tab} target="_blank" {/if}
    >
	<i class="{$icon|escape:'htmlall':'UTF-8'}"></i> {$action|escape:'htmlall':'UTF-8'}
</a>
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