<div class="kb-panel outer-border">
    <div data-toggle="kb-product-type-form" class='kb-panel-header kb-panel-header-tab'>
        <h1>{l s='Set the Product Type' mod='kbmarketplace'}</h1>
        <div class='kb-accordian-symbol kbexpand'></div>
        <div class='clearfix'></div>
    </div>
    <div id="kb-product-type-form" class='kb-panel-body'>
        <div class="kbalert kbalert-info">
            <i class="kb-material-icons"></i>{l s='Once the new product is saved, you cannot change the product type on edit.' mod='kbmarketplace'}
        </div>
        <div class="kb-vspacer5"></div>
        <form id="kb-product-type-selection" action="{$form_submit_url nofilter}" method="post">
            <div class="kb-row kb-inpoption">
                <input class="" type="radio" id="label_for_standard_product" name="kb_product_type" value="{$type_simple|intval}" checked="checked" /> <label for="label_for_standard_product">{l s='Standard Product' mod='kbmarketplace'}</label>    
            </div>
            <div class="kb-row kb-inpoption">
                <input class="" type="radio" id="label_for_pack_product" name="kb_product_type" value="{$type_pack|intval}" /> <label  for="label_for_pack_product">{l s='Pack of existing products' mod='kbmarketplace'}</label>    
            </div>
            <div class="kb-row kb-inpoption">
                <input class="" type="radio" id="label_for_virtual_product" name="kb_product_type" value="{$type_virtual|intval}" /> <label  for="label_for_virtual_product">{l s='Virtual product (services, booking, downloadable products, etc.)' mod='kbmarketplace'}</label>    
            </div>
            <button type="submit" name="submitproducttype" class="btn-sm btn-success sensitive">{l s='Next' mod='kbmarketplace'}<i class="kb-material-icons"></i></button>
        </form>
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