<div id='kb-seller-account-menus' class="kb-block lftcolrightpad">
    {if count($menus) > 0}
    <div id="kb-account-accordian" class="kb-account-accordian"><span>{l s='Seller Account Menu' mod='kbmarketplace'}</span><i class="icon-plus"></i></div>
    <div id="kb-s-account-mlist">
        <ul class="kb-menu-block">
            {foreach $menus as $menu}
                <a title="{$menu['title']|escape:'htmlall':'UTF-8'}" href="{$menu['href']|escape:'htmlall':'UTF-8'}">
                <li class="kb-menu-list-item {if $menu['active'] eq true}kb-active-menuitem{/if} {if !empty($menu['css_class'])}{$menu['css_class']|escape:'htmlall':'UTF-8'}{/if}">
                    
                        <i class="kb-material-icons">{$menu['icon_class'] nofilter}</i>{$menu['label']|escape:'htmlall':'UTF-8'}
                        {if $menu['badge'] neq false}
                            <span class="kb-menu-badge">{$menu['badge']|intval}</span>
                        {/if}
                       
                </li>
                </a> 
            {/foreach}
            {hook h="displayKbSellerAccountMenu" m=$smarty.get.module c=$smarty.get.controller}
            <li id="kb_otherfeature_menu" class="kb-menu-list-item collapsible-otherfeature-menu">
                <a title="{l s='Other Features' mod='kbmarketplace'}" href="javascript:void(0)">
                    <i class="kb-material-icons">&#xe23e;</i>{l s='Other Features' mod='kbmarketplace'}
                </a>
                <div class="kb-smenu-accordian-symbol kbexpand"><i class="kb-material-icons">&#xe145;</i></div>
                <ul style="display:none;">
                    {hook h="displayKbSellerOfeatureMenu" m=$smarty.get.module c=$smarty.get.controller}
                </ul>
            </li>
        </ul>
    </div>
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