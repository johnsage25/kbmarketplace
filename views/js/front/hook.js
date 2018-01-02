/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 * We offer the best and most useful modules PrestaShop and modifications for your online store.
 *
 * @author    knowband.com <support@knowband.com>
 * @copyright 2017 Knowband
 * @license   see file: LICENSE.txt
 * @category  PrestaShop Module
 *
 */

$(document).ready(function(){
    if ($('#open_kb_seller_agreement_modal').length) {
        $('#open_kb_seller_agreement_modal').on('click', function(){
            $('#'+$(this).attr('data-modal')).modal('show');
        });  
    }
    if ($('#open_kb_seller_agreement_modal_footer').length) {
        $('#open_kb_seller_agreement_modal_footer').on('click', function(){
            $('#'+$(this).attr('data-modal')).modal('show');
        });  
    }
    
    if ($('#seller_new_review_rating_block').length) {
        setTimeout(function(){displayRatingStartInput('seller_new_review_rating_block', 'review_rating')}, 2000);
    }
    
    $('#kbmp_registered_as_seller').on('change', function(){
        if ($(this).is(':checked')) {
            $('#kbmp_registered_as_seller_btn').attr('disabled', false);
        } else {
            $('#kbmp_registered_as_seller_btn').attr('disabled', true);
        }
    });
    $('#kbmp_registered_as_seller_footer').on('change', function(){
        if ($(this).is(':checked')) {
            $('#kbmp_registered_as_seller_btn_footer').attr('disabled', false);
        } else {
            $('#kbmp_registered_as_seller_btn_footer').attr('disabled', true);
        }
    });
    
//    $.xhrPool = [];
//    $.xhrPool.abortAll = function() {
//        $(this).each(function(idx, jqXHR) {
//            jqXHR.abort();
//        });
//        $.xhrPool = [];
//    };
//
//    $.ajaxSetup({
//        beforeSend: function(jqXHR) {
//            $.xhrPool.push(jqXHR);
//        },
//        complete: function(jqXHR) {
//            var index = $.xhrPool.indexOf(jqXHR);
//            if (index > -1) {
//                $.xhrPool.splice(index, 1);
//            }
//        }
//    });
//$('#promo-code form').on('submit',function(e){
//    e.preventDefault;
//});
   
    $('#promo-code button').on('click', function (e) {
        var coupon = $('.promo-input').val();
        var cart_url = $('#cart_rule_url').val();
        var allow_free_shipping = $('#allow_free_shipping').val();
        if (allow_free_shipping != '1') {
            $('#promo-code input[name=addDiscount]').val('');//1
            $('#promo-code input[name=ajax]').val('');//1
            $('#promo-code input[name=action]').val('');//update
            $('#promo-code form').attr('data-link-action', '');//add-voucher
            if ((coupon != '') && (allow_free_shipping != '1')) {
                var xhr = $.ajax({
                    data: {searchcoupon: true, coupon: coupon},
                    url: cart_url,
                    success: function (res) {
                        if (res == 'free') {
                            $('.js-error').show().find('.js-error-text').text("This voucher can not applied as allow Free Shipping voucher is disabled.");
                        } else if (res == 'paid') {
                            $('#promo-code input[name=addDiscount]').val('1');//1
                            $('#promo-code input[name=ajax]').val('1');//1
                            $('#promo-code input[name=action]').val('update');//update
                            $('#promo-code form').attr('data-link-action', 'add-voucher');//add-voucher
                            $('#promo-code form').submit();
                        } else if (res == 'no') {
                            $('.js-error').show().find('.js-error-text').text("This voucher does not exists.");
                        }

                    },
                });
            } else if (coupon == '') {
                $('.js-error').show().find('.js-error-text').text("You must enter a voucher code.");
            }
            return false;
        }
    });
    
});

//$(document).ajaxComplete(function( event, xhr, settings ) {
//     if (typeof (settings.data) != "undefined") {
//        var data = settings.data.split('&');
////        console.log(settings);
//        if (data[0] == "action=add-voucher") {
//            var coupon = $('.promo-input').val();
//            var cart_url = $('#cart_rule_url').val();
//            var allow_free_shipping = $('#allow_free_shipping').val();
//            if ((coupon != '') && (allow_free_shipping != '1')) {
//                var xhr= $.ajax({
//                    data: {searchcoupon: true,coupon:coupon},
//                    url:cart_url ,
//                    async:false,
//                    success: function (res) {
//                        if (res == 'free') {
//                            return false;
//    //                        showSuccessMessage('Module is successfully added');
//                            location.reload(true);
//                        } else {
//    //                        showErrorMessage('Error in adding the module');
//                        }
//    
//                    },
//                });
//
//            }
//        }
//        }
//
//    return false;
//});

//$.get("cart", function(data){
//    console.log(data);
//});




function openSellerReviewPopup(pop_id, id_seller)
{
    if ($('#'+pop_id).length) {
        $('#'+pop_id).find('input[type="text"]').val('');
        $('#'+pop_id).find('textarea').val('');
        if (id_seller != false) {
            $('#'+pop_id).find('input[name=id_seller]').val(parseInt(id_seller));
        }
        $('#'+pop_id).modal('show');
    }
}


