$(document).ready(function (){
	$('.insertButton').click(function (){
		$('<div></div>').dialog({
			title: 'New Word/Phrase',
			open: function (){
				$(this).html('Original Word: <input type="text" name="original_word" /><br />Replace With: <input type="text" name="replacement_word" />');
			},
			buttons: {
				'Save': function (){
					var $dialog = $(this);
					showAjaxLoader($dialog, 'large');
					$.ajax({
						cache: false,
						url: js_app_link(js_get_all_get_params(['action']) + 'action=saveSearchWord'),
						dataType: 'json',
						type: 'POST',
						data: $('input[name=original_word], input[name=replacement_word]').serialize(),
						success: function (data){
							removeAjaxLoader($dialog);
							$dialog.dialog('close').remove();
							js_redirect(js_app_link('app=statistics&appPage=keywords&wordList=1'));
						}
					});
				},
				'Cancel': function (){
					$(this).dialog('close').remove();
				}
			}
		});
	});
	
	$('.editButton').click(function (){
		var $selfButton = $(this);
		$('<div></div>').dialog({
			title: 'Edit Word/Phrase',
			open: function (){
				$(this).html('Original Word: <input type="text" name="original_word" value="' + $selfButton.parent().parent().find('td:eq(0)').html() + '" /><br />Replace With: <input type="text" name="replacement_word" value="' + $selfButton.parent().parent().find('td:eq(1)').html() + '" />');
			},
			buttons: {
				'Save': function (){
					var $dialog = $(this);
					showAjaxLoader($dialog, 'large');
					$.ajax({
						cache: false,
						url: js_app_link(js_get_all_get_params(['action']) + 'action=saveSearchWord&word_id=' + $selfButton.attr('data-word_id')),
						dataType: 'json',
						type: 'POST',
						data: $('input[name=original_word], input[name=replacement_word]').serialize(),
						success: function (data){
							removeAjaxLoader($dialog);
							$dialog.dialog('close').remove();
							js_redirect(js_app_link('app=statistics&appPage=keywords&wordList=1'));
						}
					});
				},
				'Cancel': function (){
					$(this).dialog('close').remove();
				}
			}
		});
	});
	
	$('.deleteButton').click(function (){
		var $selfButton = $(this);
		$('<div></div>').dialog({
			autoOpen: true,
			width: 300,
			modal: true,
			resizable: false,
			allowClose: false,
			title: 'Delete Word/Phrase Confirm',
			open: function (e){
				$(this).html('Are you sure you want to delete this word/phrase?');
			},
			close: function (){
				$(this).dialog('destroy').remove();
			},
			buttons: {
				'Delete Product': function(){
					js_redirect(js_app_link(js_get_all_get_params(['action']) + 'action=deleteSearchWord&wordId=' + $selfButton.attr('data-word_id')));
				},
				'Don\'t Delete': function(){
					$(this).dialog('close');
				}
			}
		});
		return false;
	});
});