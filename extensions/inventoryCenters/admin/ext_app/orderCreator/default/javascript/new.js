$(document).ready(function (){
	$('select[name=customers_inventory_center]').live('change', function (){
		var $self = $(this);
		showAjaxLoader($self, 'small');
		$.ajax({
			cache: false,
			url: js_app_link('rType=ajax&appExt=orderCreator&app=default&appPage=new&action=setOrdersInventory&id=' + $(this).val()),
			dataType: 'json',
			success: function (){
				$('.purchaseType').trigger('updateInfo');
				removeAjaxLoader($self);
			}
		});
	});
});