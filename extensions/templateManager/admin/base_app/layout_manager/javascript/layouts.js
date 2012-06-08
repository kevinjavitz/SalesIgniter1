function getLinkParams(addVars, isAjax) {
	var getVars = [];
	getVars.push('appExt=templateManager');
	getVars.push('app=' + thisApp);
	getVars.push('appPage=' + thisAppPage);
	getVars.push('tID=' + tID);
	getVars.push('showErrors=true');
	if ($('.gridBodyRow.state-active').size() > 0){
		getVars.push('lID=' + $('.gridBodyRow.state-active').data('layout_id'));
	}
	if (addVars){
		for(var i = 0; i < addVars.length; i++){
			getVars.push(addVars[i]);
		}
	}
	return getVars.join('&');
}

$(document).ready(function () {
	$('.gridBodyRow').live('click', function () {
		$('.gridButtonBar').find('button').button('enable');
	});

	$('.backButton').click(function () {
		js_redirect(js_app_link('appExt=templateManager&app=layout_manager&appPage=default'));
	});

	$('.editButton').click(function (){
		js_redirect(js_app_link('appExt=templateManager&app=layout_manager&appPage=editLayout&lID=' + $('.gridBodyRow.state-active').data('layout_id')));
	});

	$('.generateCode').click(function(){
		$.ajax({
			cache: false,
			url: js_app_link('appExt=templateManager&app=layout_manager&appPage=layouts&action=generateCode&lID=' + $('.gridBodyRow.state-active').data('layout_id')),
			dataType: 'json',
			type: 'post',
			success: function (data) {
				$('<div title="Javascript generated Code"></div>').html(data.html )
					.dialog({
						height: 500,
						width: 600,
						close: function(event, ui)
						{
							$(this).dialog('destroy').remove();
						}
					});
				$('.genType, .genTemplate, .genProduct').change(function(){
					$.ajax({
						cache: false,
						url: js_app_link('appExt=templateManager&app=layout_manager&appPage=layouts&action=generateCode&lID=' + $('.gridBodyRow.state-active').data('layout_id')),
						dataType: 'json',
						data:'onlyCode=1&type='+$('.genType').val()+'&templateName='+$('.genTemplate').val()+'&products_id='+$('.genProduct option:selected').val(),
						type: 'post',
						success: function (data) {
							$('.genCode').html(data.html);
						}
					});
				});
			}
		});


	});

	$('.newButton, .configureButton').click(function () {
		if ($(this).hasClass('newButton')){
			$('.gridBodyRow.state-active').removeClass('state-active');
		}
		var getVars = getLinkParams([
			'rType=ajax',
			'action=getActionWindow',
			'window=newLayout'
		]);
		gridWindow({
			buttonEl: this,
			gridEl: $('.gridContainer'),
			contentUrl: js_app_link(getVars),
			onShow: function (ui) {
				var self = this;
				$(self).find('.cancelButton').click(function () {
					$(self).effect('fade', {
						mode: 'hide'
					}, function () {
						$('.gridContainer').show();
						$('.newWindowContainer').remove();
					});
				});
				$(self).find('.saveButton').click(function () {
					var getVars = getLinkParams([
						'rType=ajax',
						'action=createLayout'
					]);
					$.ajax({
						cache: false,
						url: js_app_link(getVars),
						dataType: 'json',
						data: $(self).find('*').serialize(),
						type: 'post',
						success: function (data) {
							//alert('Updated/Added, Want To Edit Now?');
							if (data.success){
								if ($(ui.triggerEl).hasClass('configureButton')){
									$('.gridBodyRow.state-active').find('.layoutName').html(data.layoutName);
									$('.gridBodyRow.state-active').find('.layoutType').html(data.layoutType);
									$('.gridBodyRow.state-active').attr('data-layout_id',data.layoutId);
									$('.gridBodyRow.state-active').data('layout_id', data.layoutId);
								}else{
									$('.grid tbody').append('<tr class="gridBodyRow" data-layout_id="' + data.layoutId + '">' +
										'<td class="gridBodyRowColumn layoutName">' + data.layoutName + '</td>' +
										'<td class="gridBodyRowColumn layoutType">' + data.layoutType + '</td>' +
									'</tr>');
								}
								$(self).effect('fade', {
									mode: 'hide'
								}, function () {
									$('.gridContainer').show();
									$('.newWindowContainer').remove();
								});
							}
						}
					});
				});
				if ($(this).hasClass('configureButton')){
					if (typeof configureWindowOnLoad != 'undefined'){
						configureWindowOnLoad.apply(self);
					}
				}else{
					if (typeof newWindowOnLoad != 'undefined'){
						newWindowOnLoad.apply(self);
					}
				}
			}
		});
	});
	$('.duplicateButton').click(function (){
		var getVars = getLinkParams([
			'rType=ajax',
			'action=duplicateLayout'
		]);
		confirmDialog({
			title: 'Duplicate Layout',
            id:'dupLayout',
			content: 'New Layout Name: <input type="text" class="layoutName" name="layoutName">',
			errorMessage: 'This layout could not be duplicated.',
			onConfirm: function (){
				var dialogEl = this;
				$.ajax({
					cache: false,
					url: js_app_link(getVars),
					dataType: 'json',
                    data: 'layoutName='+$('#dupLayout').find('.layoutName').val(),
					type: 'post',
					success: function (data) {
						//alert('Updated/Added, Want To Edit Now?');
						if (data.success){
							$('.grid tbody').append('<tr class="gridBodyRow" data-layout_id="' + data.layoutId + '">' +
							'<td class="gridBodyRowColumn">' + data.layoutName + '</td>' +
							'</tr>');
							$(dialogEl).dialog('close').remove();
						}
					}
				});
			},
			success: function () {
			}
		});
	});

	$('.deleteButton').click(function () {
		var getVars = getLinkParams([
			'rType=ajax',
			'action=deleteLayout'
		]);

		confirmDialog({
			confirmUrl: js_app_link(getVars),
			title: 'Confirm Layout Delete',
			content: 'Are you sure you want to delete this layout?',
			errorMessage: 'This layout could not be deleted.',
			success: function () {
				$('.gridBodyRow.state-active').remove();
				$('.gridBodyRow').first().trigger('click');
			}
		});
	});
});