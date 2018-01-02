<div class="kb-block seller_profile_view">
    <div class="s-vp-banner">
        <img src="{$seller['banner'] nofilter}" />
    </div>
    <div class="info-view">
        <div class="seller-profile-photo">
            <a href="{KbGlobal::getSellerLink($seller['id_seller']) nofilter}" >
                <img src="{$seller['logo'] nofilter}" title="{$seller['title']}" alt="{$seller['title']}">
            </a>
        </div>
        <div class="seller-info">
            <div class="seller-basic">
                <div class="seller-name">
                    <span class="name">
                        {$seller['title']}
                    </span>
                    <div class="seller-rating-block">
                        {if !isset($seller['is_review_page'])}
                            <div class="kbmp-_inner_block" style="position:relative;">
                                <a href="{$link->getModuleLink($kb_module_name, 'sellerfront', ['render_type' => 'sellerreviews', 'id_seller' => $seller['id_seller']], (bool)Configuration::get('PS_SSL_ENABLED'))|escape:'htmlall':'UTF-8'}" title="{$seller['seller_review_count']|escape:'htmlall':'UTF-8'}">
                                    <div class="vss_seller_ratings">
                                        <!-- Do not customise it -->
                                        <div class="vss_rating_unfilled">★★★★★</div>

                                        <!-- Set only width in percentage according to rating -->
                                        <div class="vss_rating_filled" style="width:{$seller['seller_rating']|escape:'htmlall':'UTF-8'}%">★★★★★</div>
                                    </div>
                                </a>
                            </div>
                            <div class="kbmp-_inner_block"><a href="{$link->getModuleLink($kb_module_name, 'sellerfront', ['render_type' => 'sellerreviews', 'id_seller' => $seller['id_seller']], (bool)Configuration::get('PS_SSL_ENABLED'))|escape:'htmlall':'UTF-8'}" class="vss_active_link vss_read_review_bck"><span class="">{l s='View Reviews' mod='kbmarketplace'}</span></a></div>
                            {if $display_new_review}
                                <div class="kbmp-_inner_block">
                                    {if !$kb_is_customer_logged}
                                        <a href="{$link->getPageLink('my-account', (bool)Configuration::get('PS_SSL_ENABLED'))|escape:'htmlall':'UTF-8'}" class="vss_active_link"><span class="">{l s='Write Review' mod='kbmarketplace'}</span></a>
                                    {else}
                                        <a href="javascript:void(0)" class="vss_active_link vss_write_review_bck" data-toggle="kb-seller-new-review-popup" onclick="openSellerReviewPopup('kb-seller-new-review-popup', false);"><span class="">{l s='Write Review' mod='kbmarketplace'}</span></a>
                                    {/if}
                                </div>
                            {/if}
                        {else}
                            <div class="kbmp-_inner_block"><strong>{l s='Overall Rating' mod='kbmarketplace'}: </strong></div>
                            <div class="kbmp-_inner_block" style="position:relative;">
                                <div class="vss_seller_ratings">
                                    <!-- Do not customise it -->
                                    <div class="vss_rating_unfilled">★★★★★</div>

                                    <!-- Set only width in percentage according to rating -->
                                    <div class="vss_rating_filled" style="width:{$seller['seller_rating']|escape:'htmlall':'UTF-8'}%">★★★★★</div>
                                </div>
                            </div>
                        {/if}        
                    </div>
                </div>
                <div class="seller-social">
                    {if !empty($seller['twit_link'])}
                        <a title="{l s='Twitter' mod='kbmarketplace'}" href="{$seller['twit_link'] nofilter}" class="btn-sm btn-primary social-btn twitter" ></a>
                    {/if}
                    {if !empty($seller['fb_link'])}
                        <a title="{l s='Facebook' mod='kbmarketplace'}" href="{$seller['fb_link'] nofilter}" class="btn-sm btn-primary social-btn facebook"></a>
                    {/if}
                    {if !empty($seller['gplus_link'])}
                        <a title="{l s='Google+' mod='kbmarketplace'}" href="{$seller['gplus_link'] nofilter}" class="btn-sm btn-primary social-btn googleplus"></a>
                    {/if}       
                </div>
            </div>
        </div>
    </div>
    {if !isset($seller['is_review_page'])}
        {if !empty($seller['description'])}
            <section class="slr-f-box">
                <h3 class="page-product-heading">{l s='About Seller' mod='kbmarketplace'}</h3>
                <div  class="rte slr-content">
                    {$seller['description'] nofilter}
                </div>
            </section>
        {/if}
        <section class="slr-f-box">
            <h3 class="page-product-heading">{l s='Return Policy' mod='kbmarketplace'}</h3>
            <div  class="rte slr-content">
                {if !empty($seller['return_policy'])}
                    {$seller['return_policy'] nofilter}
                {else}
                    {l s='No Return Policy Provided by Seller Yet.' mod='kbmarketplace'}
                {/if}

            </div>
        </section>
        <section class="slr-f-box">
            <h3 class="page-product-heading">{l s='Shipping Policy' mod='kbmarketplace'}</h3>
            <div  class="rte slr-content">
                {if !empty($seller['shipping_policy'])}
                    {$seller['shipping_policy']}
                {else}
                    {l s='No Shipping Policy Provided by Seller Yet.' mod='kbmarketplace'}
                {/if}

            </div>
        </section>
        {hook h="displayKbSellerView" id_seller=$seller['id_seller'] area="profile"}
    {else}
        {hook h="displayKbSellerView" id_seller=$seller['id_seller'] area="review"}
    {/if}
</div>
{if isset($display_review_popup) && $display_review_popup}
    <div id="kb-seller-new-review-popup" class="modal fade quickview kbpopup-modal" tabindex="-1" role="dialog" style="display:none;">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h2>{l s='Write a review' mod='kbmarketplace'}</h2>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="slr-review-form" action="{KbGlobal::getSellerLink($seller['id_seller']) nofilter}" method="post">
                        <input type="hidden" name="new_review_submit" value="1" />
                        <ul>
                            <li>
                                <label>{l s='Rate this Seller' mod='kbmarketplace'}:</label>
                                <div id="seller_new_review_rating_block"></div>
                                <div class="clearfix"></div>
                            </li>
                        </ul>
                        <label for="review_title">{l s='Title' mod='kbmarketplace'}: <sup class="required">*</sup></label>
                        <div class="kb-form-label-block">
                            <input id="review_title" name="review_title" type="text" value="" class="required">
                        </div>
                        <label for="review_content">{l s='Comment' mod='kbmarketplace'}: <sup class="required">*</sup></label>
                        <div class="kb-form-label-block">
                            <textarea id="review_content" name="review_content" class="required"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <p class="fl required"><sup>*</sup> {l s='Required fields' mod='kbmarketplace'}</p>
                    <p class="fr">
                        <button id="submitSellerReview" type="button" class="btn button button-small" {if $kb_is_customer_logged}onclick="submitSellerNewReview()"{else}onclick="location.href='{$link->getPageLink('my-account', true)|escape:'htmlall':'UTF-8'}'"{/if}>
                            <span>{l s='Submit' mod='kbmarketplace'}</span>
                        </button>
                    </p>
                    <div class="clearfix"></div>
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