$(document).ready(function (){
	$('.gridBody > .gridBodyRow').click(function (){
		if ($(this).hasClass('state-active')) return;

		$('.gridButtonBar').find('button').button('enable');
		if ($(this).attr('data-canDelete') == 'false'){
			$('.gridButtonBar').find('.deleteButton').button('disable');
		}
	});

	$('.newButton, .editButton').click(function (){
		if ($(this).hasClass('newButton')){
			$('.gridBodyRow.state-active').removeClass('state-active');
		}
		
		var getVars = [];
		getVars.push('app=orders_status');
		getVars.push('appPage=default');
		getVars.push('action=getActionWindow');
		getVars.push('window=new');
		if ($('.gridBodyRow.state-active').size() > 0){
			getVars.push('sID=' + $('.gridBodyRow.state-active').attr('data-status_id'));
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
					getVars.push('app=orders_status');
					getVars.push('appPage=default');
					getVars.push('action=save');
					if ($('.gridBodyRow.state-active').size() > 0){
						getVars.push('sID=' + $('.gridBodyRow.state-active').attr('data-status_id'));
					}
					
					$.ajax({
						cache: false,
						url: js_app_link(getVars.join('&')),
						dataType: 'json',
						data: $(self).find('*').serialize(),
						type: 'post',
						success: function (data){
							if (data.success){
								js_redirect(js_app_link('app=orders_status&appPage=default&sID=' + data.sID));
							}
						}
					});
				});
			}
		});
	});
	
	$('.deleteButton').click(function (){
		var statusId = $('.gridBodyRow.state-active').attr('data-status_id');
		confirmDialog({
			confirmUrl: js_app_link('app=orders_status&appPage=default&action=deleteConfirm&sID=' + statusId),
			title: 'Confirm Orders Status Delete',
			content: 'Are you sure you want to delete this orders status?',
			errorMessage: 'This orders status could not be deleted.'
		});
	});
});