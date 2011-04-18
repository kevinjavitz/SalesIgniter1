function doRound(x, places) {
	return Math.round(x * Math.pow(10, places)) / Math.pow(10, places);
}

function getTaxRate() {
	var parameterVal = $('select[name=rent_tax_class_id] > option:selected').val();

	if ( (parameterVal > 0) && (tax_rates[parameterVal] > 0) ) {
		return tax_rates[parameterVal];
	} else {
		return 0;
	}
}

function updateGross() {
	var taxRate = getTaxRate();
	var grossValue = $('input[name=price]').val();

	if (taxRate > 0) {
		grossValue = grossValue * ((taxRate / 100) + 1);
	}

	$('input[name=gross_price]').val(doRound(grossValue, 4));
}

$(document).ready(function (){
	$('.gridBody > .gridBodyRow').click(function (){
		if ($(this).hasClass('state-active')) return;

		$('.gridButtonBar').find('button').button('enable');
	});

	$('.newButton, .editButton').click(function (){
		if ($(this).hasClass('newButton')){
			$('.gridBodyRow.state-active').removeClass('state-active');
		}
		
		var getVars = [];
		getVars.push('app=membership');
		getVars.push('appPage=packages');
		getVars.push('action=getActionWindow');
		getVars.push('window=new');
		if ($('.gridBodyRow.state-active').size() > 0){
			getVars.push('pID=' + $('.gridBodyRow.state-active').attr('data-plan_id'));
		}
		
		gridWindow({
			buttonEl: this,
			gridEl: $('.gridContainer'),
			contentUrl: js_app_link(getVars.join('&')),
			onShow: function (){
				var self = this;
				
				$(self).find('.cancelButton').click(function (){
					$(self).effect('fade', {
						mode: 'hide'
					}, function (){
						$('.gridContainer').effect('fade', {
							mode: 'show'
						}, function (){
							$(self).remove();
						});
					});
				});
				
				$(self).find('.saveButton').click(function (){
					var getVars = [];
					getVars.push('app=membership');
					getVars.push('appPage=packages');
					getVars.push('action=savePlan');
					if ($('.gridBodyRow.state-active').size() > 0){
						getVars.push('pID=' + $('.gridBodyRow.state-active').attr('data-plan_id'));
					}
					
					$.ajax({
						cache: false,
						url: js_app_link(getVars.join('&')),
						dataType: 'json',
						data: $(self).find('*').serialize(),
						type: 'post',
						success: function (data){
							if (data.success){
								if ($('.gridBodyRow.state-active').size() > 0){
									$(self).effect('fade', {
										mode: 'hide'
									}, function (){
										$('.gridContainer').effect('fade', {
											mode: 'show'
										}, function (){
											$(self).remove();
										});
									});
								}else{
									js_redirect(js_app_link('app=membership&appPage=packages&pID=' + data.pID));
								}
							}else{
								alert(data.message);
							}
						}
					});
				});
			}
		});
	});
	
	$('.deleteButton').click(function (){
		var planId = $('.gridBodyRow.state-active').attr('data-plan_id');
		confirmDialog({
			confirmUrl: js_app_link('app=membership&appPage=packages&action=deletePlan&pID=' + planId),
			title: 'Confirm Package Delete',
			content: 'Are you sure you want to delete this package?',
			errorMessage: 'This package could not be deleted.',
			success: function (){
				js_redirect(js_app_link('app=membership&appPage=packages'));
			}
		});
	});
});