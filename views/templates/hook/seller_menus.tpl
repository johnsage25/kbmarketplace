{if isset($link_to_register)}
    {if $kb_seller_agreement != ''}
        <a id="open_kb_seller_agreement_modal" class="col-lg-4 col-md-6 col-sm-6" href="javascript:void(0)" data-modal="kb_seller_agreement_modal" 
           title="{l s='Click to register as seller' mod='kbmarketplace'}" >
            <span class="link-item">
                <i class="kb-material-icons">&#xe563;</i>
                {l s='Register as seller' mod='kbmarketplace'}
            </span>
        </a>
    {else}
        <a class="col-lg-4 col-md-6 col-sm-6" href="javascript:void(0)" data-href="{$link_to_register nofilter}"
           title="{l s='Click to register as seller' mod='kbmarketplace'}" 
           onclick="takeconfirmationforregister(this)" >
            <span class="link-item">
                <i class="kb-material-icons">&#xe563;</i>
                {l s='Register as seller' mod='kbmarketplace'}
            </span>
        </a>
    {/if}
    <script type="text/javascript">
        var kb_confirm_msg = "{l s='Are you sure?' mod='kbmarketplace'}";
        function takeconfirmationforregister(e){
            if(confirm(kb_confirm_msg)){
                location.href=$(e).attr('data-href');
            }
        }
    </script>
    
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
                <div class="modal-body" style="min-height:auto;">{$kb_seller_agreement nofilter}</div>
                <div class="modal-footer">
                     <div class="checkbox">
                             <input type="checkbox" name="kbmp_registered_as_seller" id="kbmp_registered_as_seller" value="1" />
                             <label for="kbmp_registered_as_seller">{l s='I have read the agreement and register me as seller.' mod='kbmarketplace'}</label>
                         </div>
                    <button disabled="true" id="kbmp_registered_as_seller_btn" type="button" class="btn btn-success" onclick="location.href= '{$link_to_register nofilter}'; ">{l s='Register' mod='kbmarketplace'}</button>
                </div>
            </div>
        </div>
    </div>    
    
{/if}    
    
{elseif isset($menus) && count($menus) > 0}
    <div class="row_info" style="display:block;clear:both;width:100%;">	
        <h1 style="margin-left: 0.9375rem;">{l s='Seller Account' mod='kbmarketplace'}</h1>
        {foreach $menus as $menu}
            <a class="col-lg-4 col-md-6 col-sm-6" title="{$menu['title']|escape:'htmlall':'UTF-8'}" href="{$menu['href']|escape:'htmlall':'UTF-8'}">
                <span class="link-item">
                    <i class="kb-material-icons">{$menu['icon_class'] nofilter}</i>
                    {$menu['label']|escape:'htmlall':'UTF-8'}
                </span>
            </a>
        {/foreach}
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