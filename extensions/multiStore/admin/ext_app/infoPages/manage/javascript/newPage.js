$(document).ready(function (){
	$('#storeTabs').tabs();
	makeTabsVertical('#storeTabs');
	
	$('.showMethod').each(function (){
		var self = this;
		var $curTab = $(self).parent();
		var $tabEl = $('.storeTabs_langTabs', $curTab);
		
		if (self.checked && $(self).val() == 'use_custom'){
			$(self).click(function (){
				if ($(this).val() == 'use_global'){
					$tabEl.hide();
				}else if ($(this).val() == 'use_custom'){
					if (!$(self).data('clicked')){
						$tabEl.tabs().show();
					}else{
						$tabEl.show();
					}
				}
				$(self).data('clicked', true);
			});
			$tabEl.tabs().show();
		}else{
			$(self).click(function (){
				if ($(this).val() == 'use_global'){
					$tabEl.hide();
				}else if ($(this).val() == 'use_custom'){
					if (!$(self).data('clicked')){
						$tabEl.tabs().show();
						$('.makeFCK', $('#tab_global')).each(function (){
							var languageId = $(this).attr('language_id');
							var editor = $(this).ckeditorGet();
				
							var curEditor = $('.makeFCK[language_id=' + languageId + ']', $curTab).ckeditorGet();
							curEditor.setData(editor.getData());
							//alert(editor.getData());
						});
						
						$('.titleInput', $('#tab_global')).each(function (){
							var languageId = $(this).attr('language_id');
							$('.titleInput[language_id=' + languageId + ']', $curTab).val($(this).val());
							$('.metaTitleInput[language_id=' + languageId + ']', $curTab).val($('.metaTitleInput[language_id=' + languageId + ']', $('#tab_global')).val());
							$('.metaDescriptionInput[language_id=' + languageId + ']', $curTab).val($('.metaDescriptionInput[language_id=' + languageId + ']', $('#tab_global')).val());
							$('.metaKeywordsInput[language_id=' + languageId + ']', $curTab).val($('.metaKeywordsInput[language_id=' + languageId + ']', $('#tab_global')).val());
						});
					}else{
						$tabEl.show();
					}
					
					$(self).data('clicked', true);
				}
			});
		}
	});
});