{if $HOOK_KBLEFT_COLUMN}
<div id="kblayout-leftcol" class="column col-xs-12 col-sm-3 pad0">
    {$HOOK_KBLEFT_COLUMN nofilter}
</div>
{/if}
{if $TEMPLATE}
<div id="kblayout-centercol" class="center_column col-xs-12 col-sm-9 pad0">
    <div class="kb-block kb-panel centerlftoffest">
        {if isset($waiting_for_approval)}
                <div class="kbalert kbalert-warning">
                    <i class="kb-material-icons"></i>{l s='Your seller account has been created and waiting for Admin approval.' mod='kbmarketplace'}
                </div>
                {/if}
        {if isset($approval_link)}
            <div class="kbalert kbalert-warning">
                <i class="kb-material-icons"></i>{l s='Your seller account has been disapproved by Admin.' mod='kbmarketplace'} <a href="{$approval_link|escape:'htmlall':'UTF-8'}">{l s='Click' mod='kbmarketplace'}</a> {l s='to again send request for account approval.' mod='kbmarketplace'}
            </div>
        {/if}
        
        {if isset($account_dissaproved)}
                <div class="kbalert kbalert-warning">
                    <i class="kb-material-icons"></i>{l s='Your seller account has been disapproved by Admin.' mod='kbmarketplace'}
                </div>
        {/if}
        
        {if isset($account_disabled)}
                <div class="kbalert kbalert-warning">
                    <i class="kb-material-icons"></i>{l s='Your seller account is inactive.' mod='kbmarketplace'}
                </div>
        {/if}
        {if isset($kb_confirmation) && is_array($kb_confirmation) && count($kb_confirmation) > 0}
            <div class="kbalert kbalert-success">
                <ul>
                    {foreach $kb_confirmation as $con}
                        <li>{$con}</li>
                    {/foreach}
                </ul>
            </div>
        {/if}
        {if isset($kb_errors) && is_array($kb_errors) && count($kb_errors) > 0}
            <div class='pad5'>
                <div class="kbalert kbalert-danger">
                    <ul>
                        {foreach $kb_errors as $err}
                            <li>{$err}</li>
                        {/foreach}
                    </ul>
                </div>    
            </div>
        {/if}
        {$TEMPLATE nofilter}
    </div>
</div>
{/if}
{if $HOOK_KBRIGHT_COLUMN}
<div id="kblayout-rightcol" class="column col-xs-12 col-sm-3 pad0">
    {$HOOK_KBRIGHT_COLUMN nofilter}
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