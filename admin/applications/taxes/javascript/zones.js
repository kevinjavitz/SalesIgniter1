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
		getVars.push('app=taxes');
		getVars.push('appPage=zones');
		getVars.push('action=getActionWindow');
		getVars.push('window=newTaxZone');
		if ($('.gridBodyRow.state-active').size() > 0){
			getVars.push('zID=' + $('.gridBodyRow.state-active').attr('data-zone_id'));
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
					getVars.push('app=taxes');
					getVars.push('appPage=zones');
					getVars.push('action=saveTaxZone');
					if ($('.gridBodyRow.state-active').size() > 0){
						getVars.push('zID=' + $('.gridBodyRow.state-active').attr('data-zone_id'));
					}
					
					$.ajax({
						cache: false,
						url: js_app_link(getVars.join('&')),
						dataType: 'json',
						data: $(self).find('*').serialize(),
						type: 'post',
						success: function (data){
							if (data.success){
								js_redirect(js_app_link('app=taxes&appPage=zones&zID=' + data.zID));
							}
						}
					});
				});
			}
		});
	});
	
	$('.deleteButton').click(function (){
		var zoneId = $('.gridBodyRow.state-active').attr('data-zone_id');
		confirmDialog({
			confirmUrl: js_app_link('app=taxes&appPage=zones&action=deleteTaxZone&zID=' + zoneId),
			title: 'Confirm Zone Delete',
			content: 'Are you sure you want to delete this zone?',
			errorMessage: 'This zone could not be deleted.'
		});
	});
	
	$('.insertIcon').live('click', function (){
		var $td1 = $('<td></td>').append($('select[name=zone_country_id]').clone(true).attr('name', 'zone_country_id[]'));
		var $td2 = $('<td></td>').append($('select[name=zone_id]').clone(true).attr('name', 'zone_id[]'));
		var $td3 = $('<td></td>').attr('align', 'right').append('<a class="ui-icon ui-icon-closethick deleteIcon"></a>');
		var $newTr = $('<tr></tr>').append($td1).append($td2).append($td3);
		$(this).parent().parent().parent().parent().find('tbody').append($newTr);
	});
	
	$('.deleteIcon').live('click', function (){
		$(this).parent().parent().remove();
	});
	
	$('select[name="zone_country_id[]"]').live('change', function (){
		var $zoneDrop = $(this).parent().parent().find('select[name="zone_id[]"]');
		$zoneDrop.find('option').remove();
		var dropMenu = $zoneDrop.get(0);
		if (zones[$(this).val()]){
			$.each(zones[$(this).val()], function (i, el){
				dropMenu.options[i] = new Option(el[1], el[0]);
			});
		}else{
			dropMenu.options[0] = new Option('All Zones', "");
		}
	});
});