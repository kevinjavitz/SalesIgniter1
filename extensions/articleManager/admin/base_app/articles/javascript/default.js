$(document).ready(function (){
	$('.gridBody > .gridBodyRow').click(function (){
		if ($(this).hasClass('state-active')) return;

		$('.gridButtonBar').find('button').button('enable');
	});

	$('.newButton, .editButton').click(function (){
		var articleId = $('.gridBodyRow.state-active').attr('data-article_id');
		if ($(this).hasClass('newButton')){
			js_redirect(js_app_link('appExt=articleManager&app=articles&appPage=new'));
		}else{
			js_redirect(js_app_link('appExt=articleManager&app=articles&appPage=new&aID=' + articleId));
		}
	});

	$('.deleteButton').click(function (e){
		e.stopPropagation();
		var $thisRow = $('.gridBodyRow.state-active');
		var articleId = $('.gridBodyRow.state-active').attr('data-article_id');
		var articleName = jQuery.trim($thisRow.find('.articleListing-name').html());
		$('<div></div>').dialog({
			resizable: false,
			allowClose: false,
			height:180,
			modal: true,
			title: 'Confirm Delete ( ' + articleName + ' )',
			open: function (){
				var $dialogEl = $(this);
				showAjaxLoader($dialogEl, 'large');
				$.ajax({
					cache: false,
					url: js_app_link('appExt=articleManager&app=articles&appPage=default&action=loadWindow&windowName=deleteArticle&aID=' + articleId),
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
					var selectedItems = '';
					$("input[name='article_topics[]']:checked").each(function() {selectedItems = selectedItems + '&article_topics[]='+ $(this).val();});
					$.ajax({
						cache: false,
						url: js_app_link('appExt=articleManager&app=articles&appPage=default&action=deleteArticleConfirm'),
						dataType: 'json',
						type: 'POST',
						data: 'articles_id=' + articleId +selectedItems,
						success: function (data){
							if (data.success == true){
								$thisRow.remove();
							}else{
								alert('Article Was Not Deleted.');
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