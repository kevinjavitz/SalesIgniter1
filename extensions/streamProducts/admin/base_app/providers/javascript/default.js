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
		getVars.push('app=providers');
		getVars.push('appExt=streamProducts');
		getVars.push('appPage=default');
		getVars.push('action=getActionWindow');
		getVars.push('window=newProvider');
		if ($('.gridBodyRow.state-active').size() > 0){
			getVars.push('pID=' + $('.gridBodyRow.state-active').attr('data-provider_id'));
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
				
				$(self).find('.modulesBox').change(function (){
					var $thisBox = $(this);
					showAjaxLoader($thisBox, 'small');
					
					var boxGetVars = [];
					boxGetVars.push('app=providers');
					boxGetVars.push('appExt=streamProducts');
					boxGetVars.push('appPage=default');
					boxGetVars.push('action=getModuleSettings');
					boxGetVars.push('module=' + $(this).val());
					if ($('.gridBodyRow.state-active').size() > 0){
						boxGetVars.push('pID=' + $('.gridBodyRow.state-active').attr('data-provider_id'));
					}
					
					$.ajax({
						cache: false,
						url: js_app_link(boxGetVars.join('&')),
						dataType: 'json',
						success: function (data){
							removeAjaxLoader($thisBox);
							if (data.message){
								$(self).find('.providerSettings').html(data.message);
							}else{
								$(self).find('.providerSettings').html(data.fields);
							}
						}
					});
				});
				
				if ($(self).find('.modulesBox').val() != ''){
					$(self).find('.modulesBox').change();
				}
				
				$(self).find('.saveButton').click(function (){
					var getVars = [];
					getVars.push('app=providers');
					getVars.push('appExt=streamProducts');
					getVars.push('appPage=default');
					getVars.push('action=saveProvider');
					if ($('.gridBodyRow.state-active').size() > 0){
						getVars.push('pID=' + $('.gridBodyRow.state-active').attr('data-provider_id'));
					}
					
					$.ajax({
						cache: false,
						url: js_app_link(getVars.join('&')),
						dataType: 'json',
						data: $(self).find('*').serialize(),
						type: 'post',
						success: function (data){
							if (data.success){
								if ($('.gridBodyRow.state-active').size() > 0){
									$(self).effect('fade', {
										mode: 'hide'
									}, function (){
										$('.gridContainer').effect('fade', {
											mode: 'show'
										}, function (){
											$(self).remove();
										});
									});
								}else{
									js_redirect(js_app_link('app=providers&appExt=streamProducts&appPage=default&pID=' + data.pID));
								}
							}else{
								alert(data.message);
							}
						}
					});
				});
			}
		});
	});
	
	$('.deleteButton').click(function (){
		var providerId = $('.gridBodyRow.state-active').attr('data-provider_id');
		confirmDialog({
			confirmUrl: js_app_link('app=providers&appExt=streamProducts&appPage=default&action=deleteProvider&pID=' + providerId),
			title: 'Confirm Provider Delete',
			content: 'Are you sure you want to delete this provider?',
			errorMessage: 'This provider could not be deleted.',
			success: function (){
				js_redirect(js_app_link('app=providers&appExt=streamProducts&appPage=default'));
			}
		});
	});
});