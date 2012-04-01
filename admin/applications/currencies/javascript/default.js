$(document).ready(function () {
	$('.editButton, .newButton').click(function () {
		var contentUrlParams = [];
		contentUrlParams.push('action=getActionWindow');
		contentUrlParams.push('window=new');
		if ($(this).hasClass('editButton')){
			contentUrlParams.push('cID=' + $('.gridContainer').newGrid('getSelectedData', 'currency_id'));
		}

		var saveUrlParams = [];
		saveUrlParams.push('action=save');
		if ($(this).hasClass('editButton')){
			saveUrlParams.push('cID=' + $('.gridContainer').newGrid('getSelectedData', 'currency_id'));
		}

		gridWindow({
			buttonEl: this,
			gridEl: $('.gridContainer'),
			contentUrl: js_app_link(getActionLinkParams(contentUrlParams, true)),
			saveUrl: js_app_link(getActionLinkParams(saveUrlParams, true))
		});
	});

	$('.deleteButton').click(function () {
		var deleteUrlParams = [];
		deleteUrlParams.push('action=deleteConfirm');
		deleteUrlParams.push('cID=' + $('.gridContainer').newGrid('getSelectedData', 'currency_id'));

		confirmDialog({
			confirmUrl: js_app_link(getActionLinkParams(deleteUrlParams, true)),
			title: 'Confirm Currency Delete',
			content: 'Are you sure you want to delete this currency?',
			errorMessage: 'This currency could not be deleted.',
			success: function () {
				js_redirect(js_app_link('app=' + thisApp + '&appPage=' + thisAppPage));
			}
		});
	});
});