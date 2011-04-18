$(document).ready(function (){
	$('#inCart').hide();
	$('#checkAddress').click(function (e){
		e.preventDefault();

		var $this = $(this);
		showAjaxLoader($this, 'small');

		$.ajax({
			cache: false,
			dataType: 'json',
			url: js_app_link('appExt=payPerRentals&app=address_check&appPage=default&rType=ajax&action=checkAddress'),
			data: $('*', $('#addressEntry')).serialize(),
			type: 'post',
			success: function (data){

				$('.pageStackContainer').html(data.msgStack).show();
				 //transform check ajax buttonm to continue button.. on the checkout page add all the data to the form
				$('#checkAddress').hide();
				$('#inCart').show();
				removeAjaxLoader($this);
			}
		});
	});

	$('#countryDrop').change(function (){
		var $stateColumn = $('#stateCol');
		showAjaxLoader($stateColumn, 'icon', 'append');

		$.ajax({
			cache: true,
			url: js_app_link('appExt=payPerRentals&app=address_check&appPage=default&rType=ajax&action=getCountryZones'),
			data: 'cID=' + $(this).val(),
			dataType: 'html',
			success: function (data){
				removeAjaxLoader($stateColumn);
				$('#stateCol').html(data);
			}
		})
	});
	$('#countryDrop').val('223').trigger('change');
});