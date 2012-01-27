$(document).ready(function (){
	$('.updatePickupButton').click(function(){
		$.ajax({
			cache: false,
			url: js_app_link('app=rentals&appPage=queue&action=updatePickupRequest'),
			data: 'pickupRequest='+$('#pickupRequest option:selected').val(),
			type: 'post',
			success: function (data){
				js_redirect(js_app_link('app=rentals&appPage=queue'));
			}
		});
		return false;
	});
});