
$(document).ready(function (){
	$('.inCart').live('click', function() {
			$(this).parent().parent().append('<input type="hidden" name="add_reservation_product">');
			$('.selected_period').removeAttr('disabled');
			$(this).closest('form').submit();
			return false;
	});

	$('#checkAddress').click(function (e){
		e.preventDefault();
		var $this = $(this);
		showAjaxLoader($this, 'small');

		$.ajax({
			cache: false,
			dataType: 'json',
			url: js_app_link('appExt=payPerRentals&app=build_reservation&appPage=default&rType=ajax&action=checkAddress'),
			data: $('*', $('#googleAddress')).serialize(),
			type: 'post',
			success: function (data){
				removeAjaxLoader($this);
				if(data.success == true){
					$('#checkAddress').hide();
					$('#googleAddress').hide();
					$('#changeAddress').show();
					$('.dateRow').show();
					var isHidden = false;
					$('.shipmethod').each(function(){
						var hidemethod = true;
						for(i=0;i<data.methods.length;i++){
							if($(this).hasClass('row_'+data.methods[i]) == true){
								hidemethod = false;
								break;
							}
						}
						if(hidemethod == true){
							$(this).find('input').removeAttr('checked');
							isHidden = true;
							$(this).hide();
						}else{
							$(this).show();
						}
					});

					$('.shipmethod').each(function(){
						if(isHidden){
							if($(this).is(':visible')){
								$(this).find('input').attr('checked','checked');
								return false;
							}
						}
					});



				}else{
					alert(data.message);
				}

			}
		});
	});

	$('#changeAddress').click(function(){
		$('#googleAddress').show();
		$('#checkAddress').show();
		$('#changeAddress').hide();
		$('.dateRow').hide();
	});

	$('#countryDrop').change(function (){
		var $stateColumn = $('#stateCol');
		showAjaxLoader($stateColumn);

		$.ajax({
			cache: true,
			url: js_app_link('appExt=payPerRentals&app=build_reservation&appPage=default&rType=ajax&action=getCountryZones'),
			data: 'cID=' + $(this).val() + '&zName='+$('#stateCol input').val(),
			dataType: 'html',
			success: function (data){
				removeAjaxLoader($stateColumn);
				$('#stateCol').html(data);
			}
		});
	});

	$('#countryDrop').trigger('change');


});