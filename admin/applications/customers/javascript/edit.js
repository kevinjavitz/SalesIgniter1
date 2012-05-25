
$(document).ready(function (){
	$('#customers_dob').datepicker({
			dateFormat: 'mm/dd/yy',
			yearRange:'1920:2010',
			changeYear:true
		});

	$('#orderHistoryTab').find('.gridBody > .gridBodyRow').click(function (){
		if ($(this).hasClass('state-active')) return;

		$('#orderHistoryTab').find('.gridButtonBar button').button('enable');
	});

	$('#orderHistoryTab').find('.gridButtonBar .detailsButton').click(function (){
		var orderId = $('#orderHistoryTab').find('.gridBodyRow.state-active').attr('data-order_id');
		js_redirect(js_app_link('app=orders&appPage=details&oID=' + orderId));
	});

	$('#orderHistoryTab').find('.gridButtonBar .invoiceButton').click(function (){
		var orderId = $('#orderHistoryTab').find('.gridBodyRow.state-active').attr('data-order_id');
		js_redirect(js_app_link('app=orders&appPage=invoice&oID=' + orderId));
	});

	$('#orderHistoryTab').find('.gridButtonBar .packingSlipButton').click(function (){
		var orderId = $('#orderHistoryTab').find('.gridBodyRow.state-active').attr('data-customer_id');
		js_redirect(js_app_link('app=orders&appPage=packingslip&oID=' + orderId));
	});
$('select[name="activate"]').live('change',function (){
        fnClicked();
    });
	$('input[name="make_member"]').live('change',function (){
		if($('input[name="make_member"]').is(':checked') == true) {
			$('select[name="activate"]').removeAttr('disabled');
			$('select[name="activate"]').val('Y');
		} else {
			$('select[name="activate"]').attr('disabled','disabled');
			$('select[name="activate"]').val('N');

		}
		fnClicked();
	});
	$('select[name="payment_method"]').trigger('change');
	if($('select[name="activate"]').val() == 'N'){
		$('select[name="activate"]').trigger('change');
	}
	$('select[name=country]').live('change', function (){

		var stateType = 'state';
		var $stateColumn = $('#'+stateType);
		if($stateColumn.size() > 0){
			showAjaxLoader($stateColumn, 'large');
			$.ajax({
				url: js_app_link('app=customers&appPage=edit&action=getCountryZones'),
				cache: false,
				dataType: 'html',
				data: 'cID=' + $(this).val()+'&state_type='+stateType+'&state='+$stateColumn.val(),
				success: function (data){
					removeAjaxLoader($stateColumn);
					$('#'+stateType).replaceWith(data);
				}
			});
		}
	});
	$('select[name=country]').trigger('change');
}); 