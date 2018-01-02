<div id="container-carrier-view">
    <div class="row">
            {*left*}
            <div class="col-lg-6">
                    <div class="panel clearfix">
                            <div class="panel-heading">
                                    <i class="icon-user"></i>
                                    {$seller_info['seller_name']|escape:'htmlall':'UTF-8'}
                                    [{$seller_info['id_seller']|escape:'htmlall':'UTF-8'}]
                                    -
                                    <a href="mailto:{$seller_info['email']|escape:'htmlall':'UTF-8'}"><i class="icon-envelope"></i>
                                            {$seller_info['email']|escape:'htmlall':'UTF-8'}
                                    </a>
                            </div>
                            <div class="form-horizontal">
                                    <div class="row">
                                            <label class="control-label col-lg-3">{l s='Social Title' mod='kbmarketplace'}</label>
                                            <div class="col-lg-9">
                                                    <p class="form-control-static">{if $gender->name}{$gender->name|escape:'htmlall':'UTF-8'}{else}{l s='Unknown' mod='kbmarketplace'}{/if}</p>
                                            </div>
                                    </div>
                                    <div class="row">
                                            <label class="control-label col-lg-3">{l s='Age' mod='kbmarketplace'}</label>
                                            <div class="col-lg-9">
                                                    <p class="form-control-static">
                                                            {if isset($customer->birthday) && $customer->birthday != '0000-00-00'}
                                                                    {l s='%1$d years old (birth date: %2$s)' sprintf=[$customer_stats['age'], $customer_birthday] mod='kbmarketplace'}
                                                            {else}
                                                                    {l s='Unknown' mod='kbmarketplace'}
                                                            {/if}
                                                    </p>
                                            </div>
                                    </div>
                                    <div class="row">
                                            <label class="control-label col-lg-3">{l s='Registration Date' mod='kbmarketplace'}</label>
                                            <div class="col-lg-9">
                                                    <p class="form-control-static">{$registration_date|escape:'htmlall':'UTF-8'}</p>
                                            </div>
                                    </div>
                                    {if $shop_is_feature_active}
                                            <div class="row">
                                                    <label class="control-label col-lg-3">{l s='Shop' mod='kbmarketplace'}</label>
                                                    <div class="col-lg-9">
                                                            <p class="form-control-static">{$name_shop|escape:'htmlall':'UTF-8'}</p>
                                                    </div>
                                            </div>
                                    {/if}
                                    <div class="row">
                                            <label class="control-label col-lg-3">{l s='Language' mod='kbmarketplace'}</label>
                                            <div class="col-lg-9">
                                                    <p class="form-control-static">
                                                            {if isset($customerLanguage)}
                                                                    {$customerLanguage->name|escape:'htmlall':'UTF-8'}
                                                            {else}
                                                                    {l s='Unknown' mod='kbmarketplace'}
                                                            {/if}
                                                    </p>
                                            </div>
                                    </div>
                                    <div class="row">
                                            <label class="control-label col-lg-3">{l s='Status' mod='kbmarketplace'}</label>
                                            <div class="col-lg-9">
                                                    <p class="form-control-static">
                                                            {if $seller_info['approved'] == 1}
                                                                    <span class="label label-success">
                                                                            <i class="icon-check"></i>
                                                                            {l s='Approved' mod='kbmarketplace'}
                                                                    </span>
                                                            {else}
                                                                    <span class="label label-danger">
                                                                            <i class="icon-remove"></i>
                                                                            {if $seller_info['approved'] == 0}
                                                                                {l s='Waiting for Approval' mod='kbmarketplace'}
                                                                            {else}
                                                                                {l s='Disapproved' mod='kbmarketplace'}
                                                                            {/if}
                                                                    </span>
                                                            {/if}
                                                            &nbsp;
                                                            {if $seller_info['active']}
                                                                    <span class="label label-success">
                                                                            <i class="icon-check"></i>
                                                                            {l s='Active' mod='kbmarketplace'}
                                                                    </span>
                                                            {else}
                                                                    <span class="label label-danger">
                                                                            <i class="icon-remove"></i>
                                                                            {l s='Inactive' mod='kbmarketplace'}
                                                                    </span>
                                                            {/if}
                                                    </p>
                                            </div>
                                    </div>
                            </div>
                    </div>
                    <div class="panel clearfix">
                            <div class="panel-heading">
                                    <i class="icon-user"></i>{l s='Business Profile' mod='kbmarketplace'}
                                    {if $seller_info['business_email']}
                                        -
                                        <a href="mailto:{$seller_info['business_email']|escape:'htmlall':'UTF-8'}"><i class="icon-envelope"></i>
                                                {$seller_info['business_email']|escape:'htmlall':'UTF-8'}
                                        </a>    
                                    {/if}
                            </div>
                            <div class="form-horizontal">
                                    <div class="row">
                                            <label class="control-label col-lg-3">{l s='Shop Title' mod='kbmarketplace'}</label>
                                            <div class="col-lg-9">
                                                    <p class="form-control-static">{$seller_info['title']|escape:'htmlall':'UTF-8'}</p>
                                            </div>
                                    </div>
                                    <div class="row">
                                            <label class="control-label col-lg-3">{l s='Phone Number' mod='kbmarketplace'}</label>
                                            <div class="col-lg-9">
                                                    <p class="form-control-static">{$seller_info['phone_number']|escape:'htmlall':'UTF-8'}</p>
                                            </div>
                                    </div>
                                    {*<div class="row">
                                            <label class="control-label col-lg-3">{l s='Payment Info.' mod='kbmarketplace'}</label>
                                            <div class="col-lg-9">
                                                    <p class="form-control-static">{$seller_info['payment_info']|escape:'htmlall':'UTF-8'}</p>
                                            </div>
                                    </div>*}
                                    <div class="row">
                                            <label class="control-label col-lg-3">{l s='Notifcation Send To' mod='kbmarketplace'}</label>
                                            <div class="col-lg-9">
                                                <p class="form-control-static">
                                                    {if $seller_info['notification_type'] == 0}
                                                        {l s='Both Email Ids' mod='kbmarketplace'}
                                                    {elseif $seller_info['notification_type'] == 1}
                                                        {l s='Primary Email Id' mod='kbmarketplace'}
                                                    {elseif $seller_info['notification_type'] == 2}
                                                        {l s='Business Email Id' mod='kbmarketplace'}
                                                    {/if}
                                                </p>
                                            </div>
                                    </div>
                                    <div class="row">
                                            <label class="control-label col-lg-3">{l s='Facebook Link' mod='kbmarketplace'}</label>
                                            <div class="col-lg-9">
                                                    <p class="form-control-static">{$seller_info['fb_link']|escape:'htmlall':'UTF-8'}</p>
                                            </div>
                                    </div>
                                    <div class="row">
                                            <label class="control-label col-lg-3">{l s='Google Plus Link' mod='kbmarketplace'}</label>
                                            <div class="col-lg-9">
                                                    <p class="form-control-static">{$seller_info['gplus_link']|escape:'htmlall':'UTF-8'}</p>
                                            </div>
                                    </div>
                                    <div class="row">
                                            <label class="control-label col-lg-3">{l s='Twitter Link' mod='kbmarketplace'}</label>
                                            <div class="col-lg-9">
                                                    <p class="form-control-static">{$seller_info['twit_link']|escape:'htmlall':'UTF-8'}</p>
                                            </div>
                                    </div>
                            </div>
                    </div>
                    <div class="panel clearfix">
                            <div class="panel-heading">
                                    <i class="icon-ticket"></i> {l s='Payment  Information' mod='kbmarketplace'} {if isset($payment_info['name'])}({$payment_info['name']|escape:'htmlall':'UTF-8'} {l s='Details' mod='kbmarketplace'}){/if}
                            </div>
                            <div class="form-horizontal">
                            {if count($payment_info) > 0 && $payment_info != ''}
                                    {foreach $payment_info['data'] as $data}
                                            <div class="row">
                                                    <label class="control-label col-lg-3">{$data['label']|escape:'htmlall':'UTF-8'}</label>
                                                    <div class="col-lg-9">
                                                            <p class="form-control-static">{$data['value']|nl2br nofilter}</p>
                                                    </div>
                                            </div>
                                    {/foreach}
                                
                            {else}
                                <p class="text-muted text-center">
                                        {l s='No Payment Information mentioned.' mod='kbmarketplace'}
                                </p>
                            {/if}
                            </div>
                    </div>
                    {hook h="displayAdminSellerCarrier" id_seller=$seller_info['id_seller']|intval id_carrier=$carrier->id|intval display_block="left"}
            </div>
            {*right*}
            <div class="col-lg-6">
                    <div class="panel">
                            <div class="panel-heading">
                                    <i class="icon-ticket"></i> {l s='Carrier Information' mod='kbmarketplace'}
                                    {*<div class="panel-heading-action">
                                        <a class="btn btn-default" target="_blank" href="{$link->getAdminLink('AdminCarrierWizard')|escape:'htmlall':'UTF-8'}&id_carrier={$carrier->id|intval}">
                                                <i class="icon-edit"></i>
                                                {l s='Edit Carrier' mod='kbmarketplace'}
                                        </a>
                                    </div>*}
                            </div>
                            <div class="">
                                <p id="summary_meta_informations"></p>
                                <p id="summary_shipping_cost"></p>
                                <p id="summary_range"></p>
                                <div class="">
                                    <p>{l s='This carrier will be proposed for those delivery zones' mod='kbmarketplace'}</p>
                                    <ul id="summary_zones">
                                        
                                    </ul>
                                </div>
                                <div class="">
                                    <p>{l s='And it will be proposed for those client groups' mod='kbmarketplace'}</p>
                                    <ul id="summary_groups">
                                        
                                    </ul>
                                </div>
                            </div>
                    </div>
                    {hook h="displayAdminSellerCarrier" id_seller=$seller_info['id_seller']|intval id_carrier=$carrier->id|intval display_block="right"}
            </div>
    </div>

    {hook h="displayAdminSellerCarrier" id_seller=$seller_info['id_seller']|intval id_carrier=$carrier->id|intval display_block="below"}
