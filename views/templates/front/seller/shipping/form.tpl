<div class="kb-content">
    {if !isset($permission_error)}
        <div class="kb-content-header">
            <h1>{$form_heading}</h1>
            <div class="clearfix"></div>
        </div>
        <form id="kb-shipping-form" action="{$shipping_submit_url nofilter}" method="post" enctype="multipart/form-data">
            <input type="hidden" name="shipping_form" value="1" />
            <div id="kb-shipping-form-global-msg" class="kbalert kbalert-danger" style="display:none;"></div>
            <div class="kbalert kbalert-info">
                <i class="kb-material-icons"></i>{l s='Fields marked with (*) are mandatory fields.' mod='kbmarketplace'}
            </div>
            <div class="kb-panel outer-border">
                <div class='kb-panel-body'>
                    <div class="kb-block kb-form">
                        <ul class="kb-form-list">
                            <li class="kb-form-l">
                                <div class="kb-form-label-block">
                                    <span class="kblabel ">{l s='Name' mod='kbmarketplace'}</span><em>*</em>
                                </div>
                                <div class="kb-form-field-block">
                                    <input type="text" class="kb-inpfield required" id="kb_mp_shipping_name" validate="isGenericName" name="name" value="{$carrier->name}" />
                                </div>
                            </li>
                            <li class="kb-form-r">
                                <div class="kb-form-label-block">
                                    <span class="kblabel ">{l s='Delay Message' mod='kbmarketplace'}</span><em>*</em>
                                </div>
                                <div class="kb-form-field-block">
                                    <input type="text" class="kb-inpfield required" id="kb_mp_shipping_delay" validate="isGenericName" name="delay_{$default_lang|intval}" value="{$carrier->delay}" />
                                </div>
                            </li>
                            <li class="kb-form-l">
                                <div class="kb-form-label-block">
                                    <span class="kblabel ">{l s='Active' mod='kbmarketplace'}</span>
                                </div>
                                <div class="kb-form-field-block">
                                    <select name="active" class="kb-inpselect">
                                        <option value="0" {if $carrier->active eq 0}selected="selected"{/if}>{l s='No' mod='kbmarketplace'}</option>
                                        <option value="1" {if $carrier->active eq 1}selected="selected"{/if}>{l s='Yes' mod='kbmarketplace'}</option>
                                    </select>
                                </div>
                            </li>
                            <li class="kb-form-r">
                                <div class="kb-form-label-block">
                                    <span class="kblabel ">{l s='Free Shipping' mod='kbmarketplace'}</span>
                                </div>
                                <div class="kb-form-field-block">
                                    <select id='is_free' name="is_free" class="kb-inpselect">
                                        <option value="0" {if $carrier->is_free eq 0}selected="selected"{/if}>{l s='No' mod='kbmarketplace'}</option>
                                        <option value="1" {if $carrier->is_free eq 1}selected="selected"{/if}>{l s='Yes' mod='kbmarketplace'}</option>
                                    </select>
                                </div>
                            </li>
                            <li class="kb-form-fwidth">
                                <div class="form-lbl-indis">
                                    <span class="kblabel">{l s='Logo' mod='kbmarketplace'}</span>
                                    <p class="form-inp-help">{l s='Logo size should be (%s).'|sprintf:'40 X 40' mod='kbmarketplace'}</p>
                                </div>
                                <div class="form-field-indis">
                                    <div class="form-img-display" style='width: 52px;'>
                                        <img id="shipping_logo_placeholder" class="" style='height:40px; width:40px;' src="{$shipping_logo nofilter}" title="{l s='Logo of your shipping' mod='kbmarketplace'}">
                                    </div>
                                    <input id="shipping_logo" class="" type="file" name="carrier_logo_input" style="display:none;" />
                                    <input type="hidden" id="logo" name="logo" value="" />
                                    <input type="hidden" id="update_shipping_logo" value="0"/>
                                    <input type="hidden" id="is_kb_shipping_logo_updated" value="{$is_kb_shipping_logo_updated}"/>
                                    <input type="hidden" id="kb_shipping_default_logo" value="{$ship_default_logo nofilter}"/>
                                    <div class="kb-block file-uploader">
                                        <a href="javascript:void(0)" id="kb_shipping_logo_browse" onclick="$('#shipping_logo').trigger('click');" >{l s='Browse' mod='kbmarketplace'}</a>
                                        {if $is_kb_shipping_logo_updated}
                                            <a href="javascript:void(0)" id="kb_shipping_logo_remove" style="{if $is_kb_shipping_logo_updated} display:block;{else} display:none; {/if}" onclick="removeCarrierLogo('{if $is_kb_shipping_logo_updated} {$shipping_logo nofilter} {else} {$ship_default_logo nofilter} {/if}')" >{l s='Remove' mod='kbmarketplace'}</a>
                                        {else}
                                            <a href="javascript:void(0)" id="kb_shipping_logo_remove"  style="{if $is_kb_shipping_logo_updated} display:block;{else} display:none; {/if}" onclick="removeCarrierLogo('{$ship_default_logo nofilter}')" >{l s='Remove' mod='kbmarketplace'}</a>
                                        {/if}
                                        <input id="shipping_logo_update" type="hidden" name="shipping_logo_update" value="0" />
                                    </div>
                                    <div id="shipping_logo_error" class="kb-validation-error"></div>
                                </div>
                            </li>
                            <li class="kb-form-l">
                                <div class="kb-form-label-block">
                                    <span class="kblabel ">{l s='Maximum Package Width' mod='kbmarketplace'}</span>
                                </div>
                                <div class="kb-form-field-block">
                                    <div class="kb-labeled-inpfield">
                                        <span class="inplbl">{$ps_dimension_unit}</span>
                                        <input type="text" class="kb-inpfield" validate="isPrice" name="max_width" value="{$carrier->max_width}" maxlength="14" />
                                    </div>
                                </div>
                            </li>
                            <li class="kb-form-r">
                                <div class="kb-form-label-block">
                                    <span class="kblabel ">{l s='Maximum Package Height' mod='kbmarketplace'}</span>
                                </div>
                                <div class="kb-form-field-block">
                                    <div class="kb-labeled-inpfield">
                                        <span class="inplbl">{$ps_dimension_unit}</span>
                                        <input type="text" class="kb-inpfield" validate="isPrice" name="max_height" value="{$carrier->max_height}" maxlength="14" />
                                    </div>
                                </div>
                            </li>
                            <li class="kb-form-l">
                                <div class="kb-form-label-block">
                                    <span class="kblabel ">{l s='Maximum Package Depth' mod='kbmarketplace'}</span>
                                </div>
                                <div class="kb-form-field-block">
                                    <div class="kb-labeled-inpfield">
                                        <span class="inplbl">{$ps_dimension_unit}</span>
                                        <input type="text" class="kb-inpfield" validate="isPrice" name="max_depth" value="{$carrier->max_depth}" maxlength="14" />
                                    </div>
                                </div>
                            </li>
                            <li class="kb-form-r">
                                <div class="kb-form-label-block">
                                    <span class="kblabel ">{l s='Maximum Package Weight' mod='kbmarketplace'}</span>
                                </div>
                                <div class="kb-form-field-block">
                                    <div class="kb-labeled-inpfield">
                                        <span class="inplbl">{$ps_weight_unit}</span>
                                        <input type="text" class="kb-inpfield" validate="isPrice" name="max_weight" value="{$carrier->max_weight}" maxlength="14" />
                                    </div>
                                </div>
                            </li>
                            <li class="kb-form-fwidth">
                                <div class="kb-form-label-block">
                                    <span class="kblabel ">{l s='Billing' mod='kbmarketplace'}</span>
                                </div>
                                <div class="kb-form-field-block">
                                    <div class="kboption-inline kb-inpoption">
                                        <input class="" type="radio" name="shipping_method" id="label_for_shipping_method_price" value="{$shipping_method_price|intval}" {if $carrier->shipping_method == $shipping_method_price}checked="checked"{/if} /> <label for="label_for_shipping_method_price">{l s='According to total price' mod='kbmarketplace'}</label>    
                                    </div>
                                    <div class="kboption-inline kb-inpoption">
                                        <input class="" type="radio" name="shipping_method" id="label_for_shipping_method_weight" value="{$shipping_method_weight|intval}"  {if $carrier->shipping_method == $shipping_method_weight}checked="checked"{/if} /> <label  for="label_for_shipping_method_weight">{l s='According to total weight' mod='kbmarketplace'}</label>    
                                    </div>
                                </div>
                            </li>
                            <li class="kb-form-fwidth">
                                <div class="kb-form-label-block">
                                    <span class="kblabel ">{l s='Ranges' mod='kbmarketplace'}</span>
                                    <p class="kbalert kbalert-info form-inp-help">
                                        <i class="kb-material-icons" style="    font-size: 18px !important;margin-right: 6px !important;margin-left: 6px !important;"></i>{l s='Please provide valid range before using any checkbox, shown below the set range boxes.' mod='kbmarketplace'}</p>
                                </div>
                                {include file="./ranges.tpl"}
                                <div class='kb-vspacer5'></div>
                                <button type="button" class='kbbtn btn-warning' onclick="add_new_range();">{l s='Add New Range' mod='kbmarketplace'}</button>
                            </li>
                            {if isset($mapping_shipping) && mapping_shipping}
                            <li class="kb-form-fwidth kb-table-list" style="padding-left: 20px;">
                                <div class="kb-form-field-block">
                                    <div class="kboption-inline kb-inpoption">
                                        <input class="" type="checkbox" name="mapped_all" id="label_for_mapped_shipping_method" value="1"/> <label for="label_for_mapped_shipping" style="font-weight: bold;">{l s='Mapped this shipping with all products' mod='kbmarketplace'}</label>    
                                    </div>
                                </div>
                            </li>
                            {/if}
                        </ul>
                    </div>
                </div>
            </div>
            <div class='kb-vspacer5'></div>
            <button id='saveShippingBtn' type="button" class='btn-sm btn-success' onclick="validateForm()">{l s='Save' mod='kbmarketplace'}</button>
        </form>
        <script>
             {if $kb_shipping_logo_path != ''}
                var kb_shipping_logo_path = '{$kb_shipping_logo_path}';
            {else}
                  var kb_shipping_logo_path = '';  
            {/if}
            var kb_shipping_url = '{$kb_shipping_url}';
            var zones_nbr = {(count($zones)+3)|intval};
            var kb_id_carrier = {$carrier->id|intval};
            var kb_default_lang = {$default_lang|intval};
            var kb_shipping_currency_unit = '{$ps_currency_unit}';
            var kb_shipping_weight_unit = '{$ps_weight_unit}';
            var kb_shipping_range_price = '{l s='Will be applied when the price is' mod='kbmarketplace'}';
            var kb_shipping_range_weight = '{l s='Will be applied when the weight is' mod='kbmarketplace'}';
            var kb_form_validation_error = '{l s='Please fill the detail with valid values.' mod='kbmarketplace'}';
            var invalid_range = '{l s='This range is not valid' mod='kbmarketplace' mod='kbmarketplace'}';
            var range_is_overlapping = '{l s='Ranges are overlapping' mod='kbmarketplace'}';
            var select_at_least_one_zone = '{l s='Please select at least one zone' mod='kbmarketplace'}';
            var need_to_validate = '{l s='Please validate the last range before create a new one.' mod='kbmarketplace'}';
            var delete_range_confirm = '{l s='Are you sure?' mod='kbmarketplace'}';
            var labelDelete = '{l s='Delete' mod='kbmarketplace'}';
            
            var kb_img_format = [];

            {foreach $kb_img_frmats as $for}
                kb_img_format.push('{$for}');
            {/foreach} 
               
        </script>
    {/if}
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
