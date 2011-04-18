$(document).ready(function (){
	$('.addDownloadIcon').click(function (){
		var $Row = $(this).parent().parent();
		var $newRow = $('<tr></tr>');
		$Row.find('td').each(function (i, el){
			var newTd = $('<td></td>');
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

				newTd.html('<span class="downloadInfoText">' + newText + '</span>').append(newElement);
				
				if (Element.hasClass('providerFileName')){
					newTd.append('<span class="ui-icon ui-icon-newwin" style="display:none;vertical-align:middle;"></span>');
				}
			}
			$newRow.append(newTd);
		});
		
		$newRow.find('td').last().html('<span class="ui-icon ui-icon-pencil editDownloadIcon"></span><span class="ui-icon ui-icon-closethick deleteDownloadIcon"></span>');
		
		$('.downloadsTable > tbody').append($newRow);
	});
	
	$('.selectDownloadProvider').live('change', function (){
		var self = this;
		var $row = $(self).parent().parent();
		
		$row.find('.providerFileName, .providerDisplayName').val('');
		
		showAjaxLoader($(self), 'small');
		$.ajax({
			cache: false,
			dataType: 'html',
			url: js_app_link('app=products&appPage=new_product&action=getProviderDownloadTypes&pID=' + $(this).val()),
			success: function (data){
				if ($row.find('.providerTypes').size() > 0){
					$('.providerTypes').html(data);
				}else{
					var newSelect = $(data);
					if ($row.find('select[name="download_provider_type_new[]"]').size() > 0){
						var curSelect = $row.find('select[name="download_provider_type_new[]"]');
					}else{
						var curSelect = $row.find('.downloadProviderType');
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

	$('.deleteDownloadIcon').live('click', function (){
		$(this).parent().parent().remove();
	});
	
	$('.editDownloadIcon').live('click', function (){
		var $row = $(this).parent().parent();
		$row.find('.downloadInfoText').hide();
		$row.find('select, input, .ui-icon-newwin').show();
		
		$(this).removeClass('editDownloadIcon').removeClass('ui-icon-pencil').addClass('ui-icon-check').addClass('saveDownload');
	});
	
	$('.saveDownload').live('click', function (){
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
					$(this).find('.downloadInfoText').html(newText).show();
					Element.hide();
					$(this).find('.ui-icon-newwin').hide();
				}
			}
		});
		
		$(this).removeClass('ui-icon-check').removeClass('saveDownload').addClass('editDownloadIcon').addClass('ui-icon-pencil');
	});
	
	$('.ui-icon-newwin').live('click', function (){
		var self = this;
		$('<div></div>').dialog({
			width: 300,
			height: 300,
			close: function (){
				$(this).remove();
			},
			open: function (){
				var dialogBox = this;
				showAjaxLoader($(dialogBox), 'large');
				$.ajax({
					cache: false,
					dataType: 'html',
					url: js_app_link('app=products&appPage=new_product&action=getProviderBrowser&pID=' + $(self).parent().parent().find('.selectDownloadProvider').val()),
					success: function (data){
						removeAjaxLoader($(dialogBox));
						$(dialogBox).html(data);
						$(dialogBox).find('.ui-icon').css({
							display: 'inline-block'
						});
						
						$(dialogBox).find('li > ul').hide();
						
						$(dialogBox).find('.ui-icon-folder-collapsed').click(function (){
							$(this).parent().find('ul').first().show();
							$(this).removeClass('ui-icon-folder-collapsed').addClass('ui-icon-folder-open');
						});
						
						$(dialogBox).find('.ui-icon-folder-open').click(function (){
							$(this).parent().find('ul').first().hide();
							$(this).removeClass('ui-icon-folder-open').addClass('ui-icon-folder-collapsed');
						});
						
						$(dialogBox).find('.providerFile').each(function (){
							var fileSpan = this;
							$(this).parent().click(function (){
								$(self).parent().find('.providerFileName').val($(fileSpan).parent().data('file_path'));
								$(dialogBox).dialog('close');
							}).mouseover(function (){
								this.style.cursor = 'pointer';
								$(this).addClass('ui-state-hover');
							}).mouseout(function (){
								this.style.cursor = 'default';
								$(this).removeClass('ui-state-hover');
							});
						});
					}
				});
			}
		});
	});
});