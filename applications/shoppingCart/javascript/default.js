
function popupWindowInitials(url, initials, w, h) {
	$('<div id="initials"></div>').dialog({
		autoOpen: true,
		width: w,
		height: h,
		title: 'Terms and Conditions Agreement',
		close: function (e, ui){
			$(this).dialog('destroy').remove();
			return false;
		},
		open: function (e, ui){
			if (initials){
				$(e.target).html('<b>Sign with your initials here:</b> <input id="initials_input" type="text" size="5"/><br/><div id="termsText"></div>');
			}else{
				$(e.target).html('<div id="termsText"></div>');
			}
			showAjaxLoader($('#initials'), 'xlarge');
			var pos = ui.position;
			$.ajax({
				cache: false,
				url: url,
				success: function (data){
					hideAjaxLoader($('#initials'));
					$('#termsText').html(data.html);


					$('#initials').dialog( "option", "width", 330 );

				}
			});
		},
		buttons: {
				'OK': function() {
					 //ajax call to save comment on success
						dialog = $(this);

					    if (initials){
						    agrees = $('#initials_input').val();
					    }else{
						    agrees = 'yes';
					    }
						if (agrees != ''){
							 showAjaxLoader($('#initials'), 'xlarge');
							$.ajax({
								cache: false,
								url: url,
								data: 'agree='+agrees,
								type: 'post',
								dataType: 'json',
								success: function (data){
									hideAjaxLoader($('#initials'));
									dialog.dialog('close');
									js_redirect(js_app_link('app=checkout&appPage=default','SSL'));
								}
							});
							return true;
						}else{
							alert('Please sign with your Initials');							
						}
				},
				Cancel: function() {
					$(this).dialog('close');
					return false;
				}
			}
	});
	return false;
}

$(document).ready(function (){
	if ($('.startDate').size() > 0){
		$('.startDate').datepicker({
			minDate: '+1',
			onSelect: function (dateText){
				var dateObj = $.datepicker.parseDate('mm/dd/yy', dateText);
				var longDate = $.datepicker.formatDate('D, dd MM yy', dateObj);
				$('.startDateLong').html(longDate);
			}
		});
	
		$('.endDate').datepicker({
			minDate: '+1',
			onSelect: function (dateText){
				var dateObj = $.datepicker.parseDate('mm/dd/yy', dateText);
				var longDate = $.datepicker.formatDate('D, dd MM yy', dateObj);
				$('.endDateLong').html(longDate);
			}
		});
	}
});