$(document).ready(function (){
    $('#start_date').datepicker({
           dateFormat: 'yy-mm-dd'
       });
       $('#end_date').datepicker({
           dateFormat: 'yy-mm-dd'
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
	$('#selectAllOrders').change(function(){
		$('.selectedOrder').each(function(){
			if ($(this).is(':checked')){
				$(this).attr('checked', false);
			}else{
				$(this).attr('checked', true);
			}
		});
	});
	
	$('.gridBody > .gridBodyRow').click(function (){
		if ($(this).hasClass('state-active')) return;

		$('.gridButtonBar').find('button').button('enable');
	});
	
	$('.gridButtonBar').find('.detailsButton').click(function (){
		var orderId = $('.gridBodyRow.state-active').attr('data-order_id');
		js_redirect(js_app_link('app=orders&appPage=details&oID=' + orderId));
	});

	$('.gridButtonBar').find('.cancelButton').click(function (){
		var orderId = $('.gridBodyRow.state-active').attr('data-order_id');
		$self = $(this);
		showAjaxLoader($self, 'x-large');
		$.ajax({
			cache: false,
			dataType: 'json',
			url: js_app_link('app=orders&appPage=default&action=cancelOrder&oID=' + orderId),
			success: function (data) {
				removeAjaxLoader($self);
				js_redirect(js_app_link('app=orders&appPage=default'));
			}
		});
	});
	
	$('.gridButtonBar').find('.invoiceButton').click(function (){
		var orderId = $('.gridBodyRow.state-active').attr('data-order_id');
		js_redirect(js_app_link('app=orders&appPage=invoice&oID=' + orderId));
	});
	
	$('.gridButtonBar').find('.packingSlipButton').click(function (){
		var orderId = $('.gridBodyRow.state-active').attr('data-order_id');
		js_redirect(js_app_link('app=orders&appPage=packingslip&oID=' + orderId));
	});
	
	$('.gridButtonBar').find('.deleteButton').live('click', function (){
		var orderId = $('.gridBodyRow.state-active').attr('data-order_id');
		$self = $(this);
		showAjaxLoader($self, 'x-large');
		$.ajax({
			cache: false,
			dataType: 'json',
			url: js_app_link('app=orders&appPage=default&action=getDeleteOptions&oID=' + orderId),
			success: function (data) {
				removeAjaxLoader($self);
				if(data.success == true){
					$('<div></div>').html(data.html).attr('title', 'Delete').dialog({
						resizable: false,
						allowClose: false,
						modal: true,
						buttons: {
							'Confirm': function() {
								$.ajax({
									cache: false,
									dataType: 'json',
									type:'post',
									data:'deleteRestockNoReservation='+$('#deleteRestockNoReservation').val()+'&deleteReservationRestock='+$('#deleteReservationRestock').val(),
									url: js_app_link('app=orders&appPage=default&action=deleteConfirm&oID=' + orderId),
									success: function (data) {
										js_redirect(js_app_link('app=orders&appPage=default'));
									}
								});
							},
							'Cancel': function() {
								$(this).dialog('close').remove();
							}
						}
					});
				}

			}
		});
	});

	/* Get Into Order Creator Extension */
	$('.gridButtonBar').find('.editButton').click(function (){
		var orderId = $('.gridBodyRow.state-active').attr('data-order_id');
		js_redirect(js_app_link('appExt=orderCreator&app=default&appPage=new&oID=' + orderId));
	});
});