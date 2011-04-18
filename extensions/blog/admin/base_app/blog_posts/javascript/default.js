
$(document).ready(function (){
	$('.deletePostButton').click(function (){
		var $selfButton = $(this);
		$('<div></div>').dialog({
			autoOpen: true,
			width: 300,
			modal: true,
			resizable: false,
			allowClose: false,
			title: 'Delete Product Confirm',
			open: function (e){
				$(e.target).html('Are you sure you want to delete this product?');
			},
			close: function (){
				$(this).dialog('destroy');
			},
			buttons: {
				'Delete Post': function(){
					window.location = js_app_link('appExt=blog&app=blog_posts&appPage=default&action=deletePostConfirm&post_id=' + $selfButton.attr('post_id'));
				},
				'Don\'t Delete': function(){
					$(this).dialog('destroy');
				}
			}
		});
		return false;
	});

});