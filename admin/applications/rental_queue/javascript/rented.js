$(document).ready(function (){
	$('.gridBody > .gridBodyRow').click(function (){
		if ($(this).hasClass('state-active')) return;

		$('.gridButtonBar').find('button').button('enable');
	});

	$('.rentedButton').click(function (){
		js_redirect(js_app_link('app=rental_queue&appPage=return&cID=' + $('.gridBodyRow.state-active').attr('data-customer_id')));
	});
});