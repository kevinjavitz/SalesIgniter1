$(document).ready(function (){
	$('input[name="products_type[]"]').click(function(){
		if($(this).is(':checked')){
			if($(this).val() == 'package'){
				$('input[name="products_type[]"]').removeAttr('checked');
				$(this).attr('checked','checked');
			}else{
				$('input[name="products_type[]"]:checked').each(function(){
					if($(this).val() == 'package'){
						$(this).removeAttr('checked');
					}
				});
			}
		}
	});
});