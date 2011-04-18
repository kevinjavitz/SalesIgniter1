function showOptionEntry(el){
	if (el.value != 'select'){
		$('#selectOptions').hide();
	}else{
		$('#selectOptions').show();
	}
}

function makeGroupDroppable($el){
	$el.each(function (){
		var $groupBox = $(this);
		$(this).droppable({
			accept: '.draggableField',
			hoverClass: 'ui-state-highlight',
			drop: function (e, ui){
				var $this = $(this);
				$.ajax({
					cache: false,
					url: js_app_link('appExt=customFields&app=manage&appPage=default&action=addFieldToGroup&group_id=' + $this.attr('group_id') + '&field_id=' + $('.fieldName', ui.draggable).attr('field_id')),
					dataType: 'json',
					beforeSend: function (){
						showAjaxLoader($this, 'xlarge');
					},
					complete: function (){
						hideAjaxLoader($this);
					},
					success: function (data){
						if (data.success == true){
							var $newLi = $('<li></li>')
							.attr('id', 'field_' + $('.fieldName', ui.draggable).attr('field_id'))
							.css('font-size', '.8em')
							.html($('.fieldName', ui.draggable).html());
							$newLi.hover(function (){
								this.style.cursor = 'move';
							}, function (){
								this.style.cursor = 'default';
							});
							$('ul', $this).append($newLi);
							$('.sortableList', $this).sortable('refresh');
						}else{
							alert('That field already belongs to this group');
						}
					}
				});
			}
		});
	});
}

function makeTrashBinDroppable($el){
	$el.each(function (){
		$(this).droppable({
			accept: 'li',
			hoverClass: 'ui-state-highlight',
			drop: function (e, ui){
				var $this = $(this);
				$(ui.draggable).remove();
				$.ajax({
					url: js_app_link('appExt=customFields&app=manage&appPage=default&action=removeFieldFromGroup&group_id=' + $(this).parent().attr('group_id') + '&field_id=' + $(ui.draggable).attr('id')),
					cache: false,
					beforeSend: function (){
						showAjaxLoader($this.parent(), 'xlarge');
					},
					complete: function (){
						hideAjaxLoader($this.parent());
					},
					dataType: 'json',
					success: function (){
						$('.sortableList', $this).sortable('refresh');
					}
				});
			}
		});
	});
}