</div>




<script type='text/javascript'>
    
    
	var summary_translation_undefined = "{l s='[undefined]' mod='kbmarketplace'}";
	var summary_translation_meta_informations = "{l s='This carrier is @s1 and the delivery announced is: @s2.' mod='kbmarketplace'}";
	var summary_translation_free = "{l s='free' mod='kbmarketplace'}";
	var summary_translation_paid = "{l s='not free' mod='kbmarketplace'}";
	var summary_translation_range = "{l s='This carrier can deliver orders from @s1 to @s2.' mod='kbmarketplace'}";
	var summary_translation_range_limit =  "{l s='If the order is out of range, the behavior is to @s3.' mod='kbmarketplace'}";
	var summary_translation_shipping_cost = "{l s='The shipping cost is calculated @s1 and the tax rule @s2 will be applied.' mod='kbmarketplace'}";
    var summary_tax_string = "{$tax_string|escape:'htmlall':'UTF-8'}";
    var summary_bill_string = "{$billing_string|escape:'htmlall':'UTF-8'}";
    
    displaySummary();
    
    function displaySummary()
    {
        var tmp,
            delay_text = '{$carrier->delay|escape:'htmlall':'UTF-8'}';

        // Delay and pricing
        tmp = summary_translation_meta_informations.replace('@s2', '<strong>' + delay_text + '</strong>');
        {if $carrier->is_free eq 1}
            tmp = tmp.replace('@s1', '<strong>' + summary_translation_free + '</strong>');
        {else}
            tmp = tmp.replace('@s1', '<strong>' + summary_translation_paid + '</strong>');
        {/if}
        $('#summary_meta_informations').html(tmp);

        // Tax and calculation mode for the shipping cost
        tmp = summary_translation_shipping_cost.replace('@s2', '<strong>'+summary_tax_string+'</strong>');

        tmp = tmp.replace('@s1', '<strong>'+summary_bill_string+'</strong>');

        $('#summary_shipping_cost').html(tmp);

        // Weight or price ranges
        tmp = summary_translation_range+' '+summary_translation_range_limit;
        $('#summary_range').html('<span class="is_free">'+summary_translation_range+'</span> '+summary_translation_range_limit);


        tmp = summary_translation_undefined;
        {if $carrier->range_behavior eq 0}
            tmp = '{l s='Apply the cost of the highest defined range' mod='kbmarketplace'}';
        {elseif  $carrier->range_behavior eq 1}
            tmp = '{l s='Disable carrier' mod='kbmarketplace'}';
        {/if}
        $('#summary_range').html(
            $('#summary_range').html()
            .replace('@s1', '<strong>{$range_inf|escape:'htmlall':'UTF-8'} {$unit|escape:'htmlall':'UTF-8'}</strong>')
            .replace('@s2', '<strong>{$range_sup|escape:'htmlall':'UTF-8'} {$unit|escape:'htmlall':'UTF-8'}</strong>')
            .replace('@s3', '<strong>' + tmp + '</strong>')
        );


        {if $carrier->is_free eq 1}
            $('span.is_free').hide();
        {/if}

        // Delivery zones
        tmp = '';
        $('#summary_zones').html('');
        {if count($zones) > 0}
            {foreach $zones as $z}
                tmp += '<li><strong>{$z|escape:'htmlall':'UTF-8'}</strong></li>';
            {/foreach}
        {else}
            tmp = '<li><strong>{l s='No Zone' mod='kbmarketplace'}</strong></li>';
        {/if}
        $('#summary_zones').html(tmp);

        // Group restrictions
        tmp = '';
        $('#summary_groups').html('');
        {if count($groups) > 0}
            {foreach $groups as $z}
                tmp += '<li><strong>{$z|escape:'htmlall':'UTF-8'}</strong></li>';
            {/foreach}
        {else}
            tmp = '<li><strong>{l s='No Group' mod='kbmarketplace'}</strong></li>';
        {/if}
        $('#summary_groups').html(tmp);
    }
    
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