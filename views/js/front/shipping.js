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

var range_sup;
var range_inf;
var curent_index;
var current_inf;
var current_sup;
var next_inf;
var testing_index;
var index;
var testing_inf;
var testing_sup;
var fees_is_hide = false;
var last_sup_val;
var img_file = null;

$(document).ready(function(){
    
    $('#shipping_logo').change(function(e) {
        if (this.files && this.files[0]) {
            $('#update_shipping_logo').val('1');
            $('#kb_shipping_logo_remove').show();
            img_file = $(this)[0].files;
            var reader = new FileReader();
            reader.onload = shippingLogoIsLoaded;
            reader.readAsDataURL(this.files[0]);
        }
    });
    
    $('#pro-img-upload-section #star-upload-btn').on('click', function(){
        uploadShippingLogo();
    });
    
   bind_inputs();
   displayRangeType();
   if (parseInt($('select[name="is_free"] option:selected').val()))
		is_freeClick($('select[name="is_free"]'));
});

function shippingLogoIsLoaded(e)
{
    var total_size = convertBytesIntoMb(e.total);
    if(checkImgMediaUpload(e.target.result) && (total_size < kb_image_size_limit)){
        jQuery('#shipping_logo_error').html("");
        uploadShippingLogo(e);
    }
    else
    {
        jQuery('#logo').val('null');
        if(total_size > kb_image_size_limit)
            jQuery('#shipping_logo_error').html(kb_img_size_error);
        else
            jQuery('#shipping_logo_error').html(kb_img_type_error);
        jQuery('#shipping_logo_update').val(0);
    }
}

