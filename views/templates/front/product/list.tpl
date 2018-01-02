<div class="kb-content">
    <div class="kb-content-header">
        <h1>{l s='Products' mod='kbmarketplace'}</h1>
        <div class="kb-content-header-btn">
            <a href="{$new_product_link|escape:'htmlall':'UTF-8'}" class="btn-sm btn-success" title="{l s='click to add new product' mod='kbmarketplace'}"><i class="kb-material-icons"></i>{l s='Add New' mod='kbmarketplace'}</a>
        </div>
        <div class="clearfix"></div>
    </div>
    
    {if isset($kbfilter)}
        {$kbfilter nofilter}
    {/if}
    
    {if isset($kbmutiaction)}
        {$kbmutiaction nofilter}
    {/if}
    
    {if isset($kblist)}
        <div class="kb-vspacer5"></div>
        {$kblist nofilter}
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
