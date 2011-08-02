
	$("input[name='redeem_points[]']").live('click', function () {
		
            $('.orderTotalsList').each(function (){
                showAjaxLoader($(this), 'large');
            });
            var linkParams = js_get_all_get_params(['app', 'appPage', 'action']);
		//alert($(this).attr('checked'));
       $.ajax({
            url: js_app_link(linkParams + 'rType=ajax&app=checkout&appPage=default&action=redeemPoints'),
            data: 'points=' + $(this).val() + '&purchaseType=' + $(this).attr('purchase_type') + '&apply=' + ($(this).attr('checked') == true ? 'true' : 'false'),
            type: 'post',
            dataType: 'json',
            success: function (data) { //alert(data.errorMsg);
               $('.orderTotalsList').each(function () {
                    removeAjaxLoader($(this), 'large', 'append');
                });
	            if(data.success == false){
		            $("input[name='redeem_points[]']").removeAttr('checked');
	            }

                $('.orderTotalsList').html(data.orderTotalRows);
            }
        });
    });
