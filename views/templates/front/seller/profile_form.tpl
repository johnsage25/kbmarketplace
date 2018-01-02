<div id="sellerprofile-panel" class="kb-content">
    <div id="kb-seller-form-msg"></div>
    <div class="kbalert kbalert-info">
        <i class="kb-material-icons"></i>{l s='Fields marked with (*) are mandatory fields.' mod='kbmarketplace'}
    </div>
        <ul class="kb-tabs">
                <li class="active" rel="general" id="kb-sprofile-general">{l s='General' mod='kbmarketplace'}</li>
                <li rel="metadata" id="kb-sprofile-metadata">{l s='Meta Information' mod='kbmarketplace'}</li>
                <li rel="policydata" id="kb-sprofile-policydata">{l s='Policy' mod='kbmarketplace'}</li>
                <li rel="paymentinfo" id="kb-sprofile-paymentinfo">{l s='Payment Information' mod='kbmarketplace'}</li>                
        </ul>
        <div class="clearfix"></div>
        <div class="kb_tab_container">
            <form action="{$kb_current_request|escape:'htmlall':'UTF-8'}" id="sellerProfileForm" method="post" class="" enctype="multipart/form-data">
                <input type="hidden" name="updateSellerProfile" value="{$seller_form_key|escape:'htmlall':'UTF-8'}" />
                <input type="hidden" name="kb_id_seller" value="{$kb_id_seller|intval}" />
                <div id="general" class="kb_tab_content">
                    <div class="kb-block kb-form">
                        <ul class="kb-form-list">
                            <li class="kb-form-l">
                                <div class="kb-form-label-block">
                                    <span class="kblabel">{l s='Shop Title' mod='kbmarketplace'}</span><em>*</em>
                                </div>
                                <div class="kb-form-field-block">
                                    <input data-tab="general" type="text" class="kb-inpfield required" validate="isGenericName" name="seller_title" value="{$seller.title|escape:'htmlall':'UTF-8'}" onkeyup="updateSellerLinkRewrite(this)"/>
                                </div>
                            </li>
                            <li class="kb-form-r">
                                <div class="kb-form-label-block">
                                    <span class="kblabel">{l s='Phone Number' mod='kbmarketplace'}</span><em>*</em>
                                </div>
                                <div class="kb-form-field-block">
                                    <input data-tab="general" type="text" class="kb-inpfield required" validate="isPhoneNumber" name="seller_phone_number" value="{$seller.phone_number|escape:'htmlall':'UTF-8'}" maxlength="15" />
                                </div>
                            </li>
                            <li class="kb-form-l">
                                <div class="kb-form-label-block">
                                    <span class="kblabel ">{l s='Business Email' mod='kbmarketplace'}</span>
                                </div>
                                <div class="kb-form-field-block">
                                    <input data-tab="general" type="text"  id="kb_business_email" class="kb-inpfield" validate="isEmail" name="seller_business_email" value="{$seller.business_email|escape:'htmlall':'UTF-8'}" />
                                </div>
                            </li>
                            <li class="kb-form-r">
                                <div class="kb-form-label-block">
                                    <span class="kblabel ">{l s='Get Notification' mod='kbmarketplace'}</span>
                                </div>
                                <div class="kb-form-field-block">
                                    <select data-tab="general" name="seller_notification_type" id="kb_seller_notification_type" class="kb-inpselect">
                                        <option value="0" {if $seller.notification_type eq 0}selected="selected"{/if}>{l s='On Both' mod='kbmarketplace'}</option>
                                        <option value="1" {if $seller.notification_type eq 1}selected="selected"{/if}>{l s='Primary Email' mod='kbmarketplace'}</option>
                                        <option value="2" {if $seller.notification_type eq 2}selected="selected"{/if}>{l s='Business Email' mod='kbmarketplace'}</option>
                                    </select>
                                </div>
                            </li>
                            <li class="kb-form-fwidth">
                                <div class="kb-form-label-block">
                                    <span class="kblabel">{l s='Address' mod='kbmarketplace'}</span><em>*</em>
                                </div>
                                <div class="kb-form-field-block">
                                    <textarea data-tab="general" name="seller_address" rows="5" class="kb-inptexarea required"  validate="isAddress" >{$seller.address}</textarea>
                                </div>
                            </li>
                            <li class="kb-form-r">
                                <div class="kb-form-label-block">
                                    <span class="kblabel">{l s='Country' mod='kbmarketplace'}</span><em>*</em>
                                </div>
                                <div class="kb-form-field-block">
                                    <select data-tab="general" name="seller_country" class="kb-inpselect required" validate="isInt">
                                        <option value="">{l s='Select Country' mod='kbmarketplace'}</option>
                                        {foreach $countries as $key => $val}
                                            <option value="{$key|intval}" {if $key eq $seller_country} selected="selected"{/if} >{$val|escape:'htmlall':'UTF-8'}</option>
                                        {/foreach}
                                    </select>
                                </div>
                            </li>
                            <li class="kb-form-l">
                                <div class="kb-form-label-block">
                                    <span class="kblabel">{l s='State/City' mod='kbmarketplace'}</span><em>*</em>
                                </div>
                                <div class="kb-form-field-block">
                                    <input id="seller_state" data-tab="general" type="text" class="kb-inpfield required" validate="isGenericName" name="seller_state" value="{$seller.state|escape:'htmlall':'UTF-8'}" />
                                </div>
                            </li>
                            <li class="kb-form-fwidth">
                                <div class="kb-form-label-block">
                                    <span class="kblabel">{l s='Description' mod='kbmarketplace'}</span>
                                </div>
                                <div class="kb-form-field-block">
                                    <textarea data-tab="general" name="seller_description" rows="5" class="kb-inptexarea autoload_rte">{$seller.description}</textarea>{*Variable contains css and html content, escape not required*}
                                </div>
                            </li>
                            <li class="kb-form-fwidth">
                                <div class="kb-form-label-block">
                                    <span class="kblabel">{l s='Profile Url Alias' mod='kbmarketplace'}</span>
                                </div>
                                <div class="kb-form-field-block">
                                    <input data-tab="general" type="text" class="kb-inpfield" validate="isLinkRewrite" name="seller_profile_url" value="{$seller.profile_url|escape:'htmlall':'UTF-8'}" autocomplete="off" onkeyup="$('#friendly-url-demo').html(str2url($(this).val()));"/>
                                </div>
                                <div class="kbalert kbalert-warning pack-empty-warning" style="display: block; margin-top:10px;">
                                    <i class="kb-material-icons" style="font-size:12px;margin-right:5px;">&#xe250;</i> {l s='The profile link will look like this:' mod='kbmarketplace'}<br/>
                                    <strong>{$curent_shop_url|escape:'htmlall':'UTF-8'}{$editor_lang|escape:'htmlall':'UTF-8'}/seller/{$seller.id|intval}-<span id="friendly-url-demo">{if $seller.profile_url neq ''}{$seller.profile_url|escape:'htmlall':'UTF-8'}{else}{l s='you-friendly-url' mod='kbmarketplace'}{/if}</span>.html</strong>
                                </div>
                                <script type="text/javascript">
                                    function updateSellerLinkRewrite(e) {
                                        var value = $(e).val();
                                        $('input[name="seller_profile_url"]').val(str2url(value));
                                        $('input[name="seller_profile_url"]').trigger('keyup');
                                    }
                                </script>
                            </li>
                            <li class="kb-form-fwidth">
                                <div class="kb-form-label-block">
                                    <span class="kblabel">{l s='Facebook Link' mod='kbmarketplace'}</span>
                                </div>
                                <div class="kb-form-field-block">
                                    <input data-tab="general" type="text" class="kb-inpfield" validate="isUrl" name="seller_fb_link"  id='seller_fb_link' value="{$seller.fb_link|escape:'htmlall':'UTF-8'}" />
                                </div>
                            </li>
                            <li class="kb-form-fwidth">
                                <div class="kb-form-label-block">
                                    <span class="kblabel">{l s='Google Plus Link' mod='kbmarketplace'}</span>
                                </div>
                                <div class="kb-form-field-block">
                                    <input data-tab="general" type="text" class="kb-inpfield" validate="isUrl" name="seller_gplus_link" id='seller_gplus_link' value="{$seller.gplus_link|escape:'htmlall':'UTF-8'}" />
                                </div>
                            </li>
                            <li class="kb-form-fwidth">
                                <div class="kb-form-label-block">
                                    <span class="kblabel">{l s='Twitter Link' mod='kbmarketplace'}</span>
                                </div>
                                <div class="kb-form-field-block">
                                    <input data-tab="general" type="text" class="kb-inpfield" validate="isUrl" name="seller_twit_link" id='seller_twit_link' value="{$seller.twit_link|escape:'htmlall':'UTF-8'}" />
                                </div>
                            </li>
                            <li class="kb-form-fwidth" id="id_logo_wrapper">
                                <div class="form-lbl-indis">
                                    <span class="kblabel">{l s='Logo' mod='kbmarketplace'}</span>
                                    {$temp_str = {l s='Logo size should be (%s).' mod='kbmarketplace'}}
                                    <p class="form-inp-help">{$temp_str|sprintf:'150 X 150'}</p>
                                </div>
                                <div class="form-field-indis">
                                    <div class="form-img-display">
                                        <img id="seller_logo_placeholder" class="form-logo-display" src="{$seller.logo|escape:'htmlall':'UTF-8'}?{$time}" title="{l s='Logo of your shop' mod='kbmarketplace'}">
                                    </div>
                                    <input id="seller_logo" class="kb_upload_field kb_seller_logo_file" type="file" name="seller_logo" style="display:none;" />
                                    <div class="kb-block file-uploader">
                                        <a id="seller_upload_image" href="javascript:void(0)" onclick="uploadImage('seller_logo')" >{l s='Browse' mod='kbmarketplace'}</a>
                                        <a id="seller_remove_image" href="javascript:void(0)" onclick="removeSellerImage('seller_logo', '{$seller_default_logo|escape:'htmlall':'UTF-8'}')" >{l s='Remove' mod='kbmarketplace'}</a>
                                        <input id="seller_logo_update" type="hidden" name="seller_logo_update" value="0" />
                                    </div>
                                    <div id="seller_logo_error" class="kb-validation-error"></div>
                                </div>
                            </li>
                            <li class="kb-form-fwidth">
                                <div class="kb-form-label-block">
                                    <span class="kblabel">{l s='Shop Banner' mod='kbmarketplace'}</span>
                                </div>
                                <div class="kb-form-field-block">
                                    <div class="form-img-display">
                                        <img id="seller_banner_placeholder" class="form-banner-display" src="{$seller.banner|escape:'htmlall':'UTF-8'}?{$time}" title="{l s='Banner of your shop' mod='kbmarketplace'}">
                                    </div>
                                    <input id="seller_banner" class="kb_upload_field kb_seller_banner_file" type="file" name="seller_banner" style="display:none;" />
                                    <div class="kb-block file-uploader">
                                        <a href="javascript:void(0)" onclick="uploadImage('seller_banner')" >{l s='Browse' mod='kbmarketplace'}</a>
                                        <a id="kb_banner_remove" href="javascript:void(0)" onclick="removeSellerImage('seller_banner', '{$seller_default_banner|escape:'htmlall':'UTF-8'}')" >{l s='Remove' mod='kbmarketplace'}</a>
                                        <input id="seller_banner_update" type="hidden" name="seller_banner_update" value="0" />
                                    </div>
                                    {$temp_str_banner = {l s='Banner size should be (%s).' mod='kbmarketplace'}}
                                    <p class="form-inp-help">{$temp_str_banner|sprintf:'900 X 250'}</p>
                                    <div id="seller_banner_error" class="kb-validation-error"></div>
                                </div>
                            </li>
                            {hook h="displayKbMarketPlaceSellerForm" block='general'}
                        </ul>
                    </div>
                </div>
                <div id="metadata" class="kb_tab_content">
                    <div class="kb-block kb-form">
                        <ul class="kb-form-list">
                            <li class="kb-form-fwidth">
                                <div class="kb-form-label-block">
                                    <span class="kblabel">{l s='Meta Keywords' mod='kbmarketplace'}</span><em>*</em>
                                </div>
                                <div class="kb-form-field-block">
                                    <input data-tab="metadata" type="text" class="kb-inpfield required" validate="isGenericName" name="seller_meta_keywords" value="{$seller.meta_keyword|escape:'htmlall':'UTF-8'}" />
                                </div>
                            </li>
                            <li class="kb-form-fwidth">
                                <div class="kb-form-label-block">
                                    <span class="kblabel">{l s='Meta Description' mod='kbmarketplace'}</span>
                                </div>
                                <div class="kb-form-field-block">
                                    <textarea data-tab="metadata" name="seller_meta_description" rows="5" class="kb-inptexarea">{$seller.meta_description}</textarea>
                                </div>
                            </li>
                            {hook h="displayKbMarketPlaceSellerForm" block='meta'}
                        </ul>
                    </div>
                </div>
                <div id="policydata" class="kb_tab_content">
                    <div class="kb-block kb-form">
                        <ul class="kb-form-list">
                            <li class="kb-form-fwidth">
                                <div class="kb-form-label-block">
                                    <span class="kblabel">{l s='Return Policy' mod='kbmarketplace'}</span><em>*</em>
                                </div>
                                <div class="kb-form-field-block">
                                    <textarea data-tab="policydata" name="seller_return_policy" rows="5" class="kb-inptexarea autoload_rte required">{$seller.return_policy nofilter}</textarea>
                                </div>
                            </li>
                            <li class="kb-form-fwidth">
                                <div class="kb-form-label-block">
                                    <span class="kblabel">{l s='Shipping Policy' mod='kbmarketplace'}</span><em>*</em>
                                </div>
                                <div class="kb-form-field-block">
                                    <textarea data-tab="policydata" name="seller_shipping_policy" rows="5" class="kb-inptexarea autoload_rte required">{$seller.shipping_policy nofilter}</textarea>
                                </div>
                            </li>
                            {hook h="displayKbMarketPlaceSellerForm" block='policy'}
                        </ul>
                    </div>
                </div>
                <div id="paymentinfo" class="kb_tab_content">
                    <div class="kb-block kb-form">
                        <ul class="kb-form-list">
                            {if count($available_payment_file) > 0}
                                <li class="kb-form-fwidth">
                                    <div class="kb-form-label-block">
                                        <span class="kblabel">{l s='Select Payment Method' mod='kbmarketplace'}</span><em>*</em>
                                    </div>
                                    <div class="kb-form-field-block">
                                        <select name="seller_payment_option" class="kb-inpselect required" id="kb-payment-select">
                                            <option value="">{l s='Select Method' mod='kbmarketplace'}...</option>
                                            {foreach $available_payment_file as $payment_name => $display_name}
                                                <option value="{$payment_name|escape:'htmlall':'UTF-8'}" {if isset($payment_info['name']) && $payment_info['name'] == $payment_name}selected="selected"{/if}>{$display_name|escape:'htmlall':'UTF-8'}</option>
                                            {/foreach}
                                        </select>
                                            <p class="form-inp-help" id="paymentinfo-notice"><span id="paymentinfo-note">{l s='Note' mod='kbmarketplace'}:</span>{l s='Only one payment information can be saved at a time.' mod='kbmarketplace'}</p>
                                    </div>
                                </li>
                            {/if}
                            {hook h="displayKbMarketPlaceSellerForm" block='payment'}
                        </ul>
                        <div id="payment-data">
                            
                        </div>
                    </div>
                </div>
            </form>
            <div class="kb-block" style="padding:5px 15px 5px 5px; text-align: right;">
                <div id="sellerprofile-updating-progress" class="input-loader" style="display:none;vertical-align: middle;"></div>
                <button id="sellerprofile-update-btn" type="button" class="kbbtn-big kbbtn-success" onclick="validateSellerForm()">{l s='Save' mod='kbmarketplace'}</button>
            </div>
        </div>    
</div>
<script>
    var kb_img_format = [];

    {foreach $kb_img_frmats as $for}
        kb_img_format.push('{$for|escape:'htmlall':'UTF-8'}');
    {/foreach}

	var kb_editor_lang = '{$editor_lang}';
    var kb_seller_form_error = '{$kb_validation_error}';
    var kb_img_size_error = '{$kb_img_size_error}';
    var kb_img_type_error = '{$kb_img_type_error}';
</script>

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