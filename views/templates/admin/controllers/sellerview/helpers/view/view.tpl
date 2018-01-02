<div id="container-seller-view">
    {if $display_summary eq true}
        <div class="row">
            <div class="panel kpi-container">
                <div class="row">
                    <div class="col-sm-6 col-lg-3">
                        <div id="box-conversion-rate" class="box-stats color1">
                            <div class="kpi-content">
                                <i class="icon-archive"></i>

                                <span class="title">{l s='Total Products' mod='kbmarketplace'}</span>
                                <span class="value">{$total_products|intval}</span>
                            </div>
                        </div>
                    </div>			
                    <div class="col-sm-6 col-lg-3">
                        <div id="box-carts" class="box-stats color2">
                            <div class="kpi-content">
                                <i class="icon-off"></i>
                                <span class="title">{l s='Products Need Approval' mod='kbmarketplace'}</span>
                                <span class="value">{$total_product_need_approval|intval}</span>
                            </div>
                        </div>
                    </div>			
                    <div class="col-sm-6 col-lg-3">
                        <div id="box-average-order" class="box-stats color3">
                            <div class="kpi-content">
                                <i class="icon-comment"></i>
                                <span class="title">{l s='Reviews' mod='kbmarketplace'}</span>
                                <span class="value">{$total_seller_reviews|intval}</span>
                            </div>
                        </div>
                    </div>			
                    <div class="col-sm-6 col-lg-3">
                        <div id="box-net-profit-visit" class="box-stats color4">
                            <div class="kpi-content">
                                <i class="icon-tags"></i>
                                <span class="title">{l s='Pending Category Request' mod='kbmarketplace'}</span>
                                <span class="value">{$total_pending_cat_request|intval}</span>
                            </div>
                        </div>
                    </div>			
                </div>
            </div>
        </div>
    {/if}
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
                                    <div class="panel-heading-action">
                                            <a class="btn btn-default" href="{$link->getAdminLink('AdminCustomers')|escape:'htmlall':'UTF-8'}&updatecustomer&id_customer={$seller_info['id_customer']|intval}">
                                                    <i class="icon-edit"></i>
                                                    {l s='Edit Settings' mod='kbmarketplace'}
                                            </a>
                                    </div>
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
                    {hook h="displayAdminSeller" id_seller=$seller_info['id_seller']|intval display_block="left"}
            </div>
            {*right*}
            <div class="col-lg-6">
                    <div class="panel">
                            <div class="panel-heading">
                                    <i class="icon-ticket"></i> {l s='Description' mod='kbmarketplace'}
                            </div>
                            {if $seller_info['description'] != ''}
                                <p>
                                        {html_entity_decode($seller_info['description']) nofilter}
                                </p>
                            {else}
                                <p class="text-muted text-center">
                                        {l s='No description provided by seller for about himself.' mod='kbmarketplace'}
                                </p>
                            {/if}
                    </div>
                    <div class="panel">
                            <div class="panel-heading">
                                    <i class="icon-ticket"></i> {l s='Shipping Policy' mod='kbmarketplace'}
                            </div>
                            {if $seller_info['shipping_policy'] != ''}
                                <p>
                                        {html_entity_decode($seller_info['shipping_policy']) nofilter}
                                </p>
                            {else}
                                <p class="text-muted text-center">
                                        {l s='No shipping policy mentioned.' mod='kbmarketplace'}
                                </p>
                            {/if}
                    </div>
                    <div class="panel">
                            <div class="panel-heading">
                                    <i class="icon-ticket"></i> {l s='Return Policy' mod='kbmarketplace'}
                            </div>
                            {if $seller_info['return_policy'] != ''}
                                <p>
                                        {html_entity_decode($seller_info['return_policy']) nofilter}
                                </p>
                            {else}
                                <p class="text-muted text-center">
                                        {l s='No return policy mentioned.' mod='kbmarketplace'}
                                </p>
                            {/if}
                    </div>
                    <div class="panel">
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
                    {hook h="displayAdminSeller" id_seller=$seller_info['id_seller']|intval display_block="right"}
            </div>
    </div>

    {hook h="displayAdminSeller" id_seller=$seller_info['id_seller']|intval display_block="below"}
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