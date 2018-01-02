<div id="marketplace-reason-modal" class="bootstrap" style='display:none; width:400px'>
    <div class="panel form-horizontal">
        <div id='kb-reason-error'></div>
        <div class="panel-heading">
                <i class="icon-comment"></i> {$pop_heading|escape:'htmlall':'UTF-8'}
        </div>
        <div class="panel-heading">
            
            <form id='kb-reason-form' method='post' action=''>
                <div class="form-group">
                    <div class="col-lg-12">
                        <textarea class="form-control" id="marketplace_reason_comment" name="marketplace_reason_comment" rows="5"></textarea>
                        <span class="help-block">{$min_length_msg|escape:'htmlall':'UTF-8'}</span>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-lg-12">
                        <button type="button" class="btn btn-success" onclick='actionDissapprove()'>{$pop_action_label|escape:'htmlall':'UTF-8'}</button>
                    </div>
                </div>    
            </form>
            
        </div>
    </div>
</div>
<script type='text/javascript'>
    var reason_min_length = {$reson_min_length|intval}
    var reason_min_length_msg = '{$reason_min_length_error|escape:'htmlall':'UTF-8'}';
    var empty_field_error = '{$empty_field_error|escape:'htmlall':'UTF-8'}';
</script>
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