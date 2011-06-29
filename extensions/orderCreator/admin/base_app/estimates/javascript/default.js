$(document).ready(function (){
	
	$('.gridBody > .gridBodyRow').click(function (){
		if ($(this).hasClass('state-active')) return;

		$('.gridButtonBar').find('button').button('enable');
	});
	
	$('.gridButtonBar').find('.detailsButton').click(function (){
		var orderId = $('.gridBodyRow.state-active').attr('data-order_id');
		js_redirect(js_app_link('app=orders&appPage=details&oID=' + orderId));
	});
	
	$('.gridButtonBar').find('.deleteButton').live('click', function (){
		var orderId = $('.gridBodyRow.state-active').attr('data-order_id');
		$self = $(this);
		showAjaxLoader($self, 'x-large');

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
									url: js_app_link('app=orders&appPage=default&action=deleteConfirmEstimates&oID=' + orderId),
									success: function (data) {
										js_redirect(js_app_link('app=orders&appPage=estimates'));
									}
								});
							},
							'Cancel': function() {
								$(this).dialog('close').remove();
							}
						}
					});

	});

	/* Get Into Order Creator Extension */
	$('.gridButtonBar').find('.editButton').click(function (){
		var orderId = $('.gridBodyRow.state-active').attr('data-order_id');
		js_redirect(js_app_link('appExt=orderCreator&app=default&appPage=new&isEstimate=1&oID=' + orderId));
	});
});