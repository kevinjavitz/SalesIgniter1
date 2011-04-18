$(document).ready(function (){
	$('#forgottenDialog').dialog({
		allowClose: false,
		resizable: false,
		draggable: false,
		buttons: {
			'Back': function (){
				window.location = js_app_link('app=login&appPage=default');
			},
			'Send Password': function (){
				$('form[name="forgotten"]').submit();
			}
		}
	});
	
	$('.ui-button').button();
});