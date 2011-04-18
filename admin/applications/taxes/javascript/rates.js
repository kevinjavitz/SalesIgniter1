$(document).ready(function (){
	$('.gridBody > .gridBodyRow').click(function (){
		if ($(this).hasClass('state-active')) return;

		$('.gridButtonBar').find('button').button('enable');
	});

	$('.newButton, .editButton').click(function (){
		if ($(this).hasClass('newButton')){
			$('.gridBodyRow.state-active').removeClass('state-active');
		}
		
		var getVars = [];
		getVars.push('app=taxes');
		getVars.push('appPage=rates');
		getVars.push('action=getActionWindow');
		getVars.push('window=newTaxRate');
		if ($('.gridBodyRow.state-active').size() > 0){
			getVars.push('rID=' + $('.gridBodyRow.state-active').attr('data-rate_id'));
		}
		
		gridWindow({
			buttonEl: this,
			gridEl: $('.gridContainer'),
			contentUrl: js_app_link(getVars.join('&')),
			onShow: function (){
				var self = this;
				
				$(self).find('.cancelButton').click(function (){
					$(self).effect('fade', {
						mode: 'hide'
					}, function (){
						$('.gridContainer').effect('fade', {
							mode: 'show'
						}, function (){
							$(self).remove();
						});
					});
				});
				
				$(self).find('.saveButton').click(function (){
					var getVars = [];
					getVars.push('app=taxes');
					getVars.push('appPage=rates');
					getVars.push('action=saveTaxRate');
					if ($('.gridBodyRow.state-active').size() > 0){
						getVars.push('rID=' + $('.gridBodyRow.state-active').attr('data-rate_id'));
					}
					
					$.ajax({
						cache: false,
						url: js_app_link(getVars.join('&')),
						dataType: 'json',
						data: $(self).find('*').serialize(),
						type: 'post',
						success: function (data){
							if (data.success){
								js_redirect(js_app_link('app=taxes&appPage=rates&rID=' + data.rID));
							}
						}
					});
				});
			}
		});
	});
	
	$('.deleteButton').click(function (){
		var rateId = $('.gridBodyRow.state-active').attr('data-rate_id');
		confirmDialog({
			confirmUrl: js_app_link('app=taxes&appPage=classes&action=deleteTaxRate&rID=' + rateId),
			title: 'Confirm Rate Delete',
			content: 'Are you sure you want to delete this rate?',
			errorMessage: 'This rate could not be deleted.',
			success: function (){
				js_redirect(js_app_link('app=taxes&appPage=classes'));
			}
		});
	});
});