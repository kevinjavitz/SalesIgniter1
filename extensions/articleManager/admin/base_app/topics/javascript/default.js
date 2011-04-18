$(document).ready(function (){
	$('.gridBody > .gridBodyRow').click(function (){
		if ($(this).hasClass('state-active')) return;

		$('.gridButtonBar').find('button').button('enable');
	});

	$('.newButton, .newChildButton, .editButton').click(function (){
		var topicId = $('.gridBodyRow.state-active').attr('data-topic_id');
		if ($(this).hasClass('newButton')){
			js_redirect(js_app_link('appExt=articleManager&app=topics&appPage=newTopic'));
		}else if ($(this).hasClass('newChildButton')){
			js_redirect(js_app_link('appExt=articleManager&app=topics&appPage=newTopic&parent_id=' + topicId));
		}else{
			js_redirect(js_app_link('appExt=articleManager&app=topics&appPage=newTopic&tID=' + topicId));
		}
	});

	$('.deleteButton').click(function (e){
		e.stopPropagation();
		var $thisRow = $('.gridBodyRow.state-active');
		var topicId = $('.gridBodyRow.state-active').attr('data-topic_id');
		var topicName = jQuery.trim($thisRow.find('.topicListing-name').html());
		$('<div></div>').dialog({
			resizable: false,
			allowClose: false,
			height:180,
			modal: true,
			title: 'Confirm Delete ( ' + topicName + ' )',
			open: function (){
				var $dialogEl = $(this);
				showAjaxLoader($dialogEl, 'large');
				$.ajax({
					cache: false,
					url: js_app_link('appExt=articleManager&app=topics&appPage=default&action=loadWindow&windowName=deleteTopic&tID=' + topicId),
					dataType: 'html',
					success: function (data){
						removeAjaxLoader($dialogEl);
						$dialogEl.html(data);
					}
				});
			},
			buttons: {
				Confirm: function() {
					var dialogEl = this;
					$.ajax({
						cache: false,
						url: js_app_link('appExt=articleManager&app=topics&appPage=default&action=deleteTopicConfirm'),
						dataType: 'json',
						type: 'POST',
						data: 'topics_id=' + topicId,
						success: function (data){
							if (data.success == true){
								$thisRow.remove();
							}else{
								alert('Topic Was Not Deleted.');
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