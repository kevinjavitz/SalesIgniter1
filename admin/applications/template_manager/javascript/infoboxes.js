var currentOverlayEl = null;
function appendSortableList(settings, runRefresh){
	runRefresh = runRefresh || false;
	
	var $listItem = $('<li></li>')
	.attr('id', settings.itemId)
	.html('<span class="ui-icon ui-icon-pencil" style="float:right;"></span>' + settings.text)
	.hover(function (){
		this.style.cursor = 'move';
		$(this).addClass('ui-state-highlight');
	}, function (){
		this.style.cursor = 'default';
		$(this).removeClass('ui-state-highlight');
	})
	.appendTo(settings.list);
	
	if (settings.extName){
		$listItem.attr('extName', settings.extName);
	}
	
	$('.ui-icon-pencil', $listItem).click(function (e){
		e.preventDefault();
		var self = $(this);
		currentOverlayEl = self.parent().parent().parent();
		
		showAjaxLoader(currentOverlayEl, 'xlarge');
		
		var getVars = [];
		getVars.push('template=' + $('#templateSwitcher').val());
		getVars.push('box=' + self.parent().attr('id'));
		if (self.parent().attr('extName')){
			getVars.push('extName=' + self.parent().attr('extName'));
		}
		
		$.ajax({
			url: js_app_link('app=template_manager&appPage=infoboxes&action=editInfobox&' + getVars.join('&')),
			cache: false,
			dataType: 'html',
			success: function (data){
				$('.boxListing').fadeOut(1000, function (){
					$('.editWindow').html(data).fadeIn(1000);
					$('.editWindow button').button();
				});
			}
		});
	});
	
	if (runRefresh === true){
		settings.list.sortable('refresh');
	}
}

function setupTrashBin($el){
	$el.droppable({
		accept: 'li',
		hoverClass: 'ui-state-highlight',
		drop: function (e, ui){
			var self = $(this);
			$(ui.draggable).remove();
			$.ajax({
				url: js_app_link('app=template_manager&appPage=infoboxes&action=removeBox&template=' + $('#templateSwitcher').val() + '&box=' + $(ui.draggable).attr('id')),
				cache: false,
				beforeSend: function (){
					showAjaxLoader(self, 'small');
				},
				dataType: 'json',
				success: function (){
					removeAjaxLoader(self);
					$('.sortableList', self.parent()).sortable('refresh');
				}
			});
		}
	});
}

function setupColumn($column){
	setupTrashBin($('.trashBin', $column));
	
	$column.droppable({
		accept: function (el){
			if ($(el).hasClass('draggableField') && !$(el).hasClass('notInstalled')){
				return true;
			}
			return false;
		},
		hoverClass: 'ui-state-highlight',
		drop: function (e, ui){
			var $this = $(this);
			if ($('#' + $column.attr('id') + 'Box_' + $('.ui-widget-header', ui.draggable).html(), $this).size() > 0){
				alert('This box already belongs to this column.');
				return false;
			}
				
			$.ajax({
				cache: false,
				url: js_app_link('app=template_manager&appPage=infoboxes&action=addBox'),
				dataType: 'json',
				type: 'post',
				data: 'template=' + $('#templateSwitcher').val() + '&column=' + $column.attr('id') + '&box=' + $(ui.draggable).attr('id'),
				beforeSend: function (){
					showAjaxLoader($this, 'xlarge');
				},
				complete: function (){
					$('.sortableList', $this).sortable('refresh');
					hideAjaxLoader($this);
				},
				success: function (data){
					var sortableConfig = {
						itemId: data.boxId,
						text: data.boxName,
						list: $('.sortableList', $this)
					};
					
					if (data.extName){
						sortableConfig.extName = data.extName;
					}
					
					appendSortableList(sortableConfig);
				}
			});
		}
	});
	
	$('ul', $column).sortable({
		containment: $column,
		cursor: 'move',
		items: 'li',
		opacity: .5,
		revert: true,
		stop: function (e, ui){
			$.ajax({
				url: js_app_link('app=template_manager&appPage=infoboxes&action=updateSortOrders'),
				cache: false,
				dataType: 'json',
				type: 'post',
				data: 'column=' + $column.attr('id') + '&template=' + $('#templateSwitcher').val() + '&' + $(this).sortable('serialize'),
				beforeSend: function (){
					showAjaxLoader($column, 'xlarge');
				},
				complete: function (){
					hideAjaxLoader($column);
				},
				success: function (){
				}
			});
		}
	});

	$('li', $column).hover(function (){
		this.style.cursor = 'move';
		$(this).addClass('ui-state-highlight');
	}, function (){
		this.style.cursor = 'default';
		$(this).removeClass('ui-state-highlight');
	});
}

