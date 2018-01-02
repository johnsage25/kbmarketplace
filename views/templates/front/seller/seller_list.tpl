<section id="main">
    <section id="products">
        <div class="products row">
            {foreach from=$sellers item=seller name=sellers}
                <article class="product-miniature js-product-miniature">
                    <div class="thumbnail-container">
                        <a href="{$seller.href nofilter}" title="{$seller.title}" class="thumbnail product-thumbnail">
                            <img
                              src = "{$seller.logo nofilter}"
                              alt = "{$seller.title}"
                            >
                        </a>
                        <div class="product-description">
                            <h1 class="h3 product-title"><a href="{$seller.href nofilter}" target='_blank' title="{$seller.title}">{$seller.title|truncate:30:'...'}</a></h1>
                            <div class="product-price-and-shipping">
                                <div class="vss_seller_ratings">
                                    <div class="vss_rating_unfilled">★★★★★</div>
                                    <div class="vss_rating_filled" style="width:{$seller.rating_percent}%">★★★★★</div>
                                </div>
                            </div>
                            <div class="product-price-and-shipping">
                                <div class="kbmp-_row kb-tcenter">
                                <div class="kbmp-_inner_block"><a href="{$seller.view_review_href nofilter}" class="vss_active_link vss_read_review_bck" title='{l s='%s Review(s)'|sprintf:$seller.total_review mod='kbmarketplace'}'><span class="">{l s='View Reviews' mod='kbmarketplace'}</span></a></div>
                                    {if $seller.display_write_review}
                                        <div class="kbmp-_inner_block">
                                            {if !$kb_is_customer_logged}
                                                <a href="{$link->getPageLink('my-account', (bool)Configuration::get('PS_SSL_ENABLED')) nofilter}"  class="vss_active_link "><span class="">{l s='Write Review' mod='kbmarketplace'}</span></a>
                                            {else}
                                                <a href="javascript:void(0)" class="vss_active_link vss_write_review_bck" data-toggle="kb-seller-new-review-popup" onclick="openSellerReviewPopup('kb-seller-new-review-popup', {$seller.id_seller|intval});"><span class="">{l s='Write Review' mod='kbmarketplace'}</span></a>
                                            {/if}
                                        </div>
                                    {/if}
                                </div>
                            </div>
                        </div>
                    </div>
                </article>
            {/foreach}           
        </div>
    </section>
</section>
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