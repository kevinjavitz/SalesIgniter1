function showOptionEntry(el){
	if (el.value != 'select'){
		$('#selectOptions').hide();
	}else{
		$('#selectOptions').show();
	}
}

/*
 * Common Functions --BEGIN--
 */
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
/*
 * Common Functions --END--
 */

/*
 * Option Functions --BEGIN--
 */
function makeOptionEditable($el){
	$el.each(function (){
		var $icon = $(this);
		var valueId;

		$icon.click(function (e){
			e.preventDefault();

			$.ajax({
				url: $icon.attr('href'),
				cache: false,
				dataType: 'html',
				beforeSend: function (XMLHttpRequest){
					showAjaxLoader($icon.parent(), 'normal');
					$($icon.parent()).draggable('disable');
				},
				success: function (editData){
					$('<div></div>').dialog({
						autoOpen: true,
						position: 'top',
						title: 'Edit Option',
						close: function (e, ui){
							$(this).dialog('destroy');
										
							if ($icon.parent().data('ajaxOverlay')){
								hideAjaxLoader($icon.parent());
							}
							$($icon.parent()).draggable('enable');
						},
						open: function (e, ui){
							optionId = $(editData).attr('option_id');
							$('.ui-dialog-content', ui.element).html(editData);
						},
						buttons: {
							'Save': function (){
								var self = $(this);
								$.ajax({
									cache: false,
									url: js_app_link('appExt=attributes&app=manage&appPage=default&action=saveOption&oID=' + optionId, 'SSL'),
									data: $('.ui-dialog-content *', self.element).serialize(),
									type: 'post',
									dataType: 'json',
									success: function (saveData){
										$('.optionName', $icon.parent()).html(saveData.option_name);
										$('li[id="option_' + saveData.option_id + '"]').each(function (){
											$(this).html(saveData.option_name);
										});
										self.dialog('destroy');
										
										if ($icon.parent().data('ajaxOverlay')){
											hideAjaxLoader($icon.parent());
										}
										$($icon.parent()).draggable('enable');
									}
								});
							},
							'Cancel': function (e, ui){
								$(this).dialog('close');
							}
						}
					});
					$(this).data('hasDialog', true);
				}
			});
		});
	});
}

