function removeAllEditors($el){
	var windowEl = $el || $('.editWindow');
	windowEl.find('textarea').each(function (){
		if ($(this).data('editorInstance')){
			$(this).data('editorInstance').destroy();
			$(this).trigger('focusout');
		}
	});
}

function checkModifications($el){
	var windowEl = $el || $('.editWindow');
	removeAllEditors(windowEl);
	return (windowEl.find('.ui-state-modified').size() > 0);
}

function translateText(){
	var fromLang = $('select[name=fromLanguage]').val();
	var toLang = $('select[name=toLanguage]').val();
	$('textarea').each(function (){
		var self = this;
		
		showAjaxLoader($(self), 'large');
		
		google.language.translate($(self).val(), fromLang, toLang, function(result) {
			if (!result.error) {
				$(self).val(result.translation);
			}
			removeAjaxLoader($(self));
		});
	});
	$('select[name=fromLanguage]').val(toLang);
}

function loadFile(filePath){
	showAjaxLoader($('.editWindow'), 'xlarge');
	$.ajax({
		cache: false,
		url: js_app_link('app=languages&appPage=default&action=editFile&file=' + filePath),
		dataType: 'html',
		success: function (data){
			$('.editWindow').html(data);
			$('.editWindow button').button();
			removeAjaxLoader($('.editWindow'));
		}
	});
}

function processSearch(){
	showAjaxLoader($('.editWindow'), 'xlarge');
	$.ajax({
		cache: false,
		url: js_app_link('app=languages&appPage=defines&action=search&searchFor=' + $('#searchBox').val()),
		data: $('#searchBox, input[name=filter_files], input[name="filter_lang[]"]').serialize(),
		dataType: 'json',
		success: function (data){
			if (data.matches.length > 0){
				var htmlString = '';
				for(var i=0; i<data.matches.length; i++){
					htmlString = htmlString + '<fieldset style="margin-bottom:.5em;"><legend>' + data.matches[i].path + ' ( ' + data.matches[i].total + ' )' + '</legend><div><ul style="list-style:none;margin:.5em;padding:0;">';
					for(var j=0; j<data.matches[i].strings.length; j++){
						htmlString = htmlString + 
						'<li>' + 
							data.matches[i].strings[j].key + 
							'<span style="float:right;margin-right:1em;"><span class="ui-icon ui-icon-newwin"></span></span>' + 
							'<br>' + 
							'<textarea rows="5" style="width:100%;" name="text[' + data.matches[i].strings[j].key + ']">' + 
								data.matches[i].strings[j].text + 
							'</textarea>' + 
						'</li>';
					}
					htmlString = htmlString + '</ul><div style="text-align:right;"><button data-filePath="' + data.matches[i].path + '" class="searchSaveButton">Save</button></div></div></fieldset>';
				}
				$('.editWindow').html(htmlString);
				$('.editWindow button').button();
			}
			removeAjaxLoader($('.editWindow'));
		}
	});
}
	
$(document).ready(function (){
	google.language.getBranding('googleBrand');
	
	$('.ui-icon-plusthick').click(function (){
		if ($(this).hasClass('ui-icon-minusthick')){
			$(this).removeClass('ui-icon-minusthick').addClass('ui-icon-plusthick');
			$(this).parent().find('ul:first').hide();
		}else{
			$(this).removeClass('ui-icon-plusthick').addClass('ui-icon-minusthick');
			$(this).parent().find('ul:first').show();
		}
	});
	
	$('.ui-icon-closethick').live('click', function (){
		if ($(this).parent().parent().hasClass('ui-state-disabled')) return false;
		
		$(this).parent().parent().addClass('ui-state-disabled').find('textarea').attr('disabled', 'disabled');
	});
	
	$('.ui-icon-pencil').click(function (){
		var filePath = $(this).attr('data-file_path');
		if (checkModifications()){
			confirmDialog({
				title: 'Abandon Modifications',
				content: 'There are unsaved modifications, are you sure you want to abandon them?',
				onConfirm: function (){
					loadFile(filePath);
					$(this).dialog('close').remove();
				}
			});
		}else{
			loadFile(filePath);
		}
	});
	
	$('#googleTranslate').click(function (){
		if (checkModifications()){
			confirmDialog({
				title: 'Abandon Modifications',
				content: 'There are unsaved modifications, are you sure you want to abandon them?',
				onConfirm: function (){
					translateText();
					$(this).dialog('close').remove();
				}
			});
		}else{
			translateText();
		}
	});
	
	$('.ui-icon-newwin').live('click', function (){
		if ($(this).parent().parent().hasClass('ui-state-disabled')) return false;
		
		$(this).parent().parent().find('textarea').each(function (){
			if ($(this).is(':hidden')){
				$(this).data('editorInstance').destroy();
				$(this).trigger('focusout');
			}else{
				$(this).trigger('focusin');
				$(this).data('editorInstance', CKEDITOR.replace(this, {
					toolbar: 'Basic',
					enterMode: CKEDITOR.ENTER_BR,
					filebrowserBrowseUrl: DIR_WS_ADMIN + 'rentalwysiwyg/editor/filemanager/browser/default/browser.php'
				}));
			}
		});
	});
	
	$('.editWindow textarea').live('focusin focusout', function (event){
		if (event.type == 'focusin'){
			if (!$(this).data('origVal')){
				$(this).data('origVal', $(this).val());
			}
		}else if (event.type == 'focusout'){
			if ($(this).val() != $(this).data('origVal')){
				$(this).addClass('ui-state-modified');
			}else{
				$(this).removeClass('ui-state-modified');
			}
		}
	});
	
	$('.saveButton, .searchSaveButton').live('click', function (){
		var self = this;
		var windowEl = $('.editWindow');
		if ($(this).hasClass('searchSaveButton')){
			windowEl = $(this).parent().parent();
		}
		
		if (checkModifications(windowEl)){
			showAjaxLoader(windowEl, 'xlarge');
			$.ajax({
				cache: false,
				url: js_app_link('app=languages&appPage=defines&action=saveFile&filePath=' + $(self).attr('data-filePath')),
				data: windowEl.find('.ui-state-modified').serialize(),
				type: 'post',
				dataType: 'json',
				success: function (){
					windowEl.find('textarea.ui-state-modified').each(function (){
						$(this).data('origVal', $(this).val());
						$(this).removeClass('ui-state-modified');
						
						var pathCheck = $(self).attr('data-filePath');
						if (pathCheck.substr(0, 18) != 'includes/languages'){
							$(this).addClass('hasCustomDefine');
						}
					});
					removeAjaxLoader(windowEl);
				}
			});
		}
	});
	
	$('#searchButton').click(function (){
		if (checkModifications()){
			confirmDialog({
				title: 'Abandon Modifications',
				content: 'There are unsaved modifications, are you sure you want to abandon them?',
				onConfirm: function (){
					processSearch();
					$(this).dialog('close').remove();
				}
			});
		}else{
			processSearch();
		}
	});
	
	$('#searchBox').keypress(function (e){
		if (e.which == '13'){
			$('#searchButton').click();
		}
	});
});