$(document).ready(function (){
	$('#checkAddress').click(function (e){
		e.preventDefault();

		var $this = $(this);
		showAjaxLoader($this, 'small');

		$.ajax({
			cache: false,
			dataType: 'json',
			url: js_app_link('appExt=inventoryCenters&app=center_address_check&appPage=default&rType=ajax&action=checkAddress'),
			data: $('*', $('#addressEntry')).serialize(),
			type: 'post',
			success: function (data){
				if (data.inService == true){
					js_redirect(data.redirectUrl);
				}else{
					$('.pageStackContainer').html(data.msgStack).show();
					$('input[name="serviceArea"]').each(function (){
						this.checked = false;
					});
				}
				removeAjaxLoader($this);
			}
		});
	});

	$('#setLocation').click(function (e){
		e.preventDefault();

		var $this = $(this);
		showAjaxLoader($this, 'small');

		$.ajax({
			cache: false,
			dataType: 'json',
			url: js_app_link('appExt=inventoryCenters&app=center_address_check&appPage=default&rType=ajax&action=setServiceArea'),
			data: 'cID=' + $('input[name="serviceArea"]:checked').val(),
			type: 'post',
			success: function (data){
				if (data.inService == true){
					js_redirect(data.redirectUrl);
				}else{
					$('.pageStackContainer').html(data.msgStack).show();
				}
				removeAjaxLoader($this);
			}
		});
	});

	$('#countryDrop').change(function (){
		var $stateColumn = $('#stateCol');
		showAjaxLoader($stateColumn, 'icon', 'append');

		$.ajax({
			cache: true,
			url: js_app_link('appExt=inventoryCenters&app=center_address_check&appPage=default&rType=ajax&action=getCountryZones'),
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