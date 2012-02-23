$(document).ready(function (){
	$('#storeTabs').tabs();
	makeTabsVertical('#storeTabs');
	$('.netPricing').keyup(function (){
		var taxRate = getTaxRate();
		var grossValue = $(this).val();

		if (taxRate > 0) {
			grossValue = grossValue * ((taxRate / 100) + 1);
		}
		$('#' + $(this).attr('id') + '_gross').val(doRound(grossValue, 4));
	});

	$('.grossPricing').keyup(function (){
		var taxRate = getTaxRate();
		var netValue = $(this).val();

		if (taxRate > 0) {
			netValue = netValue / ((taxRate / 100) + 1);
		}
		var id = $(this).attr('id');
		$('#' + id.replace('_gross', '')).val(doRound(netValue, 4));
	});

	$('#tax_class_id').change(function (){
		$('.netPricing').trigger('keyup');
	}).trigger('change');

	$('.showMethod').each(function (){
		var self = this;
		var $curTab = $(self).parent();
		var $tabEl = $('.pricingTabs', $curTab);
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
		var $tabEl = $('.pricingTabs', $curTab);

		if($(self).attr('checked') == 'checked' && $(self).val() == 'use_global'){
			$tabEl.hide();
		}else if($(self).attr('checked') == 'checked' && $(self).val() == 'use_custom'){
			$tabEl.tabs().show();
			$(self).data('clicked', true);
		}
	});
});