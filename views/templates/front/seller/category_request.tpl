<div class="kb-content">
    <div class="kb-content-header">
        <h1>{l s='New Category Request' mod='kbmarketplace'}</h1>
        <div class="clearfix"></div>
    </div>
    <div class="kb-vspacer5"></div>
    <div class="kb-block kb-form">
        {if count($available_categories) > 0}
            <form id='kb_category_request_form' action='' method='post'>
                <ul class="kb-form-list">
                    <li class="kb-form-fwidth">
                        <div class="kb-form-label-block">
                            <span class="kblabel highlighted-form-label">{l s='Available Categories' mod='kbmarketplace'}</span><em>*</em>
                        </div>
                        <div class="kb-form-field-block">
                            <select name="category_request_id" class="kb-inpselect">
                                {foreach $available_categories as $cat}
                                    <option value="{$cat['id_category']|intval}" {if $request_category_id == $cat['id_category']}selected="selected"{/if}>{$cat['name'] nofilter}</option>
                                {/foreach}
                            </select>
                        </div>
                    </li>
                    <li class="kb-form-fwidth last-row">
                        <div class="kb-form-label-block">
                            <span class="kblabel highlighted-form-label">{l s='Request Reason' mod='kbmarketplace'}</span><em>*</em>
                        </div>
                        <div class="kb-form-field-block">
                            <textarea name="category_request_reason" rows="5" class="kb-inptexarea">{$request_category_reason}</textarea>
                            <p class="form-inp-help">{l s='Minimum %s character and Maximum %s characters are allowed' sprintf=array($min_reason_length, $max_reason_length) mod='kbmarketplace'}</p>
                        </div>
                    </li>
                    {hook h="displayKbMarketPlaceCategoryRequest"}
                </ul>
                <div class='kb-vspacer5'></div>
                <button type="button" class='btn-sm btn-success' id="kb_category_request_submit" onclick='submitCategoryRequest()'>{l s='Save' mod='kbmarketplace'}</button>
            </form>
        {else}
            <div class="kbalert kbalert-warning">{l s='Sorry! No unassigned category available for you.' mod='kbmarketplace'}</div>
        {/if}
        <script type="text/javascript">
            var cat_request_reason_min_length_error = '{l s='Minimum %s characters are required for reason.' sprintf=array($min_reason_length) mod='kbmarketplace'}';
            var cat_request_reason_max_length_error = '{l s='Maximum %s characters are allowed for reason.' sprintf=array($max_reason_length) mod='kbmarketplace'}';
            var cat_request_reason_min_length = {$min_reason_length|intval};
            var cat_request_reason_max_length = {$max_reason_length|intval};
        </script>
    </div>
    <div class="kb-content-header">
        <h1>{l s='Requested Category Statuses' mod='kbmarketplace'}</h1>
        <div class="clearfix"></div>
    </div>
    {if isset($kbfilter)}
        <div class="kb-vspacer5"></div>
        {$kbfilter nofilter}
    {/if}
    
    {if isset($kblist)}
        <div class="kb-vspacer5"></div>
        {$kblist nofilter}
    {/if}
    
    <div id="kb-seller-category-view-popup" style="display:none;">
        <div class="kb-overlay"></div>
        <div class="kb-modal">
            <div class='kb-model-content-loader'><div class="kb-modal-loading-img"></div></div>
            <div class='kb-model-content'>
                <div class="kb-modal-header">
                    <h1 id='seller_requested_category_name'>--</h1>
                    <span class="kb-modal-close" data-modal="kb-seller-category-view-popup">X</span>
                </div>
                <div class="kb-modal-content">
                    <div class="kb-big-loader"></div>
                    <div class="kb-row">
                        <div class="review-popup-time">
                            {l s='Requested on' mod='kbmarketplace'}: <span id="request-time" class="btxt">--</span>
                        </div>
                    </div>
                    <div class="kb-vspacer5"></div>
                    <div class="kb-row">
                        <div class="in-display right-offset15 btxt">{l s='Status' mod='kbmarketplace'}:</div>
                        <div id="category_request_status" class="in-display"></div>
                    </div>
                    <div class="kb-vspacer5"></div>
                    <div class="kb-row btxt b-border">
                        {l s='Your Comment For Request?' mod='kbmarketplace'}:
                    </div>
                    <div class="kb-row">
                        <pre><p id="seller_request_comment">--</p></pre>
                    </div>
                    <div id="admin-comment-block" style="display:none;">
                        <div class="kb-vspacer5"></div>
                        <div class="kb-row btxt b-border">
                            {l s='Admin Comment' mod='kbmarketplace'}:
                        </div>
                        <div class="kb-row">
                            <pre><p id="admin_comment">--</p></pre>
                        </div>    
                    </div>
                </div>    
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