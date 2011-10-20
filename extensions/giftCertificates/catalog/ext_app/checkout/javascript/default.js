$(document).ready(function (){
    attachGiftCertificates();
});

function attachGiftCertificates(){
    $('.giftCertificates').die().live('click', function () {
        $('.orderTotalsList').each(function (){
            showAjaxLoader($(this), 'large');
        });
        var linkParams = js_get_all_get_params(['app', 'appPage', 'action']);
        $.ajax({
            url: js_app_link(linkParams + 'rType=ajax&app=checkout&appPage=default&action=setGiftCertificate'),
            cache: false,
            dataType: 'json',
            type: 'post',
            data: 'gcID=' + $(this).val(),
            success: function (data) {
                $('.orderTotalsList').each(function () {
                    removeAjaxLoader($(this), 'large', 'append');
                });
                $('.orderTotalsList').html(data.orderTotalRows);
            }
        });

    });
    $('#continueButton').click(function (){
        if ($('#currentPage').val() == 'payment_shipping'){
            $("#gcRedeem").button();
        }
    });
    $("input[name='redeem_gift_certificate_balance[]']").die().live('click', function () {

        $('.orderTotalsList').each(function (){
            showAjaxLoader($(this), 'large');
        });
        var linkParams = js_get_all_get_params(['app', 'appPage', 'action']);
        //alert($(this).attr('checked'));
        $.ajax({
            url: js_app_link(linkParams + 'rType=ajax&app=checkout&appPage=default&action=redeemGiftCertificate'),
            data: 'gvBalance=' + $(this).val() + '&purchaseType=' + $(this).attr('purchase_type') + '&apply=' + ($(this).attr('checked') == true ? 'true' : 'false'),
            type: 'post',
            dataType: 'json',
            success: function (data) { //alert(data.errorMsg);
                $('.orderTotalsList').each(function () {
                    removeAjaxLoader($(this), 'large', 'append');
                });
                if(data.success == false){
                    $("input[name='redeem_gift_certificate_balance[]']").removeAttr('checked');
                }

                $('.orderTotalsList').html(data.orderTotalRows);
            }
        });
    });
    $("#gcRedeem").die().live('click', function () {

        $('.orderTotalsList').each(function (){
            showAjaxLoader($(this), 'large');
        });
        var linkParams = js_get_all_get_params(['app', 'appPage', 'action']);
        //alert($(this).attr('checked'));
        $.ajax({
            url: js_app_link(linkParams + 'rType=ajax&app=checkout&appPage=default&action=redeemGiftCertificateCode'),
            data: 'gvCode=' + $("input[name='redeem_gift_certificate_code']").val(),
            type: 'post',
            dataType: 'json',
            success: function (data) { //alert(data.errorMsg);
                $('.orderTotalsList').each(function () {
                    removeAjaxLoader($(this), 'large', 'append');
                });
                alert(data.message);
                $('.orderTotalsList').html(data.orderTotalRows);
                $('#giftCertificatesTable').html(data.giftCertificatesTable);
                $("#gcRedeem").button();
                attachGiftCertificates();
            }
        });
    });
}

