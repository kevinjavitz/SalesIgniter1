$(document).ready(function (){
	$('.gridBody > .gridBodyRow').click(function (){
		if ($(this).hasClass('state-active')) return;

		$('.gridButtonBar').find('button').button('enable');
	});
	
	$('.gridButtonBar').find('.viewButton').click(function (){
		var reportId = $('.gridBodyRow.state-active').attr('data-report_id');
		js_redirect(js_app_link('app=server_manager&appPage=viewReport&rID=' + reportId));
	});
});