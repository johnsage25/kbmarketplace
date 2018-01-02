<div class="kb-content">
    <div class="kb-content-header">
        <h1>{l s='My Earnings' mod='kbmarketplace'}</h1>
        <div class="clearfix"></div>
    </div>
    <div class="outer-border kb-tcenter">
        <ul class="summary-list-group">
            <li class="summary-box purple-summary">
                <div class="summary-single-box">
                    <div class="mo_kpi_content big">
                            <i class="big kb-material-icons">&#xe263;</i>
                            <span class="big-title">{l s='Total Sale' mod='kbmarketplace'}</span>
                            <span class="big-value">{Tools::displayPrice($total_revenue)}</span>
                    </div>
                </div>
                <div class="clearfix"></div>
            </li>
            <li class="summary-box blue-summary">
                <div class="summary-single-box">
                    <div class="mo_kpi_content big">
                            <i class="big kb-material-icons">&#xe048;</i>
                            <span class="big-title">{l s='Total Earning' mod='kbmarketplace'}</span>
                            <span class="big-value">{Tools::displayPrice($total_earning)}</span>
                    </div>
                </div>
                <div class="clearfix"></div>
            </li>
            <li class="summary-box green-summary">
                <div class="summary-single-box">
                    <div class="mo_kpi_content big">
                            <i class="big kb-material-icons">&#xe90e;</i>
                            <span class="big-title">{l s='Total Orders' mod='kbmarketplace'}</span>
                            <span class="big-value">{$total_orders|intval}</span>
                    </div>
                </div>
                <div class="clearfix"></div>
            </li>
        </ul>
    </div>
    <div class='kb-vspacer5'></div>
    <ul class="kb-tabs">
            <li class="active" rel="tab1" >{l s='Earning History' mod='kbmarketplace'}</li>
            <li rel="tab2" >{l s='Order Wise Earning' mod='kbmarketplace'}</li>                
    </ul>
    <div class="clearfix"></div>
    <div class="kb_tab_container">
        <div id="tab1" class="kb_tab_content">
            {if isset($kb_earning_history_filter)}
                {$kb_earning_history_filter nofilter}
            {/if}

            {if isset($kb_earning_history_list)}
                <div class="kb-vspacer5"></div>
                {$kb_earning_history_list nofilter}
            {/if}
        </div>
        <div id="tab2" class="kb_tab_content">
            {if isset($kb_owise_earning_filter)}
                {$kb_owise_earning_filter nofilter}
            {/if}

            {if isset($kb_owise_earning_list)}
                <div class="kb-vspacer5"></div>
                {$kb_owise_earning_list nofilter}
            {/if}
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