function makeListSortable($el){
	$el.each(function (){
		var $this = $(this);
		$(this).sortable({
			containment: $(this).parent(),
			cursor: 'move',
			items: 'li',
			opacity: .5,
			revert: true,
			stop: function (e, ui){
				$.ajax({
					url: js_app_link('appExt=customFields&app=manage&appPage=default&action=updateFieldSortOrder&group_id=' + $(this).parent().attr('group_id') + '&' + $(this).sortable('serialize')),
					beforeSend: function (){
						showAjaxLoader($this.parent(), 'xlarge');
					},
					complete: function (){
						hideAjaxLoader($this.parent());
					},
					cache: false,
					dataType: 'json',
					success: function (){
					}
				});
			}
		});

		$('li', $this).hover(function (){
			this.style.cursor = 'move';
		}, function (){
			this.style.cursor = 'default';
		});
	});
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
					var self = this;
					$(self).html('<div class="ui-ajax-loader ui-ajax-loader-xlarge" style="margin-left:auto;margin-right:auto;"></div>');
					$.ajax({
						url: $icon.attr('href'),
						cache: false,
						dataType: 'html',
						success: function (editData){
							fieldId = $(editData).attr('field_id');
							$(self).html(editData);
						}
					});
				},
				buttons: {
					'Save': function (){
						var self = $(this);
						showAjaxLoader($('.ui-dialog-content', self.element).parent(), 'xlarge');
						
						$.ajax({
							cache: false,
							url: js_app_link('appExt=customFields&app=manage&appPage=default&action=saveField&fID=' + fieldId),
							data: $('.ui-dialog-content *', self.element).serialize(),
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
								self.dialog('destroy');
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

function makeGroupDeleteable($el){
	$el.each(function (){
		$(this).click(function (e){
			e.preventDefault();
			var $icon = $(this);
			$($icon.parent()).droppable('disable');
			var confirmation = confirm('Are you sure you want to delete this group?');
			if (confirmation){
				$.ajax({
					url: $icon.attr('href'),
					beforeSend: function (){
						showAjaxLoader($icon.parent(), 'xlarge');
					},
					cache: false,
					dataType: 'json',
					success: function (data){
						if (data.success == true){
							hideAjaxLoader($icon.parent());
							$icon.parent().remove();
						}
					}
				});
			}else{
				$($icon.parent()).droppable('enable');
			}
			return false;
		});
	});
}

function makeGroupEditable($el){
	$el.each(function (){
		var $icon = $(this);
		var groupId;

		$icon.click(function (e){
			e.preventDefault();
			showAjaxLoader($icon.parent(), 'xlarge');
			$($icon.parent()).droppable('disable');

			$('<div></div>').dialog({
				autoOpen: true,
				title: 'Edit Group',
				position: 'top',
				close: function (){
					if ($icon.parent().data('ajaxOverlay')){
						hideAjaxLoader($icon.parent());
					}
					$($icon.parent()).droppable('enable');
					$(this).dialog('destroy');
				},
				open: function (e, ui){
					var self = this;
					$(self).html('<div class="ui-ajax-loader ui-ajax-loader-xlarge" style="margin-left:auto;margin-right:auto;"></div>');
					$.ajax({
						url: $icon.attr('href'),
						cache: false,
						dataType: 'json',
						success: function (editData){
							$(self).html(editData.html);
							groupId = editData.group_id;
						}
					});
				},
				buttons: {
					'Save': function (){
						var self = $(this);
						showAjaxLoader($('.ui-dialog-content', self.element).parent(), 'xlarge');
						
						$.ajax({
							cache: false,
							url: js_app_link('appExt=customFields&app=manage&appPage=default&action=saveGroup&gID=' + groupId),
							data: $('.ui-dialog-content *', self.element).serialize(),
							type: 'post',
							dataType: 'json',
							success: function (saveData){
								$('b:first', $icon.parent()).html(saveData.group_name);
								
								removeAjaxLoader($('.ui-dialog-content', self.element).parent());
								self.dialog('destroy');
									
								hideAjaxLoader($icon.parent());
								
								$($icon.parent()).droppable('enable');
							}
						});
					},
					'Cancel': function (){
						if ($icon.parent().data('ajaxOverlay')){
							hideAjaxLoader($icon.parent());
						}
						$($icon.parent()).droppable('enable');
						$(this).dialog('destroy');
					}
				}
			});

			return false;
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
				var self = this;
				$(self).html('<div class="ui-ajax-loader ui-ajax-loader-xlarge" style="margin-left:auto;margin-right:auto;"></div>');
				$.ajax({
					cache: false,
					url: js_app_link('appExt=customFields&app=manage&appPage=default&windowAction=new&action=getFieldWindow'),
					dataType: 'html',
					success: function (data){
						$(self).html(data);
					}
				});
			},
			buttons: {
				'Save': function (){
					var self = $(this);
					$.ajax({
						cache: false,
						url: js_app_link('appExt=customFields&app=manage&appPage=default&action=saveField'),
						data: $('.ui-dialog-content *', self.element).serialize(),
						type: 'post',
						dataType: 'html',
						success: function (data){
							var $newField = $(data);
							makeBoxDraggable($newField);
							makeFieldDeleteable($('a.ui-icon-circle-close', $newField));
							makeFieldEditable($('a.ui-icon-wrench', $newField));

							$('#fieldListing').append($newField);
							self.dialog('destroy');
						}
					});
				},
				'Cancel': function (){
					$(this).dialog('destroy');
				}
			}
		});
	}).button();

	$('#newGroup').click(function (){
		$('<div></div>').dialog({
			autoOpen: true,
			title: 'Create New Group',
			position: 'top',
			open: function (e, ui){
				var self = this;
				$(self).html('<div class="ui-ajax-loader ui-ajax-loader-xlarge" style="margin-left:auto;margin-right:auto;"></div>');
				$.ajax({
					cache: false,
					url: js_app_link('appExt=customFields&app=manage&appPage=default&windowAction=new&action=getGroupWindow'),
					dataType: 'json',
					success: function (data){
						$(self).html(data.html);
					}
				});
			},
			buttons: {
				'Save': function (){
					var self = $(this);
					$.ajax({
						cache: false,
						url: js_app_link('appExt=customFields&app=manage&appPage=default&action=saveGroup'),
						data: $('.ui-dialog-content *', self.element).serialize(),
						type: 'post',
						dataType: 'html',
						success: function (data){
							var $newGroupBox = $(data);
							makeGroupDroppable($newGroupBox);
							makeTrashBinDroppable($('.trashBin', $newGroupBox));
							makeListSortable($('.sortableList', $newGroupBox));
							makeGroupDeleteable($('a.ui-icon-circle-close', $newGroupBox));
							makeGroupEditable($('a.ui-icon-wrench', $newGroupBox));

							$('#groupListing').append($newGroupBox);

							self.dialog('destroy');
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

	$('.droppableField').each(function (){
		makeGroupDroppable($(this));
		makeGroupDeleteable($('a.ui-icon-circle-close', $(this)));
		makeGroupEditable($('a.ui-icon-wrench', $(this)));
	});

	makeTrashBinDroppable($('.trashBin'));
	makeListSortable($('.sortableList'));
});