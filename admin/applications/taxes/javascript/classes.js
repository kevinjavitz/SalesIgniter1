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
		getVars.push('appPage=classes');
		getVars.push('action=getActionWindow');
		getVars.push('window=newTaxClass');
		if ($(this).hasClass('editButton') && $('.gridBodyRow.state-active').size() > 0){
			getVars.push('cID=' + $('.gridBodyRow.state-active').attr('data-class_id'));
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
					getVars.push('appPage=classes');
					getVars.push('action=saveTaxClass');
					if ($('.gridBodyRow.state-active').size() > 0){
						getVars.push('cID=' + $('.gridBodyRow.state-active').attr('data-class_id'));
					}
					
					$.ajax({
						cache: false,
						url: js_app_link(getVars.join('&')),
						dataType: 'json',
						data: $(self).find('*').serialize(),
						type: 'post',
						success: function (data){
							if (data.success){
								js_redirect(js_app_link('app=taxes&appPage=classes&cID=' + data.cID));
							}
						}
					});
				});
			}
		});
	});
	
	$('.deleteButton').click(function (){
		var classId = $('.gridBodyRow.state-active').attr('data-class_id');
		confirmDialog({
			confirmUrl: js_app_link('app=taxes&appPage=classes&action=deleteTaxClass&cID=' + classId),
			title: 'Confirm Class Delete',
			content: 'Are you sure you want to delete this class?',
			errorMessage: 'This class could not be deleted.',
			success: function (){
				js_redirect(js_app_link('app=taxes&appPage=classes'));
			}
		});
	});
});