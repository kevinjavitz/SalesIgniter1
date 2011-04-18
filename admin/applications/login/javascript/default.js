$(document).ready(function (){
	$(document).keypress(function (e){
		if (e.keyCode === $.ui.keyCode.ENTER){
			$('.ui-button').click();
		}
	});
	
	$('#loginDialog').dialog({
		allowClose: false,
		resizable: false,
		draggable: false,
		buttons: {
			'Login': function (e){
				var $inputs = $('input[name=email_address], input[name=password]');
				var $button = $(e.target);
				$.ajax({
					url: $('form[name=login]').attr('action'),
					type: 'POST',
					dataType: 'json',
					data: $inputs.serialize(),
					cache: false,
					beforeSend: function (){
						showAjaxLoader($button, 'small');
					},
					success: function (data){
						if (data.loggedIn == true){
							document.location = data.redirectUrl;
						}else{
							if ($('.messageStack_pageStack').size() > 0){
								$('.messageStack_pageStack').replaceWith(data.pageStack);
							}else{
								$(data.pageStack).insertBefore($('form[name=login]'));
							}
							removeAjaxLoader($button);
						}
					}
				});
				
				return false;
			}
		}
	});
});