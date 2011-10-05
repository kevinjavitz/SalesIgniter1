function getLinkParams(addVars, isAjax) {
	var getVars = [];
	getVars.push('appExt=pdfPrinter');
	getVars.push('app=' + thisApp);
	getVars.push('appPage=' + thisAppPage);
	getVars.push('showErrors=true');

	if ($('.gridBodyRow.state-active').size() > 0){
		getVars.push('tID=' + $('.gridBodyRow.state-active').data('template_id'));
	}

	if (addVars){
		for(var i = 0; i < addVars.length; i++){
			getVars.push(addVars[i]);
		}
	}
	return getVars.join('&');
}

$(document).ready(function () {
	$('.gridBody > .gridBodyRow').live('click', function () {
		$('.gridButtonBar').find('button').button('enable');
	});

	$('.layoutsButton').click(function () {
		js_redirect(js_app_link('appExt=pdfPrinter&app=layout_manager&appPage=layouts&tID=' + $('.gridBodyRow.state-active')
		.data('template_id')));
	});

	$('.newButton').click(function () {
		var getVars = getLinkParams([
			'rType=ajax',
			'action=getActionWindow',
			'window=newTemplate'
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
						'action=createTemplate'
					]);

					$.ajax({
						cache: false,
						url: js_app_link(getVars),
						dataType: 'json',
						data: $(self).find('*').serialize(),
						type: 'post',
						success: function (data) {
							if (data.success){
								$('.grid tbody').append('<tr>' +
									'<td>' + data.layoutName + '</td>' +
								'</tr>');
								
								$(self).effect('fade', {
									mode: 'hide'
								}, function () {
									$('.gridContainer').effect('fade', {
										mode: 'show'
									}, function () {
										$(self).remove();
									});
								});
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

	$('.importButton').click(function () {
		var getVars = getLinkParams([
			'rType=ajax',
			'action=getActionWindow',
			'window=importTemplate'
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

				$(self).find('.installButton').click(function () {
					var getVars = getLinkParams([
						'rType=ajax',
						'action=importTemplate'
					]);

					$.ajax({
						cache: false,
						url: js_app_link(getVars),
						dataType: 'json',
						data: $(self).find('*').serialize(),
						type: 'post',
						success: function (data) {
							alert('Template Imported');
						}
					});
				});

				if (typeof importWindowOnLoad != 'undefined'){
					importWindowOnLoad.apply(self);
				}
			}
		});
	});

	$('.exportButton').click(function () {
		var getVars = getLinkParams([
			'rType=ajax',
			'action=exportTemplate'
		]);

		$.ajax({
			cache: false,
			url: js_app_link(getVars),
			dataType: 'json',
			success: function (data) {
				alert('Template exported successfully, you can download it from the templates directory.');
			}
		});
	});

	$('.configureButton').click(function () {
		var getVars = getLinkParams([
			'rType=ajax',
			'action=getActionWindow',
			'window=configureTemplate'
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
						'action=saveTemplate'
					]);

					$.ajax({
						cache: false,
						url: js_app_link(getVars),
						dataType: 'json',
						data: $(self).find('*').serialize(),
						type: 'post',
						success: function (data) {
							if (data.success){
								$(self).effect('fade', {
									mode: 'hide'
								}, function () {
									$('.gridContainer').effect('fade', {
										mode: 'show'
									}, function () {
										$(self).remove();
									});
								});
							}
						}
					});
				});

				if (typeof configureWindowOnLoad != 'undefined'){
					configureWindowOnLoad.apply(self);
				}
			}
		});
	});

	$('.deleteButton').click(function () {
		var getVars = getLinkParams([
			'rType=ajax',
			'action=deleteTemplate'
		]);

		confirmDialog({
			confirmUrl: js_app_link(getVars),
			title: 'Confirm Template Delete',
			content: 'Are you sure you want to delete this template and its\' layouts?',
			errorMessage: 'This template could not be deleted.',
			success: function () {
				js_redirect(js_app_link('appExt=templateManager&app=' + thisApp + '&appPage=' + thisAppPage));
			}
		});
	});
});