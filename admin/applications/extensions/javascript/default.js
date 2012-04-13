function getLinkParams(addVars, isAjax) {
	var getVars = [];
	getVars.push('app=extensions');
	getVars.push('appPage=default');
	getVars.push('extension=' + $('.gridBodyRow.state-active').attr('data-extension_key'));

	if (addVars){
		for(var i = 0; i < addVars.length; i++){
			getVars.push(addVars[i]);
		}
	}
	return getVars.join('&');
}

$(document).ready(function () {
	$('.gridBody > .gridBodyRow').click(function () {
		if ($(this).hasClass('state-active')){
			return;
		}

		$('.gridButtonBar').find('button').button('enable');
		if ($(this).attr('data-installed') == 'false'){
			$('.gridButtonBar').find('.uninstallButton').button('disable');
			$('.gridButtonBar').find('.editButton').button('disable');
		}
		else {
			$('.gridButtonBar').find('.installButton').button('disable');
		}
	});

	$('.editButton').click(function () {
		configurationGridWindow({
			buttonEl: this,
			gridEl: $('.gridContainer'),
			contentUrl: js_app_link(getLinkParams(['rType=ajax', 'action=getActionWindow', 'window=configure'])),
			saveUrl: js_app_link(getLinkParams(['rType=ajax', 'action=save'])),
			onSaveSuccess: function (){
				js_redirect(js_app_link('app=extensions&appPage=default'));
			}
		});
	});

	$('.isSelected').each(function(){
		$(this).trigger('click');
	});

	$('.isEdit').each(function(){
		$('.editButton').trigger('click');
	});

	$('.installButton').click(function () {
		var getVars = getLinkParams(['action=install']);
		js_redirect(js_app_link(getVars));
	});

	$('.uninstallButton').click(function () {
		var getVars = getLinkParams([
			'rType=ajax',
			'action=remove'
		]);

		confirmDialog({
			confirmUrl: js_app_link(getVars),
			title: 'Confirm Extension Uninstall',
			content: 'Are you sure you want to uninstall this extension?',
			errorMessage: 'This extension could not be uninstalled.',
			success: function () {
				js_redirect(js_app_link('app=' + thisApp + '&appPage=' + thisAppPage));
			}
		});
	});

	/*
	 * Global function for javascript tables in the windows
	 */
	$('.deleteIcon').live('click', function () {
		$(this).parent().parent().remove();
	});
});