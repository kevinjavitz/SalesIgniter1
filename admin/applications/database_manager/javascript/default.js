$(document).ready(function (){
	$('select').change(function (){
		var self = this;
		showAjaxLoader($(self), 'small');
		$.ajax({
			url: $(this).data('action_url') + '&to=' + $(self).val() + '&variable=' + $(self).data('variable'),
			cache: false,
			dataType: 'json',
			success: function (data){
				removeAjaxLoader($(self));
				if (data.isOk){
					var TableRow = $(self).parentsUntil('.gridBody').last();
					TableRow.find('.gridBodyRowColumn:eq(2)').html($(self).val());
				}
				alert(data.message);
				js_redirect(js_app_link('app=database_manager&appPage=default'));
			}
		});
	});

	$('.resButton').click(function (e){
		e.preventDefault();
		
		var self = this;
		showAjaxLoader($(self), 'small');
		$.ajax({
			url: $(this).attr('href'),
			cache: false,
			dataType: 'json',
			success: function (data){
				removeAjaxLoader($(self));
				$(self).trigger('fixAllClicked');
				if (data.isOk){
					if (data.isUTF){
						$(self).parentsUntil('.gridBody').last().find('.utfStatusIcon').removeClass('ui-icon-circle-close').addClass('ui-icon-circle-check');
					}
					var Table = $(self).parentsUntil('.gridBodyRowColumn').last();
					$(self).parent().parent().remove();
					
					if (Table.find('tr').size() <= 0){
						Table.parentsUntil('.gridBody').last().find('.statusIcon').removeClass('ui-icon-circle-close').addClass('ui-icon-circle-check');
						Table.parent().parent().find('.allResButton').remove();
						Table.remove();
					}
				}
				alert(data.message);
			}
		});
	});
	
	$('.allResButton').click(function (e){
		e.preventDefault();
		var self = this;
		
		var buttons = [];
		var cnt = 0;
		$(this).parentsUntil('.gridBody').last().find('.resButton').each(function(){
			buttons.push($(this));
			$(this).bind('fixAllClicked', function (){
				cnt++;
				if (buttons[cnt]){
					buttons[cnt].click();
				}else{
					$(self).trigger('fixAllClicked');
				}
			});
		});
		
		buttons[cnt].click();
	});
	
	$('.fixEverythingButton').click(function (e){
		e.preventDefault();
		
		var buttons = [];
		var cnt = 0;
		$('.allResButton').each(function(){
			buttons.push($(this));
			$(this).bind('fixAllClicked', function (){
				cnt++;
				if (buttons[cnt]){
					buttons[cnt].click();
				}
			});
		});
		
		buttons[cnt].click();
	});
});