$(document).ready(function (){
	$('.gridBody > .gridBodyRow').click(function (){
		if ($(this).hasClass('state-active')) return;

		$('.gridButtonBar').find('button').button('enable');
		if ($(this).attr('data-is_default') == 'true'){
			$('.gridButtonBar').find('.deleteButton').button('disable');
		}
	});
	
	$('.gridButtonBar').find('.deleteButton').click(function (){
		var languageId = $('.gridBodyRow.state-active').attr('data-language_id');
		
		confirmDialog({
			confirmUrl: js_app_link('app=languages&appPage=default&action=deleteConfirm&lID=' + languageId),
			title: 'Confirm Delete',
			content: 'Are you sure you want to delete this language?',
			success: function (){
				js_redirect(js_app_link('app=languages&appPage=default'));
			}
		});
	});
	
	$('.gridButtonBar').find('.defineButton').click(function (){
		var langDir = $('.gridBodyRow.state-active').attr('data-language_dir');
		var langCode = $('.gridBodyRow.state-active').attr('data-language_code');
		js_redirect(js_app_link('app=languages&appPage=defines'));
	});
	
	$('.gridButtonBar').find('.cleanButton').click(function (){
		js_redirect(js_app_link('app=languages&appPage=default&action=cleanLanguages'));
	});
	
	$('.gridButtonBar').find('.editButton').click(function (){
		var langDir = $('.gridBodyRow.state-active').attr('data-language_dir');
		$('<div></div>').dialog({
			title: 'Edit Language',
			width: 400,
			height: 400,
			open: function (){
				var self = this;
				showAjaxLoader($(self), 'large');
				$.ajax({
					cache: false,
					url: js_app_link('app=languages&appPage=default&action=edit&langDir=' + langDir),
					dataType: 'html',
					success: function (data){
						removeAjaxLoader($(self));
						$(self).html(data);
					}
				});
			},
			buttons: {
				'Save': function (){
					var self = this;
					showAjaxLoader($(self), 'large');
					$.ajax({
						cache: false,
						url: js_app_link('app=languages&appPage=default&action=save'),
						dataType: 'json',
						type: 'post',
						data: $(self).find('input').serialize(),
						success: function (data){
							removeAjaxLoader($(self));
							$(self).dialog('destroy').remove();
						}
					});
				},
				'Cancel': function (){
					$(this).dialog('destroy').remove();
				}
			}
		});
	});
	
	$('.gridButtonBar').find('.newLanguageButton').click(function (){
		$('.dialogToLangCode').dialog({
			width: 600,
			height: 400,
			autoShow: true,
			open: function (){
				var self = this;
				$(this).find('.selectAll').click(function (){
					var allBoxChecked = this.checked;
					$(self).find('input[name="translate_model[]"]').each(function (){
						this.checked = allBoxChecked;
					});
				});
			},
			buttons: {
				'Create Language': function (){
					var self = this;
					showAjaxLoader($(self), 'small');
					
					var postVars = [];
					postVars.push('fromLangCode=en');
					if ($(self).find('[name=toLangCode]').size() > 0){
						postVars.push('toLangCode=' + $(self).find('[name=toLangCode]').val());
					}
					postVars.push('toLanguage=' + $(self).find('[name=toLanguage]').val());
					postVars.push($(self).find('input[name="translate_model[]"]:checked').serialize());
					
					$.ajax({
						cache: false,
						url: js_app_link('app=languages&appPage=defines&action=newLanguage&rType=ajax'),
						dataType: 'json',
						data: postVars.join('&'),
						type: 'post',
						success: function (data){
							if (data.success){
								js_redirect(js_app_link('app=languages&appPage=defines&langCode=' + data.langCode + '&langDir=' + data.langDir));
							}else{
								alert('There was an error generating the language files');
							}
							removeAjaxLoader($(self));
						}
					});
					
					updateNewLanguageStatus();
				}
			}
		});
	});
});

function updateNewLanguageStatus(){
	$.ajax({
		cache: false,
		url: js_app_link('app=languages&appPage=default&action=newLanguageStatus&rType=ajax'),
		dataType: 'html',
		type: 'post',
		success: function (data){
			$('.dialogToLangCode').html(data);
			updateNewLanguageStatus();
		}
	});
}