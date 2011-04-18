$(document).ready(function (){
	$('.ui-icon-plusthick').live('click', function (){
		$(this).removeClass('ui-icon-plusthick').addClass('ui-icon-minusthick').removeClass('ui-icon-green').addClass('ui-icon-red');
		
		if ($('.view_info_' + $(this).data('stream_id')).size() > 0){
			$('.view_info_' + $(this).data('stream_id')).show();
			$(this).parent().parent().addClass('infoShown');
		}else{
			var getVars = [];
			getVars.push('appExt=royaltiesSystem');
			getVars.push('app=account_addon');
			getVars.push('appPage=view_royalties');
			getVars.push('action=getViews');
			getVars.push('sID=' + $(this).data('stream_id'));
			getVars.push('cID=' + $(this).data('customer_id'));
		
			var $icon = $(this);
			showAjaxLoader($icon.parent().parent(), 'small');
			$.ajax({
				url: js_app_link(getVars.join('&')),
				cache: false,
				dataType: 'html',
				success: function (data){
					removeAjaxLoader($icon.parent().parent());
					$icon.parent().parent().addClass('infoShown');
					$(data).insertAfter($icon.parent().parent());
				}
			});
		}
	});
	
	$('.ui-icon-minusthick').live('click', function (){
		$(this).removeClass('ui-icon-minusthick').addClass('ui-icon-plusthick').removeClass('ui-icon-red').addClass('ui-icon-green');
		$(this).parent().parent().removeClass('infoShown');
		
		$('.view_info_' + $(this).data('stream_id')).hide();
	});
});