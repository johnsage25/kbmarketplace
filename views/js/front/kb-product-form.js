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

var img_file = {};

$(document).ready(function() {

    if ($(".datepicker").length > 0)
        $(".datepicker").datepicker({
            prevText: '',
            nextText: '',
            dateFormat: 'yy-mm-dd'
        });

    if($('#curPackItemName').length){
        $('#curPackItemName').select2({
                placeholder: 'Search Product',
                minimumInputLength: 2,
                width: '100%',
                dropdownCssClass: "bootstrap",
                ajax: {
                    url: kb_current_request + ((kb_current_request.indexOf('?') < 0) ? '?' : '&')+'rand=' + new Date().getTime() + '&ajax=true&method=searchedproduct',
                    dataType: 'json',
                    data: function (term) {
                        return {q: term};
                    },
                    results: function (data) {
                        var excludeIds = getSelectedIds();
                        var returnIds = new Array();
                        if (data) {
                            for (var i = data.length - 1; i >= 0; i--) {
                                var is_in = 0;
                                for (var j = 0; j < excludeIds.length; j ++) {
                                    if (data[i].id == excludeIds[j][0] && (typeof data[i].id_product_attribute == 'undefined' || data[i].id_product_attribute == excludeIds[j][1]))
                                        is_in = 1;
                                }
                                if (!is_in)
                                    returnIds.push(data[i]);
                            }
                            return {results: returnIds}
                        } else {
                            return {results: []}
                        }
                    }
                },
                formatResult: productFormatResult,
                formatSelection: productFormatSelection,
        })
        .on("select2-selecting", function(e) {
                selectedProduct = e.object
        });

        $('#add_pack_item').on('click', addPackItem);    
    }
    
    $('#divPackItems').on('click', '.delPackItem', function(e){
            e.preventDefault();
            e.stopPropagation();
            delPackItem($(this).data('delete'));
    });

    $('#product-img-uploader').on('click', function() {
        $('#upload-error').hide();
        $('#upload-error').html('');
        $('#product-img-uploader-btn').trigger('click');
    });

    $('#product-img-uploader-btn').change(function(e) {
        if ($(this)[0].files !== undefined && $(this)[0].files.length > 0)
        {
            var has_invalid_file = false;
            for (var i = 0; i < $(this)[0].files.length; i++) {
                var image = $(this)[0].files[i];
                if (checkImgMediaUpload(image.type)) {
                    img_file["index-" + i] = image;
                    var name = '';
                    name += image.name;

                    var image_info_html = '<div id="uploaded-img-info-' + i + '" class="uploaded-img-info">';
                    image_info_html += '<i class="kb-material-icons"></i>';
                    image_info_html += '<span class="new-img-name">' + name.slice(0, -2) + ' (' + humanizeSize(image.size) + ')' + '</span>';
                    image_info_html += '<a href="javascript:void(0)" onclick="removeTempImage(' + i + ')" class="kbbtn btn-danger" ><i class="kb-material-icons"></i><span>Remove</span></a>';
                    image_info_html += '</div>';

                    $('#img-upload-info-block-container').append(image_info_html);
                } else {
                    has_invalid_file = true;
                }
            }
            if (has_invalid_file) {
                $('#upload-error').html(kb_img_format_error);
                $('#upload-error').show();
            }
        }
        else // Internet Explorer 9 Compatibility
        {
            var name = $(this).val().split(/[\\/]/);
            $('#new-img-name').html(name[name.length - 1]);
        }

        $('#pro-img-upload-section #star-upload-btn').bind('click');
        $('#pro-img-upload-section #remove-upload-btn').bind('click');
        $('#img-upload-info-block').show();
    });

    $('#pro-img-upload-section #remove-upload-btn').on('click', function() {
        $('#img-upload-info-block').hide();
        $('input[name="legend"]').val('')
        $('#product-img-uploader-btn').val('');
        $('#new-img-name').html('');
        $('#pro-img-upload-section #remove-upload-btn').unbind('click');
        $('#pro-img-upload-section #star-upload-btn').unbind('click');
    });

    $('#pro-img-upload-section #star-upload-btn').on('click', function() {
        uploadNewImage();
    });

    $('#virtual-file-upload-trigger').on('click', function() {
        $('#virtual-upload-error').hide();
        $('#virtual-upload-error').html('');
        $('#virtual_product_file_uploader').trigger('click');
    });

    $('#virtual_product_file_uploader').change(function(e) {
        if ($(this)[0].files !== undefined && $(this)[0].files.length > 0)
        {
            var files = $(this)[0].files;
            var name = '';

            $.each(files, function(index, value) {
                name += value.name + ', ';
            });

            $('#virtual_product_file_uploader-name').val(name.slice(0, -2));
            $('#virtual_product_name').val(name.slice(0, -2));
        }
        else // Internet Explorer 9 Compatibility
        {
            var name = $(this).val().split(/[\\/]/);
            $('#virtual_product_file_uploader-name').val(name[name.length - 1]);
            $('#virtual_product_name').val(name[name.length - 1]);
        }
    });

    $("#addShipping").on('click', function() {
        $('#availableShipping option:selected').each(function() {
            $('#selectedShipping').append("<option value='" + $(this).val() + "'>" + $(this).text() + "</option>");
            $(this).remove();
        });
        $('#selectedShipping option').prop('selected', true);
        if ($('#selectedShipping').find("option").length == 0)
            $('#shipping-selection-msg').show();
        else
            $('#shipping-selection-msg').hide();
    });
    $("#removeShipping").on('click', function() {
        $('#selectedShipping option:selected').each(function() {
            $('#availableShipping').append("<option value='" + $(this).val() + "'>" + $(this).text() + "</option>");
            $(this).remove();
        });
        $('#selectedShipping option').prop('selected', true);
        if ($('#selectedShipping').find("option").length == 0)
            $('#shipping-selection-msg').show();
        else
            $('#shipping-selection-msg').hide();
    });
    $('#label_for_available_order').on('click', function() {
        if ($('#label_for_available_order:checkbox:checked').length > 0) {
            $("#label_for_show_price").attr("checked", 'checked');
            $('#uniform-label_for_show_price span').addClass('checked');
            //$('#label_for_show_price').attr('disabled','disabled');   
        }
        else {
            $('#label_for_show_price').removeAttr('disabled');
        }
    });
    if ($('#label_for_available_order:checkbox:checked').length > 0) {
        $("#label_for_show_price").attr("checked", 'checked');
        $('#uniform-label_for_show_price span').addClass('checked');
        //$('#label_for_show_price').attr('disabled','disabled');
    }
    else {
        $('#label_for_show_price').removeAttr('disabled');
    }
    
    if ($('#selectedShipping').find('option').length > 0) {
        $('#selectedShipping').find('option').attr('selected', 'selected');
    }
    
    $('#kb_category_tree_container').on('click', 'input[type="checkbox"]', function (){
        var tree_id_category = $(this).val();
        if ($(this).is(':checked') && $('#pro_category_default option[value="'+tree_id_category+'"]').length == 0) {

            for (var i in product_categories_for_default) {
                if (product_categories_for_default[i].id_category == tree_id_category) {
                    if ($('#pro_category_default option[value=""]').length == 1) {
                        $('#pro_category_default').html('');
                    }
                    $('#pro_category_default').append('<option value="'+product_categories_for_default[i].id_category+'">'+product_categories_for_default[i].name+'</option>');
                    break;
                }
            }
        } else {
            $('#pro_category_default option[value="'+tree_id_category+'"]').remove();
            if ($('#pro_category_default option').length == 0) {
                var no_option_title = 'Select';
                $('#pro_category_default').append('<option value="">'+no_option_title+'</option>');
            }
        }
    });
    
    $('#kb-product-form #kb_product_duplicate_btn').on('click',function(){
        $('#kb-product-form #kb_product_duplicate_btn').css('pointer-events','none');
//       $(this).data('clicked',true);
    });
    
//    var click_duplicate = 1;
//    if($('#kb-product-form #kbbtn-group .btn-info').data('clicked')) {
//        if(click_duplicate == '1') {
//            
//            click_duplicate++;
//        } else {
//            $('#kb-product-form #kbbtn-group .btn-info').css('pointer-events','none');
//        }
//    }
});

