$(document).ready(function (){
	$('.gridBody > .gridBodyRow').click(function (){
		if ($(this).hasClass('state-active')) return;

		$('.gridButtonBar').find('button').button('enable');
	});

	$('.editButton').click(function (){
		var productId = $('.gridBodyRow.state-active').attr('data-product_id');
		js_redirect(js_app_link('app=products&appPage=new_product&pID=' + productId));
	});
});