$(document).ready(function (){
	$(window).scroll(function (){
		if ($('.leftColumn').data('ajaxOverlay')) return;
		
		var newTop = $(window).scrollTop();
		var currentTop = $('.leftColumn').offset().top;
		
		if (newTop < $('.centerColumn').offset().top){
			newTop = $('.centerColumn').offset().top;
		}
		
		if (newTop == currentTop) return;
		
		$('.leftColumn, .rightColumn').animate({
			top: newTop
		}, {
			queue:false,
			duration:250
		});
	});
	
	$('#templateSwitcher').change(function (){
		var $leftColumn = $('.leftColumn');
		var $rightColumn = $('.rightColumn');
		$.ajax({
			cache: false,
			url: js_app_link('app=template_manager&appPage=infoboxes&action=getTemplatesBoxes&template=' + $(this).val()),
			dataType: 'json',
			beforeSend: function (){
				showAjaxLoader($leftColumn, 'xlarge');
				showAjaxLoader($rightColumn, 'xlarge');
			},
			complete: function (){
				$('.sortableList', $leftColumn).sortable('refresh');
				$('.sortableList', $rightColumn).sortable('refresh');
				hideAjaxLoader($leftColumn);
				hideAjaxLoader($rightColumn);
			},
			success: function (data){
				$('.sortableList li', $leftColumn).remove();
				$('.sortableList li', $rightColumn).remove()
				
				$(data.leftColumn).each(function(idx, bInfo){
					var boxId = 'leftColumnBox_' + bInfo[0];
					
					var sortableConfig = {
						itemId: boxId,
						text: bInfo[0],
						list: $('.sortableList', $leftColumn)
					};
					
					if (bInfo[1] != ''){
						sortableConfig.extName = bInfo[1];
					}
					appendSortableList(sortableConfig);
				});
				
				$(data.rightColumn).each(function(idx, bInfo){
					var boxId = 'rightColumnBox_' + bInfo[0];
					
					var sortableConfig = {
						itemId: boxId,
						text: bInfo[0],
						list: $('.sortableList', $rightColumn)
					};
					
					if (bInfo[1] != ''){
						sortableConfig.extName = bInfo[1];
					}
					appendSortableList(sortableConfig);
				});
			}
		});
	});
	
	$('.draggableField').each(function (){
		$(this).draggable({
			revert: 'invalid',
			scroll: false,
			containment: 'document',
			helper: 'clone',
			opacity: .5
		}).hover(function (){
			this.style.cursor = 'move';
			$(this).addClass('ui-state-highlight');
		}, function (){
			this.style.cursor = 'default';
			$(this).removeClass('ui-state-highlight');
		});
	});
	
	setupColumn($('.leftColumn'));
	setupColumn($('.rightColumn'));
	$('#templateSwitcher').change();
	
	$('.dropToInstall').droppable({
		accept: function (el){
			if ($(el).hasClass('draggableField') && $(el).hasClass('notInstalled')){
				return true;
			}
			return false;
		},
		hoverClass: 'ui-state-highlight',
		drop: function (e, ui){
			var $this = $(this);
			var boxCode = $(ui.draggable).attr('id');
			
			var getVars = [];
			getVars.push('module=' + boxCode);
			
			if ($(ui.draggable).attr('extName')){
				var extName = $(ui.draggable).attr('extName');
				getVars.push('extName=' + extName);
			}
			
			$.ajax({
				cache: false,
				url: js_app_link('rType=ajax&app=modules&appPage=infoboxes&action=install&' + getVars.join('&')),
				dataType: 'json',
				beforeSend: function (){
					showAjaxLoader($this, 'xlarge');
				},
				complete: function (){
					hideAjaxLoader($this);
				},
				success: function (data){
					$(ui.draggable).removeClass('notInstalled').addClass('installed');
					var lastDraggable = $this.find('.draggableField').last();
					if (lastDraggable.size() <= 0){
						$(ui.draggable).insertBefore($this.find('.ui-helper-clearfix').last());
					}else{
						$(ui.draggable).insertAfter(lastDraggable);
					}
				}
			});
		}
	});
	
	$('.dropToUninstall').droppable({
		accept: function (el){
			if ($(el).hasClass('draggableField') && $(el).hasClass('installed')){
				return true;
			}
			return false;
		},
		hoverClass: 'ui-state-highlight',
		drop: function (e, ui){
			var $this = $(this);
			var boxCode = $(ui.draggable).attr('id');
			
			var getVars = [];
			getVars.push('module=' + boxCode);
			
			if ($(ui.draggable).attr('extName')){
				var extName = $(ui.draggable).attr('extName');
				getVars.push('extName=' + extName);
			}
			
			$.ajax({
				cache: false,
				url: js_app_link('rType=ajax&app=modules&appPage=infoboxes&action=remove&' + getVars.join('&')),
				dataType: 'json',
				beforeSend: function (){
					showAjaxLoader($this, 'xlarge');
				},
				complete: function (){
					hideAjaxLoader($this);
				},
				success: function (data){
					$(ui.draggable).removeClass('installed').addClass('notInstalled');
					var lastDraggable = $this.find('.draggableField').last();
					if (lastDraggable.size() <= 0){
						$(ui.draggable).insertBefore($this.find('.ui-helper-clearfix').last());
					}else{
						$(ui.draggable).insertAfter(lastDraggable);
					}
					
					$('#templateSwitcher').trigger('change');
				}
			});
		}
	});
	
	$('.cancelButton').live('click', function (){
		$('.editWindow').fadeOut(1000, function (){
			$('.editWindow').empty();
			$('.boxListing').fadeIn(2000, function (){
				removeAjaxLoader(currentOverlayEl);
			});
		});
	});
	
	$('.saveButton').live('click', function (){
		var box = $(this).attr('data-box');
		var boxId = $(this).attr('data-infobox_id');
		var templateName = $('#templateSwitcher').val();
		
		var postVars = [];
		postVars.push($('.editWindow *').serialize());
		if ($('.searchOptions').size() > 0){
			postVars.push($('.searchOptions').sortable('serialize'));
		}
		$.ajax({
			cache: false,
			url: js_app_link('app=template_manager&appPage=infoboxes&action=saveInfobox&template=' + templateName + '&box_id=' + boxId + '&box=' + box),
			data: postVars.join('&'),
			type: 'post',
			dataType: 'json',
			success: function (data){
				$('.editWindow').empty();
				$('.boxListing').fadeIn(2000, function (){
					removeAjaxLoader(currentOverlayEl);
				});
			}
		});
	});
});