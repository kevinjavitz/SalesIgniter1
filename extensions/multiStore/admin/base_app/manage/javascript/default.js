$(document).ready(function (){
	$('.deleteButton').click(function (e){
		e.stopPropagation();
		var self = this;
		var $thisRow = $(this).parent().parent();
		showAjaxLoader($thisRow, 'small');
		$('<div></div>').dialog({
			resizable: false,
			allowClose: false,
			height:180,
			modal: true,
			title: 'Confirm Delete',
			open: function (){
				$(this).html('Are you sure you want to delete this store and all its information?');
			},
			buttons: {
				Confirm: function() {
					var dialogEl = this;
					$.ajax({
						cache: false,
						url: js_app_link('appExt=multiStore&app=manage&appPage=default&action=deleteConfirm'),
						dataType: 'json',
						type: 'POST',
						data: 'store_id=' + $(self).attr('data-store_id'),
						success: function (data){
							removeAjaxLoader($thisRow);
							if (data.success == true){
								$thisRow.remove();
							}else{
								alert('Store Was Not Deleted.');
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