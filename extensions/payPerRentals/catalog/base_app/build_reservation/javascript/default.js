
$(document).ready(function (){
	$('.inCart').live('click', function() {
			if($(this).hasClass('inQueue')){
				$(this).parent().parent().append('<input type="hidden" name="add_queue_reservation_product">');
			}else{
			$(this).parent().parent().append('<input type="hidden" name="add_reservation_product">');
			}
			$('.selected_period').removeAttr('disabled');
			$(this).closest('form').submit();
			return false;
	});

});