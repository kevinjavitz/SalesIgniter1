$(document).ready(function (){
	$('#limit').change(function(){
		$('#search').submit();
	});
	$('.gridBody > .gridBodyRow').click(function (){
		if ($(this).hasClass('state-active')) return;

		$('.gridButtonBar').find('button').button('enable');
		if ($(this).attr('data-has_orders') == 'false'){
			$('.gridButtonBar').find('.ordersButton').button('disable');
		}
	});
	$('#csvFieldsTable').hide();
	$('#showFields').click(function(){
		if ($('#csvFieldsTable').is(':visible')){
			$('#csvFieldsTable').hide();
		}else{
			$('#csvFieldsTable').show();
		}
		return false;
	});
	$('#selectAllCustomers').change(function(){
		$('.selectedCustomer').each(function(){
			if ($(this).is(':checked')){
				$(this).attr('checked', false);
			}else{
				$(this).attr('checked', true);
			}
		});
	});
	
	$('.gridButtonBar').find('.ordersButton').click(function (){
		var customerId = $('.gridBodyRow.state-active').attr('data-customer_id');
		js_redirect(js_app_link('app=orders&appPage=default&cID=' + customerId));
	});
	
	$('.gridButtonBar').find('.emailButton').click(function (){
		var customerEmail = $('.gridBodyRow.state-active').attr('data-customer_email');
		js_redirect(js_app_link('app=mail&appPage=default&customer=' + customerEmail));
	});
	
	$('.gridButtonBar').find('.editButton').click(function (){
		var customerId = $('.gridBodyRow.state-active').attr('data-customer_id');
		js_redirect(js_app_link('app=customers&appPage=edit&cID=' + customerId));
	});
	
	$('.gridButtonBar').find('.deleteButton').click(function (){
		var customerId = $('.gridBodyRow.state-active').attr('data-customer_id');
		
		confirmDialog({
			confirmUrl: js_app_link('app=customers&appPage=default&action=deleteConfirm&cID=' + customerId),
			title: 'Confirm Delete',
			content: 'Are you sure you want to delete this customer?',
			success: function (){
				js_redirect(js_app_link('app=customers&appPage=default'));
			}
		});
	});
});