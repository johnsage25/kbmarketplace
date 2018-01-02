<div class="kb-block kb-panel">
    <div class="kb-content">
        <div class="kb-content-header">
            <h1>{l s='Dashboard' mod='kbmarketplace'}</h1>
            <div class="clearfix"></div>
        </div>
        <div class='outer-border'>
            <ul class='summary-list-group'>
                <li class='summary-box blue-summary'>
                    <div class="summary-single-box">
                        <div class="mo_kpi_content big">
                                <i class="big kb-material-icons">&#xe263;</i>
                                <span class="big-title">{l s='Total Sale' mod='kbmarketplace'}</span>
                                <span class="big-value">{Tools::displayPrice($total_revenue)}</span>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </li>
                <li class='summary-box purple-summary'>
                    <div class="summary-single-box">
                        <div class="mo_kpi_content big">
                                <i class="big kb-material-icons">&#xe048;</i>
                                <span class="big-title">{l s='Total Earning' mod='kbmarketplace'}</span>
                                <span class="big-value">{Tools::displayPrice($total_earning)}</span>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </li>
                <li class='summary-box green-summary'>
                    <div class="summary-single-box">
                        <div class="mo_kpi_content big">
                                <i class="big kb-material-icons">&#xe8d0;</i>
                                <span class="big-title">{l s='Total Orders' mod='kbmarketplace'}</span>
                                <span class="big-value">{$total_orders|intval}</span>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </li>
                <li class='summary-box yellow-summary'>
                    <div class="summary-single-box">
                        <div class="mo_kpi_content big">
                                <i class="big kb-material-icons">&#xe54c;</i>
                                <span class="big-title">{l s='Total Products Sold' mod='kbmarketplace'}</span>
                                <span class="big-value">{$total_sold_products|intval}</span>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </li>
            </ul>
        </div>
        <div class="kb-vspacer5"></div>
        <div class="kb-panel outer-border">
            <script type="text/javascript">
                var kb_graph_revenue_label = '{l s='Your Revenue' mod='kbmarketplace'}';
                var kb_graph_orders_label = '{l s='Total Orders' mod='kbmarketplace'}';
                var kb_graph_products_label = '{l s='Total Products Sold' mod='kbmarketplace'}';
            </script>
            <div class='kb-panel-header kb-sale-stat-tab'>
                <h1>{l s='Sales Statistics' mod='kbmarketplace'}</h1>
                <span class="link" style="display:none;">
                    <select name="sales_statistics_type" onchange="displaySalesGraph(this)">
                        <option value="last_7_days">{l s='Last 7 Days' mod='kbmarketplace'}</option>
                        <option value="week">{l s='This Week' mod='kbmarketplace'}</option>
                        <option value="month">{l s='This Month' mod='kbmarketplace'}</option>
                        <option value="year">{l s='This Year' mod='kbmarketplace'}</option>
                    </select>
                </span>
                <div class='clearfix'></div>
            </div>
            <div id="kb_seller_sales_graph_container" class='kb-panel-body kb-content'>
                <div class="loader128"></div>
                <div class="kb_graph_area" style="width:100%;">
                    <div id="kb_graph_legend_holder" class="kb_graph_legend_container"></div>
                    <div id="kb_seller_sales_graph" style="width:100%; height:300px" class="kb_graph_container"></div>
                </div>
            </div>
        </div>
        <div class="kb-vspacer5"></div>
        <div class="kb-panel outer-border">
            <div data-toggle="comaprison-panel-body" class='kb-panel-header kb-panel-header-tab'>
                <h1>{l s='Sales Comparison' mod='kbmarketplace'}</h1>
                <div  class='kb-accordian-symbol kbexpand'></div>
                <div class='clearfix'></div>
            </div>
            <div id="comaprison-panel-body" class='kb-panel-body'>
                <ul class="tbl-list-group">
                    {foreach $sale_variation_report as $report}
                        <li class="tbl-list">
                            <table class="dh-tbl-smmry">
                                <thead>
                                    <tr>
                                        <th colspan="3">{$report['title']|escape:'htmlall':'UTF-8'}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{l s='Orders' mod='kbmarketplace'}</td>
                                        <td class="kb-tright">{$report['data']['order']['current']|intval}</td>
                                        <td class="kb-tright">
                                            <div class="kb-analysis-popper">
                                                {if $report['data']['order']['diff'] < 0}
                                                    <span class="kb-negative-sign">▼ {$report['data']['order']['diff_percent']|escape:'htmlall':'UTF-8'}%</span>
                                                {elseif $report['data']['order']['diff'] > 0}
                                                    <span class="kb-positive-sign">▲ {$report['data']['order']['diff_percent']|escape:'htmlall':'UTF-8'}%</span>
                                                {else}
                                                    <span class="">{$report['data']['order']['diff_percent']|escape:'htmlall':'UTF-8'}%</span>
                                                {/if}
                                                <div class="kb-popper-info">
                                                    <b>{$report['curent_title']|escape:'htmlall':'UTF-8'}: </b>{$report['data']['order']['current']|intval}<br>
                                                    <b>{$report['prev_title']|escape:'htmlall':'UTF-8'}: </b>{$report['data']['order']['previous']|intval}
                                                </div>    
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>{l s='Earning' mod='kbmarketplace'}</td>
                                        <td class="kb-tright">{Tools::displayPrice($report['data']['revenue']['current'])}</td>
                                        <td class="kb-tright">
                                            <div class="kb-analysis-popper">
                                                {if $report['data']['revenue']['diff'] < 0}
                                                    <span class="kb-negative-sign">▼ {$report['data']['revenue']['diff_percent']|escape:'htmlall':'UTF-8'}%</span>
                                                {elseif $report['data']['revenue']['diff'] > 0}
                                                    <span class="kb-positive-sign">▲ {$report['data']['revenue']['diff_percent']|escape:'htmlall':'UTF-8'}%</span>
                                                {else}
                                                    <span class="">{$report['data']['revenue']['diff_percent']|escape:'htmlall':'UTF-8'}%</span>
                                                {/if}
                                                <div class="kb-popper-info">
                                                    <b>{$report['curent_title']|escape:'htmlall':'UTF-8'}: </b>{Tools::displayPrice($report['data']['revenue']['current'])}<br>
                                                    <b>{$report['prev_title']|escape:'htmlall':'UTF-8'}: </b>{Tools::displayPrice($report['data']['revenue']['previous'])}
                                                </div>    
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>{l s='Product Sold' mod='kbmarketplace'}</td>
                                        <td class="kb-tright">{$report['data']['ordered_qty']['current']|intval}</td>
                                        <td class="kb-tright">
                                            <div class="kb-analysis-popper">
                                                {if $report['data']['ordered_qty']['diff'] < 0}
                                                    <span class="kb-negative-sign">▼ {$report['data']['ordered_qty']['diff_percent']|escape:'htmlall':'UTF-8'}%</span>
                                                {elseif $report['data']['ordered_qty']['diff'] > 0}
                                                    <span class="kb-positive-sign">▲ {$report['data']['ordered_qty']['diff_percent']|escape:'htmlall':'UTF-8'}%</span>
                                                {else}
                                                    <span class="">{$report['data']['ordered_qty']['diff_percent']|escape:'htmlall':'UTF-8'}%</span>
                                                {/if}
                                                <div class="kb-popper-info">
                                                    <b>{$report['curent_title']|escape:'htmlall':'UTF-8'}: </b>{$report['data']['ordered_qty']['current']|intval}<br>
                                                    <b>{$report['prev_title']|escape:'htmlall':'UTF-8'}: </b>{$report['data']['ordered_qty']['previous']|intval}
                                                </div>    
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </li>
                    {/foreach}
                </ul>    
            </div>
        </div>
        <div class="kb-vspacer5"></div>
        <div class="kb-panel outer-border">
            <div data-toggle="orderlist-panel-body" class='kb-panel-header kb-panel-header-tab'>
                <h1>{l s='Last 10 Orders' mod='kbmarketplace'}</h1>
                <div data-toggle="orderlist-panel-body" class='kb-accordian-symbol kbexpand'></div>
                <span class='link'><a href="{$link->getModuleLink('kbmarketplace', 'order', [], (bool)Configuration::get('PS_SSL_ENABLED'))|escape:'htmlall':'UTF-8'}" >{l s='View All' mod='kbmarketplace'}</a></span>
                <div class='clearfix'></div>
            </div>
            <div id="orderlist-panel-body" class='kb-panel-body' style="overflow-x:auto;">
                <table class="kb-table-list">
                    <thead>
                        <tr class="heading-row">
                            <th width="100">{l s='Reference' mod='kbmarketplace'}</th>
                            <th width="90">{l s='Order Date' mod='kbmarketplace'}</th>
                            <th>{l s='Customer Name' mod='kbmarketplace'}</th>
                            <th>{l s='Customer Email' mod='kbmarketplace'}</th>
                            <th width="50">{l s='Qty' mod='kbmarketplace'}</th>
                            <th>{l s='Status' mod='kbmarketplace'}</th>
                            <th width="80">{l s='Order Total' mod='kbmarketplace'}</th>
                        </tr>
                    </thead>
                    <tbody>
                        {if count($orders) > 0}
                            {foreach $orders as $order}
                                <tr>                        
                                    <td class="kb-tright"><a target="_blank" href="{$order['view_link']|escape:'htmlall':'UTF-8'}" title="{l s='Click to view order' mod='kbmarketplace'}">{$order['reference']|escape:'htmlall':'UTF-8'}</a></td>
                                    <td>{dateFormat date=$order['order_date'] full=0}</td>
                                    <td>{$order['customer_name']|escape:'htmlall':'UTF-8'}</td>
                                    <td>{$order['email']|escape:'htmlall':'UTF-8'}</td>
                                    <td class="kb-tright">{$order['qty']|intval}</td>
                                    <td>{if count($order['status'])}{$order['status']['name']|escape:'htmlall':'UTF-8'}{/if}</td>
                                    <td class="kb-tright">{Tools::displayPrice($order['total'])}</td>
                                </tr>        
                            {/foreach}
                        {else}
                            <tr><td colspan="7" class="kb-tcenter kb-empty-table">{l s='There are no order found' mod='kbmarketplace'}</td></tr>
                        {/if}
                    </tbody>
                </table>
            </div>
        </div>
        {hook h="displayKbMarketPlaceSellerDashboard"}
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