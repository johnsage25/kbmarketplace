<div class="kb-content">
    <div class="kb-content-header">
        <h1>{l s='Transaction History' mod='kbmarketplace'}</h1>
        <div class="clearfix"></div>
    </div>
    <div class="outer-border kb-tcenter">
        <ul class="summary-list-group">
            <li class="summary-box purple-summary">
                <div class="summary-single-box">
                    <div class="mo_kpi_content big">
                            <i class="big kb-material-icons">&#xe263;</i>
                            <span class="big-title">{l s='Total Earning' mod='kbmarketplace'}</span>
                            <span class="big-value">{Tools::displayPrice($total_earning)}</span>
                    </div>
                </div>
                <div class="clearfix"></div>
            </li>
            <li class="summary-box green-summary">
                <div class="summary-single-box">
                    <div class="mo_kpi_content big">
                            <i class="big kb-material-icons">&#xe06c;</i>
                            <span class="big-title">{l s='Total Paid' mod='kbmarketplace'}</span>
                            <span class="big-value">{Tools::displayPrice($total_paid_amount)}</span>
                    </div>
                </div>
                <div class="clearfix"></div>
            </li>
            <li class="summary-box yellow-summary">
                <div class="summary-single-box">
                    <div class="mo_kpi_content big">
                            <i class="big kb-material-icons">&#xe870;</i>
                            <span class="big-title">{l s='Balance' mod='kbmarketplace'}</span>
                            <span class="big-value">{Tools::displayPrice($total_bal_amount)}</span>
                    </div>
                </div>
                <div class="clearfix"></div>
            </li>
        </ul>
    </div>
    <div class='kb-vspacer5'></div>
    {if isset($kbfilter)}
        {$kbfilter nofilter}
    {/if}
    
    {if isset($kblist)}
        <div class="kb-vspacer5"></div>
        {$kblist nofilter}
    {/if}
    <script type="text/javascript">
        var transaction_view_url = '{$transaction_detail_url nofilter}';
    </script>
    
    <div id="kb-seller-transaction-view-popup" style="display:none;">
        <div class="kb-overlay"></div>
        <div class="kb-modal">
            <div class='kb-model-content-loader'><div class="kb-modal-loading-img"></div></div>
            <div class='kb-model-content'>
                <div class="kb-modal-header">
                    <h1>{l s='Transaction Detail' mod='kbmarketplace'}</h1>
                    <span class="kb-modal-close" data-modal="kb-seller-transaction-view-popup">X</span>
                </div>
                <div class="kb-modal-content">
                    <div id="transaction-content">
                        <div class="kb-row">
                            <div id="transaction-post_0" class="review-popup-time">
                                Amount of <b id="transaction-amount_0" >--</b> credited into your paypal account on <b id="transaction-time_0">--</b>
                            </div>
                            <div id="transaction-post_1" class="review-popup-time" style="display:none;">
                                Amount of <b id="transaction-amount_1">--</b> has been deducted by admin on <b id="transaction-time_1">--</b>
                            </div>
                        </div>
                        <div class="kb-vspacer5"></div>
                        <div class="kb-row">
                            <div class="in-display right-offset15 btxt">{l s='Transaction Id' mod='kbmarketplace'}:</div>
                            <div id="transaction_number" class="in-display">--</div>
                        </div>
                        <div class="kb-vspacer5"></div>
                        <div class="kb-row btxt b-border">{l s='Comment' mod='kbmarketplace'}:</div>
                        <div class="kb-row">
                            <p id="transaction-comment">--</p>
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
