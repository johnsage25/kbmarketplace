{if isset($seller_account_menus) && count($seller_account_menus) > 0}
<div id="seller-account-menus">
	<div class="language-selector-wrapper">
        <span class="hidden-md-up">{l s='Seller Account' mod='kbmarketplace'}</span>
        <div class="language-selector seller_menu_selector dropdown js-dropdown">
          <span class="expand-more hidden-sm-down" data-toggle="dropdown">{l s='Seller Account' mod='kbmarketplace'}</span>
          <a data-target="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="hidden-sm-down">
            <i class="kb-material-icons expand-more">&#xE5C5;</i>
          </a>
          <ul class="dropdown-menu hidden-sm-down">
            {foreach $seller_account_menus as $menu}
                <li>
                    <a title="{$menu['title']|escape:'htmlall':'UTF-8'}" href="{$menu['href']|escape:'htmlall':'UTF-8'}"  class="dropdown-item">
                        {$menu['label']|escape:'htmlall':'UTF-8'}
                    </a>
                </li>
            {/foreach}
          </ul>
          <select class="link hidden-md-up">
            {foreach $seller_account_menus as $menu}
              <option value="{$menu['href']|escape:'htmlall':'UTF-8'}" >{$menu['label']|escape:'htmlall':'UTF-8'}</option>
            {/foreach}
          </select>
        </div>
    </div>
</div>
{/if}
{if isset($kb_mp_custom_js) && $kb_mp_custom_js != ''}
        <script type='text/javascript'>{$kb_mp_custom_js nofilter}</script>
{/if}
{if isset($kb_mp_custom_css) && $kb_mp_custom_css != ''}
    <style>{$kb_mp_custom_css nofilter}</style>
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