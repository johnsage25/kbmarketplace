/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


var filter_paramters = {};
var kb_rsn_min_chars = 30;

function KbFilterList(filter_block_id)
{
    var validation = true;
    $('#'+filter_block_id+'_filter .kb-validation-error').remove();
    $('#'+filter_block_id+'_filter input[type="text"]').each(function(){
        var value = $(this).val();
        value = value.trim();
        if($(this).hasClass('required'))
        {
            if(value == '')
            {
                validation = false;
                $(this).parent().append('<div class="kb-validation-error">'+kb_required_field+'</div>');
            }
            else{
                if($(this).attr('validate') != undefined && !kbValidateField(value, $(this).attr('validate')))
                {
                    validation = false;
                    $(this).parent().append('<div class="kb-validation-error">'+kb_invalid_field+'</div>');
                }
            }
        }else if(value != '' && $(this).attr('validate') != undefined && !kbValidateField(value, $(this).attr('validate'))){
            validation = false;
            $(this).parent().append('<div class="kb-validation-error">'+kb_invalid_field+'</div>');
        }
    });
    
    $('#'+filter_block_id+'_filter select').each(function(){
        var value = $(this).val();
        value = value.trim();
        if($(this).hasClass('required'))
        {
            if(value == '')
            {
                validation = false;
                $(this).parent().append('<div class="kb-validation-error">'+kb_required_field+'</div>');
            }
            else{
                if($(this).attr('validate') != undefined && !kbValidateField(value, $(this).attr('validate')))
                {
                    validation = false;
                    $(this).parent().append('<div class="kb-validation-error">'+kb_invalid_field+'</div>');
                }
            }
        }else if(value != '' && $(this).attr('validate') != undefined && !kbValidateField(value, $(this).attr('validate'))){
            validation = false;
            $(this).parent().append('<div class="kb-validation-error">'+kb_invalid_field+'</div>');
        }
    });

    if(validation){
        filter_paramters = $('#'+filter_block_id+'_filter input, #'+filter_block_id+'_filter select').serializeObject();
        $.ajax({
            type: 'POST',
            headers: { "cache-control": "no-cache" },
            url: kb_current_request + ((kb_current_request.indexOf('?') < 0) ? '?' : '&')+'rand=' + new Date().getTime(),
            async: true,
            cache: false,
            dataType : "json",
            data: 'ajax=true&method='+$('#kb_filter_action_'+filter_block_id).val()
                + '&'+$('#'+filter_block_id+'_filter input, #'+filter_block_id+'_filter select').serialize() ,
            beforeSend: function() {
                $('#'+filter_block_id+'_filter').attr('disable', true);
                $('#uploading-progress').css('display','inline-block');
            },
            success: function(json)
            {
                $('#'+filter_block_id+'_filter').attr('disable', false);
                $('#uploading-progress').css('display','none');
                if(json['status'] == true){
                    $('#'+filter_block_id+'_body').html(json['html']);
                    $('#'+filter_block_id+'-panel-body .kb-paginator-block').html(json['pagination']);
                }else{
                    var html = '<tr><td colspan="'+$('#'+filter_block_id+'-panel-body thead tr th').length+'" class="kb-tcenter kb-empty-table">'+json['msg']+'</td></tr>';
                    $('#'+filter_block_id+'_body').html(html);
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                jAlert(kb_ajax_request_fail_err);
                $('#'+filter_block_id+'_filter').attr('disable', false);
                $('#uploading-progress').css('display','none');
            }
        });
    }else{
        setTimeout(function(){$('#'+filter_block_id+'_filter .kb-validation-error').remove();}, message_delay);
    }
}

function resetKbFilters(filter_block_id)
{
    $('#'+filter_block_id+'_filter input[type="text"]').val('');
    $('#'+filter_block_id+'_filter select option').removeAttr('selected');
    if (filter_block_id == 'seller_shipping_mapping_product') {
        var request_parameter = 'method='+$('#kb_filter_action_'+filter_block_id).val()+'&id_carrier='+$('#'+filter_block_id+'_filter input[name="id_carrier"]').val();
    } else {
        var request_parameter = 'method='+$('#kb_filter_action_'+filter_block_id).val();
    }
    filter_paramters = {};
    $.ajax({
        type: 'POST',
        headers: { "cache-control": "no-cache" },
        url: kb_current_request + ((kb_current_request.indexOf('?') < 0) ? '?' : '&')+'rand=' + new Date().getTime(),
        async: true,
        cache: false,
        dataType : "json",
        data: 'ajax=true&'+request_parameter,
        beforeSend: function() {
            $('#'+filter_block_id+'_filter').attr('disable', true);
            $('#uploading-progress').css('display','inline-block');
        },
        success: function(json)
        {
            $('#'+filter_block_id+'_filter').attr('disable', false);
            $('#uploading-progress').css('display','none');
            if(json['status'] == true){
                $('#'+filter_block_id+'_body').html(json['html']);
                $('#'+filter_block_id+'-panel-body .kb-paginator-block').html(json['pagination']);
            }else{
                var html = '<tr><td colspan="'+$('#'+filter_block_id+'-panel-body thead tr th').length+'" class="kb-tcenter kb-empty-table">'+json['msg']+'</td></tr>';
                $('#'+filter_block_id+'_body').html(html);
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            jAlert(kb_ajax_request_fail_err);
            $('#'+filter_block_id+'_filter').attr('disable', false);
            $('#uploading-progress').css('display','none');
        }
    });
}

function kbMultiactionFormSubmit(block_id){

    if($('#'+block_id+'-multiaction-type').val() != ''){
        var kb_multiaction_status_value = $('input[type="hidden"].'+block_id+'_status_drop_value').val();
        if($('#'+block_id+'-multiaction-type').val() == kb_multiaction_status_value)
        {
            if($('#'+block_id+'-status-list select').val() != '')
            {
                if($('#'+block_id+'_body tr .kb_list_row_checkbox:checked').length){
                    var selected_items = [];
                    $('#'+block_id+'_body tr .kb_list_row_checkbox:checked').each(function(){
                        selected_items.push($(this).val());
                    });

                    $('input[type="hidden"].'+block_id+'_selected_items').val(selected_items.join());

                    $('form#'+block_id+'_multiaction_form').submit();
                }else{
                    jAlert('Select atleast one item from list');
                }
            }else{
                jAlert('Status is required');
            }
        }else{
            if($('#'+block_id+'_body tr .kb_list_row_checkbox:checked').length){
                var selected_items = [];
                $('#'+block_id+'_body tr .kb_list_row_checkbox:checked').each(function(){
                    selected_items.push($(this).val());
                });

                $('input[type="hidden"].'+block_id+'_selected_items').val(selected_items.join());
                
                if(confirm(kb_delete_confirmation)){
                    if($('#'+block_id+'_reason_modal').length)
                    {
                        $('#'+block_id+'_reason_modal .multiaction_reason').val('');
                        $('#'+block_id+'_reason_modal .multiaction_reason').html('');
                        $('#'+block_id+'_reason_modal').show();
                        $("html, body").animate({scrollTop:0}, '500');
                    }else{
                        $('form#'+block_id+'_multiaction_form').submit();
                    }
                }
            }else{
                jAlert('Select atleast one item from list');
            }
        }  
    }else{
        jAlert('Action is required');
    }
}

function KbDeleteAction(id) {
    var selected_item = [];
    selected_item.push(id);
    $("#seller_product-multiaction-type").val(2);
    $('input[type="hidden"].seller_product_selected_items').val(selected_item.join());
    if(confirm(kb_delete_confirmation)){
        $('form#seller_product_multiaction_form').submit();
    }
    
}

function kbMappedAction(id, carrier_id) {
    var selected_item = [];
    selected_item.push(id);
    $("#seller_product-multiaction-type").val(carrier_id);
    $('input[type="hidden"].seller_product_selected_items').val(selected_item.join());
    $('input[name="id_carrier"]').val(carrier_id);
    if(confirm(kb_delete_confirmation)){
        $('form#seller_product_multiaction_form').submit();
    }
}

function multiactionCheck(el)
{
    if (el.checked == true) {
        checkAll();
    } else {
        uncheckAll();
    }
}

function checkAll()
{
        // Iterate each checkbox
        $(':checkbox').each(function() {
            this.checked = true;
            $(this).parent().addClass('checked');
        });

}

function uncheckAll()
{
        // Iterate each checkbox
        $(':checkbox').each(function() {
            this.checked = false;
            $(this).parent().removeClass('checked');
        });

}
function submitMultiactionReason(block_id)
{
    var reason_txt = $('#'+block_id+'_reason_modal .multiaction_reason').val();
    var click= 1;
    if(reason_txt == ''){
        jAlert('Reason is required');
    }else if(reason_txt.length < kb_rsn_min_chars){
        jAlert('Minimum '+kb_rsn_min_chars+' chanracters required');
    }else{
        if (click == 1) {
            $('form#'+block_id+'_multiaction_form').submit();
            $('#seller_product_reason_modal .kbbtn-success').attr('disabled',true);
            click++;
        } else {
             $('#seller_product_reason_modal .kbbtn-success').attr('disabled',true);
        }
    }
}

function actionDeleteConfirmation(e)
{
    if(confirm(kb_delete_confirmation)){
        window.location.href = $(e).attr('data-href');
    }
}


function getSellerProducts(kb_table_id, page_number){
    var request_params = serializeObjectToSerialize(filter_paramters);
    request_params += '&start='+page_number;
    $.ajax({
        type: 'POST',
        headers: { "cache-control": "no-cache" },
        url: kb_current_request + ((kb_current_request.indexOf('?') < 0) ? '?' : '&')+'rand=' + new Date().getTime(),
        async: true,
        cache: false,
        dataType : "json",
        data: 'ajax=true&method=getSellerProducts'+request_params,
        beforeSend: function() {
            $('#'+kb_table_id+'_filter').attr('disable', true);
            $('#kb-list-loader').show();
        },
        success: function(json)
        {
            $('#'+kb_table_id+'_filter').attr('disable', false);
            $('#kb-list-loader').hide();
            if(json['status'] == true){
                $('#'+kb_table_id+'_body').html(json['html']);
                $('#'+kb_table_id+'-panel-body .kb-paginator-block').html(json['pagination']);
            }else{
                var html = '<tr><td colspan="'+$('#'+kb_table_id+'-panel-body thead tr th').length+'" class="kb-tcenter kb-empty-table">'+json['msg']+'</td></tr>';
                $('#'+kb_table_id+'_body').html(html);
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            jAlert(kb_ajax_request_fail_err);
            $('#'+kb_table_id+'_filter').attr('disable', false);
            $('#kb-list-loader').hide();
        }
    });
}

function getSellerProductReviews(kb_table_id, page_number){
    var request_params = serializeObjectToSerialize(filter_paramters);
    request_params += '&start='+page_number;
    $.ajax({
        type: 'POST',
        headers: { "cache-control": "no-cache" },
        url: kb_current_request + ((kb_current_request.indexOf('?') < 0) ? '?' : '&')+'rand=' + new Date().getTime(),
        async: true,
        cache: false,
        dataType : "json",
        data: 'ajax=true&method=getSellerProductReviews'+request_params,
        beforeSend: function() {
            $('#'+kb_table_id+'_filter').attr('disable', true);
            $('#kb-list-loader').show();
        },
        success: function(json)
        {
            $('#'+kb_table_id+'_filter').attr('disable', false);
            $('#kb-list-loader').hide();
            if(json['status'] == true){
                $('#'+kb_table_id+'_body').html(json['html']);
                $('#'+kb_table_id+'-panel-body .kb-paginator-block').html(json['pagination']);
            }else{
                var html = '<tr><td colspan="'+$('#'+kb_table_id+'-panel-body thead tr th').length+'" class="kb-tcenter kb-empty-table">'+json['msg']+'</td></tr>';
                $('#'+kb_table_id+'_body').html(html);
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            jAlert(kb_ajax_request_fail_err);
            $('#'+kb_table_id+'_filter').attr('disable', false);
            $('#kb-list-loader').hide();
        }
    });
}

function getSellerReviews(kb_table_id, page_number){
    var request_params = serializeObjectToSerialize(filter_paramters);
    request_params += '&start='+page_number;
    $.ajax({
        type: 'POST',
        headers: { "cache-control": "no-cache" },
        url: kb_current_request + ((kb_current_request.indexOf('?') < 0) ? '?' : '&')+'rand=' + new Date().getTime(),
        async: true,
        cache: false,
        dataType : "json",
        data: 'ajax=true&method=getSellerReviews'+request_params,
        beforeSend: function() {
            $('#'+kb_table_id+'_filter').attr('disable', true);
            $('#kb-list-loader').show();
        },
        success: function(json)
        {
            $('#'+kb_table_id+'_filter').attr('disable', false);
            $('#kb-list-loader').hide();
            if(json['status'] == true){
                $('#'+kb_table_id+'_body').html(json['html']);
                $('#'+kb_table_id+'-panel-body .kb-paginator-block').html(json['pagination']);
            }else{
                var html = '<tr><td colspan="'+$('#'+kb_table_id+'-panel-body thead tr th').length+'" class="kb-tcenter kb-empty-table">'+json['msg']+'</td></tr>';
                $('#'+kb_table_id+'_body').html(html);
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            jAlert(kb_ajax_request_fail_err);
            $('#'+kb_table_id+'_filter').attr('disable', false);
            $('#kb-list-loader').hide();
        }
    });
}

function getSellerOrders(kb_table_id, page_number){
    var request_params = serializeObjectToSerialize(filter_paramters);
    request_params += '&start='+page_number;
    $.ajax({
        type: 'POST',
        headers: { "cache-control": "no-cache" },
        url: kb_current_request + ((kb_current_request.indexOf('?') < 0) ? '?' : '&')+'rand=' + new Date().getTime(),
        async: true,
        cache: false,
        dataType : "json",
        data: 'ajax=true&method=getSellerOrders'+request_params,
        beforeSend: function() {
            $('#'+kb_table_id+'_filter').attr('disable', true);
            $('#kb-list-loader').show();
        },
        success: function(json)
        {
            $('#'+kb_table_id+'_filter').attr('disable', false);
            $('#kb-list-loader').hide();
            if(json['status'] == true){
                $('#'+kb_table_id+'_body').html(json['html']);
                $('#'+kb_table_id+'-panel-body .kb-paginator-block').html(json['pagination']);
            }else{
                var html = '<tr><td colspan="'+$('#'+kb_table_id+'-panel-body thead tr th').length+'" class="kb-tcenter kb-empty-table">'+json['msg']+'</td></tr>';
                $('#'+kb_table_id+'_body').html(html);
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            jAlert(kb_ajax_request_fail_err);
            $('#'+kb_table_id+'_filter').attr('disable', false);
            $('#kb-list-loader').hide();
        }
    });
}

function getEarningHistory(kb_table_id, page_number){
    var request_params = serializeObjectToSerialize(filter_paramters);
    request_params += '&start='+page_number;
    $.ajax({
        type: 'POST',
        headers: { "cache-control": "no-cache" },
        url: kb_current_request + ((kb_current_request.indexOf('?') < 0) ? '?' : '&')+'rand=' + new Date().getTime(),
        async: true,
        cache: false,
        dataType : "json",
        data: 'ajax=true&method=getEarningHistory'+request_params,
        beforeSend: function() {
            $('#'+kb_table_id+'_filter').attr('disable', true);
            $('#kb-list-loader').show();
        },
        success: function(json)
        {
            $('#'+kb_table_id+'_filter').attr('disable', false);
            $('#kb-list-loader').hide();
            if(json['status'] == true){
                $('#'+kb_table_id+'_body').html(json['html']);
                $('#'+kb_table_id+'-panel-body .kb-paginator-block').html(json['pagination']);
            }else{
                var html = '<tr><td colspan="'+$('#'+kb_table_id+'-panel-body thead tr th').length+'" class="kb-tcenter kb-empty-table">'+json['msg']+'</td></tr>';
                $('#'+kb_table_id+'_body').html(html);
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            jAlert(kb_ajax_request_fail_err);
            $('#'+kb_table_id+'_filter').attr('disable', false);
            $('#kb-list-loader').hide();
        }
    });
}

function getOrderwiseEarning(kb_table_id, page_number){
    var request_params = serializeObjectToSerialize(filter_paramters);
    request_params += '&start='+page_number;
    $.ajax({
        type: 'POST',
        headers: { "cache-control": "no-cache" },
        url: kb_current_request + ((kb_current_request.indexOf('?') < 0) ? '?' : '&')+'rand=' + new Date().getTime(),
        async: true,
        cache: false,
        dataType : "json",
        data: 'ajax=true&method=getOrderwiseEarning'+request_params,
        beforeSend: function() {
            $('#'+kb_table_id+'_filter').attr('disable', true);
            $('#kb-list-loader').show();
        },
        success: function(json)
        {
            $('#'+kb_table_id+'_filter').attr('disable', false);
            $('#kb-list-loader').hide();
            if(json['status'] == true){
                $('#'+kb_table_id+'_body').html(json['html']);
                $('#'+kb_table_id+'-panel-body .kb-paginator-block').html(json['pagination']);
            }else{
                var html = '<tr><td colspan="'+$('#'+kb_table_id+'-panel-body thead tr th').length+'" class="kb-tcenter kb-empty-table">'+json['msg']+'</td></tr>';
                $('#'+kb_table_id+'_body').html(html);
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            jAlert(kb_ajax_request_fail_err);
            $('#'+kb_table_id+'_filter').attr('disable', false);
            $('#kb-list-loader').hide();
        }
    });
}

function getTransactionsList(kb_table_id, page_number){
    var request_params = serializeObjectToSerialize(filter_paramters);
    request_params += '&start='+page_number;
    $.ajax({
        type: 'POST',
        headers: { "cache-control": "no-cache" },
        url: kb_current_request + ((kb_current_request.indexOf('?') < 0) ? '?' : '&')+'rand=' + new Date().getTime(),
        async: true,
        cache: false,
        dataType : "json",
        data: 'ajax=true&method=getTransactionsList'+request_params,
        beforeSend: function() {
            $('#'+kb_table_id+'_filter').attr('disable', true);
            $('#kb-list-loader').show();
        },
        success: function(json)
        {
            $('#'+kb_table_id+'_filter').attr('disable', false);
            $('#kb-list-loader').hide();
            if(json['status'] == true){
                $('#'+kb_table_id+'_body').html(json['html']);
                $('#'+kb_table_id+'-panel-body .kb-paginator-block').html(json['pagination']);
            }else{
                var html = '<tr><td colspan="'+$('#'+kb_table_id+'-panel-body thead tr th').length+'" class="kb-tcenter kb-empty-table">'+json['msg']+'</td></tr>';
                $('#'+kb_table_id+'_body').html(html);
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            jAlert(kb_ajax_request_fail_err);
            $('#'+kb_table_id+'_filter').attr('disable', false);
            $('#kb-list-loader').hide();
        }
    });
}


function getSellerCategories(kb_table_id, page_number){
    var request_params = serializeObjectToSerialize(filter_paramters);
    request_params += '&start='+page_number;
    $.ajax({
        type: 'POST',
        headers: { "cache-control": "no-cache" },
        url: kb_current_request + ((kb_current_request.indexOf('?') < 0) ? '?' : '&')+'rand=' + new Date().getTime(),
        async: true,
        cache: false,
        dataType : "json",
        data: 'ajax=true&method=getSellerCategories'+request_params,
        beforeSend: function() {
            $('#'+kb_table_id+'_filter').attr('disable', true);
            $('#kb-list-loader').show();
        },
        success: function(json)
        {
            $('#'+kb_table_id+'_filter').attr('disable', false);
            $('#kb-list-loader').hide();
            if(json['status'] == true){
                $('#'+kb_table_id+'_body').html(json['html']);
                $('#'+kb_table_id+'-panel-body .kb-paginator-block').html(json['pagination']);
            }else{
                var html = '<tr><td colspan="'+$('#'+kb_table_id+'-panel-body thead tr th').length+'" class="kb-tcenter kb-empty-table">'+json['msg']+'</td></tr>';
                $('#'+kb_table_id+'_body').html(html);
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            jAlert(kb_ajax_request_fail_err);
            $('#'+kb_table_id+'_filter').attr('disable', false);
            $('#kb-list-loader').hide();
        }
    });
}

function getSellerShippings(kb_table_id, page_number){
    var request_params = serializeObjectToSerialize(filter_paramters);
    request_params += '&start='+page_number;
    $.ajax({
        type: 'POST',
        headers: { "cache-control": "no-cache" },
        url: kb_current_request + ((kb_current_request.indexOf('?') < 0) ? '?' : '&')+'rand=' + new Date().getTime(),
        async: true,
        cache: false,
        dataType : "json",
        data: 'ajax=true&method=getSellerShippings'+request_params,
        beforeSend: function() {
            $('#'+kb_table_id+'_filter').attr('disable', true);
            $('#kb-list-loader').show();
        },
        success: function(json)
        {
            $('#'+kb_table_id+'_filter').attr('disable', false);
            $('#kb-list-loader').hide();
            if(json['status'] == true){
                $('#'+kb_table_id+'_body').html(json['html']);
                $('#'+kb_table_id+'-panel-body .kb-paginator-block').html(json['pagination']);
            }else{
                var html = '<tr><td colspan="'+$('#'+kb_table_id+'-panel-body thead tr th').length+'" class="kb-tcenter kb-empty-table">'+json['msg']+'</td></tr>';
                $('#'+kb_table_id+'_body').html(html);
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            jAlert(kb_ajax_request_fail_err);
            $('#'+kb_table_id+'_filter').attr('disable', false);
            $('#kb-list-loader').hide();
        }
    });
}

function getSellerList(page_number){
    var request_params = serializeObjectToSerialize(filter_paramters);
    var orderby = getURLParameter('orderby');
    var orderway = getURLParameter('orderway');
    if (orderby != 'undefined' && orderby != '' && orderby != null) {
        request_params += '&orderby='+orderby;
    }
    if (orderway != 'undefined' && orderway != '' && orderway != null) {
        request_params += '&orderway='+orderway;
    }
    
    request_params += '&start='+page_number;
    $.ajax({
        type: 'POST',
        headers: { "cache-control": "no-cache" },
        url: kb_current_request + ((kb_current_request.indexOf('?') < 0) ? '?' : '&')+'rand=' + new Date().getTime(),
        async: true,
        cache: false,
        dataType : "json",
        data: 'ajax=true&method=getSellerList'+request_params,
        beforeSend: function() {
//            $('#'+kb_table_id+'_filter').attr('disable', true);
            $('#kb-list-loader').show();
        },
        success: function(json)
        {
             $('#kb-list-loader').hide();
            $(".content_sortPagiBar>.sortPagiBar>.display>.display-title").html(json['pagination_string']);
            $("#front-end-customer-pagination>.sv-p-paging").html(json['kb_pagination']['pagination']+"<div class='clearfix'></div>");
            $("#seller_list_to_customers").html(json['content']);
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            jAlert(kb_ajax_request_fail_err);
            $('#'+kb_table_id+'_filter').attr('disable', false);
            $('#kb-list-loader').hide();
        }
    });
}

function getURLParameter(name) {
  return decodeURIComponent((new RegExp('[?|&]' + name + '=' + '([^&;]+?)(&|#|;|$)').exec(location.search) || [null, ''])[1].replace(/\+/g, '%20')) || null;
}

function getSellerShippingProducts(kb_table_id, page_number){
    var request_params = serializeObjectToSerialize(filter_paramters);
    var id_carrier = getURLParameter('id_carrier');
    if (id_carrier != 'undefined' && id_carrier != '' && id_carrier != null) {
        request_params += '&id_carrier='+id_carrier;
    }
    request_params += '&start='+page_number;
    $.ajax({
        type: 'POST',
        headers: { "cache-control": "no-cache" },
        url: kb_current_request + ((kb_current_request.indexOf('?') < 0) ? '?' : '&')+'rand=' + new Date().getTime(),
        async: true,
        cache: false,
        dataType : "json",
        data: 'ajax=true&method=getSellerProducts'+request_params,
        beforeSend: function() {
            $('#'+kb_table_id+'_filter').attr('disable', true);
            $('#kb-list-loader').show();
        },
        success: function(json)
        {
            $('#'+kb_table_id+'_filter').attr('disable', false);
            $('#kb-list-loader').hide();
            if(json['status'] == true){
                $('#'+kb_table_id+'_body').html(json['html']);
                $('#'+kb_table_id+'-panel-body .kb-paginator-block').html(json['pagination']);
            }else{
                var html = '<tr><td colspan="'+$('#'+kb_table_id+'-panel-body thead tr th').length+'" class="kb-tcenter kb-empty-table">'+json['msg']+'</td></tr>';
                $('#'+kb_table_id+'_body').html(html);
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            jAlert(kb_ajax_request_fail_err);
            $('#'+kb_table_id+'_filter').attr('disable', false);
            $('#kb-list-loader').hide();
        }
    });
}