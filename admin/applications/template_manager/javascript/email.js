var selectedTabTextarea = null;
$(document).ready(function (){
	/*
	google.language.getBranding('googleBrand');

	$('#googleTranslate').click(function (){
		var toLang = $('select[name=toLanguage]').val();
		showAjaxLoader(selectedTabTextarea, 'large');

		var text = $('textarea[name="email_text[1]"]').val();
		google.language.translate(text.substr(0, 1200), 'en', toLang, function(result) {
			if (!result.error) {
				selectedTabTextarea.val(result.translation);
			}
			removeAjaxLoader(selectedTabTextarea);
		});
	});
    */

	$('.editLink').hover(function (){
		if ($(this).hasClass('ui-state-active')) return;
		$('.editLink.ui-state-hover').removeClass('ui-state-hover');
		$(this).addClass('ui-state-hover');
		this.style.cursor = 'pointer';
	}, function (){
		if ($(this).hasClass('ui-state-active')) return;
		$(this).removeClass('ui-state-hover');
		this.style.cursor = null;
	}).click(function (){
		if ($(this).hasClass('ui-state-active')) return;
		$('.editLink.ui-state-active').removeClass('ui-state-active');
		$(this).addClass('ui-state-active');
		
		if ($(this).attr('template_id') == 'new'){
			$('.templateId').remove();
			$('.emailSubject').val('');
			$('.emailFile').val('');
			$('#emailEvent').val('').change();
			$('#emailTemplate').val('');
			$('#emailTemplateOriginal').attr('disabled', 'disabled').val('');
			$('.makeFCK').each(function (){
				$(this).val('');
				//var thisEditor = $(this).data('editorInstance');
				//thisEditor.setData('');
			});
		}else{
			showAjaxLoader($('#templateConfigure'), 'xlarge', 'append');
			$.ajax({
				url: js_app_link('rType=ajax&app=template_manager&appPage=email&action=getEmailInfo&tID=' + $(this).attr('template_id')),
				cache: false,
				dataType: 'json',
				success: function (data){
					$('.templateId').remove();
					$('<input type="hidden" class="templateId" name="template_id" value="' + data.templateId + '">').insertAfter($('#emailTemplate'));
					$('div').filter(function() { return $(this).attr('lang_name'); }).each(function (){
						var langName = $(this).attr('lang_name');
						$(this).find('.makeFCK').each(function(){
							$(this).val(data.emailText[langName])
						});
						$(this).find('.emailSubject').val(data.emailSubject[langName]);
					});
					$('.emailAtt').val(data.emailFile);
					$('#emailEvent').val(data.emailEvent);
					$('#emailTemplate').val(data.emailTemplate);
					$('#emailTemplateOriginal').removeAttr('disabled').val(data.emailTemplate);
					
					//$('.globalVars').html(data.globalVars.join('<br />'));
					
					if (data.standardVars == '[""]'){
						$('.standardVars').html('No Variables Available.');
					}else{
						$('.standardVars').html(data.standardVars.join('<br />') + '<br>');
					}
					
					if (data.conditionalVars == '[""]'){
						$('.conditionVars').html('No Variables Available');
					}else{
						$('.conditionVars').html(data.conditionalVars.join('<br />') + '<br>');
					}
					removeAjaxLoader($('#templateConfigure'));
				}
			});
		}
	});
	
	$('.ui-tabs-container').tabs({
		select: function(event, ui){
			selectedTabTextarea = $(ui.panel).find('textarea');
			return true;
	    }
	});
	
	$('.saveButton').click(function (){
		if ($('#emailEvent').val() == ''){
			alert('An action must be selected.');
			return false;
		}
		
		if ($('#emailTemplate').val() == ''){
			alert('Template must have a name.');
			return false;
		}
		return true;
	});

	$('.addStandardVar').click(function (){
		var varBox = $('.standardVars');
		if (varBox.find('.noVars').size() > 0){
			varBox.find('.noVars').remove();
		}

		varBox.append('{$<input type="text" name="variable[standard][]">}<br>');
	});

	$('.addConditionVar').click(function (){
		var varBox = $('.conditionVars');
		if (varBox.find('.noVars').size() > 0){
			varBox.find('.noVars').remove();
		}

		var condition = '&lt;!-- if ($<input type="text" name="variable[condition][check][]">)<br />';
		condition = condition + '&nbsp;&nbsp;&nbsp;$<input type="text" name="variable[condition][var][]"><br />';
		condition = condition + '--&gt;<br>';
		
		varBox.append(condition);
	});
});