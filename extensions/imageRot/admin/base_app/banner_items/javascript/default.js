
$(document).ready(function (){
	$('.deleteBannerButton').click(function (){
		var $selfButton = $(this);
		$('<div></div>').dialog({
			autoOpen: true,
			width: 300,
			modal: true,
			resizable: false,
			allowClose: false,
			title: 'Delete Banner Confirm',
			open: function (e){
				$(e.target).html('Are you sure you want to delete this banner?');
			},
			close: function (){
				$(this).dialog('destroy');
			},
			buttons: {
				'Delete Post': function(){
					window.location = js_app_link('appExt=imageRot&app=banner_items&appPage=default&action=deleteBannerConfirm&banners_id=' + $selfButton.attr('banners_id'));
				},
				'Don\'t Delete': function(){
					$(this).dialog('destroy');
				}
			}
		});
		return false;
	});

});