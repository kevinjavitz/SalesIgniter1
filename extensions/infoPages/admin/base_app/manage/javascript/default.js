$(document).ready(function (){
	$('.actionButton').click(function (){
		$(this).parent().parent().parent().find('.ui-state-active').removeClass('ui-state-active');
		$(this).parent().parent().addClass('ui-state-active');
		showAjaxLoader($('.gridHolder'), 'xlarge');
		$.ajax({
			cache: false,
			url: js_app_link('appExt=infoPages&app=manage&appPage=default&action=' + $(this).attr('action')),
			dataType: 'html',
			success: function (htmlData){
				var $html = $(htmlData);
				$html.find('.ui-button').button();
				$('.gridHolder').html($html);
				
				removeAjaxLoader($('.gridHolder'));
			}
		});
	});
	
	$('.deleteButton').live('click', function (e){
		e.stopPropagation();
		var $thisRow = $(this).parent().parent();
		var pageId = $(this).attr('data-page_id');
		showAjaxLoader($thisRow, 'small');
		$('<div></div>').dialog({
			resizable: false,
			allowClose: false,
			height:180,
			modal: true,
			title: 'Confirm Delete',
			open: function (){
				$(this).html('Are you sure you want to delete this content entry?');
			},
			buttons: {
				Confirm: function() {
					var dialogEl = this;
					$.ajax({
						cache: false,
						url: js_app_link('appExt=infoPages&app=manage&appPage=default&action=deleteConfirm'),
						dataType: 'json',
						type: 'POST',
						data: 'page_id=' + pageId,
						success: function (data){
							removeAjaxLoader($thisRow);
							if (data.success == true){
								$thisRow.remove();
							}else{
								alert('Content Entry Was Not Deleted.');
							}
							$(dialogEl).dialog('close').remove();
						}
					});
				},
				Cancel: function() {
					$(this).dialog('close').remove();
				}
			}
		});
	});
});