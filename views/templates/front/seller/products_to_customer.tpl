<div >
    <h1 class="page-heading">
        <span clas="">{$kb_page_title}</span>
        <div class="clearfix"></div>
    </h1>
    <section class="tabs page-product-box slr-f-box">
        <h3 class="page-product-heading s-p-filter">
            <form action="{$filter_form_action nofilter}" method="post" id="seller_products_form">
            <ul>
                <li class="heading">{l s='Filter your search' mod='kbmarketplace'}: </li>
                {if isset($category_list) && count($category_list) > 0}
                <li>
                    <select name="s_filter_category" onchange="$('#seller_product_pagination_var').val(0);$('#seller_products_form').submit();">
                        <option value="">{l s='Category' mod='kbmarketplace'}</option>
                        {foreach $category_list as $cat}
                            <option value="{$cat['id_category']|intval}" {if $selected_category == $cat['id_category']}selected="selected"{/if} >{$cat['name']}</option>
                        {/foreach}
                    </select>
                </li>
                {/if}
                <li>
                    <select name="s_filter_sortby" onchange="$('#seller_product_pagination_var').val(0);$('#seller_products_form').submit();">
                        <option value="">{l s='Sort By' mod='kbmarketplace'}</option>
                        <option value="pl.name:ASC" {if $selected_sort == 'pl.name:ASC'}selected="selected"{/if} >{l s='Name (A - Z)' mod='kbmarketplace'}</option>
                        <option value="pl.name:DESC" {if $selected_sort == 'pl.name:DESC'}selected="selected"{/if} >{l s='Name (Z - A)' mod='kbmarketplace'}</option>
                        <option value="p.price:ASC" {if $selected_sort == 'p.price:ASC'}selected="selected"{/if} >{l s='Price(low to high)' mod='kbmarketplace'}</option>
                        <option value="p.price:DESC" {if $selected_sort == 'p.price:DESC'}selected="selected"{/if} >{l s='Price(high to low)' mod='kbmarketplace'}</option>
                    </select>
                </li>
            </ul>
            <input type="hidden" name="page_number" value="{$seller_product_current_page|intval}" id="seller_product_pagination_var"/>
            </form>
            {if isset($pagination_string)}<div id="svp-p-count" class="svp-p-count">{$pagination_string nofilter}</div>{/if}
            <div class="clearfix"></div>
        </h3>
        {if isset($pagination) && $pagination neq ''}
        <div class="sv-p-paging">
            {$pagination nofilter}
            <div class='clearfix'></div>
        </div>
        {/if}
        <div id="seller_products_to_customer" class="slr-content">
            {if count($products) > 0}
                <section id="main">
                    <section id="products">
                        <div class="products row">
                            {foreach from=$products item="product"}
                                {block name='product_miniature'}
                                  {include file='catalog/_partials/miniatures/product.tpl' product=$product}
                                {/block}
                            {/foreach}            
                        </div>
                    </section>
                </section>
            {else}
                <div class="alert alert-warning">
                    {l s='No product is available for sale from this seller' mod='kbmarketplace'}
                </div>
            {/if}
        </div>
        {if isset($kb_pagination.pagination) && $kb_pagination.pagination neq ''}
        <div class="sv-p-paging" style='padding-bottom:0;'>
            {$kb_pagination.pagination nofilter}
            <div class='clearfix'></div>
        </div>
        {/if}
    </section>
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