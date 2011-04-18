	$(document).ready(function (){
		/*$('#insure_all_product').change(function(){
			$('.insure_product').each(function(){
				if ($(this).is(':checked')){
					$(this).attr('checked', false);
				}else{
					$(this).attr('checked', true);
				}
			});
		});*/
        $('#insure_button').button();
		$('#insure_button').live('click',function(){
			var url = js_app_link('app=checkout&appPage=default&action=saveInsuranceCheckboxes');
            var $tableInsure = $(this).parent().parent().parent();
			showAjaxLoader($tableInsure, 'xlarge');
			$.ajax({
				cache: false,
				url: url,
				type: 'post',
				data: $('#insure_form *').serialize(),
				dataType: 'json',
				success: function (data){
					hideAjaxLoader($tableInsure);
                    $('#shoppingCart').html(data.pageHtml);
                    updateTotals();
                    if(data.isRemove == true){
                        $('#insuranceTextRemove').show();
                        $('#insuranceText').hide();
                    }else{
                        $('#insuranceTextRemove').hide();
                        $('#insuranceText').show();                        
                    }
                    $('#insure_button').button();
				}
			});
			return false;
		});
	});