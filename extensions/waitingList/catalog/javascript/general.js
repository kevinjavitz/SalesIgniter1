$(document).ready(function (){
	$('.waitingListButton').live('click', function (){
		var $self = $(this);
		showAjaxLoader($self, 'small');
		
		$.ajax({
			cache: false,
			dataType: 'json',
			url: js_app_link('appExt=waitingList&app=notify&appPage=default&action=ajaxSave&pID=' + $self.data('product_id') + '&purchaseType=' + $self.data('purchase_type')),
			success: function (data){
				removeAjaxLoader($self);
				alertWindow(data.message);
			}
		});
		
		return false;
	});
});