function removeTempImage(index)
{
    $('#uploaded-img-info-' + index).remove();
    img_file["index-" + index] = undefined;
    if ($('#img-upload-info-block-container .uploaded-img-info').length == 0) {
        img_file = {};
        $('#img-upload-info-block').hide();
        $('input[name="legend"]').val('')
        $('#product-img-uploader-btn').val('');
        //$('#new-img-name').html('');
        //$('#pro-img-upload-section #remove-upload-btn').unbind('click');
        $('#pro-img-upload-section #star-upload-btn').unbind('click');
    }
}


function humanizeSize(bytes)
{
    if (typeof bytes !== 'number') {
        return '';
    }

    if (bytes >= 1000000000) {
        return (bytes / 1000000000).toFixed(2) + ' GB';
    }

    if (bytes >= 1000000) {
        return (bytes / 1000000).toFixed(2) + ' MB';
    }

    return (bytes / 1000).toFixed(2) + ' KB';
}

function updateLinkRewrite(e)
{
    var value = $(e).val();
    $('input[name="link_rewrite_' + $(e).attr('lang-id') + '"]').val(str2url(value));
    $('input[name="link_rewrite_' + $(e).attr('lang-id') + '"]').trigger('keyup');
}

function uploadNewImage()
{
    if (kb_id_product > 0) {
        var img_data = new FormData();
        var img_index = 0;
        $.each(img_file, function(key, value) {
            if (img_file[key] != undefined) {
                img_data.append('file[' + img_index + ']', value);
                img_index++;
            }
        });

        img_data.append('ajax', true);
        img_data.append('id_product', kb_id_product);
        img_data.append('method', 'addProductImage');
        img_data.append('token', prestashop.static_token);
        img_data.append('legend', $('input[name="legend"]').val());

        $.ajax({
            type: 'POST',
            headers: {"cache-control": "no-cache"},
            processData: false,
            contentType: false,
            url: kb_current_request + ((kb_current_request.indexOf('?') < 0) ? '?' : '&') + 'rand=' + new Date().getTime(),
            async: true,
            cache: false,
            dataType: "json",
            data: img_data,
            beforeSend: function() {
                $('#img-upload-error').hide();
                $('#img-upload-error').removeClass('kbalert-danger');
                $('#img-upload-error').removeClass('kbalert-success');
                $('#pro-img-upload-section #star-upload-btn').attr('disabled', 'disabled');
                $('#pro-img-upload-section #remove-upload-btn').attr('disabled', 'disabled');
                $('#uploading-progress').show();
            },
            complete: function() {
                $('#pro-img-upload-section #star-upload-btn').removeAttr('disabled');
                $('#pro-img-upload-section #remove-upload-btn').removeAttr('disabled');
            },
            success: function(json)
            {
                var mesg = '';
                var has_error = false;
                if (json.file != undefined && json.file.length > 0) {
                    for (var i in json.file) {
                        if (json.file[i].error != '') {
                            mesg += json.file[i].error + '<br>';
                            has_error = true;
                        } else {
                            img_file["index-" + i] = undefined;
                            $('#img-upload-info-block-container #uploaded-img-info-' + i).remove();
                            mesg = json.file[i].status;
                            var legend = '';
                            if (json.file[i].legend != null || json.file[i].legend != '') {
                                legend = json.file[i].legend;
                            }
                            displayNewLineImage(json.file[i].id, json.file[i].path, json.file[i].position, json.file[i].cover, legend);

                            //Update combination form
                            if (typeof ('updateCombinationFormImage') === typeof (Function))
                                updateCombinationFormImage(json.file[i].id, json.file[i].path, legend);
                        }
                    }
                }

                if(has_error){
                    $('#img-upload-error').html('<i class="kb-material-icons"></i> '+mesg);
                    $('#img-upload-error').addClass('kbalert-danger');
                }else{
                    $('#img-upload-error').html('<i class="kb-material-icons"></i> '+mesg);
                    $('#img-upload-error').addClass('kbalert-success');
                    $('#img-upload-info-block').hide();
                    $('input[name="legend"]').val('');
                }
                $('#img-upload-error').show();
                setTimeout(function(){$('#img-upload-error').hide();}, message_delay);
                $('#uploading-progress').hide();
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                jAlert(kb_ajax_request_fail_err);
                $('#uploading-progress').hide();
            }
        });
    } else {
        return jAlert(kb_img_save_error);
    }
}

