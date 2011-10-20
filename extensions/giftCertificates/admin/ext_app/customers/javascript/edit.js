$(document).ready(function (){
	$('.pageStackContainer').hide();
	$('#btnAddGCBalance').click(function (e){
		e.preventDefault();

		var $this = $(this);
		showAjaxLoader($this, 'small');
		if($('input[name="gcBalance"]').val() == ''){
			alert('Please enter valid number for GC balance');
			removeAjaxLoader($this);
			return;
		}
		$.ajax({
			cache: false,
			dataType: 'json',
			url: js_app_link('appExt=giftCertificates&app=update_gc_balance&appPage=default&action=save&rType=ajax'),
			data: 'purchaseType=' + $('select[name="purchaseType"]').val() + '&gcBalance=' + $('input[name="gcBalance"]').val() + '&actionAddRemove=add&customers_id=' + $('#manageGCBalanceTable').attr('customers_id'),
			type: 'post',
			success: function (data){
				$('.pageStackContainer').html(data.msgStack).show();
				removeAjaxLoader($this);
			}
		});
	});
	$('#btnDeductGCBalance').click(function (e){
		e.preventDefault();

		var $this = $(this);
		showAjaxLoader($this, 'small');
		if($('input[name="gcBalance"]').val() == ''){
			alert('Please enter valid number for GC balance');
			removeAjaxLoader($this);
			return;
		}
		$.ajax({
			cache: false,
			dataType: 'json',
			url: js_app_link('appExt=giftCertificates&app=update_gc_balance&appPage=default&action=save&rType=ajax'),
			data: 'purchaseType=' + $('select[name="purchaseType"]').val() + '&gcBalance=' + $('input[name="gcBalance"]').val() + '&actionAddRemove=deduct&customers_id=' + $('#manageGCBalanceTable').attr('customers_id'),
			type: 'post',
			success: function (data){
				$('.pageStackContainer').html(data.msgStack).show();
				removeAjaxLoader($this);
			}
		});
	});
});