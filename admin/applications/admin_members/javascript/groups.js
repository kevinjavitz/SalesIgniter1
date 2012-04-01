$(document).ready(function () {
	$('.editButton, .newButton').click(function () {
		var contentUrlParams = [];
		contentUrlParams.push('action=getActionWindow');
		contentUrlParams.push('window=newGroup');
		if ($(this).hasClass('editButton')){
			contentUrlParams.push('gID=' + $('.gridContainer').newGrid('getSelectedData', 'group_id'));
		}

		var saveUrlParams = [];
		saveUrlParams.push('action=saveMember');
		if ($(this).hasClass('editButton')){
			saveUrlParams.push('gID=' + $('.gridContainer').newGrid('getSelectedData', 'group_id'));
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
		deleteUrlParams.push('action=deleteGroup');
		deleteUrlParams.push('gID=' + $('.gridContainer').newGrid('getSelectedData', 'group_id'));

		confirmDialog({
			confirmUrl: js_app_link(getActionLinkParams(deleteUrlParams, true)),
			title: 'Confirm Admin Group Delete',
			content: 'Are you sure you want to delete this admin group?',
			errorMessage: 'This admin group could not be deleted.',
			success: function () {
				js_redirect(js_app_link('app=' + thisApp + '&appPage=' + thisAppPage));
			}
		});
	});

	$('.permissionsButton').click(function (){
		js_redirect(js_app_link('app=admin_members&appPage=permissions&gID=' + $('.gridContainer').newGrid('getSelectedData', 'group_id')));
	});
});