function deleteImage(e)
{
    var cfm = confirm(kb_delete_confirmation);
    if (cfm) {
        $.ajax({
            type: 'POST',
            headers: {"cache-control": "no-cache"},
            url: kb_current_request + ((kb_current_request.indexOf('?') < 0) ? '?' : '&') + 'rand=' + new Date().getTime(),
            async: true,
            cache: false,
            dataType: "json",
            data: 'ajax=true'
                    + '&method=deleteImage&token=' + prestashop.static_token
                    + '&id_image=' + $(e).attr('data-id')
                    + '&id_product=' + kb_id_product
                    + '&id_category=' + kb_product_default_category,
            beforeSend: function() {
                $('#img-upload-error').hide();
                $('#img-upload-error').removeClass('kbalert-danger');
                $('#img-upload-error').removeClass('kbalert-success');
            },
            complete: function() {
            },
            success: function(json)
            {
                var mesg = '';
                if(json['status'] == true){
                    $('#img-upload-error').html('<i class="kb-material-icons"></i> '+json['msg']);
                    $('#img-upload-error').addClass('kbalert-success');
                    $('#product-images tr#' + $(e).attr('data-id')).remove();

                    if (!$('#product-images tr').length) {
                        $('#product-images').html('<tr><td colspan="5" class="kb-tcenter kb-empty-table">' + kb_no_image_msg + '</td></tr>');
                    }

                    //Delete image from combination form
                    $('#combination-imgs-list #combination-form-image_' + $(e).attr('data-id')).remove();

                    updateCover();
                    
                }else{
                    $('#img-upload-error').html('<i class="kb-material-icons"></i> '+json['msg']);
                    $('#img-upload-error').addClass('kbalert-danger');
                }
                $('#img-upload-error').show();
                setTimeout(function(){$('#img-upload-error').hide();}, message_delay);
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                jAlert(kb_ajax_request_fail_err);
            }
        });
    }

}

function updateCover()
{
    if ($("#product-images tr").length) {
        if (!$('#product-images input[type="radio"]:checked').length) {
            $('#product-images input[type="radio"]').each(function() {
                $(this).attr('checked', true);
                $(this).parent().addClass('checked');
                return;
            });
        }
    }
}

function displayNewLineImage(id, path, position, cover, legend)
{
    if ($('#product-images .kb-empty-table').length) {
        $('#product-images .kb-empty-table').remove();
    }
    var src_path = $('#kb_product_image_new_line_path').val();
    $("#lineType img").attr('src', src_path);
    var line = $("#lineType").html();

    line = line.replace(/image_id/g, id);
    line = line.replace(/image_path/g, path);
    line = line.replace(/image_position/g, position);
    if (legend == 'null') {
        line = line.replace(/image_legend/g, '');
    } else {
        line = line.replace(/image_legend/g, legend);
    }
    $("#product-images").append(line);
    var radio = $("#product-images tr").last().find('input[type="radio"]');
    if ($(radio).length) {
        if (cover == 1) {
            $(radio).attr('checked', true);
            $(radio).parent().addClass('checked');
        }
    }
}

function displayCombinationRow(id_product_attribute, combination, reference, ean13, upc, stock_available, is_default) {
    if ($('#kb_product_combination_list .kb-empty-table').length) {
        $('#kb_product_combination_list .kb-empty-table').remove();
    }
    $("#kb_product_combination_list tr#" + id_product_attribute).remove();
    var line = $("#new_combination_template").html();
    line = line.replace(/id_product_attribute/g, id_product_attribute);
    line = line.replace(/combination/g, combination);
    if (reference != '')
        line = line.replace(/reference/g, reference);
    else
        line = line.replace(/reference/g, '--');

    if (ean13 != '')
        line = line.replace(/ean13/g, ean13);
    else
        line = line.replace(/ean13/g, '--');

    if (upc != '')
        line = line.replace(/upc/g, upc);
    else
        line = line.replace(/upc/g, '--');

    line = line.replace(/stock_available/g, stock_available);

    if (is_default == 1)
        line = line.replace(/is_default/g, 'Yes');
    else
        line = line.replace(/is_default/g, 'No');

    $("#kb_product_combination_list").append(line);

    if (is_default == 1) {
        $("#kb_product_combination_list tr").removeClass('default_combination');
        $("#kb_product_combination_list tr").each(function() {
            $(this).find('td.de_com_col').html('No');
        });

        $("#kb_product_combination_list tr#" + id_product_attribute + " td.de_com_col").html('Yes');
        $("#kb_product_combination_list tr#" + id_product_attribute).addClass('default_combination');
    }

    $('#combination-section-msg').show();
}

function populate_attrs()
{
    var attr_group = $('#attribute_group');
    if (!attr_group)
        return;
    var attr_name = $('#attribute');
    var number = attr_group[0].options.length ? attr_group[0].options[attr_group[0].selectedIndex].value : 0;

    if (!number)
    {
        attr_name[0].options.length = 0;
        attr_name[0].options[0] = new Option('---', 0);
        return;
    }

    var list = attrs[number];
    attr_name[0].options.length = 0;
    if (typeof list !== 'undefined')
    {
        for (i = 0; i < list.length; i += 2)
            attr_name[0].options[i / 2] = new Option(list[i + 1], list[i]);
    }
}

/**
 * Add an attribute from a group in the declination multilist
 */
