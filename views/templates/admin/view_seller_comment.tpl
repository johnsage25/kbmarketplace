<div id='kb-review-box' class="panel">
    <div class='kb-review-box-heading'>
        <i class="icon-comment"></i> {$seller_name|escape:'htmlall':'UTF-8'} ({$seller_title|escape:'htmlall':'UTF-8'})
    </div>
    <div class="row">
        <div class="col-xs-12">
            <div class='kb-review-row kb-review-tym'>{$post_on_title|escape:'htmlall':'UTF-8'} {$posted_on|escape:'htmlall':'UTF-8'} {$by_title|escape:'htmlall':'UTF-8'} <span id="kb-review-author">{$customer_name|escape:'htmlall':'UTF-8'}</span></div>
            <div class='kb-review-row'>
                <ul class="vssmp-rating-option-list">
                    <li>
                        <div class="vss_mp_display_inline vssmp-font14" style='color:rgb(5, 197, 5);'><span><strong>{$rating_title|escape:'htmlall':'UTF-8'}:</strong></span></div>
                        <div class="vss_mp_display_inline">
                            <div class="rating-box">
                                <div class="vss_seller_ratings vssmp-font20"><div class="vss_rating_unfilled">★★★★★</div><div id="vss_product_reviewpopup_average_rating" class="vss_rating_filled" style="width:{$rating_percent|escape:'htmlall':'UTF-8'}%">★★★★★</div></div>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
            <div class='kb-review-row'>
                <div class='kb-review-label'>{$summary_title|escape:'htmlall':'UTF-8'}</div>
                <p id='kb-review-summary'>
                    {$review_title|escape:'htmlall':'UTF-8'}
                </p>
            </div>
            <div class='kb-review-row'>
                <div class='kb-review-label'>{$comment_title nofilter}</div>
                <p id='kb-review-content'>
                {$review_comment|nl2br nofilter}
                </p>
            </div>
        </div>
    </div>
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