/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 * We offer the best and most useful modules PrestaShop and modifications for your online store. 
 *
 * @category  PrestaShop Module
 * @author    knowband.com <support@knowband.com>
 * @copyright 2016 knowband
 * @license   see file: LICENSE.txt
 */

var validation_fields = {
        'isGenericName': /^[^<>={}]*$/,
        'isAddress': /^[^!<>?=+@{}_$%]*$/,
        'isPhoneNumber': /^[+0-9. ()-]*$/,
        'isInt': /^[0-9]*$/,
        'isIntExcludeZero': /^[1-9]*$/,
        'isPrice': /^[0-9]*(?:\.\d{1,6})?$/,
        'isPriceExcludeZero': /^[1-9]*(?:\.\d{1,6})?$/,
        'isDate': /^([0-9]{4})-(0[1-9]|1[012])-(0[1-9]|[12][0-9]|3[01])$/,
        'isUrl': /[-a-zA-Z0-9@:%_\+.~#?&//=]{2,256}\.[a-z]{2,4}\b(\/[-a-zA-Z0-9@:%_\+.~#?&//=]*)?/gi,
        'isEmail': /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
};

var submit_url = '';
$(document).ready(function(){
    
    try
    {
        $(".kb_mp_html_decode").each(function( index ) {
            var text = $(this).text();
            text = text.replace("<","&lt;");
            text = text.replace(">","&gt;");
            $(this).html(text);
        });
    } catch (e) {}
    
    $('.fancybox-inner').attr('style','height:auto;');
    
    if($('#kb-marketplce-allw-cat').length){
        $('#kb-marketplce-allw-cat').multipleSelect({
            placeholder: 'Choose Category(s)',
            onCheckAll: function() {

            },
            onUncheckAll: function() {

            },
            onClick: function() {

            }
        });
    }
    
    if($('.marketplace-reason-modal').length){
        $('.marketplace-reason-modal').fancybox({
            width: 400,
            autoWidth: false,
            beforeLoad: function () {
                submit_url = $(this.element).attr('data-url');
                $('#kb-reason-error').html('');
                $('#marketplace_reason_comment').val('');
                $('#marketplace-reason-modal').show();
            },
            beforeClose: function () {
                $('#marketplace-reason-modal').hide();
            }
        });    
    }
    
    if($('.marketplace-view-modal').length){
        $('.marketplace-view-modal').fancybox({
            autoWidth: false,
            beforeLoad: function () {
                submit_url = $(this.element).attr('data-url');
                $.ajax({
                    type: 'POST',
                    headers: { "cache-control": "no-cache" },
                    url: submit_url + ((submit_url.indexOf('?') < 0) ? '?' : '&')+'rand=' + new Date().getTime(),
                    async: true,
                    cache: false,
                    dataType : "html",
                    data: 'ajax=true',
                    beforeSend: function() {
                        $('div#marketplace-view-modal').html('<div class="modal-content-loader"></div>');
                        $('div#marketplace-view-modal').show();
                    },
                    success: function(html)
                    {
                        $('div#marketplace-view-modal').html(html);
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        $('div#marketplace-view-modal').html('');
                    }
                });
            },
            beforeClose: function () {
                $('div#marketplace-view-modal').hide();
            }
        });    
    }
    
    if($('.open_new_transaction_form').length){
        $('.open_new_transaction_form').fancybox({
            beforeLoad: function () {
                $('#kb-new-transaction-form').show();
            },
            beforeClose: function () {
                $('#kb-new-transaction-form').hide();
            }
        });    
    }
    
    if($('#kb_mp_seller_config_form_reset_btn').length){
        $('#kb_mp_seller_config_form_reset_btn').on('click', function(){
            var html = '<input type="hidden" name="kbmp_reset_setting" value="1" />';
            $('form#kb_mp_seller_config_form').append(html);
            $('form#kb_mp_seller_config_form').submit();
        });
    }
    
    $(".kb_checkbox_seller_settings:checked").each(function()
    {
//        $(this).parent().parent().find("div").find("input").each(function ()
//        {
//            $(this).attr('disabled',true);
//        });
        $(this).parent().parent().find("span").find("input").each(function ()
        {
            $(this).attr('disabled',true);
        });
    });
    
//    $('#kb_mp_email_template_form button[name=submitAddkb_mp_email_template]').on('click',function(){
//        alert('sdf');
//    });
//    $('#kb_mp_email_template_form button[name=submitAddkb_mp_email_templateAndStay]').on('click',function(){
//        
//    });
    
});

function kbValidateField(value, element)
{
    var value = $(element).val();
    
    for (var key in validation_fields)
    {
        if ($(element).hasClass(key))
        {
            var reg = new RegExp(validation_fields[key]);
            if(reg.test(value))
            {
                return true;
                break;
            }     
        }
    }
    
    return false;
}

//function validateTransactionAmount(id_form) {
//    $('#'+id_form+' .form-wrapper').find('.alert').remove();
//    $('#'+id_form+' .form-wrapper').find('.form-group').removeClass('has-error');
//    var error = false;
//    if(id_form == 'kb_new_transaction_form')
//    {
//        var element = $('#new_transaction_amount');
//        if(element.val() == '0')
//        {
//            element.closest(".form-group").addClass('has-error');
//            error = true;
//        }
//    }
//     if(!error){
//        return true;
//    }else{
//        return false;
//    }
//}


function validateKbHelperForm(id_form)
{
    $('#'+id_form+' .form-wrapper').find('.alert').remove();
    $('#'+id_form+' .form-wrapper').find('.form-group').removeClass('has-error');
    var error = false;
    if(id_form == 'kb_new_transaction_form')
    {
        var element = $('#new_transaction_amount');
        if(element.val() == '0')
        {
            element.closest(".form-group").addClass('has-error');
            error = true;
        }
    }
     if($('#'+id_form+' input[type="text"]').length || $('#'+id_form+' select').length)
    {
        var value = '';
        $('#'+id_form+' input[type="text"], #'+id_form+' select').each(function(){
            value = $(this).val();
            value = value.trim();
            if($(this).attr('required') && $(this).attr('required') == 'required')
            {
                if(value == '')
                {
                    error = true;
                    $(this).closest(".form-group").addClass('has-error');
                }
                else{
                    if(!kbValidateField(value, this))
                    {
                        error = true;
                        $(this).closest(".form-group").addClass('has-error');
                    }
                }
            }else if(!kbValidateField(value, this)){
                error = true;
                $(this).closest(".form-group").addClass('has-error');
            }
        });
    }
    
    if(!error){
        return true;
    }else{
        return false;
    }
}

function changeCommsionView(e)
{
    $('#marketplace-extra-content #configuration_form').submit();
}

function changeTransactionView(e)
{
    location.href = $(e).val();
}

function changeEmailTranslation(e)
{
    location.href = email_translation_url+'&id_email_template_lang='+$(e).val();
}

function openNewTransactionForm(e, id_seller)
{
    $('#kb_new_transaction_form input[type="text"]').val('');
    $('#kb_new_transaction_form select option').removeAttr('selected');
    $('#kb_new_transaction_form textarea').html('');
    if (id_seller > 0)
    {
        $('#kb_new_transaction_form select option').each(function(){
            if($(this).val() == id_seller){
                $(this).attr('selected', 'selected');
            }
        });
    }
    
    if($('#kb-new-transaction-form').is(':visible'))
    {
        $('#kb-new-transaction-form').slideUp('fast');
//        $('.open_new_transaction_form i').removeClass('icon-minus-sign');
//        $('#update_transaction_form_btn').removeClass('icon-plus-sign').addClass('icon-minus-sign');
        $('#update_transaction_form_btn').removeClass('icon-minus-sign').addClass('icon-plus-sign');
        $('#kb-new-trabsaction-btn-label').html('Add New Transaction');
    }else{
        $('#kb-new-transaction-form').slideDown('fast');
        $('#update_transaction_form_btn').removeClass('icon-plus-sign').addClass('icon-minus-sign');
//        $('#update_transaction_form_btn').removeClass('icon-minus-sign').addClass('icon-plus-sign');
//        $('.open_new_transaction_form i').addClass('icon-minus-sign');
        $('#kb-new-trabsaction-btn-label').html('Close Transaction Form');
    }
}

function actionDissapprove()
{
    $('#kb-reason-error').html('');
    var txt = $('#marketplace_reason_comment').val();
    txt = txt.replace(/^\s+|\s+$/g,'');
    var click = 1;
    if(txt == ''){
        $('#kb-reason-error').html('<div class="alert alert-danger">'+empty_field_error+'</div>');
    }else if(txt.length < reason_min_length){
        $('#kb-reason-error').html('<div class="alert alert-danger">'+reason_min_length_msg+'</div>');
    }else{
        if (click == '1') {
            $('form#kb-reason-form').attr('action', submit_url);
            $('form#kb-reason-form').submit();
            $('#kb-reason-form .btn-success').attr('disabled','true');
            click++;
        }  else {
            $('#kb-reason-form .btn-success').attr('disabled','true');
        }
    }
}

function validateKbNewTransactionForm()
{
    var status = validateKbHelperForm('kb_new_transaction_form');
    var errors = [];
    if(status){
        $('#kb_new_transaction_form').submit();
    }else{
        console.log($('#new_transaction_amount').val());
        if ($('#select_seller_transaction').val() == '0') {
            errors.push('Please Select Seller.');
//            $('#kb_new_transaction_form .form-wrapper').prepend('<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">×</button>Please Select Seller.</div>');
        }
        if (($('#new_transaction_amount').val() <= 0) || $('#new_transaction_amount').length == '' ) {
            errors.push('Transaction Amount must be greater than 0.');
//            $('#kb_new_transaction_form .form-wrapper').prepend('<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">×</button>Please provide information with valid values.Tansaction Amount must be greater than 0.</div>');
        } if ($('#new_transaction_id').val() == '') {
            errors.push('Transaction id can not be blank.');
//            $('#kb_new_transaction_form .form-wrapper').prepend('<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">×</button>Please provide information with valid values.Tansaction Amount must be greater than 0.</div>');
        }
        if(errors.length > 0) {
            $('#kb_new_transaction_form .form-wrapper').prepend('<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">×</button>' +errors.length +' errors <br><ol></ol></div>');
            for (var i=0;i<errors.length;i++) {
                $('.alert ol').prepend('<li>'+errors[i] + '</li>');
            }
            
        }
    }
}

function disapproveWithConfirmation(e)
{
    if (confirm('Do you want to disapprove?')){
        return true;
    }else{
        event.stopPropagation(); event.preventDefault();
    }
    return false;
}
function deleteWithConfirmation(e)
{
    if (confirm('Do you want to delete?')){
        return true;
    }else{
        event.stopPropagation(); event.preventDefault();
    }
    return false;
}
function approveWithConfirmation(e)
{
    if (confirm('Do you want to approve?')){
        return true;
    }else{
        event.stopPropagation(); event.preventDefault();
    }
    return false;
}

function changeSwitchColor(element)
{
    if($(element).prop("checked") == true)
    {
//        $(element).parent().parent().find("div").find("input").each(function ()
//        {
//            $(this).attr('disabled',true);
//        });
        $(element).parent().parent().find("span").find("input").each(function ()
        {
            $(this).attr('disabled',true);
        });
    }
    else
    {
//        $(element).parent().parent().find("div").find("input").each(function ()
//        {
//            $(this).attr('disabled',false);
//        });
        $(element).parent().parent().find("span").find("input").each(function ()
        {
            $(this).attr('disabled',false);
        });
    }
}
