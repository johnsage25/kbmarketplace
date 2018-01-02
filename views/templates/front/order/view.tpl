<div class="kb-content">
    <div class="kb-content-header">
        <h1>{l s='Order Detail' mod='kbmarketplace'}</h1>
        <div class="clearfix"></div>
    </div>
    <div class="kb-content">
        {hook h="displayKbMarketPlaceOrderDetail" id_order=$order->id block='top'}
        <div class='kb-cn-lb'>
            <ul class='ov-s-l'>
                <li class='ov-s-head'>{l s='Summary' mod='kbmarketplace'}</li>
                <li>
                    <div class='ov-s-lbl'>{l s='Reference' mod='kbmarketplace'} :</div>
                    <div class='ov-s-value'><b class="highlight">{$order->getUniqReference()}</b> <small>{l s='(%s items)' sprintf=array($items_ordered) mod='kbmarketplace'}</small></div>
                </li>
                {if count($order_state) > 0}
                <li>
                    <div class='ov-s-lbl'>{l s='Status' mod='kbmarketplace'} :</div>
                    <div class='ov-s-value' style='color:{$order_state['color']}'>{$order_state['name']}</div>
                </li>
                {/if}
                <li>
                    <div class='ov-s-lbl'>{l s='Date' mod='kbmarketplace'} :</div>
                    <div class='ov-s-value'>{dateFormat date=$order->date_add full=true}</div>
                </li>
                <li>
                    <div class='ov-s-lbl'>{l s='Payment Method' mod='kbmarketplace'} :</div>
                    <div class='ov-s-value'>{$order->payment}</div>
                </li>
                {if count($order->getShipping()) == 1}
                    {if $carrier->id}
                    <li>
                        <div class='ov-s-lbl'>{l s='Shipping' mod='kbmarketplace'} :</div>
                        <div class='ov-s-value'>{if $carrier->name == "0"}{$shop_name}{else}{$carrier->name}{/if}</div>
                    </li>
                    {/if}
                    <li>
                        <div class='ov-s-lbl'>{l s='Tracking No.' mod='kbmarketplace'} :</div>
                        <div class='ov-s-value'>{if $order->getWsShippingNumber() neq ''}{$order->getWsShippingNumber()}{else}--{/if}</div>
                    </li>
                {/if}
                <li>
                    <div class='ov-s-lbl'>{l s='Email' mod='kbmarketplace'} :</div>
                    <div class='ov-s-value'>{$customer_email}</div>
                </li>
            </ul>
        </div>
        <div class='kb-cn-rb'>
            <ul class='ov-a-l'>
                <li class='ov-s-head'>{l s='Invoice Address' mod='kbmarketplace'}</li>
                <li class='ov-add-name'>{$invoice_address_txt['name']}</li>
                <li class='ov-add-detail'>{$invoice_address_txt['address'] nofilter}</li>
            </ul>
            {if !$order->isVirtual()}
            <ul class='ov-a-l'>
                <li class='ov-s-head'>{l s='Delivery Address' mod='kbmarketplace'}</li>
                <li class='ov-add-name'>{$delv_address_txt['name']}</li>
                <li class='ov-add-detail'>{$delv_address_txt['address'] nofilter}</li>
            </ul>
            {/if}
        </div>
        <div class='clearfix'></div>
        <div class="kb-vspacer5"></div>
        <div class="kb-block kb-content">
            {if $can_handle_order}
                {if Configuration::get('PS_INVOICE') && count($order->getInvoicesCollection()) && $order->invoice_number}
                    <a style="display: inline-block;" id="view_invoice" class="btn-sm btn-tertiary-outline " href="{$link->getModuleLink($kb_module_name, 'order', ['generateInvoicePdf' => true, 'id_order' => $order->id], (bool)Configuration::get('PS_SSL_ENABLED')) nofilter}">
                        <i class="kb-material-icons"></i>
                        {l s='View invoice' mod='kbmarketplace'}
                    </a>
                {else}
                    <span class="span label label-inactive">
                        <i class="kb-material-icons"></i>
                        {l s='No invoice' mod='kbmarketplace'}
                    </span>
                {/if}
                &nbsp;
                {if $order->delivery_number}
                    <a style="display: inline-block;" id="view_delivery_slip" class="btn-sm btn-tertiary-outline _blank"  href="{$link->getModuleLink($kb_module_name, 'order', ['generateDeliverySlipPDF' => true, 'id_order' => $order->id], (bool)Configuration::get('PS_SSL_ENABLED')) nofilter}">
                        <i class="kb-material-icons"></i>
                        {l s='View delivery slip' mod='kbmarketplace'}
                    </a>
                {else}
                    <span class="span label label-inactive">
                        <i class="kb-material-icons"></i>
                        {l s='No delivery slip' mod='kbmarketplace'}
                    </span>
                {/if}
                &nbsp;
                <hr style="margin-top:5px;margin-bottom:5px;">
            {/if}
            {hook h="displayKbMarketPlaceOrderDetail" id_order=$order->id block='print'}
            {if !$order->isVirtual()}
                {if $order->recyclable}
                    <span class="label label-success"><i class="kb-material-icons"></i> {l s='Recycled packaging' mod='kbmarketplace'}</span>
                {else}
                    <span class="label label-inactive"><i class="kb-material-icons"></i> {l s='Recycled packaging' mod='kbmarketplace'}</span>
                {/if}

                {if $order->gift}
                    <span class="label label-success"><i class="kb-material-icons"></i> {l s='Gift wrapping' mod='kbmarketplace'}</span>
                {else}
                    <span class="label label-inactive"><i class="kb-material-icons"></i> {l s='Gift wrapping' mod='kbmarketplace'}</span>
                {/if}
            {/if}
        </div>
        <div class="kb-vspacer5"></div>
        <div class="" style="overflow-x:auto; overflow-y:hidden; background-color:#fff;">
            {if count($history) > 0}
                <div class='ov-s-head' style="padding-left:0 !important; background-color:transparent;">{l s='Status History' mod='kbmarketplace'}</div>
                <table class='ov-p-l'>
                    <thead>
                        <tr>
                            <th>{l s='Date' mod='kbmarketplace'}</th>
                            <th>{l s='Status' mod='kbmarketplace'}</th>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach from=$history item=row key=key}
                            <tr>
                                <td>
                                    {dateFormat date=$row['date_add'] full=true}
                                <td>
                                    <span style="padding:3px;background-color:{$row['color']};color:{$row['text-color']}">{$row['ostate_name']}</span>
                                </td>
                            </tr>
                        {/foreach}
                    </tbody>
                </table>
                <div class="kb-vspacer5"></div>
            {/if}
            {if $can_handle_order}
                <form id='order_status_form' method='post' action="{$link->getModuleLink($kb_module_name, 'order', ['id_order' => $order->id], (bool)Configuration::get('PS_SSL_ENABLED')) nofilter}" class="well kb-form">
                    <div class="kb-form-field-block">
                        <div class="col-lg-9 kb-form-field-inblock">
                            <select name="id_order_state" class="kb-inpselect">
                                {foreach from=$states item=state}
                                    <option value="{$state['id_order_state']|intval}"{if isset($currentState) && $state['id_order_state'] == $currentState->id} selected="selected" disabled="disabled"{/if}>{$state['name']|escape}</option>
                                {/foreach}
                            </select>
                        </div>
                        <div class="col-lg-3 kb-form-field-inblock">
                            <input type="hidden" id="seller_order_handling" value="{$seller_order_handling}"/>
                            <button type="submit" name="submitState" id="update_status_btn" class="btn btn-primary" style="padding: 4px 12px;">{l s='Update status' mod='kbmarketplace'}</button>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </form>
            {/if}
        </div>
        <div class="kb-vspacer5"></div>
        <div class="shipping_detail_list" style="overflow-x:auto; overflow-y:hidden; background-color:#fff;">
            <div class='ov-s-head' style="padding-left:0 !important; background-color:transparent;">{l s='Shippings' mod='kbmarketplace'}</div>
            <table class='ov-p-l'>
                <thead>
                    <tr>
                        <th>{l s='Date' mod='kbmarketplace'}</th>
                        <th>&nbsp;</th>
                        <th>{l s='Shipping' mod='kbmarketplace'}</th>
                        <th>{l s='Weight' mod='kbmarketplace'}</th>
                        <th>{l s='Cost' mod='kbmarketplace'}</th>
                        <th>{l s='Tracking number' mod='kbmarketplace'}</th>
                        {if $can_handle_order}<th>&nbsp;</th>{/if}
                    </tr>
                </thead>
                <tbody>
                    {foreach from=$order->getShipping() item=line}
                    <tr>
                        <td>{dateFormat date=$line.date_add full=true}</td>
                        <td>&nbsp;</td>
                        <td>{str_replace($carrier_replace_str, '', $line.carrier_name)}</td>
                        <td class="weight">{number_format($line.weight, 3, '.', '')} {Configuration::get('PS_WEIGHT_UNIT')}</td>
                        <td class="center">
                            {if $order->getTaxCalculationMethod() == $smarty.const.PS_TAX_INC}
                                {Tools::displayPrice($line.shipping_cost_tax_incl) nofilter}
                            {else}
                                {Tools::displayPrice($line.shipping_cost_tax_excl) nofilter}
                            {/if}
                        </td>
                        <td>
                            <span class="shipping_number_show">{if $line.url && $line.tracking_number}<a class="_blank" href="{str_replace('@', $line.tracking_number, $line.url) nofilter}">{$line.tracking_number}</a>{else}{$line.tracking_number}{/if}</span>
                        </td>
                        {if $can_handle_order}
                            <td>
                                {if $line.can_edit}
                                    <form method="post" action="{$link->getModuleLink($kb_module_name, 'order', ['id_order' => $order->id], (bool)Configuration::get('PS_SSL_ENABLED'))|escape:'htmlall':'UTF-8'}">
                                        <span class="shipping_number_edit" style="display:none;">
                                            <input type="hidden" name="id_order_carrier" value="{$line.id_order_carrier|intval}" />
                                            <input class="kb-inpfield" type="text" name="tracking_number" value="{$line.tracking_number}" style="width:40%; margin-bottom:5px;"/>
                                            <button type="submit" class="btn-sm btn-success" name="submitShippingNumber">
                                                <i class="kb-material-icons"></i>
                                            </button>
                                        </span>
                                        <a href="javascript:void(0)" class="edit_shipping_number_link btn-sm btn-warning">
                                            <i class="kb-material-icons"></i>
                                        </a>
                                        <a href="javascript:void(0)" class="cancel_shipping_number_link btn-sm btn-danger" style="display: none;">
                                            <i class="kb-material-icons">&#xe5c9;</i>
                                        </a>
                                    </form>
                                {/if}
                            </td>
                        {/if}
                    </tr>
                    {/foreach}
                </tbody>
            </table>
            {if $can_handle_order}
                <script type='text/javascript'>
                    $(document).ready(function(){
                        $('.edit_shipping_number_link').on('click', function(){
                            $('.shipping_number_edit').show();
                            $('.cancel_shipping_number_link').show();
                            $(this).hide();
                        });

                        $('.cancel_shipping_number_link').on('click', function(){
                            $('.shipping_number_edit').hide();
                            $('.edit_shipping_number_link').show();
                            $(this).hide();
                        });
                    });
                </script>
            {/if}
            <div class="kb-vspacer5"></div>
        </div>
        <div class="kb-vspacer5"></div>
        <div class="" style="overflow-x:auto; overflow-y:hidden; background-color:#fff;">
            <div class='ov-s-head' style="padding-left:0 !important; background-color:transparent;">{l s='Products Details' mod='kbmarketplace'}</div>
            {assign var='hasBeenPaid' value=$order->hasBeenPaid()}
            {assign var='hasBeenDelivered' value=$order->hasBeenDelivered()}
            {assign var='hasProductReturned' value=$order->hasProductReturned()}
            {assign var='grid_column' value=4}
            {if ($hasBeenPaid)}
                {assign var='grid_column' value=5}
            {/if}
            {if ($hasBeenDelivered || $hasProductReturned)}
                {assign var='grid_column' value=6}
            {/if}
            <table class='ov-p-l'>
                <thead>
                    <tr>
                        <th>{l s='Product' mod='kbmarketplace'}</th>
                        <th>{l s='Qty' mod='kbmarketplace'}</th>
                        {if ($hasBeenPaid)}<th >{l s='Refunded' mod='kbmarketplace'}</th>{/if}
                        {if ($hasBeenDelivered || $hasProductReturned)}<th>{l s='Returned' mod='kbmarketplace'}</th>{/if}
                        <th width="80">{l s='Price' mod='kbmarketplace'}</th>
                        <th width="80">{l s='Total' mod='kbmarketplace'}</th>
                    </tr>
                </thead>
                <tbody>
                    {foreach from=$products item=product name=products}
                            {if !isset($product.deleted)}
                                    {assign var='productId' value=$product.product_id}
                                    {assign var='productAttributeId' value=$product.product_attribute_id}
                                    {if isset($product.customizedDatas)}
                                            {assign var='productQuantity' value=$product.product_quantity-$product.customizationQuantityTotal}
                                    {else}
                                            {assign var='productQuantity' value=$product.product_quantity}
                                    {/if}
                                    <!-- Customized products -->
                                    {if isset($product.customizedDatas)}
                                        <tr class="item">
                                            <td>
                                                <div class='ov-p-info'>
                                                    {$product.product_name}
                                                    <span class='ref'>{l s='Ref:' mod='kbmarketplace'} {if $product.product_reference}{$product.product_reference}{else}--{/if}</span>
                                                </div>
                                            </td>
                                            <td>{$product.customizationQuantityTotal|intval}</td>
                                            {if $grid_column > 4}<td colspan="{($grid_column-4)|intval}"></td>{/if}
                                            <td>{Tools::displayPrice($product.unit_price_tax_incl) nofilter}</td>
                                            <td>
                                                {if isset($customizedDatas.$productId.$productAttributeId)}
                                                        {Tools::displayPrice($product.total_customization_wt) nofilter}
                                                {else}
                                                        {Tools::displayPrice($product.total_price_tax_incl) nofilter}
                                                {/if}
                                            </td>
                                        </tr>
                                        {foreach $product.customizedDatas  as $customizationPerAddress}
                                                {foreach $customizationPerAddress as $customizationId => $customization}
                                                <tr class="alternate_item">
                                                    <td>
                                                        {foreach from=$customization.datas key='type' item='datas'}
                                                            <div class="customized_row">
                                                                {if $type == $CUSTOMIZE_FILE}
                                                                    <ul class="customizationUploaded">
                                                                            {foreach from=$datas item='data'}
                                                                                    <li><img src="{$pic_dir}{$data.value}_small" alt="" class="customizationUploaded" /></li>
                                                                            {/foreach}
                                                                    </ul>
                                                                {elseif $type == $CUSTOMIZE_TEXTFIELD}
                                                                    <ul class="typedText">{counter start=0 print=false}
                                                                            {foreach from=$datas item='data'}
                                                                                    {assign var='customizationFieldName' value="Text #"|cat:$data.id_customization_field}
                                                                                    <li>
											{if $data.name neq ''}
											    {$data.name}
											{else}
											    {$customizationFieldName}
											{/if}
											: {$data.value}</li>
                                                                            {/foreach}
                                                                    </ul>
                                                                {/if}
                                                            </div>
                                                        {/foreach}
                                                    </td>
                                                    <td>{$customization.quantity|intval}</td>
                                                    <td colspan="{($grid_column-2)|intval}"></td>
                                                </tr>
                                                {/foreach}
                                        {/foreach}
                                    {/if}
                                    {if $product.product_quantity > $product.customizationQuantityTotal}
                                        <tr>
                                            <td>
                                                {if isset($product.image) && $product.image->id}<div class='ov-p-img'>{$product.image_tag nofilter}</div>{/if}
                                                <div class='ov-p-info'>
                                                    {$product['product_name']}
                                                    <span class='ref'>{l s='Ref:' mod='kbmarketplace'} {if $product.product_reference}{$product.product_reference}{else}--{/if}</span>
                                                </div>
                                            </td>
                                            <td>{$product['product_quantity']|intval}</td>
                                            {if ($hasBeenPaid)}
                                                <td>
                                                    {$product['product_quantity_refunded']|intval}
                                                    {if count($product['refund_history'])}
                                                        <span class='ov-p-refund_history'>{l s='Refund history' mod='kbmarketplace'}</span>
                                                        <span class="ov-p-refund_history_tooltip">
                                                            {foreach $product['refund_history'] as $refund}
                                                                    {l s='%1s - %2s' sprintf=[{dateFormat date=$refund.date_add}, {Tools::displayPrice($refund.amount_tax_incl) nofilter}] mod='kbmarketplace'}<br />
                                                            {/foreach}
                                                        </span>
                                                    {/if}
                                                </td>
                                            {/if}
                                            {if $hasBeenDelivered || $hasProductReturned}
                                                <td>
                                                    {$product['product_quantity_return']|intval}
                                                    {if count($product['return_history'])}
                                                        <span class='ov-p-refund_history'>{l s='Return history' mod='kbmarketplace'}</span>
                                                        <span class="ov-p-refund_history_tooltip">
                                                            {foreach $product['return_history'] as $return}
                                                                    {l s='%1s - %2s - %3s' sprintf=[{dateFormat date=$return.date_add}, $return.product_quantity, $return.state] mod='kbmarketplace'}<br />
                                                            {/foreach}
                                                        </span>
                                                    {/if}
                                                </td>
                                            {/if}
                                            <td>{Tools::displayPrice($product['unit_price_tax_incl']) nofilter}</td>
                                            <td>{Tools::displayPrice((Tools::ps_round($product['unit_price_tax_incl'], 2) * ($product['product_quantity'] - $product['customizationQuantityTotal']))) nofilter}</td>
                                        </tr>
                                    {/if}
                            {/if}
                    {/foreach}
                    {hook h="displayKbMarketPlaceOrderDetail" id_order=$order->id block='product_line'}
                </tbody>
                <tfoot>
                    {if !$can_handle_order}
                        {hook h="displayKbMarketPlaceOrderDetail" id_order=$order->id block='order_total'}
                        <tr>
                            <td colspan="{($grid_column-1)|intval}" class="kb-tright">{l s='Total' mod='kbmarketplace'}</td>
                            <td>{displayPrice price=$seller_earning['total_earning'] currency=$order_currency->id}</td>
                        </tr>
                    {else}
                        {* Assign order price *}
                        {if ($order->getTaxCalculationMethod() == $smarty.const.PS_TAX_EXC)}
                            {assign var=order_product_price value=($order->total_products)}
                            {assign var=order_discount_price value=$order->total_discounts_tax_excl}
                            {assign var=order_wrapping_price value=$order->total_wrapping_tax_excl}
                            {assign var=order_shipping_price value=$order->total_shipping_tax_excl}
                        {else}
                            {assign var=order_product_price value=$order->total_products_wt}
                            {assign var=order_discount_price value=$order->total_discounts_tax_incl}
                            {assign var=order_wrapping_price value=$order->total_wrapping_tax_incl}
                            {assign var=order_shipping_price value=$order->total_shipping_tax_incl}
                        {/if}
                        <tr>
                            <td colspan="{($grid_column-1)|intval}" class="kb-tright">{l s='Sub-Total' mod='kbmarketplace'}</td>
                            <td>{displayPrice price=$order_product_price currency=$order_currency->id}</td>
                        </tr>
                        <tr {if $order->total_discounts_tax_incl == 0}style="display: none;"{/if}>
                            <td colspan="{($grid_column-1)|intval}" class="kb-tright">{l s='Discounts' mod='kbmarketplace'}</td>
                            <td>{displayPrice price=$order_discount_price currency=$order_currency->id}</td>
                        </tr>
                        <tr {if $order->total_wrapping_tax_incl == 0}style="display: none;"{/if}>
                            <td colspan="{($grid_column-1)|intval}" class="kb-tright">{l s='Wrapping' mod='kbmarketplace'}</td>
                            <td>{displayPrice price=$order_wrapping_price currency=$order_currency->id}</td>
                        </tr>
                        <tr>
                            <td colspan="{($grid_column-1)|intval}" class="kb-tright">{l s='Shipping & Handling' mod='kbmarketplace'}</td>
                            <td>{displayPrice price=$order_shipping_price currency=$order_currency->id}</td>
                        </tr>
                        {if ($order->getTaxCalculationMethod() == $smarty.const.PS_TAX_EXC)}
                        <tr>
                            <td colspan="{($grid_column-1)|intval}" class="kb-tright">{l s='Taxes' mod='kbmarketplace'}</td>
                            <td>{displayPrice price=($order->total_paid_tax_incl-$order->total_paid_tax_excl) currency=$order_currency->id}</td>
                        </tr>
                        {/if}
                        {hook h="displayKbMarketPlaceOrderDetail" id_order=$order->id block='order_total'}
                        <tr>
                            <td colspan="{($grid_column-1)|intval}" class="kb-tright">{l s='Total' mod='kbmarketplace'}</td>
                            <td>{displayPrice price=($order->total_paid_tax_incl) currency=$order_currency->id}</td>
                        </tr>
                    {/if}
                </tfoot>
            </table>    
        </div>
        {if !$order->isVirtual() && $order->gift_message}
            <div class="kb-vspacer5"></div>
            <div class="kb-block">
                <table class='ov-p-l'>
                    <thead>
                        <tr>
                            <th>{l s='Gift Message' mod='kbmarketplace'}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                {nl2br($order->gift_message)}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        {/if}
        {if count($messages)}
            <div class="kb-vspacer5"></div>
            <div class="" style="overflow-x:auto; overflow-y:hidden; background-color:#fff;">
                <table class='ov-p-l'>
                    <thead>
                        <tr>
                            <th width="150">{l s='From' mod='kbmarketplace'}</th>
                            <th>{l s='Message' mod='kbmarketplace'}</th>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach from=$messages item=message name="messageList"}
                            <tr>
                                <td>
                                    <b>
                                        {if isset($message.elastname) && $message.elastname}
                                                {$message.efirstname} {$message.elastname}
                                        {elseif $message.clastname}
                                                {$message.cfirstname} {$message.clastname}
                                        {else}
                                                {$shop_name}
                                        {/if}
                                    </b>
                                    <br><small>{dateFormat date=$message.date_add full=1}</small></td>
                                <td>
                                    {$message.message|nl2br}
                                </td>
                            </tr>
                        {/foreach}
                    </tbody>
                </table>    
            </div>    
        {/if}
        <div class="kb-vspacer5"></div>
        {if $can_handle_order}
            <div class="kb-block">
                <form id='order_message_form' method='post' action="{$link->getModuleLink($kb_module_name, 'order', ['id_order' => $order->id], (bool)Configuration::get('PS_SSL_ENABLED'))|escape:'htmlall':'UTF-8'}" class="well kb-form" style="margin-bottom:5px;">
                    <input type="hidden" name="submitMessage" value="1" />
                    <div class="kb-block">
                        <div class="kb-form-label-block">
                            <span class="kblabel">{l s='New Message' mod='kbmarketplace'}</span><em>*</em>
                        </div>
                        <div class="kb-form-field-block">
                            <textarea id="order_message" name="message" rows="5" class="kb-inptexarea" onfocus="$(this).removeClass('kb-highlight-error-field');"></textarea>
                        </div>    
                    </div>
                    <div class="kb-block" style="margin-top: 10px;">
                        <div class="kb-form-label-inblock">
                            <span class="kblabel ">{l s='Display to Customer' mod='kbmarketplace'}?</span>
                        </div>
                        <div class="kb-form-field-inblock">
                            <div class="kboption-inline kb-inpoption">
                                <input type="radio" id="label_for_visibility_yes" name="visibility" value="0" /> <label for="label_for_visibility_yes">{l s='Yes' mod='kbmarketplace'}</label>    
                            </div>
                            <div class="kboption-inline kb-inpoption">
                                <input type="radio" id="label_for_visibility_no" name="visibility" value="1" checked="checked" /> <label  for="label_for_visibility_no">{l s='No' mod='kbmarketplace'}</label>    
                            </div>
                        </div>   
                    </div>
                </form>
                <button id="submit_order_message" type="button" class="btn btn-continue"><i class="icon-share"></i> {l s='Send Message' mod='kbmarketplace'}</button>
                <script type="text/javascript">
                    $(document).ready(function(){
                        $('#submit_order_message').on('click', function(){
                            var message = $('#order_message').val();
                            if(message == ''){
                                $('#order_message').addClass('kb-highlight-error-field');
                            }else{
                                $('form#order_message_form').submit();
                            }
                        });
                    });
                </script>
            </div>
        {/if}
        {hook h="displayKbMarketPlaceOrderDetail" id_order=$order->id block='bottom'}
    </div>
</div>
{if isset($kb_print_order) && $kb_print_order}
<script type="text/javascript">
    $(document).ready(function(){
        window.print();
    });
</script>
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
