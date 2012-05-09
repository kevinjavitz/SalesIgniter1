$(document).ready(function (){
	$('#timefeesTabs').tabs();
	$('#storeTabs').tabs();
	makeTabsVertical('#storeTabs');
	$('.showMethod').each(function (){
		var self = this;
		var $curTab = $(self).parent();
		var $tabEl = $('.timefeesTabs', $curTab);
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
	});
	$('.showMethod').each(function (){
		var self = this;
		var $curTab = $(self).parent();
		var $tabEl = $('.timefeesTabs', $curTab);

		if($(self).attr('checked') == 'checked' && $(self).val() == 'use_global'){
			$tabEl.hide();
		}else if($(self).attr('checked') == 'checked' && $(self).val() == 'use_custom'){
			$tabEl.tabs().show();
			$(self).data('clicked', true);
		}
	});

});