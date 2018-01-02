<div class="kb-vspacer5"></div>
<div class="kb-panel outer-border kb_product_section">
    <div data-toggle="kb-product-form-seo" class='kb-panel-header kb-panel-header-tab'>
        <h1>{$form_title|escape:'htmlall':'UTF-8'}</h1>
        <div class='kb-accordian-symbol kbexpand'><i class="kb-material-icons">&#xe145;</i></div>
        <div class='clearfix'></div>
    </div>
    <div id="kb-product-form-seo" class='kb-panel-body'>
        <div class="kb-block kb-form">
            <script type="text/javascript">
                var kb_seo_field_title_length = 70;
                var kb_seo_field_desc_length = 160;
            </script>
            <ul class="kb-form-list">
                <li class="kb-form-fwidth">
                    <div class="kb-form-label-block">
                        <span class="kblabel "><i class="kb-material-icons" data-toggle="tooltip" data-placement="top" title="{l s='Public title for the product\'s page, and for search engines. Leave blank to use the product name.' mod='kbmarketplace'} {l s='The number of remaining characters is displayed to the left of the field.' mod='kbmarketplace'}"></i>{l s='Meta Title' mod='kbmarketplace'}</span>
                    </div>
                    <div class="kb-form-field-block">
                        <div class="kb-labeled-inpfield">
                            <span class="inplbl character-count">70</span>
                            <input type="text" class="kb-inpfield" name="meta_title_{$default_lang|intval}" value="{$meta_title|htmlentitiesUTF8|default:''}" maxlength="70" onkeyup="$(this).parent().find('.character-count').html(kb_seo_field_title_length - ($(this).val().length));" autocomplete="off"/>
                        </div>
                    </div>
                </li>
                <li class="kb-form-fwidth">
                    <div class="kb-form-label-block">
                        <span class="kblabel "><i class="kb-material-icons" data-toggle="tooltip" data-placement="top" title="{l s='This description will appear in search engines. You need a single sentence, shorter than 160 characters (including spaces).' mod='kbmarketplace'}"></i>{l s='Meta Description' mod='kbmarketplace'}</span>
                    </div>
                    <div class="kb-form-field-block">
                        <div class="kb-labeled-inpfield">
                            <span class="inplbl character-count">160</span>
                            <input type="text" class="kb-inpfield" name="meta_description_{$default_lang|intval}" value="{$meta_description|htmlentitiesUTF8|default:''}" maxlength="160" onkeyup="$(this).parent().find('.character-count').html(kb_seo_field_desc_length - ($(this).val().length));" autocomplete="off"/>
                        </div>
                    </div>
                </li>
                <li class="kb-form-fwidth last-row">
                    <div class="kb-form-label-block">
                        <span class="kblabel "><i class="kb-material-icons" data-toggle="tooltip" data-placement="top" title="{l s='This is the human-readable URL, as generated from the product\'s name. You can change it if you want.' mod='kbmarketplace'}{l s='Allowed characters are' mod='kbmarketplace'}: a-z, A-Z, 0-9, _"></i>{l s='Friendly Url' mod='kbmarketplace'}</span><em>*</em>
                    </div>
                    <div class="kb-form-field-block">
                        <input type="text" class="kb-inpfield required" validate="isLinkRewrite" name="link_rewrite_{$default_lang|intval}" value="{$link_rewrite|htmlentitiesUTF8|default:''}"  autocomplete="off" onkeyup="$('#friendly-url-demo').html(str2url($(this).val()));"/>
                    </div>
                    <div class="kbalert kbalert-warning pack-empty-warning" style="display: block; margin-top:10px;">
                        <i class="kb-material-icons" style="font-size:20px; margin-right:5px;"></i> {l s='The product link will look like this:' mod='kbmarketplace'}<br/>
                        <strong>{$curent_shop_url|escape:'htmlall':'UTF-8'}{$editor_lang|escape:'htmlall':'UTF-8'}/{if $id_product > 0}{$id_product|intval}{else}id_product{/if}-<span id="friendly-url-demo">{if $link_rewrite neq ''}{$link_rewrite|escape:'htmlall':'UTF-8'}{else}{l s='you-friendly-url' mod='kbmarketplace'}{/if}</span>.html</strong>
                    </div>
                </li>
                {hook h="displayKbMarketPlacePForm" product_id=$id_product type=$product_type form="seo"}
            </ul>
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