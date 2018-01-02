<div id="kbmp-seller-info" class="tabs kbmp-_block box-info-product">
    <div class="kbmp-_row">
        <div class="kbmp-_inner_block"><span class="title">{l s='Sold By' mod='kbmarketplace'}:</span></div>
        <div class="kbmp-_inner_block"><a href="{KbGlobal::getSellerLink($id_seller) nofilter}"><span class="">{$seller_title}</span></a></div>
    </div>
    <div class="kbmp-_row">
        <div class="kbmp-_inner_block" style="position:relative;">
            <a href="{$link->getModuleLink('kbmarketplace', 'sellerfront', ['render_type' => 'sellerreviews', 'id_seller' => $id_seller], (bool)Configuration::get('PS_SSL_ENABLED')) nofilter}" title="{l s='Total %s review(s)'|sprintf:$seller_review_count mod='kbmarketplace'}">
            <div class="vss_seller_ratings">
                <!-- Do not customise it -->
                <div class="vss_rating_unfilled">★★★★★</div>
                
                <!-- Set only width in percentage according to rating -->
                <div class="vss_rating_filled" style="width:{$seller_rating|string_format:'%.2f'}%">★★★★★</div>
            </div>
            </a>
        </div>
        <div class="kbmp-_inner_block"><a title="{l s='Total %s review(s)'|sprintf:$seller_review_count mod='kbmarketplace'}" href="{$link->getModuleLink('kbmarketplace', 'sellerfront', ['render_type' => 'sellerreviews', 'id_seller' => $id_seller], (bool)Configuration::get('PS_SSL_ENABLED')) nofilter}" class="vss_active_link vss_read_review_bck"><span class="">{l s='View Reviews' mod='kbmarketplace'}</span></a></div>
        <div class="kbmp-_inner_block"><a href="{$link->getModuleLink('kbmarketplace', 'sellerfront', ['render_type' => 'sellerreviews', 'id_seller' => $id_seller], (bool)Configuration::get('PS_SSL_ENABLED')) nofilter}" class="vss_active_link vss_write_review_bck"><span class="">{l s='Write Review' mod='kbmarketplace'}</span></a></div>
    </div>
    <div class="kbmp-_row" style="padding-top:10px;">
        <i class="kb-material-icons"></i><a href="{$link->getModuleLink('kbmarketplace', 'sellerfront', ['render_type' => 'sellerproducts', 'id_seller' => $id_seller], (bool)Configuration::get('PS_SSL_ENABLED')) nofilter}" style="padding-left:7px;font-size:13px;">{l s='View more products of this seller' mod='kbmarketplace'}</a>
    </div>
    {hook h="displayKbSellerOnProductView" id_seller=$id_seller}
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