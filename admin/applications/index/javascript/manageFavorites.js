function getLinkParams(addVars, isAjax) {
	var getVars = [];
	getVars.push('app=index');
	getVars.push('appPage=manageFavorites');

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
				$('.favoritesLinks').sortable({
				});
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
						'action=saveAsSet',
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
								js_redirect(js_app_link('app=index&appPage=manageFavorites'));
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

	$('.remoFav').live('click', function(){
		$(this).parent().remove();
		return false;
	});

	$('.loadSetButton').click(function () {
		var getVars = getLinkParams([
			'rType=ajax',
			'action=loadSet',
			'mID=' + $('.gridBodyRow.state-active').attr('data-admin_id')
		]);

		confirmDialog({
			confirmUrl: js_app_link(getVars),
			title: 'Confirm Load as My set',
			content: 'Are you sure you want to load as your set. The set will be overwritten with your current ones?',
			errorMessage: 'This set could not be loaded.',
			success: function () {
				js_redirect(js_app_link('app=' + thisApp + '&appPage=' + thisAppPage));
			}
		});
	});

	$('.deleteButton').click(function () {
		var getVars = getLinkParams([
			'rType=ajax',
			'action=deleteSet',
			'mID=' + $('.gridBodyRow.state-active').attr('data-admin_id')
		]);

		confirmDialog({
			confirmUrl: js_app_link(getVars),
			title: 'Confirm Set Delete',
			content: 'Are you sure you want to delete this set?',
			errorMessage: 'This set could not be deleted.',
			success: function () {
				js_redirect(js_app_link('app=' + thisApp + '&appPage=' + thisAppPage));
			}
		});
	});
});