{if $kb_seller_agreement != ''}
<div id="kb_seller_agreement_modal" class="modal fade" tabindex="-1" role="dialog" style="display:none;">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="min-height:auto;">
            <div class="modal-header">
                
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <h1 style="    text-align: left;" class="modal-title">{l s='Terms & Conditions' mod='kbmarketplace'}</h1>
            </div>
            <div class="modal-body" style="min-height:auto;">
                {$kb_seller_agreement nofilter}
            </div>
        </div>
    </div>
</div>
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