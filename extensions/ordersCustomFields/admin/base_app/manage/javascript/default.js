function showOptionEntry(el){
	if (el.value != 'select' && el.value != 'select_other'){
		$('#selectOptions').hide();
	}else{
		$('#selectOptions').show();
	}
}

function makeBoxDraggable($el){
	$el.each(function (){
		$(this).draggable({
			revert: 'invalid',
			scroll: false,
			containment: 'document',
			helper: 'clone',
			opacity: .5
		}).hover(function (){
			this.style.cursor = 'move';
		}, function (){
			this.style.cursor = 'default';
		});
	});
}

function makeFieldDeleteable($el){
	$el.each(function (){
		$(this).click(function (e){
			e.preventDefault();

			var $icon = $(this);
			var $container = $icon.parent();
			$container.draggable('disable');
			var confirmation = confirm('Are you sure you want to delete this field?');
			if (confirmation){
				$.ajax({
					url: $icon.attr('href'),
					cache: false,
					beforeSend: function (){
						showAjaxLoader($icon.parent(), 'normal');
					},
					dataType: 'json',
					success: function (data){
						if (data.success == true){
							hideAjaxLoader($icon.parent());
							$container.remove();
							$('li[id="field_' + data.field_id + '"]').each(function (){
								$(this).remove();
								$(this).parent().parent().sortable('refresh');
							});
						}else{
							$($icon.parent()).draggable('enable');
						}
					}
				});
			}else{
				$($icon.parent()).draggable('enable');
			}
			return false;
		});
	});
}

function makeFieldEditable($el){
	$el.each(function (){
		var $icon = $(this);
		var fieldId;

		$icon.click(function (e){
			e.preventDefault();
			showAjaxLoader($icon.parent(), 'normal');
			$($icon.parent()).draggable('disable');

			$('<div></div>').dialog({
				autoOpen: true,
				title: 'Edit Field',
				position: 'top',
				close: function (){
					if ($icon.parent().data('ajaxOverlay')){
						hideAjaxLoader($icon.parent());
					}
					$($icon.parent()).draggable('enable');
					$(this).dialog('destroy');
				},
				open: function (e, ui){
					var $el = $(this);
					$el.html('<div class="ui-ajax-loader ui-ajax-loader-xlarge" style="margin-left:auto;margin-right:auto;"></div>');
					$.ajax({
						url: $icon.attr('href'),
						cache: false,
						dataType: 'html',
						success: function (editData){
							fieldId = $(editData).attr('field_id');
							$el.html(editData);
						}
					});
				},
				buttons: {
					'Save': function (){
						var self = this;
						showAjaxLoader($(self).parent(), 'xlarge');
						
						$.ajax({
							cache: false,
							url: js_app_link('appExt=ordersCustomFields&app=manage&appPage=default&action=saveField&fID=' + fieldId),
							data: $('*', self).serialize(),
							type: 'post',
							dataType: 'html',
							success: function (data){
								var $newField = $(data);
								makeBoxDraggable($newField);
								makeFieldDeleteable($('a.ui-icon-circle-close', $newField));
								makeFieldEditable($('a.ui-icon-wrench', $newField));

								hideAjaxLoader($('.ui-dialog-content', self.element).parent());
								hideAjaxLoader($icon.parent());
								
								$icon.parent().replaceWith($newField);
								$(self).dialog('destroy');
							}
						});
					},
					'Cancel': function (){
						if ($icon.parent().data('ajaxOverlay')){
							hideAjaxLoader($icon.parent());
						}
						$($icon.parent()).draggable('enable');
						$(this).dialog('destroy');
					}
				}
			});
		});
	});
}

$(document).ready(function (){
	$('#newField').click(function (){
		$('<div></div>').dialog({
			autoOpen: true,
			title: 'Create New Field',
			position: 'top',
			open: function (e, ui){
				var $el = $(this);
				$el.html('<div class="ui-ajax-loader ui-ajax-loader-xlarge" style="margin-left:auto;margin-right:auto;"></div>');
				$.ajax({
					cache: false,
					url: js_app_link('appExt=ordersCustomFields&app=manage&appPage=default&windowAction=new&action=getFieldWindow'),
					dataType: 'html',
					success: function (data){
						$el.html(data);
					}
				});
			},
			buttons: {
				'Save': function (){
					var dial = this;					
					$.ajax({
						cache: false,
						url: js_app_link('appExt=ordersCustomFields&app=manage&appPage=default&action=saveField'),
						data: $('*', dial).serialize(),
						type: 'post',
						dataType: 'html',
						success: function (data){
							var $newField = $(data);
							makeBoxDraggable($newField);
							makeFieldDeleteable($('a.ui-icon-circle-close', $newField));
							makeFieldEditable($('a.ui-icon-wrench', $newField));

							$('#fieldListing').append($newField);
							$(dial).dialog('destroy');
						}
					});
				},
				'Cancel': function (){
					$(this).dialog('destroy');
				}
			}
		});
	}).button();

	$('.draggableField').each(function (){
		makeBoxDraggable($(this));
		makeFieldDeleteable($('a.ui-icon-circle-close', $(this)));
		makeFieldEditable($('a.ui-icon-wrench', $(this)));
	});
});