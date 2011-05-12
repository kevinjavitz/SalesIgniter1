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
});