
$(document).ready(function (){
	$('.inCart').live('click', function() {
			$(this).parent().parent().append('<input type="hidden" name="add_reservation_product">');
			$('.selected_period').removeAttr('disabled');
			$(this).closest('form').submit();
			return false;
	});

});