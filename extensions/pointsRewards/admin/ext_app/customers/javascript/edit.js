$(document).ready(function (){
	$('.pageStackContainer').hide();
	$('#btnAddPoints').click(function (e){
		e.preventDefault();

		var $this = $(this);
		showAjaxLoader($this, 'small');
		if($('input[name="points"]').val() == ''){
			alert('Number of points should be at least 1');
			removeAjaxLoader($this);
			return;
		}
		$.ajax({
			cache: false,
			dataType: 'json',
			url: js_app_link('appExt=pointsRewards&app=update_points&appPage=default&action=save&rType=ajax'),
			data: 'purchaseType=' + $('select[name="purchaseType"]').val() + '&points=' + $('input[name="points"]').val() + '&actionAddRemove=add&customers_id=' + $('#managePointsTable').attr('customers_id'),
			type: 'post',
			success: function (data){
				$('.pageStackContainer').html(data.msgStack).show();
                		$('#pointsHistoryTable').parent().html(data.history);
				removeAjaxLoader($this);
			}
		});
	});
	$('#btnDeductPoints').click(function (e){
		e.preventDefault();

		var $this = $(this);
		showAjaxLoader($this, 'small');
		if($('input[name="points"]').val() == ''){
			alert('Number of points should be at least 1');
			removeAjaxLoader($this);
			return;
		}
		$.ajax({
			cache: false,
			dataType: 'json',
			url: js_app_link('appExt=pointsRewards&app=update_points&appPage=default&action=save&rType=ajax'),
			data: 'purchaseType=' + $('select[name="purchaseType"]').val() + '&points=' + $('input[name="points"]').val() + '&actionAddRemove=deduct&customers_id=' + $('#managePointsTable').attr('customers_id'),
			type: 'post',
			success: function (data){
				$('.pageStackContainer').html(data.msgStack).show();
                		$('#pointsHistoryTable').parent().html(data.history);
				removeAjaxLoader($this);
			}
		});
	});
});