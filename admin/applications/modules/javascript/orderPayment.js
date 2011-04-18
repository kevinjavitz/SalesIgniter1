function getLinkParams(addVars, isAjax){
	var getVars = [];
	getVars.push('app=' + thisApp);
	getVars.push('appPage=' + thisAppPage);
	getVars.push('module=' + $('.gridBodyRow.state-active').attr('data-module_code'));
	getVars.push('moduleType=' + $('.gridBodyRow.state-active').attr('data-module_type'));
	
	if (addVars){
		for(var i=0; i<addVars.length; i++){
			getVars.push(addVars[i]);
		}
	}
	return getVars.join('&');
}

$(document).ready(function (){
	$('.gridBody > .gridBodyRow').click(function (){
		if ($(this).hasClass('state-active')) return;

		$('.gridButtonBar').find('button').button('enable');
		if ($(this).attr('data-installed') == 'false'){
			$('.gridButtonBar').find('.uninstallButton').button('disable');
			$('.gridButtonBar').find('.editButton').button('disable');
		}else{
			$('.gridButtonBar').find('.installButton').button('disable');
		}
	});

	$('.editButton').click(function (){
		var getVars = getLinkParams([
			'rType=ajax',
			'action=getActionWindow',
			'window=edit'
		]);
		
		gridWindow({
			buttonEl: this,
			gridEl: $('.gridContainer'),
			contentUrl: js_app_link(getVars),
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
					var getVars = getLinkParams([
						'rType=ajax',
						'action=save'
					]);
					
					$.ajax({
						cache: false,
						url: js_app_link(getVars),
						dataType: 'json',
						data: $(self).find('*').serialize(),
						type: 'post',
						success: function (data){
							if (data.success){
								js_redirect(js_app_link('app=' + thisApp + '&appPage=' + thisAppPage));
							}
						}
					});
				});
				
				if (typeof editWindowOnLoad != 'undefined'){
					editWindowOnLoad.apply(self);
				}
			}
		});
	});
	
	$('.installButton').click(function (){
		var getVars = getLinkParams(['action=install']);
		js_redirect(js_app_link(getVars));
	});
	
	$('.uninstallButton').click(function (){
		var getVars = getLinkParams([
			'rType=ajax',
			'action=remove'
		]);
		
		confirmDialog({
			confirmUrl: js_app_link(getVars),
			title: 'Confirm Module Uninstall',
			content: 'Are you sure you want to uninstall this module?',
			errorMessage: 'This module could not be uninstalled.',
			success: function (){
				js_redirect(js_app_link('app=' + thisApp + '&appPage=' + thisAppPage));
			}
		});
	});
});