var storeUsedGroups = {};
function add_attr()
{
//    console.lo
    var attr_group = $('#attribute_group option:selected');
    var attr_name = $('#attribute option:selected');
    console.log(attr_group.val()+'sds'+attr_name.val());
//    console.log(msg_combination_3);
    
    if (attr_group.val() == 0)
        return jAlert(msg_combination_1);

    
    if (attr_name.val() == 0)
        return jAlert(msg_combination_2);

    if (attr_group.val()+'-'+attr_name.val() in storeUsedGroups)
        return jAlert(msg_combination_3);

    storeUsedGroups[attr_group.val()+'-'+attr_name.val()] = true;
    $('<option></option>')
            .attr('value', attr_name.val())
            .attr('groupid', attr_group.val())
            .text(attr_group.text() + ' : ' + attr_name.text())
            .appendTo('#product_att_list');
}

/**
 * Delete one or several attributes from the declination multilist
 */
function del_attr()
{
    $('#product_att_list option:selected').each(function()
    {
        delete storeUsedGroups[$(this).attr('groupid')];
        $(this).remove();
    });
}

function editCombination(id_product_attribute)
{
    $('#attribute_group option').removeAttr('selected');
    $('#attribute').html('<option value="0">-</option>');
    $('#product_att_list').html('');
    $('#attribute_reference').val('');
    $('#attribute_ean13').val('');
    $('#attribute_upc').val('');
    $('#attribute_stock_available').val('');
    $('#available_date_attribute').val('');
    $('input[name="id_image_attr[]"]').each(function() {
        $(this).removeAttr('checked');
        $(this).parent().addClass('unchecked');
    });
    $('input[name="attribute_default"]').removeAttr('checked');
    $('input[name="attribute_default"]').parent().removeClass('checked');
    $('input[name="attribute_default"]').parent().addClass('unchecked');
    $('#combination-submit').attr('data-id', 0);
    if (id_product <= 0) {
        return jAlert(msg_combination_4);
    } else if (id_product_attribute == 0) {
        storeUsedGroups = {};
        $('#kb_combination_form_title').html(kb_new_combination_title);
        $('#attribute_group').trigger('change');
        $('#kb-combination-modal-form #combination-loader').hide();
        $('#kb-combination-modal-form #combination-form-content').show();
        $('#kb-combination-modal-form').show();
        $("html, body").animate({scrollTop: 0}, '500');
    } else if (id_product_attribute > 0) {
        $('#kb_combination_form_title').html(kb_edit_combination_title);
        $('#kb-combination-modal-form #combination-loader').show();
        $('#kb-combination-modal-form #combination-form-content').hide();
        $('#kb-combination-modal-form').show();
        $("html, body").animate({scrollTop: 0}, '500');
        $.ajax({
            type: 'POST',
            headers: {"cache-control": "no-cache"},
            url: kb_current_request + ((kb_current_request.indexOf('?') < 0) ? '?' : '&') + 'rand=' + new Date().getTime(),
            async: true,
            cache: false,
            dataType: "json",
            data: 'ajax=true'
                    + '&method=getCombination&token=' + prestashop.static_token
                    + '&id_product_attribute=' + id_product_attribute
                    + '&id_product=' + kb_id_product,
            beforeSend: function() {

            },
            success: function(json)
            {
                if (json.length) {
                    storeUsedGroups = {};
                    for (var i in json)
                    {
                        storeUsedGroups[json[i].id_attribute_group] = true;
                        var combination_html = '<option value="' + json[i].id_attribute + '" groupid="' + json[i].id_attribute_group + '">' + json[i].group_name + '&nbsp;&nbsp; : ' + json[i].attribute_name + '</option>';
                        $('#product_att_list').append(combination_html);
                    }
                    $('#attribute_reference').val(json[0].reference);
                    $('#attribute_ean13').val(json[0].ean13);
                    $('#attribute_upc').val(json[0].upc);
                    $('#attribute_stock_available').val(json[0].stock_available);
                    $('#available_date_attribute').val(json[0].available_date);
                    var arr = $.map(json[0].id_img_attr, function(el) {
                        return el;
                    });
                    if (arr.length) {
                        $('input[name="id_image_attr[]"]').each(function() {
                            if ($.inArray(parseInt($(this).val()), arr) > -1) {
                                $(this).attr('checked', 'checked');
                                $(this).parent().addClass('checked');
                            }
                        });
                    }
                    if (json[0].default_on == '1' || json[0].default_on == 1) {
                        $('#attribute_default').attr('checked', 'checked');
                        $('#attribute_default').parent().removeClass('unchecked');
                        $('#attribute_default').parent().addClass('checked');
                    }
                    $('#combination-submit').attr('data-id', id_product_attribute);
                    $('#kb-combination-modal-form #combination-loader').hide();
                    $('#kb-combination-modal-form #combination-form-content').show();
                } else {
                    jAlert(msg_combination_5);
                    $('#kb-combination-modal-form #combination-loader').show();
                    $('#kb-combination-modal-form #combination-form-content').hide();
                    $('#kb-combination-modal-form').hide();
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                jAlert(kb_ajax_request_fail_err);
                $('#kb-combination-modal-form #combination-loader').show();
                $('#kb-combination-modal-form #combination-form-content').hide();
                $('#kb-combination-modal-form').hide();
            }
        });
    }
}

function saveCombination()
{
    $('#new-combination-form').find('.kb-validation-error').remove();
    $('#new-combination-form-msg').html('');
    $('#new-combination-form-msg').removeClass('kbalert-danger');
    $('#new-combination-form-msg').removeClass('kbalert-success');
    var error = false;
    $('#new-combination-form input[type="text"]').each(function() {
        var value = $(this).val();
        value = value.trim();
        if ($(this).hasClass('required'))
        {
            if (value == '')
            {
                error = true;
                $(this).parent().append('<div class="kb-validation-error">' + kb_required_field + '</div>');
            }
            else {
                if ($(this).attr('validate') != undefined && !kbValidateField(value, $(this).attr('validate')))
                {
                    error = true;
                    $(this).parent().append('<div class="kb-validation-error">' + kb_invalid_field + '</div>');
                }
            }
        } else if (value != '' && $(this).attr('validate') != undefined && !kbValidateField(value, $(this).attr('validate'))) {
            error = true;
            $(this).parent().append('<div class="kb-validation-error">' + kb_invalid_field + '</div>');
        }
    });

    if (!$('#product_att_list option').length) {
        error = true;
        $(this).parent().append('<div class="kb-validation-error">' + msg_combination_6 + '</div>');
    }

    if (!error) {
        var attribs = [];
        $('#product_att_list option').each(function() {
            attribs.push($(this).val());
        });
        var id_product_attribute = parseInt($('#combination-submit').attr('data-id'));
        $.ajax({
            type: 'POST',
            headers: {"cache-control": "no-cache"},
            url: kb_current_request + ((kb_current_request.indexOf('?') < 0) ? '?' : '&') + 'rand=' + new Date().getTime(),
            async: true,
            cache: false,
            dataType: "json",
            data: 'ajax=true'
                    + '&method=saveCombination&token=' + prestashop.static_token
                    + '&id_product_attribute=' + id_product_attribute
                    + '&id_product=' + kb_id_product
                    + '&attribute_combination_list=' + attribs
                    + '&' + $('#new-combination-form input, #new-combination-form select').serialize(),
            beforeSend: function() {
                $('#new-combination-form').attr('disable', true);
                $('#combination-updating-progress').css('display', 'inline-block');
            },
            success: function(json)
            {
                if (json['status'] == true) {
                    if (json['data'].id_product_attribute > 0) {
                        displayCombinationRow(json['data'].id_product_attribute, json['data'].attribute_designation, json['data'].reference, json['data'].ean13, json['data'].upc, json['data'].stock_available, json['data'].default_on);
                    }
                    $('#kb-combination-modal-form').hide();
                    $('#kb-combination-modal-form #combination-loader').show();
                    $('#kb-combination-modal-form #combination-form-content').hide();
                    storeUsedGroups = {};
                    jAlert(json['msg']);
                } else {
                    var error_html = '';
                    for (var i = 0; i < json['errors'].length; i++)
                        error_html += json['errors'][i] + '<br>';
                    jAlert(error_html);
                }
                $('#combination-updating-progress').css('display', 'none');
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                jAlert(kb_ajax_request_fail_err);
                $('#combination-updating-progress').css('display', 'none');
            }
        });
    }
}

function deleteCombination(id_product_attribute)
{
    var cfm = confirm(kb_delete_confirmation);
    if (cfm) {
        $.ajax({
            type: 'POST',
            headers: {"cache-control": "no-cache"},
            url: kb_current_request + ((kb_current_request.indexOf('?') < 0) ? '?' : '&') + 'rand=' + new Date().getTime(),
            async: true,
            cache: false,
            dataType: "json",
            data: 'ajax=true'
                    + '&method=deleteCombination&token=' + prestashop.static_token
                    + '&id_product_attribute=' + id_product_attribute
                    + '&id_product=' + kb_id_product,
            beforeSend: function() {

            },
            success: function(json)
            {
                if (json['status'] == 'ok') {
                    $("#kb_product_combination_list tr#" + id_product_attribute).remove();
                    if (!$('#kb_product_combination_list tr').length) {
                        $('#kb_product_combination_list').html('<tr><td colspan="7" class="kb-tcenter kb-empty-table">' + kb_no_combination_msg + '</td></tr>');
                    }
                }
                jAlert(json['message']);
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                jAlert(kb_ajax_request_fail_err);
            }
        });
    }
}

function deleteVirtualFile()
{
    if (kb_id_product != undefined && kb_id_product > 0) {
        var cfm = confirm(kb_delete_confirmation);
        if (cfm) {
            $.ajax({
                type: 'POST',
                headers: {"cache-control": "no-cache"},
                url: kb_current_request + ((kb_current_request.indexOf('?') < 0) ? '?' : '&') + 'rand=' + new Date().getTime(),
                async: true,
                cache: false,
                dataType: "json",
                data: 'ajax=true'
                        + '&method=deleteVirtualFile&token=' + prestashop.static_token
                        + '&id_product=' + kb_id_product,
                beforeSend: function() {
                    $('#virtual-section-msg').show();
                    $('#virtual-section-msg').html('');
                },
                success: function(json)
                {
                    if (json['status'] == true) {
                        if (json['redirect'] != '') {
                            location.href = json['redirect'];
                        } else {
                            $('#virtual-section-msg').html('<i class="kb-material-icons"></i>  ' + json['msg']);
                            $('#virtual-section-msg').addClass('kbalert-success');
                            $('.virtual_good').hide();
                            $('.virtual_good input[type="text"]').val('');
                            $('input[name="is_virtual_file"]').each(function() {
                                if ($(this).val() == 0)
                                    $(this).trigger('click');
                            });
                        }
                    } else {
                        $('#virtual-section-msg').html('<i class="kb-material-icons"></i> ' + json['msg']);
                        $('#virtual-section-msg').addClass('kbalert-danger');
                    }
                    $('#virtual-section-msg').show();
                    setTimeout(function() {
                        $('#virtual-section-msg').hide();
                    }, message_delay);
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    jAlert(kb_ajax_request_fail_err);
                }
            });
        }
    }
}

var ints;
var id_product;
var selectedProduct;
function getSelectedIds()
{
    if ($('#inputPackItems').val() === undefined)
        return '';
    var ids = '';
    if (typeof (id_product) != 'undefined')
        ids += id_product + ',';

    ids += $('#inputPackItems').val().replace(/\d*x/g, '').replace(/\-/g, ',');
    ids = ids.replace(/\,$/, '');
    ids = ids.split(',');
    ints = new Array();

    for (var i = 0; i < ids.length; i++) {
        ints[i] = parseInt(ids[i]);
    }

    return ints;
}

function productFormatResult(item) {
    var itemTemplate = "<div class='media'>";
    itemTemplate += "<div class='pull-left'>";
    itemTemplate += "<img class='media-object' width='40' src='" + item.image + "' alt='" + item.name + "'>";
    itemTemplate += "</div>";
    itemTemplate += "<div class='media-body'>";
    itemTemplate += "<h4 class='media-heading'>" + item.name + "</h4>";
    itemTemplate += "<span>REF: " + item.ref + "</span>";
    itemTemplate += "</div>";
    itemTemplate += "</div>";
    return itemTemplate;
}

function productFormatSelection(item) {
    return item.name;
}

function addPackItem() {

    if (selectedProduct) {
        selectedProduct.qty = $('#curPackItemQty').val();
        if (selectedProduct.id == '' || selectedProduct.name == '' && !kbValidateField($('#curPackItemQty').val(), 'isInt')) {
            jAlert('Error');
            return false;
        } else if (selectedProduct.qty == '' || !kbValidateField($('#curPackItemQty').val(), 'isInt')) {
            jAlert('Error');
            return false;
        }
        var divContent = $('#divPackItems').html();
        divContent += '<li class="product-pack-item packpro-prev-pack" data-product-name="' + selectedProduct.name + '" data-product-qty="' + selectedProduct.qty + '" data-product-id="' + selectedProduct.id + '">';
        divContent += '<img class="packpro-prev-pack-img" src="' + selectedProduct.image + '"/>';
        divContent += '<span class="packpro-prev-pack-title">' + selectedProduct.name + '</span>';
        divContent += '<span class="packpro-prev-pack-ref">' + kb_package_product_ref_lbl + ': ' + selectedProduct.ref + '</span>';
        divContent += '<span class="packpro-prev-pack-quantity"><span class="text-muted">x</span> ' + selectedProduct.qty + '</span>';
        divContent += '<a href="javascript:void(0)" class="btn-sm btn-tertiary delPackItem packpro-prev-pack-action" data-delete="' + selectedProduct.id + '" ><i class="kb-material-icons"></i></a>';
        divContent += '</li>';

        // QTYxID-QTYxID
        // @todo : it should be better to create input for each items and each qty
        // instead of only one separated by x, - and ¤
        var line = selectedProduct.qty + 'x' + selectedProduct.id;
        if (selectedProduct.id_product_attribute != undefined)
            line += 'x' + selectedProduct.id_product_attribute;

        var lineDisplay = selectedProduct.qty + 'x ' + selectedProduct.name;

        $('#divPackItems').html(divContent);
        $('#inputPackItems').val($('#inputPackItems').val() + line + '-');
        $('#namePackItems').val($('#namePackItems').val() + lineDisplay + '¤');

        selectedProduct = null;
        $('#curPackItemName').select2("val", "");
        $('.pack-empty-warning').hide();
    } else {
        error_modal(error_heading_msg, msg_select_one);
        return false;
    }
}

function delPackItem(id) {

    var reg = new RegExp('-', 'g');
    var regx = new RegExp('x', 'g');

    var input = $('#inputPackItems');
    var name = $('#namePackItems');

    var inputCut = input.val().split(reg);
    var nameCut = name.val().split(new RegExp('¤', 'g'));

    input.val(null);
    name.val(null);

    for (var i = 0; i < inputCut.length; ++i)
        if (inputCut[i]) {
            var inputQty = inputCut[i].split(regx);
            if (inputQty[1] != id) {
                input.val(input.val() + inputCut[i] + '-');
                name.val(name.val() + nameCut[i] + '¤');
            }
        }

    var elem = $('.product-pack-item[data-product-id = "' + id + '"]');
    elem.remove();

    if ($('.product-pack-item').length === 0) {
        $('.pack-empty-warning').show();
    }
}


function submitProductForm(submission_type)
{
    if ($('#selectedShipping').find('option').length > 0) {
        $('#selectedShipping').find('option').attr('selected', 'selected');
    }
    $('#kb-product-form').find('.kb-validation-error').remove();
    $('#kb-product-form-global-msg').hide();
    $('#kb-product-form-global-msg').html('');
    var error = false;

    var value = '';
    $('#kb-product-form input[type="text"]').each(function() {
        value = $(this).val();
        value = value.trim();
        if ($(this).hasClass('required'))
        {
            if (value == '')
            {
                error = true;
                $(this).parent().parent().append('<div class="kb-validation-error">' + kb_required_field + '</div>');
                highlightProductErrorTab(this);
            }
            else {
                if ($(this).attr('validate') != undefined && !kbValidateField(value, $(this).attr('validate')))
                {
                    error = true;
                    $(this).parent().parent().append('<div class="kb-validation-error">' + kb_invalid_field + '</div>');
                    highlightProductErrorTab(this);
                }
            }
        } else if (value != '' && $(this).attr('validate') != undefined && !kbValidateField(value, $(this).attr('validate'))) {
            error = true;
            $(this).parent().parent().append('<div class="kb-validation-error">' + kb_invalid_field + '</div>');
            highlightProductErrorTab(this);
        }
    });

    if ($('#kb-product-form input[name="ean13"]').val() != '') {
        var ean13_reg = /^[0-9]{0,13}$/;
        if (!ean13_reg.test($('#kb-product-form input[name="ean13"]').val())) {
            error = true;
            $('#kb-product-form input[name="ean13"]').parent().parent().append('<div class="kb-validation-error">' + kb_invalid_field + '</div>');
            highlightProductErrorTab('#kb-product-form input[name="ean13"]');
        }
    }
    if ($('#minimal_quantity_input').val() == '' || $('#minimal_quantity_input').val() == '0') {
//        var upc_reg = /^[0-9]{0,12}$/;
//        if (!upc_reg.test($('#kb-product-form input[name="upc"]').val())) {
            error = true;
            $('#minimal_quantity_input').parent().parent().append('<div class="kb-validation-error">Minimum Quantity can not be zero or blank</div>');
            highlightProductErrorTab('#minimal_quantity_input');
//        }
    }
    console.log($('#kb_product_quantity').val());
    if ($('#kb_product_quantity').val() == '' || $('#kb_product_quantity').val() <= 0) {
//        var upc_reg = /^[0-9]{0,12}$/;
//        if (!upc_reg.test($('#kb-product-form input[name="upc"]').val())) {
            error = true;
            $('#kb_product_quantity').parent().parent().append('<div class="kb-validation-error">Product Quantity can not be less than 1 or blank</div>');
            highlightProductErrorTab('#kb_product_quantity');
//        }
    }
    
    if ($('#kb-product-form input[name="upc"]').val() != '') {
        var upc_reg = /^[0-9]{0,12}$/;
        if (!upc_reg.test($('#kb-product-form input[name="upc"]').val())) {
            error = true;
            $('#kb-product-form input[name="upc"]').parent().parent().append('<div class="kb-validation-error">' + kb_invalid_field + '</div>');
            highlightProductErrorTab('#kb-product-form input[name="upc"]');
        }
    }

    //added to validate category of product
    if ($('#pro_category_default').val() == '') {
        error = true;
        $('#pro_category_default').parent().parent().append('<div class="kb-validation-error">' + kb_required_field + '</div>');
        highlightProductErrorTab('#pro_category_default');
    }
    //Product Special Price Validation
    var price = $('#price').val().trim();
    var special_price = $('#sp_reduction').val().trim();
    if (price != '' && special_price != '') {
        if (parseFloat(price) < parseFloat(special_price)) {
            $('#sp_reduction').parent().parent().append('<div class="kb-validation-error">' + kb_special_price_invalid + '</div>');
            error = true;
            highlightProductErrorTab('#sp_reduction');
        }
    }

    //Product Special Dates Validation
    if (special_price != '' && parseInt(special_price) > 0)
    {
        var start_date = '';
        if ($('#sp_from_date').val() == '') {
            $('#sp_from_date').parent().parent().append('<div class="kb-validation-error">' + kb_required_field + '</div>');
            error = true;
            highlightProductErrorTab('#sp_from_date');
        } else if (!kbValidateField($('#sp_from_date').val(), $('#sp_from_date').attr('validate'))) {
            error = true;
            $('#sp_from_date').parent().parent().append('<div class="kb-validation-error">' + kb_invalid_field + '</div>');
            highlightProductErrorTab('#sp_from_date');
        } else {
            start_date = $('#sp_from_date').val();
        }

        var end_date = '';
        if ($('#sp_to').val() == '') {
            $('#sp_to').parent().parent().append('<div class="kb-validation-error">' + kb_required_field + '</div>');
            error = true;
            highlightProductErrorTab('#sp_to');
        } else if (!kbValidateField($('#sp_to').val(), $('#sp_to').attr('validate'))) {
            error = true;
            $('#sp_to').parent().parent().append('<div class="kb-validation-error">' + kb_invalid_field + '</div>');
            highlightProductErrorTab('#sp_to');
        } else {
            end_date = $('#sp_to').val();
        }
        if (!error) {
            if ((new Date(start_date).getTime()) > (new Date(end_date).getTime())) {
                $('#sp_to').parent().parent().append('<span class="kb-validation-error">' + kb_invalid_sp_date_msg + '</span>');
                error = true;
                highlightProductErrorTab('#sp_to');
            }
        }
//        if($('#sp_from_date').val() != '' && $('#sp_to').val() != ''){
//            var start_date = $('#sp_from_date').val();
//            var end_date = $('#sp_to').val();
//            
//        }    
    }

    var product_type = $('#kb_product_type').val();
    var glob_msg = '';
    if (!$.inArray(product_type, kb_product_types)) {
        error = true;
        glob_msg = 'Product type is missing';
    } else if (product_type == kb_product_type_pack
            && $('#inputPackItems').length && $('#inputPackItems').val() == '') {
        error = true;
        glob_msg = 'This pack is empty. You must add at least one product item.';
        highlightProductErrorTab('#inputPackItems');
    }
    var click =1;
    if (!error) {
        if (submission_type == 'savenstay') {
            $('#kb_submission_type').val('savenstay');
        }
        else {
            $('#kb_submission_type').val('save');
        }
        if (click=='1') {
            $('#kb-product-form').submit();
            $('#kb-product-form #submit_product_form_butn').css('pointer-events','none');
//            $('#kb_category_request_form #kb_category_request_submit').attr('disabled','true');
            click++;
        } else {
            $('#kb-product-form #submit_product_form_butn').css('pointer-events','none');
//            $('#kb_category_request_form #kb_category_request_submit').attr('disabled','true');
        }
    } else {
        if (glob_msg != '') {
            $('#kb-product-form-global-msg').html('<i class="icon-exclamation-sign"></i>' + kb_form_validation_error + '<br>' + glob_msg);
        } else {
            $('#kb-product-form-global-msg').html('<i class="icon-exclamation-sign"></i>' + kb_form_validation_error);
        }
        $('#kb-product-form-global-msg').show();
        $("html, body").animate({scrollTop: 0}, '500');
    }
}

function highlightProductErrorTab(element)
{
    $(element).closest('.kb_product_section').find('.kb-panel-header-tab').addClass('error_tab');
}

function str2url(str, encoding, ucfirst)
{
    str = str.toUpperCase();
    str = str.toLowerCase();
    /* Lowercase */
    str = str.replace(/[\u00E0\u00E1\u00E2\u00E3\u00E4\u00E5\u0101\u0103\u0105\u0430]/g, 'a');
    str = str.replace(/[\u0431]/g, 'b');
    str = str.replace(/[\u00E7\u0107\u0109\u010D\u0446]/g, 'c');
    str = str.replace(/[\u010F\u0111\u0434]/g, 'd');
    str = str.replace(/[\u00E8\u00E9\u00EA\u00EB\u0113\u0115\u0117\u0119\u011B\u0435\u044D]/g, 'e');
    str = str.replace(/[\u0444]/g, 'f');
    str = str.replace(/[\u011F\u0121\u0123\u0433\u0491]/g, 'g');
    str = str.replace(/[\u0125\u0127]/g, 'h');
    str = str.replace(/[\u00EC\u00ED\u00EE\u00EF\u0129\u012B\u012D\u012F\u0131\u0438\u0456]/g, 'i');
    str = str.replace(/[\u0135\u0439]/g, 'j');
    str = str.replace(/[\u0137\u0138\u043A]/g, 'k');
    str = str.replace(/[\u013A\u013C\u013E\u0140\u0142\u043B]/g, 'l');
    str = str.replace(/[\u043C]/g, 'm');
    str = str.replace(/[\u00F1\u0144\u0146\u0148\u0149\u014B\u043D]/g, 'n');
    str = str.replace(/[\u00F2\u00F3\u00F4\u00F5\u00F6\u00F8\u014D\u014F\u0151\u043E]/g, 'o');
    str = str.replace(/[\u043F]/g, 'p');
    str = str.replace(/[\u0155\u0157\u0159\u0440]/g, 'r');
    str = str.replace(/[\u015B\u015D\u015F\u0161\u0441]/g, 's');
    str = str.replace(/[\u00DF]/g, 'ss');
    str = str.replace(/[\u0163\u0165\u0167\u0442]/g, 't');
    str = str.replace(/[\u00F9\u00FA\u00FB\u00FC\u0169\u016B\u016D\u016F\u0171\u0173\u0443]/g, 'u');
    str = str.replace(/[\u0432]/g, 'v');
    str = str.replace(/[\u0175]/g, 'w');
    str = str.replace(/[\u00FF\u0177\u00FD\u044B]/g, 'y');
    str = str.replace(/[\u017A\u017C\u017E\u0437]/g, 'z');
    str = str.replace(/[\u00E6]/g, 'ae');
    str = str.replace(/[\u0447]/g, 'ch');
    str = str.replace(/[\u0445]/g, 'kh');
    str = str.replace(/[\u0153]/g, 'oe');
    str = str.replace(/[\u0448]/g, 'sh');
    str = str.replace(/[\u0449]/g, 'ssh');
    str = str.replace(/[\u044F]/g, 'ya');
    str = str.replace(/[\u0454]/g, 'ye');
    str = str.replace(/[\u0457]/g, 'yi');
    str = str.replace(/[\u0451]/g, 'yo');
    str = str.replace(/[\u044E]/g, 'yu');
    str = str.replace(/[\u0436]/g, 'zh');

    /* Uppercase */
    str = str.replace(/[\u0100\u0102\u0104\u00C0\u00C1\u00C2\u00C3\u00C4\u00C5\u0410]/g, 'A');
    str = str.replace(/[\u0411]/g, 'B');
    str = str.replace(/[\u00C7\u0106\u0108\u010A\u010C\u0426]/g, 'C');
    str = str.replace(/[\u010E\u0110\u0414]/g, 'D');
    str = str.replace(/[\u00C8\u00C9\u00CA\u00CB\u0112\u0114\u0116\u0118\u011A\u0415\u042D]/g, 'E');
    str = str.replace(/[\u0424]/g, 'F');
    str = str.replace(/[\u011C\u011E\u0120\u0122\u0413\u0490]/g, 'G');
    str = str.replace(/[\u0124\u0126]/g, 'H');
    str = str.replace(/[\u0128\u012A\u012C\u012E\u0130\u0418\u0406]/g, 'I');
    str = str.replace(/[\u0134\u0419]/g, 'J');
    str = str.replace(/[\u0136\u041A]/g, 'K');
    str = str.replace(/[\u0139\u013B\u013D\u0139\u0141\u041B]/g, 'L');
    str = str.replace(/[\u041C]/g, 'M');
    str = str.replace(/[\u00D1\u0143\u0145\u0147\u014A\u041D]/g, 'N');
    str = str.replace(/[\u00D3\u014C\u014E\u0150\u041E]/g, 'O');
    str = str.replace(/[\u041F]/g, 'P');
    str = str.replace(/[\u0154\u0156\u0158\u0420]/g, 'R');
    str = str.replace(/[\u015A\u015C\u015E\u0160\u0421]/g, 'S');
    str = str.replace(/[\u0162\u0164\u0166\u0422]/g, 'T');
    str = str.replace(/[\u00D9\u00DA\u00DB\u00DC\u0168\u016A\u016C\u016E\u0170\u0172\u0423]/g, 'U');
    str = str.replace(/[\u0412]/g, 'V');
    str = str.replace(/[\u0174]/g, 'W');
    str = str.replace(/[\u0176\u042B]/g, 'Y');
    str = str.replace(/[\u0179\u017B\u017D\u0417]/g, 'Z');
    str = str.replace(/[\u00C6]/g, 'AE');
    str = str.replace(/[\u0427]/g, 'CH');
    str = str.replace(/[\u0425]/g, 'KH');
    str = str.replace(/[\u0152]/g, 'OE');
    str = str.replace(/[\u0428]/g, 'SH');
    str = str.replace(/[\u0429]/g, 'SHH');
    str = str.replace(/[\u042F]/g, 'YA');
    str = str.replace(/[\u0404]/g, 'YE');
    str = str.replace(/[\u0407]/g, 'YI');
    str = str.replace(/[\u0401]/g, 'YO');
    str = str.replace(/[\u042E]/g, 'YU');
    str = str.replace(/[\u0416]/g, 'ZH');

    str = str.toLowerCase();

    str = str.replace(/[^a-z0-9\s\'\:\/\[\]-]/g,'');
    str = str.replace(/[\u0028\u0029\u0021\u003F\u002E\u0026\u005E\u007E\u002B\u002A\u002F\u003A\u003B\u003C\u003D\u003E]/g, '');
    str = str.replace(/[\s\'\:\/\[\]-]+/g, ' ');

    // Add special char not used for url rewrite
    str = str.replace(/[ ]/g, '-');
    str = str.replace(/[\/\\"'|,;%]*/g, '');

    if (ucfirst == 1) {
            var first_char = str.charAt(0);
            str = first_char.toUpperCase()+str.slice(1);
    }

	return str;
}