<div >
    {include file="./info_to_customer.tpl" seller=$seller}
    <div class="slr-f-box">
        <h3>{l s='Reviews' mod='kbmarketplace'}</h3>
        <div  class="slr-content">
            <ul class="s-review-l">
                {if isset($reviews) && count($reviews) > 0}
                {foreach $reviews as $rev}
                    <li>
                        <div class="kbmp-_block">
                            <span class="r-title"><strong class="vssmp-font-13px">{$rev['title'] nofilter}</strong></span>
                            <br><span class="sr-time"><i>{l s='Posted on: %s by %s %s'|sprintf:date('m/d/y H:i A', strtotime($rev['date_add'])):$rev['firstname']:$rev['lastname'] mod='kbmarketplace'} </i></span>
                        </div>
                        <div class="kbmp-_row">
                            <p><pre>{$rev['comment'] nofilter}</pre></p>
                        </div>
                        <div class="kbmp-_row">
                            <div class="rating-title"><b>{l s='Rating' mod='kbmarketplace'}:</b></div>
                            <div class="rating-point">
                                <div class="vss_seller_ratings" style="vertical-align: middle;">
                                    <!-- Do not customise it -->
                                    <div class="vss_rating_unfilled">★★★★★</div>

                                    <!-- Set only width in percentage according to rating -->
                                    <div class="vss_rating_filled" title="{$rev['rating']|intval}" style="width:{$rev['rating_percent']|string_format:'%.2f'}%">★★★★★</div>
                                </div>
                            </div>
                        </div>
                    </li>
                {/foreach}
                {else}
                    <li><i>{l s='No Review(s) Found' mod='kbmarketplace'}</i></li>
                {/if}
            </ul>
            {if $display_new_review}
                <div id='slr-new-review-blk' class='s-review-n'>
                    {if !$kb_is_customer_logged}
                        <p>
                            <button type="button" class="btn btn-primary" onclick="location.href='{$link->getPageLink('my-account', (bool)Configuration::get('PS_SSL_ENABLED')) nofilter}'">
                                <span>{l s='Login to write review first' mod='kbmarketplace'}</span>
                            </button>
                        </p>
                    {else}
                        <h2>{l s='Write a review' mod='kbmarketplace'}</h2>
                        <div id="new_comment_form_error" class="error" style="display: none; padding: 15px 25px">
                                <ul></ul>
                        </div>
                        <form id="slr-review-form" action="{$link->getModuleLink($kb_module_name, 'sellerfront', ['render_type' => 'sellerreviews', 'id_seller' => $seller['id_seller']], (bool)Configuration::get('PS_SSL_ENABLED')) nofilter}" method="post">
                            <ul>
                                <li>
                                    <label>{l s='Rate this Seller' mod='kbmarketplace'}:</label>
                                    <div id="seller_new_review_rating_block"></div>
                                    <div class="clearfix"></div>
                                </li>
                            </ul>
                            <br>
                            <label for="review_title">{l s='Title' mod='kbmarketplace'}: <sup class="required">*</sup></label>
                            <div class="kb-form-label-block">
                                <input id="review_title" name="review_title" type="text" value="" class="required">
                            </div>
                            <label for="review_content">{l s='Comment' mod='kbmarketplace'}: <sup class="required">*</sup></label>
                            <div class="kb-form-label-block">
                                <textarea id="review_content" name="review_content" class="required" rows="5"></textarea>
                            </div>
                            <div>
                                <p class="fl required"><sup>*</sup> {l s='Required fields' mod='kbmarketplace'}</p>
                                <p class="fr">
                                    <input type="hidden" name="new_review_submit" value="1" />
                                    <button id="submitSellerReview" type="button" class="btn btn-success" {if $kb_is_customer_logged}onclick="submitSellerNewReview()"{else}onclick="location.href='{$link->getPageLink('my-account', true)}'"{/if}>
                                        <span>{l s='Submit' mod='kbmarketplace'}</span>
                                    </button>
                                </p>
                                <div class="clearfix"></div>
                            </div>
                        </form>
                    {/if}
                </div>
            {/if}
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