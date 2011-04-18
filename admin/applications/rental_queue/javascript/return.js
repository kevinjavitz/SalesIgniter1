function runAction($thisRow, actionMsg, callback){
	$thisRow.fadeTo('fast', .3);

	$('<div>').attr('id', 'overlay').css({
		position: 'absolute',
		display: 'none',
		top: $thisRow.offset().top,
		left: $thisRow.offset().left,
		width: $thisRow.width(),
		height: $thisRow.height(),
		background: '#000000',
		color: '#FFFFFF',
		textAlign: 'center'
	}).html(actionMsg).show().appendTo(document.body).fadeTo('fast', .6, callback);
}

$(document).ready(function (){
	$('select[name="inventory_center"]').change(function (){
		if ($(this).val() != $(this).attr('defaultValue')){
			var answer = confirm('Are you sure you want to change this barcodes inventory center to:' + "\n\n" + $('option:selected', this).html());
			if (answer){
				return true;
			}else{
				$(this).val($(this).attr('defaultValue'));
				return false;
			}
		}
	});

	$('.returnOk').click(function (){
		var $thisRow = $(this).parent().parent();
		runAction($thisRow, 'Returning OK Item, Please Wait', function (){
			$.ajax({
				cache: false,
				url: js_app_link('app=rental_queue&appPage=return&action=returnRental&status=ok'),
				dataType: 'json',
				data: $('#queue_id, #comments, #inventory_center', $thisRow).serialize(),
				type: 'post',
				success: function (data){
					if (typeof data.errorMsg == 'undefined'){
						$thisRow.remove();
					}else{
						alert(data.errorMsg);
						$thisRow.fadeTo('fast', 1);
					}
					$('#overlay').remove();
				}
			});
		});
	});

	$('.returnBroken').click(function (){
		var $thisRow = $(this).parent().parent();
		runAction($thisRow, 'Returning Broken Item, Please Wait', function (){
			$.ajax({
				cache: false,
				url: js_app_link('app=rental_queue&appPage=return&action=returnRental&status=broken'),
				dataType: 'json',
				data: $('#queue_id, #comments, #inventory_center', $thisRow).serialize(),
				type: 'post',
				success: function (data){
					if (typeof data.errorMsg == 'undefined'){
						$thisRow.remove();
					}else{
						alert(data.errorMsg);
						$thisRow.fadeTo('fast', 1);
					}
					$('#overlay').remove();
				}
			});
		});
	});

	$('.appendComments').click(function (){
		var $thisRow = $(this).parent().parent();
		runAction($thisRow, 'Appending Comments, Please Wait', function (){
			$.ajax({
				cache: false,
				url: js_app_link('app=rental_queue&appPage=return&action=append_comments'),
				dataType: 'json',
				data: $('#queue_id, #comments', $thisRow).serialize(),
				type: 'post',
				success: function (data){
					if (typeof data.errorMsg != 'undefined'){
						alert(data.errorMsg);
						$thisRow.fadeTo('fast', 1);
					}
					$('#overlay').remove();
				}
			});
		});
	});
});