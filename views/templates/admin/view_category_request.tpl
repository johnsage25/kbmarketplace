<div id='kb-review-box' class="panel">
    <div class='kb-review-box-heading'>
        <i class="icon-anchor"></i> {$seller_name|escape:'htmlall':'UTF-8'} ({$seller_shop|escape:'htmlall':'UTF-8'})
    </div>
    <div class="row">
        <div class="col-xs-12">
            <div class='kb-review-row'>
                <div class='kb-review-label'>{$req_cat_heading|escape:'htmlall':'UTF-8'}</div>
                <p id='kb-review-summary'>
                    {$req_category|escape:'htmlall':'UTF-8'}
                </p>
            </div>
            <div class='kb-review-row'>
                <div class='kb-review-label'>{$comment_heading|escape:'htmlall':'UTF-8'}</div>
                <p id='kb-review-content'>
                {$comment|nl2br|escape:'htmlall':'UTF-8'}
                </p>
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