function bind_inputs(){
    $('tr.range_sup td input:text, tr.range_inf td input:text').focus(function () {
		$(this).closest('div.input-group').removeClass('has-error');
	});
    
    $('tr.delete_range td button').off('click').on('click', function () {
		if (confirm(delete_range_confirm))
		{
			index = $(this).closest('td').index();
			$('tr.range_sup td:eq('+index+'), tr.range_inf td:eq('+index+'), tr.fees_all td:eq('+index+'), tr.delete_range td:eq('+index+')').remove();
			$('tr.fees').each(function () {
				$(this).find('td:eq('+index+')').remove();
			});
			rebuildTabindex();
		}
		return false;
	});
    
    $('tr.fees td input:checkbox').off('change').on('change', function ()
	{
		if($(this).is(':checked'))
		{
			$(this).closest('tr').find('td').each(function () {
				index = $(this).index();
				if ($('tr.fees_all td:eq('+index+')').hasClass('validated'))
				{
					enableGlobalFees(index);
					$(this).find('div.input-group input:text').removeAttr('disabled');
				}
				else
					disabledGlobalFees(index);
			});
		}
		else
			$(this).closest('tr').find('td').find('div.input-group input:text').attr('disabled', 'disabled');

		return false;
	});
    
   $('tr.range_sup td input:text, tr.range_inf td input:text').keypress(function (evn) {
		index = $(this).closest('td').index();
		if (evn.keyCode == 13)
		{
			if (validateRange(index))
				enableRange(index);
			else
				disableRange(index);
			return false;
		}
	});
    
    $('tr.fees_all td input:text').keypress(function (evn) {
		index = $(this).parent('td').index();
		if (evn.keyCode == 13)
			return false;
	});
    
    $('tr.range_sup td input:text, tr.range_inf td input:text').typeWatch({
		captureLength: 0,
		highlight: false,
		wait: 1000,
		callback: function() {
			index = $(this.el).closest('td').index();
			range_sup = $('tr.range_sup td:eq('+index+')').find('div.input-group input:text').val().trim();
			range_inf = $('tr.range_inf td:eq('+index+')').find('div.input-group input:text').val().trim();
			if (range_sup != '' && range_inf != '')
			{
				if (validateRange(index))
					enableRange(index);
				else
					disableRange(index);
			}
		}
	});
    
    $(document.body).off('change', 'tr.fees_all td input').on('change', 'tr.fees_all td input', function() {
	   index = $(this).closest('td').index();
		val = $(this).val();
		$(this).val('');
		$('tr.fees').each(function () {
			$(this).find('td:eq('+index+') input:text:enabled').val(val);
		});

		return false;
	});
    
    $('select[name="is_free"]').off('change').on('change', function() {
		is_freeClick(this);
	});
    
    $('input[name="shipping_method"]').off('change').on('change', function() {
        $('input[name="shipping_method"]').parent().removeClass('checked');
        $(this).parent().addClass('checked');
		$.ajax({
			type:"POST",
			url : kb_current_request + ((kb_current_request.indexOf('?') < 0) ? '?' : '&')+'rand=' + new Date().getTime(),
			async: false,
			dataType: 'html',
            data: 'ajax=true'
                +'&id_carrier='+kb_id_carrier
                +'&shipping_method='+parseInt($(this).val())
                +'&method=changeRanges',
			success : function(data) {
				$('#zone_ranges').replaceWith(data);
				displayRangeType();
				bind_inputs();
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {
				jAlert(kb_ajax_request_fail_err);
			}
		});
	});
}

function is_freeClick(elt)
{
	var is_free = $(elt).find('option:selected').val();
	if (parseInt(is_free))
		hideFees();
	else if (fees_is_hide)
		showFees();
}

function hideFees()
{
	$('tr.range_inf td, tr.range_sup td, tr.fees_all td, tr.fees td').each(function () {
		if ($(this).index() >= 2)
		{
            $(this).find('input:text, button').attr('disabled', 'disabled');
//			$(this).find('input:text, button').val('').attr('disabled', 'disabled').css('background-color', '#999999').css('border-color', '#999999');
//			$(this).css('background-color', '#999999');
		}
	});
	fees_is_hide = true;
}

function showFees()
{
	$('tr.range_inf td, tr.range_sup td, tr.fees_all td, tr.fees td').each(function () {
		if ($(this).index() >= 2)
		{
			//enable only if zone is active
			tr = $(this).closest('tr');
			validate = $('tr.fees_all td:eq('+$(this).index()+')').hasClass('validated');
			if ($(tr).index() > 2 && $(tr).find('td:eq(1) input').attr('checked') && validate || !$(tr).hasClass('range_sup') || !$(tr).hasClass('range_inf'))
				$(this).find('div.input-group input:text').removeAttr('disabled');
			$(this).find('input:text, button').css('background-color', '').css('border-color', '');
			$(this).find('button').css('background-color', '').css('border-color', '').removeAttr('disabled');
			$(this).css('background-color', '');
		}
	});
}

function validateRange(index)
{
    $('#saveShippingBtn').removeClass('disabled');
	$('#kb-shipping-form-global-msg').hide();
    $('#kb-shipping-form-global-msg').html('');
	//reset error css
	$('tr.range_sup td input:text').closest('div.input-group').removeClass('has-error');
	$('tr.range_inf td input:text').closest('div.input-group').removeClass('has-error');

	var is_valid = true;
	range_sup = parseFloat($('tr.range_sup td:eq('+index+')').find('div.input-group input:text').val().trim());
	range_inf = parseFloat($('tr.range_inf td:eq('+index+')').find('div.input-group input:text').val().trim());

	if (isNaN(range_sup) || range_sup.length === 0)
	{
		$('tr.range_sup td:eq('+index+')').find('div.input-group input:text').closest('div.input-group').addClass('has-error');
		is_valid = false;
        displayError(invalid_range);
	}
	else if (is_valid && (isNaN(range_inf) || range_inf.length === 0))
	{
		$('tr.range_inf td:eq('+index+')').find('div.input-group input:text').closest('div.input-group').addClass('has-error');
		is_valid = false;
		displayError(invalid_range);
	}
	else if (is_valid && range_inf >= range_sup)
	{
		$('tr.range_sup td:eq('+index+')').find('div.input-group input:text').closest('div.input-group').addClass('has-error');
		$('tr.range_inf td:eq('+index+')').find('div.input-group input:text').closest('div.input-group').addClass('has-error');
		is_valid = false;
        displayError(invalid_range);
	}
	else if (is_valid && index > 2) //check range only if it's not the first range
	{
		$('tr.range_sup td').not('.range_type, .range_sign, tr.range_sup td:last').each(function ()
		{
			if ($('tr.fees_all td:eq('+index+')').hasClass('validated'))
			{
				is_valid = false;
				curent_index = $(this).index();

				current_sup = $(this).find('div.input-group input').val();
				current_inf = $('tr.range_inf td:eq('+curent_index+') input').val();

				if ($('tr.range_inf td:eq('+curent_index+1+') input').length)
					next_inf = $('tr.range_inf td:eq('+curent_index+1+') input').val();
				else
					next_inf = false;

				//check if range already exist
				//check if ranges is overlapping
				if ((range_sup != current_sup && range_inf != current_inf) && ((range_sup > current_sup || range_sup <= current_inf) && (range_inf < current_inf || range_inf >= current_sup)))
					is_valid = true;
			}

		});

		if (!is_valid)
		{
			$('tr.range_sup td:eq('+index+')').find('div.input-group input:text').closest('div.input-group').addClass('has-error');
			$('tr.range_inf td:eq('+index+')').find('div.input-group input:text').closest('div.input-group').addClass('has-error');
            displayError(range_is_overlapping);
		}
		else
			isOverlapping();
	}
	return is_valid;
}

function enableZone(index)
{
	$('tr.fees').each(function () {
		if ($(this).find('td:eq(1)').find('input[type=checkbox]:checked').length)
			$(this).find('td:eq('+index+')').find('div.input-group input').removeAttr('disabled');
	});
}

function disableZone(index)
{
	$('tr.fees').each(function () {
		$(this).find('td:eq('+index+')').find('div.input-group input').attr('disabled', 'disabled');
	});
}

function enableRange(index)
{
	$('tr.fees').each(function () {
		//only enable fees for enabled zones
		if ($(this).find('td').find('input:checkbox').attr('checked') == 'checked')
			enableZone(index);
	});
	$('tr.fees_all td:eq('+index+')').addClass('validated').removeClass('not_validated');

	//if ($('.zone input[type=checkbox]:checked').length)
		enableGlobalFees(index);
	bind_inputs();
}

function enableGlobalFees(index)
{
	$('span.fees_all').show();
	$('tr.fees_all td:eq('+index+')').find('div.input-group input').show().removeAttr('disabled');
	$('tr.fees_all td:eq('+index+')').find('div.input-group .currency_sign').show();
}

function disabledGlobalFees(index)
{
	$('span.fees_all').hide();
	$('tr.fees_all td:eq('+index+')').find('div.input-group input').hide().attr('disabled', 'disabled');
	$('tr.fees_all td:eq('+index+')').find('div.input-group .currency_sign').hide();
}


function disableRange(index)
{
	$('tr.fees').each(function () {
		//only enable fees for enabled zones
		if ($(this).find('td').find('input:checkbox').attr('checked') == 'checked')
			disableZone(index);
	});
	$('tr.fees_all td:eq('+index+')').find('div.input-group input').attr('disabled', 'disabled');
	$('tr.fees_all td:eq('+index+')').removeClass('validated').addClass('not_validated');
}

function checkAllFieldIsNumeric()
{
	$('#saveShippingBtn').removeClass('disabled');
	$('#zones_table td input[type=text]').each(function () {
		if (!$.isNumeric($(this).val()) && $(this).val() != '')
			$(this).closest('div.input-group').addClass('has-error');
	});
}

function isOverlapping()
{
	var is_valid = false;
    $('#saveShippingBtn').removeClass('disabled');
	$('tr.range_sup td').not('.range_type, .range_sign').each( function ()
	{
		index = $(this).index();
		current_inf = parseFloat($('.range_inf td:eq('+index+') input').val());
		current_sup = parseFloat($('.range_sup td:eq('+index+') input').val());

		$('tr.range_sup td').not('.range_type, .range_sign').each( function ()
		{
			testing_index = $(this).index();

			if (testing_index != index) //do not test himself
			{
				testing_inf = parseFloat($('.range_inf td:eq('+testing_index+') input').val());
				testing_sup = parseFloat($('.range_sup td:eq('+testing_index+') input').val());

				if ((current_inf >= testing_inf && current_inf < testing_sup) || (current_sup > testing_inf && current_sup < testing_sup))
				{
					$('tr.range_sup td:eq('+testing_index+') div.input-group, tr.range_inf td:eq('+testing_index+') div.input-group').addClass('has-error');
					displayError(range_is_overlapping);
					is_valid = true;
				}
			}
		});
	});
	return is_valid;
}

function displayError(errors)
{
	$('#saveShippingBtn').removeClass('disabled');
	$('#kb-shipping-form-global-msg').html(errors);
    $('#kb-shipping-form-global-msg').show();
	bind_inputs();
}

function checkAllZones(elt)
{
    if ($(elt).is(':checked'))
    {
        $('.input_zone').attr('checked', 'checked');
        $('.input_zone').parent().addClass('checked');
        $('.fees div.input-group input:text').each(function () {
            index = $(this).closest('td').index();
            enableGlobalFees(index);
            if ($('tr.fees_all td:eq(' + index + ')').hasClass('validated'))
            {
                $(this).removeAttr('disabled');
                $('.fees_all td:eq(' + index + ') div.input-group input:text').removeAttr('disabled');
            }
        });
    } else
    {
        $('.input_zone').removeAttr('checked');
        $('.input_zone').parent().removeClass('checked');
        $('.fees div.input-group input:text, .fees_all div.input-group input:text').attr('disabled', 'disabled');
    }

}

function rebuildTabindex()
{
	i = 1;
	$('#zones_table tr').each(function ()
	{
		j = i;
		$(this).find('td').each(function ()
		{
			j = zones_nbr + j;
			if ($(this).index() >= 2 && $(this).find('div.input-group input'))
				$(this).find('div.input-group input').attr('tabindex', j);
		});
		i++;
	});
}

function add_new_range()
{
	if (!$('tr.fees_all td:last').hasClass('validated'))
	{
		displayError(need_to_validate);
		return false;
	}

	last_sup_val = $('tr.range_sup td:last input').val();
	//add new rand sup input
	$('tr.range_sup td:last').after('<td class="range_data"><div class="kb-labeled-inpfield input-group"><span class="inplbl weight_unit" style="display: none;">'+kb_shipping_weight_unit+'</span><span class="inplbl price_unit" style="display: none;">'+kb_shipping_currency_unit+'</span><input class="kb-inpfield" name="range_sup[]" type="text" autocomplete="off" /></div></td>');
	//add new rand inf input
	$('tr.range_inf td:last').after('<td><div class="kb-labeled-inpfield input-group"><span class="inplbl weight_unit" style="display: none;">'+kb_shipping_weight_unit+'</span><span class="inplbl price_unit" style="display: none;">'+kb_shipping_currency_unit+'</span><input class="kb-inpfield" name="range_inf[]" type="text" value="'+last_sup_val+'" autocomplete="off" /></div></td>');
	$('tr.fees_all td:last').after('<td><div class="kb-labeled-inpfield input-group"><span class="inplbl" >'+kb_shipping_currency_unit+'</span><input class="kb-inpfield" type="text" /></div></td>');

	$('tr.fees').each(function () {
		$(this).find('td:last').after('<td><div class="kb-labeled-inpfield input-group"><span class="inplbl">'+kb_shipping_currency_unit+'</span><input class="kb-inpfield" disabled="disabled" name="fees['+$(this).data('zoneid')+'][]" type="text" /></div></td>');
	});
	$('tr.delete_range td:last').after('<td><button class="btn btn-default">'+labelDelete+'</button</td>');

	bind_inputs();
	rebuildTabindex();
	displayRangeType();
	resizeWizard();
	return false;
}

var string;
function displayRangeType()
{
	if ($('input[name="shipping_method"]:checked').val() == 1)
	{
		string = kb_shipping_range_weight;
		$('.weight_unit').show();
		$('.price_unit').hide();
	}
	else
	{
		string = kb_shipping_range_price;
		$('.price_unit').show();
		$('.weight_unit').hide();
	}
	is_freeClick($('select[name="is_free"]'));
	$('.range_type').html(string);
}

function resizeWizard()
{
	
}

function validateForm()
{
    $('#kb-shipping-form-global-msg').hide();
    $('#kb-shipping-form-global-msg').html('');
	var is_ok = true;
        var click = 1;
	if (parseInt($('#is_free option:selected').val()) == 0)
    {
        is_ok = false;
        $('.input_zone').each(function () {
            if ($(this).prop('checked'))
                is_ok = true;
        });

        if (!is_ok)
        {
            displayError(select_at_least_one_zone);
            return;
        }
    }
    if ($.trim($('#kb_mp_shipping_name').val()) == '') {
        is_ok = false;
        displayError('Shipping name can not be blank');
        return;
    }
    if ($.trim($('#kb_mp_shipping_delay').val()) == '') {
        is_ok = false;
        displayError('Delay Message can not be blank');
        return;
    }

    if (parseInt($('#is_free option:selected').val()) == 0 && !validateRange(2))
        is_ok = false;

	if (is_ok && isOverlapping())
		is_ok = false;

	if (is_ok)
	{
            if (click == '1') {
		submitShippingForm();
                $('#saveShippingBtn').attr('disabled',true);
                click++;
            } else {
                $('#saveShippingBtn').attr('disabled',true);
            }
	}
}

function submitShippingForm()
{
	$.ajax({
		type:"POST",
		url : kb_current_request + ((kb_current_request.indexOf('?') < 0) ? '?' : '&')+'rand=' + new Date().getTime() + '&id_carrier='+kb_id_carrier,
		async: false,
		dataType: 'json',
		data : $('#kb-shipping-form').serialize() + '&method=finish&ajax=true',
		success : function(data) {
			if (data['has_error'] != undefined && data['has_error'])
			{
                var html = '<ul>';
                for (var i in data['errors']){
                    html += '<li>'+data['errors'][i]+'</li>';
                }
                html += '</ul>';
                $('#kb-shipping-form-global-msg').html(html);
                $('#kb-shipping-form-global-msg').show();
                
				$('#saveShippingBtn').removeClass('disabled');
                bind_inputs();
				resizeWizard();
			}
			else
				window.location.href = kb_current_request;
            
            $('#saveShippingBtn').removeClass('disabled');
            bind_inputs();
            resizeWizard();
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) {
			jAlert(kb_ajax_request_fail_err);
		}
	});
}


function uploadShippingLogo(e) {
    var img_data = new FormData();
    $.each(img_file, function(key, value){img_data.append('file['+key+']', value);});

    img_data.append('ajax', true);
    img_data.append('method', 'uploadLogo');

    $.ajax({
        type: 'POST',
        headers: { "cache-control": "no-cache"},
        processData: false,
        contentType: false,
        url: kb_current_request + ((kb_current_request.indexOf('?') < 0) ? '?' : '&')+'rand=' + new Date().getTime(),
        async: true,
        cache: false,
        dataType : "json",
        data: img_data,
        beforeSend: function() {
        },
        success: function(data) {console.log(data);
            img_file = null;
            if(data['success'] != undefined){
                jQuery('#shipping_logo_update').val(1);
                jQuery('#shipping_logo_placeholder').attr('src', e.target.result);    
                jQuery('#kb-shipping-form #logo').val(data['success']);
                $('#kb_shipping_logo_remove').show();
            }else{
                if ($('#is_kb_shipping_logo_updated').val() == '0') {
                    $('#kb_shipping_logo_remove').hide();
                }
                jAlert(data['error']);
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            jAlert(kb_ajax_request_fail_err);
        }
    });
}

function removeCarrierLogo(default_logo)
{
    if (confirm('Are you sure you want to delete the logo?'))
    {
        var default_shipping_logo = $('#kb_shipping_default_logo').val();
        
        $('#carrier_logo_img').attr('src', default_shipping_logo);
        $('#shipping_logo_placeholder').attr('src', default_shipping_logo);
        $('#kb-shipping-form #logo').val('null');
        console.log($('#update_shipping_logo').val());
        if ($('#is_kb_shipping_logo_updated').val() == '1') {
            var img = kb_shipping_logo_path;
            $.ajax({
                type: 'POST',
                url:kb_shipping_url,
                data:{kbshipping_logo:true, shipping_logo:img},
                success: function(res){
                    if (res == 'removed') {
                        $('#kb_shipping_logo_remove').hide();
                    }
                },
            });
        } else {
            $('#kb_shipping_logo_remove').hide();
        }
        $('#update_shipping_logo').val('0');
    }
} 

