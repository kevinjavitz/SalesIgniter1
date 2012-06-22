$(document).ready(function (){
	$('#products_custom_fields_group').change(function (){
		var self = $(this);
		showAjaxLoader(self, 'normal');
		$.ajax({
			cache: false,
			url: js_app_link('app=products&appPage=new_product&action=getTypeFields' + (productID > 0 ? '&pID=' + productID : '') + '&gID=' + $(this).val()),
			dataType: 'html',
			success: function (data){
				$('#productsCustomFields').html(data);
				hideAjaxLoader(self);
			}
		});
	}).trigger('change');
});