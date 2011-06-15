	$(document).ready(function (){
		$('.starRating').stars({
			disabled: true
		});
		
		$('#reviewRating').stars('option', 'disabled', false);
	});