function makeOptionDeleteable($el){
	$el.each(function (){
		$(this).click(function (e){
			e.preventDefault();
			var $icon = $(this);
			$($icon.parent()).droppable('disable');
			var confirmation = confirm('Are you sure you want to delete this option?');
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
							$('li[id="option_' + data.option_id + '"]').each(function (){
								$(this).remove();
								$(this).parent().parent().sortable('refresh');
							});
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

function makeOptionDroppable($el){
	$el.each(function (){
		var $groupBox = $(this);
		$(this).droppable({
			accept: '.draggableValue',
			hoverClass: 'ui-state-highlight',
			drop: function (e, ui){
				var $this = $(this);
				$.ajax({
					cache: false,
					url: js_app_link('appExt=attributes&app=manage&appPage=default&action=addValueToOption&option_id=' + $this.attr('option_id') + '&value_id=' + $('.valueName', ui.draggable).attr('value_id'), 'SSL'),
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
							.attr('id', 'value_' + $('.valueName', ui.draggable).attr('value_id'))
							.css('font-size', '.8em')
							.html($('.valueName', ui.draggable).html());
							$newLi.hover(function (){
								this.style.cursor = 'move';
							}, function (){
								this.style.cursor = 'default';
							});
							$('ul', $this).append($newLi);
							$('.sortableList', $this).sortable('refresh');
						}else{
							alert('That value already belongs to this option');
						}
					}
				});
			}
		});
	});
}

function makeOptionTrashBinDroppable($el){
	$el.each(function (){
		$(this).droppable({
			accept: 'li',
			hoverClass: 'ui-state-highlight',
			drop: function (e, ui){
				var $this = $(this);
				var valueId = $(ui.draggable).attr('id');
				$(ui.draggable).remove();
				$.ajax({
					async: false,
					url: js_app_link('appExt=attributes&app=manage&appPage=default&action=removeValueFromOption&option_id=' + $(this).parent().attr('option_id') + '&value_id=' + valueId, 'SSL'),
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

function makeOptionListSortable($el){
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
					url: js_app_link('appExt=attributes&app=manage&appPage=default&action=updateValueSortOrder&option_id=' + $(this).parent().attr('option_id') + '&' + $(this).sortable('serialize'), 'SSL'),
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
/*
 * Option Functions --END--
 */

/*
 * Value Functions --BEGIN--
 */
function makeValueDeleteable($el){
	$el.each(function (){
		$(this).click(function (e){
			e.preventDefault();

			var $icon = $(this);
			var $container = $icon.parent();
			$container.draggable('disable');
			var confirmation = confirm('Are you sure you want to delete this value?');
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
							$('li[id="value_' + data.value_id + '"]').each(function (){
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

function makeValueEditable($el){
	$el.each(function (){
		var $icon = $(this);
		var valueId;

		$icon.click(function (e){
			e.preventDefault();

			$.ajax({
				url: $icon.attr('href'),
				cache: false,
				dataType: 'html',
				beforeSend: function (XMLHttpRequest){
					showAjaxLoader($icon.parent(), 'normal');
					$($icon.parent()).draggable('disable');
				},
				success: function (editData){
					$('<div></div>').dialog({
						autoOpen: true,
						position: 'top',
						title: 'Edit Value',
						close: function (e, ui){
							$(this).dialog('destroy');
										
							if ($icon.parent().data('ajaxOverlay')){
								hideAjaxLoader($icon.parent());
							}
							$($icon.parent()).draggable('enable');
						},
						open: function (e, ui){
							valueId = $(editData).attr('value_id');
							$('.ui-dialog-content', ui.element).html(editData);
						},
						buttons: {
							'Save': function (){
								var self = $(this);
								$.ajax({
									cache: false,
									url: js_app_link('appExt=attributes&app=manage&appPage=default&action=saveValue&vID=' + valueId, 'SSL'),
									data: $('.ui-dialog-content *', self.element).serialize(),
									type: 'post',
									dataType: 'json',
									success: function (saveData){
										$('span:first', $icon.parent()).html(saveData.value_name);
										$('li[id="value_' + saveData.value_id + '"]').each(function (){
											$(this).html(saveData.value_name);
										});
										self.dialog('destroy');
										
										if ($icon.parent().data('ajaxOverlay')){
											hideAjaxLoader($icon.parent());
										}
										$($icon.parent()).draggable('enable');
									}
								});
							},
							'Cancel': function (){
								$(this).dialog('close');
							}
						}
					});
				}
			});
		});
	});
}
/*
 * Value Functions --END--
 */

/*
 * Group Functions --BEGIN--
 */
function makeGroupDroppable($el){
	$el.each(function (){
		var $groupBox = $(this);
		$(this).droppable({
			accept: '.draggableOption',
			hoverClass: 'ui-state-highlight',
			drop: function (e, ui){
				var $this = $(this);
				$.ajax({
					cache: false,
					url: js_app_link('appExt=attributes&app=manage&appPage=default&action=addOptionToGroup&group_id=' + $this.attr('group_id') + '&option_id=' + $('.optionName', ui.draggable).attr('option_id'), 'SSL'),
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
							.attr('id', 'option_' + $('.optionName', ui.draggable).attr('option_id'))
							.css('font-size', '.8em')
							.html($('.optionName', ui.draggable).html());
							$newLi.hover(function (){
								this.style.cursor = 'move';
							}, function (){
								this.style.cursor = 'default';
							});
							$('ul', $this).append($newLi);
							$('.sortableList', $this).sortable('refresh');
						}else{
							alert('That option already belongs to this group');
						}
					}
				});
			}
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

			$.ajax({
				url: $icon.attr('href'),
				cache: false,
				dataType: 'html',
				beforeSend: function (XMLHttpRequest){
					showAjaxLoader($icon.parent(), 'xlarge');
					$($icon.parent()).droppable('disable');
				},
				success: function (editData){
					$('<div></div>').dialog({
						autoOpen: true,
						title: 'Edit Group',
						position: 'top',
						close: function (){
							hideAjaxLoader($icon.parent());
							$($icon.parent()).droppable('enable');
						},
						open: function (e, ui){
							groupId = $(editData).attr('group_id');
							$('.ui-dialog-content', ui.element).html(editData);
						},
						buttons: {
							'Save': function (){
								var self = $(this);
								$.ajax({
									cache: false,
									url: js_app_link('appExt=attributes&app=manage&appPage=default&action=saveGroup&gID=' + groupId, 'SSL'),
									data: $('*', $icon.data('dialog')).serialize(),
									type: 'post',
									dataType: 'json',
									success: function (saveData){
										$('b:first', $icon.parent()).html(saveData.group_name);
										self.dialog('destroy');
										
										if ($icon.parent().data('ajaxOverlay')){
											hideAjaxLoader($icon.parent());
										}
										$($icon.parent()).droppable('enable');
									}
								});
							},
							'Cancel': function (){
								$(this).dialog('close');
							}
						}
					});
				}
			});

			return false;
		});
	});
}

function makeGroupTrashBinDroppable($el){
	$el.each(function (){
		$(this).droppable({
			accept: 'li',
			hoverClass: 'ui-state-highlight',
			drop: function (e, ui){
				var $this = $(this);
				var optionId = $(ui.draggable).attr('id');
				$(ui.draggable).remove();
				$.ajax({
					async: false,
					url: js_app_link('appExt=attributes&app=manage&appPage=default&action=removeOptionFromGroup&group_id=' + $(this).parent().attr('group_id') + '&option_id=' + optionId, 'SSL'),
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

function makeGroupListSortable($el){
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
					url: js_app_link('appExt=attributes&app=manage&appPage=default&action=updateOptionSortOrder&group_id=' + $(this).parent().attr('group_id') + '&' + $(this).sortable('serialize'), 'SSL'),
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
/*
 * Group Functions --END--
 */

$(document).ready(function (){
	$('#newValue').click(function (){
		if ($(this).data('hasDialog')){
			$('#newValueDialog').dialog('open');
			return;
		}

		$('#newValueDialog').dialog({
			autoOpen: true,
			position: 'top',
			open: function (){
				var $self = $(this);
				$self.html('<div class="ui-ajax-loader ui-ajax-loader-xlarge" style="margin-left:auto;margin-right:auto;"></div>');
				$.ajax({
					cache: false,
					url: js_app_link('appExt=attributes&app=manage&appPage=default&windowAction=new&action=getValueWindow', 'SSL'),
					dataType: 'html',
					success: function (data){
						$self.html(data);
					}
				});
			},
			buttons: {
				'Save': function (){
					var self = this;
					$.ajax({
						cache: false,
						url: js_app_link('appExt=attributes&app=manage&appPage=default&action=saveValue', 'SSL'),
						data: $(this).find('*').serialize(),
						type: 'post',
						dataType: 'html',
						success: function (data){
							var $newValue = $(data);
							makeBoxDraggable($newValue);
							makeValueDeleteable($('a.ui-icon-circle-close', $newValue));
							//makeValueEditable($('a.ui-icon-wrench', $newValue));

							$('#valuesListing').append($newValue);
							$(self).dialog('close');
						}
					});
				},
				'Cancel': function (){
					$(this).dialog('close');
				}
			}
		});
		$(this).data('hasDialog', true)
	}).button();

	$('#newOption').click(function (){
		if ($(this).data('hasDialog')){
			$('#newOptionDialog').dialog('open');
			return;
		}

		$('#newOptionDialog').dialog({
			autoOpen: true,
			position: 'top',
			open: function (){
				var $self = $(this);
				$self.html('<div class="ui-ajax-loader ui-ajax-loader-xlarge" style="margin-left:auto;margin-right:auto;"></div>');
				$.ajax({
					cache: false,
					url: js_app_link('appExt=attributes&app=manage&appPage=default&windowAction=new&action=getOptionWindow', 'SSL'),
					dataType: 'html',
					success: function (data){
						$self.html(data);
					}
				});
			},
			buttons: {
				'Save': function (){
					var self = this;
					$.ajax({
						cache: false,
						url: js_app_link('appExt=attributes&app=manage&appPage=default&action=saveOption', 'SSL'),
						data: $(self).find('*').serialize(),
						type: 'post',
						dataType: 'html',
						success: function (data){
							var $newOption = $(data);
							makeBoxDraggable($newOption);
							makeOptionDroppable($newOption);
							makeOptionDeleteable($('a.ui-icon-circle-close', $newOption));
							//makeOptionEditable($('a.ui-icon-wrench', $newOption));

							$('#optionsListing').append($newOption);
							$(self).dialog('close');
						}
					});
				},
				'Cancel': function (){
					$(this).dialog('close');
				}
			}
		});
		$(this).data('hasDialog', true)
	}).button();

	$('#newGroup').click(function (){
		if ($(this).data('hasDialog')){
			$('#newGroupDialog').dialog('open');
			return;
		}

		$('#newGroupDialog').dialog({
			autoOpen: true,
			position: 'top',
			open: function (){
				var $self = $(this);
				$self.html('<div class="ui-ajax-loader ui-ajax-loader-xlarge" style="margin-left:auto;margin-right:auto;"></div>');
				$.ajax({
					cache: false,
					url: js_app_link('appExt=attributes&app=manage&appPage=default&windowAction=new&action=getGroupWindow', 'SSL'),
					dataType: 'html',
					success: function (data){
						$self.html(data);
					}
				});
			},
			buttons: {
				'Save': function (){
					var self = this;
					$.ajax({
						cache: false,
						url: js_app_link('appExt=attributes&app=manage&appPage=default&action=saveGroup', 'SSL'),
						data: $(self).find('*').serialize(),
						type: 'post',
						dataType: 'html',
						success: function (data){
							var $newGroupBox = $(data);
							makeGroupDroppable($newGroupBox);
							makeGroupTrashBinDroppable($('.trashBin', $newGroupBox));
							makeGroupListSortable($('.sortableList', $newGroupBox));
							makeGroupDeleteable($('a.ui-icon-circle-close', $newGroupBox));
							makeGroupEditable($('a.ui-icon-wrench', $newGroupBox));

							$('#groupsListing').append($newGroupBox);
							$(self).dialog('close');
						}
					});
				},
				'Cancel': function (){
					$(this).dialog('close');
				}
			}
		});
		$(this).data('hasDialog', true)
	}).button();

	$('.draggableValue').each(function (){
		makeBoxDraggable($(this));
		makeValueDeleteable($('a.ui-icon-circle-close', $(this)));
		makeValueEditable($('a.ui-icon-wrench', $(this)));
	});

	$('.droppableOption').each(function (){
		makeBoxDraggable($(this));
		makeOptionDroppable($(this));
		makeOptionDeleteable($('a.ui-icon-circle-close', $(this)));
		makeOptionEditable($('a.ui-icon-wrench', $(this)));
		makeOptionTrashBinDroppable($('.trashBin', $(this)));
		makeOptionListSortable($('.sortableList', $(this)));
	});

	$('.droppableGroup').each(function (){
		makeGroupDroppable($(this));
		makeGroupDeleteable($('a.ui-icon-circle-close', $(this)));
		makeGroupEditable($('a.ui-icon-wrench', $(this)));
		makeGroupTrashBinDroppable($('.trashBin', $(this)));
		makeGroupListSortable($('.sortableList', $(this)));
	});
});