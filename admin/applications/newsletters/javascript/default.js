$(document).ready(function (){
	$('.gridBody > .gridBodyRow').click(function (){
		if ($(this).hasClass('state-active')) return;

		$('.gridButtonBar').find('button').button('disable');
		
		$('.gridButtonBar').find('.newButton').button('enable');
		if ($(this).attr('data-locked') == 'true'){
			$('.gridButtonBar').find('.editButton').button('enable');
			$('.gridButtonBar').find('.deleteButton').button('enable');
			$('.gridButtonBar').find('.previewButton').button('enable');
			$('.gridButtonBar').find('.sendButton').button('enable');
			$('.gridButtonBar').find('.unlockButton').button('enable');
		}else{
			$('.gridButtonBar').find('.previewButton').button('enable');
			$('.gridButtonBar').find('.lockButton').button('enable');
		}
	});

	$('.newButton, .editButton, .previewButton, .sendButton, .unlockButton, .lockButton').click(function (){
		if ($(this).hasClass('newButton')){
			js_redirect(js_app_link('app=newsletters&appPage=new'));
		}else{
			var newsletterId = $('.gridBodyRow.state-active').attr('data-newsletter_id');
		}

		if ($(this).hasClass('editButton')){
			js_redirect(js_app_link('app=newsletters&appPage=new&nID=' + newsletterId));
		}else if ($(this).hasClass('previewButton')){
			js_redirect(js_app_link('app=newsletters&appPage=preview&nID=' + newsletterId));
		}else if ($(this).hasClass('sendButton')){
			js_redirect(js_app_link('app=newsletters&appPage=send&nID=' + newsletterId));
		}else if ($(this).hasClass('unlockButton')){
			js_redirect(js_app_link('app=newsletters&appPage=default&action=unlock&nID=' + newsletterId));
		}else if ($(this).hasClass('lockButton')){
			js_redirect(js_app_link('app=newsletters&appPage=default&action=lock&nID=' + newsletterId));
		}
	});
	
	$('.deleteButton').click(function (){
		var newsletterId = $('.gridBodyRow.state-active').attr('data-newsletter_id');
		confirmDialog({
			confirmUrl: js_app_link('app=newsletters&appPage=default&action=deleteConfirm&nID=' + newsletterId),
			title: 'Confirm Newsletter Delete',
			content: 'Are you sure you want to delete this newsletter?',
			errorMessage: 'This newsletter could not be deleted.',
			success: function (){
				js_redirect(js_app_link('app=newsletters&appPage=default'));
			}
		});
	});
});