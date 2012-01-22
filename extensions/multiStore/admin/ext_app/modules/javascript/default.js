function editWindowOnLoad(){
	$('.showMethod').each(function (){
		var self = this;
		var $tabPanel = $(self).parent();

		if (self.checked && $(self).val() == 'use_custom'){
			$tabPanel.find('.makeTabPanel').show();
		}

		$(self).click(function (){
			if ($(this).val() == 'use_global'){
				$tabPanel.find('.makeTabPanel').hide();
			}else if ($(this).val() == 'use_custom'){
				$tabPanel.find('.makeTabPanel').show();
			}
		});
	});
}