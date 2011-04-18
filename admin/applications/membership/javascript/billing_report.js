$(document).ready(function (){
	$('.gridBody > .gridBodyRow').click(function (){
		if ($(this).hasClass('state-active')) return;
		
		
	});
	
	$('input[name="report[]"]').click(function (){
		if ($('input[name="report[]"]:checked').size() > 0){
			$('.gridButtonBar').find('button').button('enable');
		}else{
			$('.gridButtonBar').find('button').button('disable');
		}
	});

	$('.deleteButton').click(function (){
		var selectedReports = $('form[name=actions]').serialize();
		confirmDialog({
			confirmUrl: js_app_link('app=membership&appPage=billing_report&action=deleteReports&' + selectedReports),
			title: 'Confirm Reports Delete',
			content: 'Are you sure you want to delete the selected reports?',
			errorMessage: 'The selected reports could not be deleted.',
			success: function (){
				js_redirect(js_app_link('app=membership&appPage=billing_report'));
			}
		});
	});
});