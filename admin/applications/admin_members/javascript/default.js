function getLinkParams(addVars, isAjax) {
	var getVars = [];
	getVars.push('app=admin_members');
	getVars.push('appPage=default');

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
	});

	$('.editButton').click(function () {
		var getVars = getLinkParams([
			'rType=ajax',
			'action=getActionWindow',
			'window=new_edit',
			'aID=' + $('.gridBodyRow.state-active').attr('data-admin_id')
		]);

		gridWindow({
			buttonEl: this,
			gridEl: $('.gridContainer'),
			contentUrl: js_app_link(getVars),
			onShow: function () {
				var self = this;

				$(self).find('.cancelButton').click(function () {
					$(self).effect('fade', {
							mode: 'hide'
						}, function () {
							$('.gridContainer').effect('fade', {
									mode: 'show'
								}, function () {
									$(self).remove();
								});
						});
				});

				$(self).find('.saveButton').click(function () {
					var getVars = getLinkParams([
						'rType=ajax',
						'action=saveMember',
						'aID=' + $('.gridBodyRow.state-active').attr('data-admin_id')
					]);

					$.ajax({
						cache: false,
						url: js_app_link(getVars),
						dataType: 'json',
						data: $(self).find('*').serialize(),
						type: 'post',
						success: function (data) {
							if (data.success){
								js_redirect(js_app_link('app=admin_members&appPage=default'));
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

	$('.newButton').click(function () {
		var getVars = getLinkParams([
			'rType=ajax',
			'action=getActionWindow',
			'window=new_edit'
		]);

		gridWindow({
			buttonEl: this,
			gridEl: $('.gridContainer'),
			contentUrl: js_app_link(getVars),
			onShow: function () {
				var self = this;

				$(self).find('.cancelButton').click(function () {
					$(self).effect('fade', {
							mode: 'hide'
						}, function () {
							$('.gridContainer').effect('fade', {
									mode: 'show'
								}, function () {
									$(self).remove();
								});
						});
				});

				$(self).find('.saveButton').click(function () {
					var getVars = getLinkParams([
						'rType=ajax',
						'action=saveMember'
					]);

					$.ajax({
						cache: false,
						url: js_app_link(getVars),
						dataType: 'json',
						data: $(self).find('*').serialize(),
						type: 'post',
						success: function (data) {
							if (data.success){
								js_redirect(js_app_link('app=admin_members&appPage=default'));
							}
						}
					});
				});

				if (typeof newWindowOnLoad != 'undefined'){
					newWindowOnLoad.apply(self);
				}
			}
		});
	});

	$('.deleteButton').click(function () {
		var getVars = getLinkParams([
			'rType=ajax',
			'action=deleteMember',
			'mID=' + $('.gridBodyRow.state-active').attr('data-admin_id')
		]);

		confirmDialog({
			confirmUrl: js_app_link(getVars),
			title: 'Confirm Admin Delete',
			content: 'Are you sure you want to delete this administrator?',
			errorMessage: 'This administrator could not be deleted.',
			success: function () {
				js_redirect(js_app_link('app=' + thisApp + '&appPage=' + thisAppPage));
			}
		});
	});
});