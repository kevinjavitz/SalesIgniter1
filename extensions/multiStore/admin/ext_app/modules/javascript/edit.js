$(document).ready(function (){
	$('#storeTabs').tabs();
	makeTabsVertical('#storeTabs');
	
	$('.showMethod').each(function (){
		var self = this;
		var $curTab = $(self).parent();
		var $tabEl = $('.configTable', $curTab);
		
		if (self.checked && $(self).val() == 'use_custom'){
			$tabEl.show();
		}

		$(self).click(function (){
			if ($(this).val() == 'use_global'){
				$tabEl.hide();
			}else if ($(this).val() == 'use_custom'){
				$tabEl.show();
			}
		});
	});
});