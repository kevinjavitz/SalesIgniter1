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
		getVars.push('app=countries');
		getVars.push('appPage=default');
		getVars.push('action=getActionWindow');
		getVars.push('window=newCountry');
		if ($('.gridBodyRow.state-active').size() > 0){
			getVars.push('cID=' + $('.gridBodyRow.state-active').attr('data-country_id'));
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
					getVars.push('app=countries');
					getVars.push('appPage=default');
					getVars.push('action=save');
					if ($('.gridBodyRow.state-active').size() > 0){
						getVars.push('cID=' + $('.gridBodyRow.state-active').attr('data-country_id'));
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
									js_redirect(js_app_link('app=countries&appPage=default&cID=' + data.cID));
								}
							}
						}
					});
				});
			}
		});
	});
	
	$('.deleteButton').click(function (){
		var countryId = $('.gridBodyRow.state-active').attr('data-country_id');
		confirmDialog({
			confirmUrl: js_app_link('app=countries&appPage=default&action=deleteConfirm&cID=' + countryId),
			title: 'Confirm Country Delete',
			content: 'Are you sure you want to delete this country and all it\'s zones?',
			errorMessage: 'This country could not be deleted.',
			success: function (){
				js_redirect(js_app_link('app=countries&appPage=default'));
			}
		});
	});
	
	$('.insertIcon').live('click', function (){
		var $td1 = $('<td></td>').append('<input type="text" name="new_zone_name[]">');
		var $td2 = $('<td></td>').append('<input type="text" name="new_zone_code[]">');
		var $td3 = $('<td></td>').attr('align', 'right').append('<a class="ui-icon ui-icon-closethick deleteIcon"></a>');
		var $newTr = $('<tr></tr>').append($td1).append($td2).append($td3);
		$(this).parent().parent().parent().parent().find('tbody').prepend($newTr);
	});
	
	$('.deleteIcon').live('click', function (){
		$(this).parent().parent().remove();
	});
});