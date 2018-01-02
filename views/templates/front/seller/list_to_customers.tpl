{if isset($sellers) && count($sellers) > 0}
    <h1 class="page-heading">
        <span clas="cat-name">{l s='Sellers' mod='kbmarketplace'}</span>
        <div class="clearfix"></div>
    </h1>

    <div class="row products-selection">
        <div class="col-lg-3 hidden-md-down total-products">
            <p>{$pagination_string nofilter}</p>
        </div>
        <div class="col-lg-5 col-md-6">
            <div class="row">
                <span class="col-sm-3 col-md-3 hidden-sm-down sort-by">{l s='Sort by' mod='kbmarketplace'}:</span>
                <div class="col-sm-7 col-xs-6 col-md-7">
                    <select id="selectProductSort" class="selectSellerSort form-control">
                        <option value="" selected="selected">--</option>
                        {foreach $sorting_types as $sort}
                            <option value="{$sort['value']|escape:'htmlall':'UTF-8'}" {if $sort['value'] == $selected_sort} selected="selected"{/if} >{$sort['label']|escape:'htmlall':'UTF-8'}</option>
                        {/foreach}
                    </select>
                </div>
            </div>
        </div>
        {if isset($kb_pagination.pagination) && $kb_pagination.pagination neq ''}
            <div id="front-end-customer-pagination" class="top-pagination-content clearfix">
                <div class="sv-p-paging">
                    {$kb_pagination.pagination nofilter}
                    <div class='clearfix'></div>
                </div>
            </div>
        {/if}        
    </div>
    <div class="clearfix"></div>
        <img id="kb-list-loader" src="{$kb_image_path|escape:'htmlall':'UTF-8'}loader128.gif" />

    <div class='kbmp-_block'>

    </div>
    <div id="seller_list_to_customers">
        {include file="./seller_list.tpl"}
    </div>
    <script type="text/javascript">
        var kb_page_start = {$kb_pagination.page_position|intval};
    </script>
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
                    <form id="slr-review-form" action="{$link->getModuleLink($kb_module_name, 'sellerfront', [], (bool)Configuration::get('PS_SSL_ENABLED')) nofilter}" method="post">
                        <input type="hidden" name="id_seller" value="0" />
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
{else}
    <h1 class="page-heading" style='border:0;'>
        <span clas="cat-name">{$empty_list|escape:'htmlall':'UTF-8'}</span>
        <div class="clearfix"></div>
    </h1>
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