{if $display_feature}
<div class="kb-content">
    <div class="kb-content-header">
        <h1>{l s='Product Reviews' mod='kbmarketplace'}</h1>
        <div class="clearfix"></div>
    </div>
    <div class='kb-vspacer5'></div>
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
    
    <div id="kb-product-review-view-popup" style="display:none;">
    <div class="kb-overlay"></div>
    <div class="kb-modal">
        <div class='kb-model-content-loader'><div class="kb-modal-loading-img"></div></div>
        <div class='kb-model-content'>
            <div class="kb-modal-header">
                <h1 id='review_on_product_name'>--</h1>
                <span class="kb-modal-close" data-modal="kb-product-review-view-popup">X</span>
            </div>
            <div class="kb-modal-content">
                <div class="kb-big-loader"></div>
                <div class="kb-row">
                    <div class="review-popup-time">
                        {l s='Posted on' mod='kbmarketplace'}: <span id="review-time" class="btxt">--</span> {l s='by' mod='kbmarketplace'} <span id="review-posted-by" class="btxt">--</span>
                    </div>
                </div>
                <div class="kb-row">
                    <div class="in-display right-offset15 btxt vssmp-font14" style='color:rgb(5, 197, 5);'>{l s='Overall Rating' mod='kbmarketplace'}:</div>
                    <div class="in-display">
                        <div class="vss_seller_ratings"><div class="vss_rating_unfilled">★★★★★</div><div id='rating_percent_display' class="vss_rating_filled" style="width:0%">★★★★★</div></div>
                    </div>
                </div>
                <div id='kb-individual-review-rating'></div>
                <div class="kb-vspacer5"></div>
                <div class="kb-row btxt b-border">
                    {l s='Title' mod='kbmarketplace'}:
                </div>
                <div class="kb-row">
                    <pre><p id="review-title">--</p></pre>
                </div>
                <div class="kb-vspacer5"></div>
                <div class="kb-row btxt b-border">
                    {l s='Comment' mod='kbmarketplace'}:
                </div>
                <div class="kb-row">
                    <pre><p id="review-comment">--</p></pre>
                </div>
            </div>    
        </div>
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
