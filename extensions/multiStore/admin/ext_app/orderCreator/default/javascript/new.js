$(document).ready(function (){
	$('select[name=customers_store]').live('change', function (){
		var $self = $(this);
		showAjaxLoader($self, 'small');
		$.ajax({
			cache: false,
			url: js_app_link('rType=ajax&appExt=orderCreator&app=default&appPage=new&action=setOrdersStore&id=' + $(this).val()),
			dataType: 'json',
			success: function (){
				removeAjaxLoader($self);
				$('.purchaseType').trigger('updateInfo');
			}
		});
	});
	$('select[name=customers_store]').trigger('change');
});