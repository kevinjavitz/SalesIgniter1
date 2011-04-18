$(document).ready(function (){
	$('.addStreamIcon').click(function (){
		var $Row = $(this).parent().parent();
		var $newRow = $('<tr></tr>');
		$Row.find('td').each(function (i, el){
			var newTd = $('<td></td>');
			if ($(this).hasClass('previewBoxCol')){
				newTd.append('<input type="checkbox" class="previewStreamSetting" name="preview_stream_new[]" value="1">');
			}else{
				if ($(this).attr('align')){
					newTd.attr('align', $(this).attr('align'));
				}
				if ($(this).find('select').size() > 0 || $(this).find('input').size() > 0){
					if ($(this).find('select').size() > 0){
						var Element = $(this).find('select:eq(0)');
						var newText = Element.find('option:selected').html();
						var newValue = Element.find('option:selected').val();
					}else{
						var Element = $(this).find('input:eq(0)');
						var newText = Element.val();
						var newValue = Element.val();
					}
			
					var oldName = Element.attr('name');
					var newName = oldName.replace('new_', '');
				
					var newElement = Element.clone();
					newElement.attr('name', newName + '_new[]').val(newValue).hide();

					newTd.html('<span class="streamInfoText">' + newText + '</span>').append(newElement);
				}
			}
			$newRow.append(newTd);
		});
		
		$newRow.find('td').last().html('<span class="ui-icon ui-icon-pencil editStreamIcon"></span><span class="ui-icon ui-icon-closethick deleteStreamIcon"></span>');
		
		$('.streamsTable > tbody').append($newRow);
	});
	
	$('.selectStreamProvider').live('change', function (){
		var self = this;
		var $row = $(self).parent().parent();
		
		showAjaxLoader($(self), 'small');
		$.ajax({
			cache: false,
			dataType: 'html',
			url: js_app_link('app=products&appPage=new_product&action=getProviderStreamTypes&pID=' + $(this).val()),
			success: function (data){
				if ($row.find('.providerTypes').size() > 0){
					$('.providerTypes').html(data);
				}else{
					var newSelect = $(data);
					if ($row.find('select[name="stream_provider_type_new[]"]').size() > 0){
						var curSelect = $row.find('select[name="stream_provider_type_new[]"]');
					}else{
						var curSelect = $row.find('.streamProviderType');
					}
					$('option', curSelect).remove();
					$('option', newSelect).each(function (){
						curSelect.append(this);
					});
				}
				removeAjaxLoader($(self));
			}
		});
	});

	$('.deleteStreamIcon').live('click', function (){
		$(this).parent().parent().remove();
	});
	
	$('.editStreamIcon').live('click', function (){
		var $row = $(this).parent().parent();
		$row.find('.streamInfoText').hide();
		$row.find('select, input').show();
		
		$(this).removeClass('editStreamIcon').removeClass('ui-icon-pencil').addClass('ui-icon-check').addClass('saveStream');
	});
	
	$('.saveStream').live('click', function (){
		var $Row = $(this).parent().parent();
		$Row.find('td').each(function (){
			if ($(this).find('select').size() > 0 || $(this).find('input').size() > 0){
				if ($(this).find('select').size() > 0){
					var Element = $(this).find('select:eq(0)');
					var newText = Element.find('option:selected').html();
				}else{
					var Element = $(this).find('input:eq(0)');
					var newText = Element.val();
				}
			
				if (!Element.hasClass('noHide')){
					$(this).find('.streamInfoText').html(newText).show();
					Element.hide();
				}
			}
		});
		
		$(this).removeClass('ui-icon-check').removeClass('saveStream').addClass('editStreamIcon').addClass('ui-icon-pencil');
	});
	
	$('.previewStreamSetting').live('click', function(){
		var self = this;
		$('.previewStreamSetting').each(function (){
			if (this != self){
				this.checked = false;
			}
		});
	});
});