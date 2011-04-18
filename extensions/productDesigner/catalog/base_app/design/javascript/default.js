var _GLOBALS = [];
_GLOBALS['curFontSize'] = 2;
_GLOBALS['curFontSizePx'] = 0;
_GLOBALS['zoomVal'] = .5;
_GLOBALS['editableWidth'] = 0;
_GLOBALS['editableHeight'] = 0;
_GLOBALS['scale'] = 1;
_GLOBALS['currentItem'] = null;
_GLOBALS['placementArrowNorth'] = '.placementArrowNorth';
_GLOBALS['placementArrowSouth'] = '.placementArrowSouth';
_GLOBALS['placementArrowEast'] = '.placementArrowEast';
_GLOBALS['placementArrowWest'] = '.placementArrowWest';
_GLOBALS['zIndexArrowNorth'] = '.zIndexArrowNorth';
_GLOBALS['zIndexArrowSouth'] = '.zIndexArrowSouth';
_GLOBALS['highestZindex'] = 55;
_GLOBALS['ppi'] = 72;

/*Clipart*/
var selected_image = '';
var last_selected_image = null;
function catclick(cat){
	showAjaxLoader($('#clipartimages'),'xlarge');
	//alert(cat);
	$.ajax({
		url: js_catalog_app_link('appExt=productDesigner&app=design&appPage=default&action=getCategoryImages'),
		data: 'cID=' + cat,
		type: 'post',
		cache: false,
		dataType: 'json',
		success: function (content){
			//alert(content.data);
			//alert($('#clipartimages').html());
			$('#clipartimages').html(content.data);
			hideAjaxLoader($('#clipartimages'));
		}
	});
}

function setSelected(obj, cat, image){
	var t = $(obj);
	selected_image = t.children().attr('file');
	if (last_selected_image != null)
		last_selected_image.css('border','');
	t.css('border','1px solid red');
	last_selected_image = t;
	//alert(selected_image);
	return false;
}

function buildClipartImageLink(o){
	var getVars = [];
	getVars.push('img=CLIPART');
	getVars.push('file=' + o.imageSrc);
	if (o.fileDir){
		getVars.push('fileDir=' + o.fileDir);
		//addNoCalc = false;
	}
	
	var addNoCalc = true;
	if (o.imageWidth){
		getVars.push('w=' + o.imageWidth);
		//addNoCalc = false;
	}
	if (o.imageHeight){
		getVars.push('h=' + o.imageHeight);
		//addNoCalc = false;
	}
	
	if (o.clipartVariable){
		getVars.push('clipartVariable=' + o.clipartVariable);
	}
	
	if (addNoCalc == true){
		getVars.push('noCalc=true');
	}
	
	getVars.push('zoom=' + _GLOBALS['zoomVal']);
	getVars.push('scale=' + _GLOBALS['scale']);
	getVars.push('sid=' + Math.round(Math.random() * 999999));
/* @TODO: figure out how to use js_app_link ( maybe js_catalog_app_link - may already exist ) */
	//return 'extensions/productDesigner/catalog/thumb_image.php?' + getVars.join('&');
	//return DIR_WS_CATALOG + 'extensions/productDesigner/catalog/base_app/thumb_image/app.php?' + getVars.join('&');
	return js_catalog_app_link('appExt=productDesigner&app=thumb_image&appPage=process&' + getVars.join('&'));
}
/*Clipart*/

function buildTextLink(o){
	var getVars = [];
	getVars.push('noCalc=true');
	getVars.push('img=TEXT');
	getVars.push('fontSize=' + o.fontSize);
	getVars.push('fontFamily=' + o.fontFamily);
	getVars.push('fontColor=' + o.fontColor);
	getVars.push('fontStroke=' + o.fontStroke);
	getVars.push('fontStrokeColor=' + o.fontStrokeColor);
	getVars.push('imageText=' + o.imageText);
	getVars.push('textTransform=' + o.textTransform);
	getVars.push('origZoomVal=' + o.origZoomVal);
	getVars.push('zoom=' + _GLOBALS['zoomVal']);
	getVars.push('scale=' + _GLOBALS['scale']);
	getVars.push('sid=' + Math.round(Math.random() * 999999));
	//return 'extensions/productDesigner/catalog/thumb_image.php?' + getVars.join('&');
	//return '/rentalstore2/productDesigner/thumb_image/process.php?' + getVars.join('&');
	return js_catalog_app_link('appExt=productDesigner&app=thumb_image&appPage=process&' + getVars.join('&'));
	//return DIR_WS_CATALOG + 'extensions/productDesigner/catalog/base_app/thumb_image/app.php?' + getVars.join('&');
}

function buildUploadImageLink(o){
	var getVars = [];
	getVars.push('img=IMAGE');
	getVars.push('file=' + o.imageSrc);
	if (o.fileDir){
		getVars.push('fileDir=' + o.fileDir);
		//addNoCalc = false;
	}
	
	var addNoCalc = true;
	if (o.imageWidth){
		getVars.push('w=' + o.imageWidth);
		//addNoCalc = false;
	}
	if (o.imageHeight){
		getVars.push('h=' + o.imageHeight);
		//addNoCalc = false;
	}
	
	if (addNoCalc == true){
		getVars.push('noCalc=true');
	}
	
	getVars.push('zoom=' + _GLOBALS['zoomVal']);
	getVars.push('scale=' + _GLOBALS['scale']);
	getVars.push('sid=' + Math.round(Math.random() * 999999));
	
	return js_catalog_app_link('appExt=productDesigner&app=thumb_image&appPage=process&' + getVars.join('&'));
}

function popupDialog(settings){
	$('<div></div>').dialog({
		dialogClass: 'productDesignerPopupWindow',
		resizable: false,
		autoOpen: true,
		width: settings.width || 'auto',
		height: settings.height || 'auto',
		position: settings.position || 'center',
		close: function (e, ui){
			$(e.target).dialog('destroy').remove();
		},
		open: function (e, ui){
			$('.ui-dialog-title', $(e.target).parent()).html(settings.headerText);
			if (settings.headerInfo){
				$('.ui-dialog-title', $(e.target).parent()).html(settings.headerText + '<hr style="width:365px;" />' + settings.headerInfo);
			}

			$('.ui-dialog-content', $(e.target).parent()).html(settings.body);
		},
		buttons: settings.buttons
	});
}

function adjustItemZindex($item, direction){
	var zIndex_old = $item.zIndex();
	var setHighestIndex = false;
	
	if (zIndex_old < 55 || (direction == 'down' && (zIndex_old - 1) < 55) || (direction == 'up' && zIndex_old == _GLOBALS['highestZindex'])) return;
	
	if (direction == 'up'){
		$item.zIndex(zIndex_old + 1);
	}else{
		if (_GLOBALS['highestZindex'] == zIndex_old){
			setHighestIndex = true;
			$item.zIndex(zIndex_old - 2);
		}else{
			$item.zIndex(zIndex_old - 1);
		}
	}
	
	var itemData = $item.data('itemData');
	itemData.zIndex = $item.zIndex();
	
	if ($item.zIndex() > _GLOBALS['highestZindex']){
		_GLOBALS['highestZindex'] = $item.zIndex();
	}else if (setHighestIndex == true){
		var highest = zIndex_old;
		$('img', $('#customizeArea')).each(function (){
			if ($(this).zIndex() > highest){
				highest = $(this).zIndex();
			}
		});
		_GLOBALS['highestZindex'] = highest;
	}
}

function convertItemPosToInches($item){
	var curX = $item.position().left;
	var curY = $item.position().top;
	var zoomVal = _GLOBALS['zoomVal'];
			
	if (zoomVal == 1){
		var actualX = curX;
		var actualY = curY;
	}else if (zoomVal > 1){
		var actualX = (curX / zoomVal);
		var actualY = (curY / zoomVal);
	}else{
		var actualX = (curX * (1 / zoomVal));
		var actualY = (curY * (1 / zoomVal));
	}
	//alert((curX * zoomVal) + ' + ' + (curX * (1 + zoomVal)));
	var itemData = $item.data('itemData');
	itemData.xPos = (actualX * _GLOBALS['scale']) / _GLOBALS['ppi'];
	itemData.yPos = (actualY * _GLOBALS['scale']) / _GLOBALS['ppi'];
			
	$item.data('real_pos', {
		x: actualX,
		y: actualY
	});
}

function convertItemWidthToInches($item){
	var zoomVal = _GLOBALS['zoomVal'];
	var width = $item.width();
	var height = $item.height();
			
	if (zoomVal == 1){
		var actualW = width;
		var actualH = height;
	}else if (zoomVal > 1){
		var actualW = (width / zoomVal);
		var actualH = (height / zoomVal);
	}else{
		var actualW = (width * (1 / zoomVal));
		var actualH = (height * (1 / zoomVal));
	}
	var itemData = $item.data('itemData');
	itemData.imageWidth = (actualW * _GLOBALS['scale']) / _GLOBALS['ppi'];
	itemData.imageHeight = (actualH * _GLOBALS['scale']) / _GLOBALS['ppi'];
	
	return itemData;
}

function makeItemDraggable(o){
	o.item.hover(function (){
		this.style.cursor = 'move';
	}, function (){
		this.style.cursor = 'default';
	}).click(function (e){
		$('.activeItem').removeClass('activeItem');
		$(this).addClass('activeItem');
		
		o.onClick.apply(this, [e]);

		e.stopPropagation();
	}).draggable({
		containment: '#customizeArea',
		start: function (){
			_GLOBALS['currentItem'] = this;
		},
		stop: function (){
			convertItemPosToInches($(this));
			
			var top = parseFloat($(this).position().top);
			var left = parseFloat($(this).position().left);
			var width = parseFloat($(this).outerWidth());
			var height = parseFloat($(this).outerHeight());
		
			if (itemData.centerVertical){
				$(o.editorSelector + ' ' + _GLOBALS['placementArrowNorth'] + ', ' + o.editorSelector + ' ' + _GLOBALS['placementArrowSouth']).addClass('ui-state-disabled');
			}else{
				var northArrow = $(o.editorSelector + ' ' + _GLOBALS['placementArrowNorth']);
				var southArrow = $(o.editorSelector + ' ' + _GLOBALS['placementArrowSouth']);
				if (top > 0){
					northArrow.removeClass('ui-state-disabled');
				}else{
					northArrow.addClass('ui-state-disabled');
				}
		
				if ((top + height) >= _GLOBALS['editableHeight']){
					southArrow.addClass('ui-state-disabled');
				}else{
					southArrow.removeClass('ui-state-disabled');
				}
			}
				
			if (itemData.centerHorizontal){
				$(_GLOBALS['placementArrowWest'] + ', ' + _GLOBALS['placementArrowEast'], o.editorSelector).addClass('ui-state-disabled');
			}else{
				var westArrow = $(o.editorSelector + ' ' + _GLOBALS['placementArrowWest']);
				var eastArrow = $(o.editorSelector + ' ' + _GLOBALS['placementArrowEast']);
				if (left > 0){
					westArrow.removeClass('ui-state-disabled');
				}else{
					westArrow.addClass('ui-state-disabled');
				}

				if ((left + width) >= _GLOBALS['editableWidth']){
					eastArrow.addClass('ui-state-disabled');
				}else{
					eastArrow.removeClass('ui-state-disabled');
				}
			}

			if (o.onDragStop){
				o.onDragStop.apply(this);
			}
		}
	});
	
	if (o.allowResize && o.allowResize === true){
		makeItemResizable(o);
	}
	
	var itemData = o.item.data('itemData');
	if (itemData.centerVertical){
		centerItem(o.item, 'v');
	}
	if (itemData.centerHorizontal){
		centerItem(o.item, 'h');
	}
	
	convertItemPosToInches(o.item);
}

function makeItemResizable(o){
	o.item.resizable({
		handles: 'nw,ne,se,sw',
		containment: '#customizeArea',
		minHeight: 10,
		minWidth: 10,
		resize: function (e, ui){
			$('img', o.item).css({
				width: ui.size.width + 'px',
				height: ui.size.height + 'px'
			});
		},
		stop: function (e, ui){
			if (o.item.hasClass('clipartEntry') || o.item.hasClass('imageEntry')){
				var itemData = convertItemWidthToInches(o.item);
				
				if (o.item.hasClass('clipartEntry')){
					$('img', o.item).attr('src', buildClipartImageLink(itemData));
				}else{
					$('img', o.item).attr('src', buildUploadImageLink(itemData));
				}
			}
		}
	});
}

function makeClipartDraggable($item){
	makeItemDraggable({
		item: $item,
		editorSelector: '.clipartEditor',
		allowResize: true,
		onClick: function (e){
			loadClipartEditor.apply(this);
		}
	});
}

function makeTextDraggable($item){
	makeItemDraggable({
		item: $item,
		editorSelector: '.textEditor',
		allowResize: false,
		onClick: function (e){
			loadTextEditor.apply(this);
		}
	});
}

function makeImageDraggable($item){
	makeItemDraggable({
		item: $item,
		editorSelector: '.imageEditor',
		allowResize: true,
		onClick: function (e){
			loadImageEditor.apply(this);
		}
	});
}

function setupMoveZindexArrows($el){
	$(_GLOBALS['zIndexArrowNorth'], $el).click(function (){
		if ($(this).hasClass('ui-state-disabled')) return;
		
		adjustItemZindex($(_GLOBALS['currentItem']), 'up');
	});

	$(_GLOBALS['zIndexArrowSouth'], $el).click(function (){
		if ($(this).hasClass('ui-state-disabled')) return;
		
		adjustItemZindex($(_GLOBALS['currentItem']), 'down');
	});
}

function setupMoveItemArrows($el){
	$('.productDesignerArrow', $el).hover(function (){
		if ($(this).hasClass('ui-state-disabled')) return;
		$(this).addClass('ui-state-hover');
	}, function (){
		if ($(this).hasClass('ui-state-disabled')) return;
		$(this).removeClass('ui-state-hover');
	});
	
	$(_GLOBALS['placementArrowNorth'], $el).click(function (){
		if ($(this).hasClass('ui-state-disabled')) return;
		var top = parseFloat($(_GLOBALS['currentItem']).position().top);
		if (top > 0){
			$(_GLOBALS['currentItem']).css('top', top-1);
		}
		
		if ((top-1) <= 0){
			$(this).addClass('ui-state-disabled').removeClass('ui-state-hover');
		}
		$(_GLOBALS['placementArrowSouth'], $el).removeClass('ui-state-hover');
	});

	$(_GLOBALS['placementArrowSouth'], $el).click(function (){
		if ($(this).hasClass('ui-state-disabled')) return;
		var top = parseFloat($(_GLOBALS['currentItem']).position().top);
		var height = parseFloat($(_GLOBALS['currentItem']).outerHeight());
		if ((top + height) < _GLOBALS['editableHeight']){
			$(_GLOBALS['currentItem']).css('top', top+1);
		}
			
		if (((top+1) + height) >= _GLOBALS['editableHeight']){
			$(this).addClass('ui-state-disabled').removeClass('ui-state-hover');
		}
	
		$(_GLOBALS['placementArrowNorth'], $el).removeClass('ui-state-disabled');
	});

	$(_GLOBALS['placementArrowEast'], $el).click(function (){
		if ($(this).hasClass('ui-state-disabled')) return;
		var left = parseFloat($(_GLOBALS['currentItem']).position().left);
		var width = parseFloat($(_GLOBALS['currentItem']).outerWidth());
		if ((left + width) < _GLOBALS['editableWidth']){
			$(_GLOBALS['currentItem']).css('left', left+1);
		}
		
		if (((left+1) + width) >= _GLOBALS['editableWidth']){
			$(this).addClass('ui-state-disabled').removeClass('ui-state-hover');
		}
			
		$(_GLOBALS['placementArrowWest'], $el).removeClass('ui-state-disabled');
	});
		
	$(_GLOBALS['placementArrowWest'], $el).click(function (){
		if ($(this).hasClass('ui-state-disabled')) return;
		var left = parseFloat($(_GLOBALS['currentItem']).position().left);
		if (left > 0){
			$(_GLOBALS['currentItem']).css('left', left-1);
		}
			
		if ((left-1) <= 0){
			$(this).addClass('ui-state-disabled').removeClass('ui-state-hover');
		}
		$(_GLOBALS['placementArrowEast'], $el).removeClass('ui-state-disabled');
	});
	
	$('.moveItemContainer', $el).each(function (){
		$(this).wrap('<div style="position:relative;"></div>');
		var $shadow = $('<div></div>').addClass('ui-widget-shadow ui-corner-all-big').css({
			width: $(this).outerWidth() - 1,
			height: $(this).outerHeight() - 5,
			position: 'absolute',
			top: '5px',
			left: 1,
			zIndex: 4
		}).insertBefore(this);
	});
}

function setupColorReplaceCheckboxes($el){
	$('input[name=use_color_replace]', $el).each(function (){
		$(this).click(function (){
			var $currentItem = $(_GLOBALS['currentItem']);
			var itemData = $currentItem.data('itemData');
			itemData.useColorReplace = this.checked;

			if ($el.find('.useReplaceNo').size() > 0){
				$(this).trigger('toggleReplaceEl');
			}
		}).bind('toggleReplaceEl', function (){
			if (this.checked){
				$el.find('.useReplaceNo').hide();
				$el.find('.useReplaceYes').show();
			}else{
				$el.find('.useReplaceNo').show();
				$el.find('.useReplaceYes').hide();
			}
		});

		var $currentItem = $(_GLOBALS['currentItem']);
		var itemData = $currentItem.data('itemData');
		this.checked = itemData.useColorReplace;
		
		if ($el.find('.useReplaceNo').size() > 0){
			$(this).trigger('toggleReplaceEl');
		}
	});
}

function centerItem($item, direction){
	var itemData = $item.data('itemData');
	var $editWindow = $('.editWindow').not(':hidden');
	
	if (itemData.centerVertical && itemData.centerHorizontal){
		$item.draggable('disable');
		$item.removeClass('ui-state-disabled');
	}else{
		$item.draggable('option', 'axis', (direction == 'h' ? 'y' : 'x'));
	}
	
	alignToCenter($item, direction);
	
	if (direction == 'h'){
		var $arrows = $(_GLOBALS['placementArrowWest'] + ', ' + _GLOBALS['placementArrowEast'], $editWindow);
	}else{
		var $arrows = $(_GLOBALS['placementArrowNorth'] + ', ' + _GLOBALS['placementArrowSouth'], $editWindow);
	}
	$arrows.addClass('ui-state-disabled');
}

function decenterItem($item, direction){
	var itemData = $item.data('itemData');
	var $editWindow = $('.editWindow').not(':hidden');
	
	if (direction == 'h' && itemData.centerVertical){
		$item.draggable('option', 'axis', 'x');
	}else if (direction == 'v' && itemData.centerHorizontal){
		$item.draggable('option', 'axis', 'y');
	}else{
		$item.draggable('option', 'axis', false);
	}
	$item.draggable('enable');
	
	if (direction == 'h'){
		var posCheck = $item.position().left;
		var dimensionCheck = $item.outerWidth();
		var globalVar = _GLOBALS['editableWidth'];
		var $arrow1 = $(_GLOBALS['placementArrowWest'], $editWindow);
		var $arrow2 = $(_GLOBALS['placementArrowEast'], $editWindow);
	}else{
		var posCheck = $item.position().top;
		var dimensionCheck = $item.outerHeight();
		var globalVar = _GLOBALS['editableHeight'];
		var $arrow1 = $(_GLOBALS['placementArrowNorth'], $editWindow);
		var $arrow2 = $(_GLOBALS['placementArrowSouth'], $editWindow);
	}
	
	var posCheck = parseFloat(posCheck);
	if ((posCheck-1) <= 0){
		$arrow1.addClass('ui-state-disabled').removeClass('ui-state-hover');
	}else{
		$arrow1.removeClass('ui-state-disabled');
	}
				
	if ((posCheck+dimensionCheck) >= globalVar){
		$arrow2.addClass('ui-state-disabled').removeClass('ui-state-hover');
	}else{
		$arrow2.removeClass('ui-state-disabled');
	}
}

function setupCenteringCheckboxes($el){
	$('input[name=centerHorizontal]', $el).click(function (){
		var $currentItem = $(_GLOBALS['currentItem']);
		var itemData = $currentItem.data('itemData');
		itemData.centerHorizontal = this.checked;
		
		if (this.checked){
			centerItem($currentItem, 'h');
		}else{
			decenterItem($currentItem, 'h');
		}
	});
	
	$('input[name=centerVertical]', $el).click(function (){
		var $currentItem = $(_GLOBALS['currentItem']);
		var itemData = $currentItem.data('itemData');
		itemData.centerVertical = this.checked;
		
		if (this.checked){
			centerItem($currentItem, 'v');
		}else{
			decenterItem($currentItem, 'v');
		}
	});
}

function updateItemImage($item, itemData){
	var newImage = new Image();
	$(newImage).attr('src', buildTextLink(itemData)).one('load', function (){
		if ($('img:first', $item).size() <= 0){
			$($item).html('').append($(this));
		}else{
			$('img:first', $item).replaceWith($(this));
		}
		fixItemPlacement($item);
	});
}

function setupRemoveButton($window){
	$window.find('.removeButton').click(function (){
		$('.activeItem').remove();
		$('.editWindow').hide();
	});
}

function loadClipartEditor(){
	_GLOBALS['currentItem'] = this;
	$('.editWindow').hide();
	
	$('.productDesignerInfoBoxHeader > span').html($('.clipartEditor').attr('title') + 'CLIPART');
	if (!$('.clipartEditor').hasClass('loaded')){
		
		$('.clipartEditor').addClass('loaded');
		setupMoveItemArrows($('.clipartEditor'));
		setupMoveZindexArrows($('.clipartEditor'));
		setupCenteringCheckboxes($('.clipartEditor'));
		setupRemoveButton($('.clipartEditor'));
	}
	$('.clipartEditor').show();

	var $currentItem = $(_GLOBALS['currentItem']);
	var itemData = $currentItem.data('itemData');
	
	$('.clipartEditor input[name=centerVertical]').each(function (){
		this.checked = itemData.centerVertical;
	});
	$('.clipartEditor input[name=centerHorizontal]').each(function (){
		this.checked = itemData.centerHorizontal;
	});
	
	var top = parseFloat($currentItem.position().top);
	var left = parseFloat($currentItem.position().left);
	if (top == 0){
		$('.clipartEditor ' + _GLOBALS['placementArrowNorth']).addClass('ui-state-disabled');
	}
	if (left == 0){
		$('.clipartEditor ' + _GLOBALS['placementArrowWest']).addClass('ui-state-disabled');
	}
	
	if (itemData.centerVertical){
		$('.clipartEditor ' + _GLOBALS['placementArrowWest'] + ', .clipartEditor ' + _GLOBALS['placementArrowEast']).addClass('ui-state-disabled');
	}
	
	if (itemData.centerHorizontal){
		$('.clipartEditor ' + _GLOBALS['placementArrowNorth'] + ', .clipartEditor ' + _GLOBALS['placementArrowSouth']).addClass('ui-state-disabled');
	}
}

function loadTextEditor(){
	_GLOBALS['currentItem'] = this;
	$('.editWindow').hide();
	
	$('.productDesignerInfoBoxHeader > span').html($('.textEditor').attr('title') + 'TEXT');
	if (!$('.textEditor').hasClass('loaded')){
		
		$('.textEditor').addClass('loaded');
		setupMoveItemArrows($('.textEditor'));
		setupMoveZindexArrows($('.textEditor'));
		setupCenteringCheckboxes($('.textEditor'));
		setupRemoveButton($('.textEditor'));
		setupColorReplaceCheckboxes($('.textEditor'));

		$('.textEditor #colorBlocks').simpleColor({
			columns: 8,
			cellWidth: 18,
			cellHeight: 13,
			cellMargin: 3
		});
		
		$('.textEditor #fontStrokeColorBlocks').simpleColor({
			columns: 8,
			cellWidth: 18,
			cellHeight: 13,
			cellMargin: 3
		});
		
		$('.textEditor select[name=fontFamily]').change(function (){
			var $currentItem = $(_GLOBALS['currentItem']);
			var itemData = $currentItem.data('itemData');
			itemData.fontFamily = $(this).val();
			
			updateItemImage($currentItem, itemData);
		});
	
		$('.textEditor select[name=fontSize]').change(function (){
			var $currentItem = $(_GLOBALS['currentItem']);
			var itemData = $currentItem.data('itemData');
			itemData.fontSize = $(this).val();

			updateItemImage($currentItem, itemData);
		});
		
		$('.textEditor select[name=textTransform]').change(function (){
			var $currentItem = $(_GLOBALS['currentItem']);
			var itemData = $currentItem.data('itemData');
			itemData.textTransform = $(this).val();

			updateItemImage($currentItem, itemData);
		});
		
		$('.textEditor select[name=fontStroke]').change(function (){
			var $currentItem = $(_GLOBALS['currentItem']);
			var itemData = $currentItem.data('itemData');
			itemData.fontStroke = $(this).val();

			updateItemImage($currentItem, itemData);
		});
		
		$('.textEditor input[name=fontColor]').click(function (){
			var $currentItem = $(_GLOBALS['currentItem']);
			var itemData = $currentItem.data('itemData');
			itemData.fontColor = $(this).val();
			
			updateItemImage($currentItem, itemData);
		});
		
		$('.textEditor input[name=fontStrokeColor]').click(function (){
			var $currentItem = $(_GLOBALS['currentItem']);
			var itemData = $currentItem.data('itemData');
			itemData.fontStrokeColor = $(this).val();
			
			updateItemImage($currentItem, itemData);
		});
	
		$('.textEditor #colorBlocks .simpleColorCell').click(function (){
			var $currentItem = $(_GLOBALS['currentItem']);
			var itemData = $currentItem.data('itemData');
			itemData.fontColor = $(this).attr('id');
			$('img', $currentItem).attr('src', buildTextLink(itemData));
		});
		
		$('.textEditor #fontStrokeColorBlocks .simpleColorCell').click(function (){
			var $currentItem = $(_GLOBALS['currentItem']);
			var itemData = $currentItem.data('itemData');
			itemData.fontStrokeColor = $(this).attr('id');
			$('img', $currentItem).attr('src', buildTextLink(itemData));
		});
		
		$('.textEditor input[name=edit_image_text]').focus(function (){
			var $currentItem = $(_GLOBALS['currentItem']);
			var itemData = $currentItem.data('itemData');
			$currentItem.html(itemData.imageText);
		}).blur(function (){
			var $currentItem = $(_GLOBALS['currentItem']);
			var itemData = $currentItem.data('itemData');
			itemData.imageText = $(this).val();
			
			updateItemImage($currentItem, itemData);
		}).keyup(function (){
			var $currentItem = $(_GLOBALS['currentItem']);
			var itemData = $currentItem.data('itemData');
			itemData.imageText = $(this).val();
			$currentItem.html(itemData.imageText);
		});
	}
	$('.textEditor').show();

	var $currentItem = $(_GLOBALS['currentItem']);
	var itemData = $currentItem.data('itemData');
	
	$('.textEditor input[name=edit_image_text]').val(itemData.imageText);
	$('.textEditor select[name=fontSize]').val(itemData.fontSize);
	$('.textEditor select[name=fontFamily]').val(itemData.fontFamily);
	$('.textEditor #colorBlocks > #' + itemData.fontColor).parent().addClass('ui-state-active');
	$('.textEditor input[name=textCenterVertical]').each(function (){
		this.checked = itemData.centerVertical;
	});
	$('.textEditor input[name=textCenterHorizontal]').each(function (){
		this.checked = itemData.centerHorizontal;
	});
	$('.textEditor input[name=use_color_replace]').each(function (){
		this.checked = itemData.useColorReplace;
	});
	
	var top = parseFloat($currentItem.position().top);
	var left = parseFloat($currentItem.position().left);
	if (top == 0){
		$('.textEditor ' + _GLOBALS['placementArrowNorth']).addClass('ui-state-disabled');
	}
	if (left == 0){
		$('.textEditor ' + _GLOBALS['placementArrowWest']).addClass('ui-state-disabled');
	}
	
	if (itemData.centerVertical){
		$('.textEditor ' + _GLOBALS['placementArrowWest'] + ', .textEditor ' + _GLOBALS['placementArrowEast']).addClass('ui-state-disabled');
	}
	
	if (itemData.centerHorizontal){
		$('.textEditor ' + _GLOBALS['placementArrowNorth'] + ', .textEditor ' + _GLOBALS['placementArrowSouth']).addClass('ui-state-disabled');
	}
}

function loadImageEditor(){
	_GLOBALS['currentItem'] = this;
	$('.editWindow').hide();
	
	$('.productDesignerInfoBoxHeader > span').html($('.imageEditor').attr('title') + 'IMAGE');
	if (!$('.imageEditor').hasClass('loaded')){
		
		$('.imageEditor').addClass('loaded');
		setupMoveItemArrows($('.imageEditor'));
		setupMoveZindexArrows($('.imageEditor'));
		setupCenteringCheckboxes($('.imageEditor'));
		setupRemoveButton($('.imageEditor'));
	}
	$('.imageEditor').show();

	var $currentItem = $(_GLOBALS['currentItem']);
	var itemData = $currentItem.data('itemData');
	
	$('.imageEditor input[name=centerVertical]').each(function (){
		this.checked = itemData.centerVertical;
	});
	$('.imageEditor input[name=centerHorizontal]').each(function (){
		this.checked = itemData.centerHorizontal;
	});
	
	var top = parseFloat($currentItem.position().top);
	var left = parseFloat($currentItem.position().left);
	if (top == 0){
		$('.imageEditor ' + _GLOBALS['placementArrowNorth']).addClass('ui-state-disabled');
	}
	if (left == 0){
		$('.imageEditor ' + _GLOBALS['placementArrowWest']).addClass('ui-state-disabled');
	}
	
	if (itemData.centerVertical){
		$('.imageEditor ' + _GLOBALS['placementArrowWest'] + ', .imageEditor ' + _GLOBALS['placementArrowEast']).addClass('ui-state-disabled');
	}
	
	if (itemData.centerHorizontal){
		$('.imageEditor ' + _GLOBALS['placementArrowNorth'] + ', .imageEditor ' + _GLOBALS['placementArrowSouth']).addClass('ui-state-disabled');
	}
}

function fixItemPlacement($el){
	var itemData = $el.data('itemData');
	
	if (itemData.centerHorizontal || itemData.centerVertical){
		if (itemData.centerHorizontal){
			alignToCenter($el, 'h');
		}
		if (itemData.centerVertical){
			alignToCenter($el, 'v');
		}
		var width = parseFloat($el.outerWidth());
		if (width > _GLOBALS['editableWidth']){
			alert('Text is too long for editable area, please adjust size or content.');
		}
	}else if ($el.data('real_pos')){
		var pos = $el.data('real_pos');
					
		$el.css({
			top: (pos.y * _GLOBALS['zoomVal']) + 'px',
			left: (pos.x * _GLOBALS['zoomVal']) + 'px'
		});
	}else{
		var left = parseFloat($el.position().left);
		var width = parseFloat($el.outerWidth());
		if (left < 0){
			$el.css('left', 0);
		}
		if ((left + width) >= _GLOBALS['editableWidth']){
			if (width > _GLOBALS['editableWidth']){
				alert('Text is too long for editable area, please adjust size or content.');
			}else{
				$el.css('left', (_GLOBALS['editableWidth'] - width));
			}
		}
	}
	convertItemPosToInches($el);
}

function alignToCenter($el, direction){
	var itemData = $el.data('itemData');
	if (direction == 'h'){
		var width = parseFloat($el.outerWidth());
	
		var halfEdit = _GLOBALS['editableWidth']/2;
		var halfImg = width/2;
				
		var newX = halfEdit - halfImg;
				
		$el.css('left', newX);
	}else if (direction == 'v'){
		var height = parseFloat($el.outerHeight());
	
		var halfEdit = _GLOBALS['editableHeight']/2;
		var halfImg = height/2;
				
		var newY = halfEdit - halfImg;
				
		$el.css('top', newY);
	}
}

$(document).ready(function (){
	_GLOBALS['products_id'] = $('input[name=products_id]').val();
	
	$('#addTextButton').click(function (){
		popupDialog({
			width: '400px',
			headerText: '<span class="headerText">ADD TEXT</span>',
			headerInfo: '<span class="headerSubText">Please enter one line of text to add to your design.</span>',
			body: 'Text to Add: <input type="text" name="new_image_text" style="width:75%;">',
			buttons: {
				'SUBMIT': function (){
					var $newText = $('<span class="textEntry"></span>');
					$newText.zIndex(_GLOBALS['highestZindex']+1);
					_GLOBALS['highestZindex']++;
					
					$newText.data('itemData', {
						fontSize: 1,
						fontColor: '000000',
						fontStroke: 0,
						fontStrokeColor: '000000',
						fontFamily: 'arial.ttf',
						imageText: $('input[name=new_image_text]', this).val(),
						textTransform: 'straight',
						centerVertical: false,
						centerHorizontal: false,
						xPos: 1,
						yPos: 1,
						zIndex: $newText.zIndex(),
						scale: _GLOBALS['scale']
					});
					$newText.html('<img src="' + buildTextLink($newText.data('itemData')) + '"></img>');
					$('#customizeArea').append($newText);
					makeTextDraggable($newText);
					
					$(this).dialog('destroy').remove();
				}
			}
		});
	});
	
	$('#uploadImageButton').click(function (){
		popupDialog({
			width: '400px',
			headerText: '<span class="headerText">ADD IMAGE</span>',
			headerInfo: '<span class="headerSubText">Please upload an image.</span>',
			body: 'Upload Image: <input type="file" id="new_image_upload" name="new_image_upload" style="width:75%;">',
			buttons: {
				'SUBMIT': function (){
					var $newImage = $('<span class="imageEntry"></span>');
					$newImage.zIndex(_GLOBALS['highestZindex']+1);
					_GLOBALS['highestZindex']++;
					
					$newImage.data('itemData', {
						imageSrc: $('input[name=design_image]').val(),
						centerVertical: false,
						centerHorizontal: false,
						xPos: 1,
						yPos: 1,
						zIndex: $newImage.zIndex(),
						scale: _GLOBALS['scale']
					});
					$newImage.html('<img src="'+buildUploadImageLink($newImage.data('itemData'))+'"></img>');
					$('#customizeArea').append($newImage);
					$('img', $newImage).one('load', function (){
						var zoomVal = _GLOBALS['zoomVal'];
			
						if (zoomVal == 1){
							var actualW = $(this).width();
							var actualH = $(this).height();
						}else if (zoomVal > 1){
							var actualW = ($(this).width() / zoomVal);
							var actualH = ($(this).height() / zoomVal);
						}else{
							var actualW = ($(this).width() * (1 / zoomVal));
							var actualH = ($(this).height() * (1 / zoomVal));
						}
						var itemData = $newImage.data('itemData');
						itemData.imageWidth = actualW / _GLOBALS['ppi'];
						itemData.imageHeight = actualH / _GLOBALS['ppi'];
						
						makeImageDraggable($newImage);
					});

					$(this).dialog('destroy').remove();
				}
			}
		});
		
		$('input[name=new_image_upload]').uploadify({
			uploader: DIR_WS_CATALOG + 'ext/jQuery/external/uploadify/uploadify.swf',
			script: 'application.php',
			method: 'GET',
			scriptData: {
				'appExt': 'productDesigner',
				'app': 'design',
				'appPage': 'default',
				'action': 'saveUploadImage',
				'rType':'ajax',
				'osCID':sessionId
			},
			cancelImg: DIR_WS_CATALOG + 'ext/jQuery/external/uploadify/images/cancel.png',
			auto: true,
			folder: 'images',
			onError: function (event, queueID, fileObj, errorObj){
				alert('error');
				alert(errorObj.type + ' :: ' + errorObj.info);
			},
			onComplete: function (event, queueID, fileObj, resp, data){
				var theResp = eval('(' + resp + ')');			

				if (theResp.success == true){
					$('.uploadedImage').remove();
					
					var $img = $('<img></img>')
					.attr('src', theResp.thumb_path);
					
					var $hiddenField = $('<input type="hidden"></input>')
					.attr('name', 'design_image')
					.val(theResp.image_name);
					
					var $thumbHolder = $('<div></div>')
					.css('text-align', 'center')
					.append($img)
					.append($hiddenField);
					
					var $theBox = $('<div class="uploadedImage">').css({
						'width'  : '80px',
						'height' : '100px',
						'border' : '1px solid #cccccc',
						'margin' : '.5em'
					}).append($thumbHolder);
				
					$theBox.insertAfter($('.uploadifyQueue'));
				}
			}
		});
	});
	
	/*Clipart*/
	$("#clipartBrowser").treeview();

	$('#addClipartButton').click(function (){
		$('#clipartDialogBox').dialog({
			width: '850px',
			dialogClass: 'productDesignerPopupWindow',
			resizable: false,
			autoOpen: true,
			title:'<span class="headerText">ADD CLIPART</span>'+'<hr style="width:365px;" />'+ '<span class="headerSubText">Please select one clipart to add to your design.</span>',
			close: function (e, ui){
				$(this).dialog('destroy').remove();
			},
			buttons: {
				'SUBMIT': function (){
					var $newClipart = $('<span class="clipartEntry"></span>');
					$newClipart.zIndex(_GLOBALS['highestZindex']+1);
					_GLOBALS['highestZindex']++;
					
					$newClipart.data('itemData', {
						imageSrc: selected_image,
						centerVertical: false,
						centerHorizontal: false,
						xPos: 1,
						yPos: 1,
						zIndex: $newClipart.zIndex(),
						scale: _GLOBALS['scale']
					});
					$newClipart.html('<img src="'+buildClipartImageLink($newClipart.data('itemData'))+'"></img>');
					$('#customizeArea').append($newClipart);
					$('img', $newClipart).one('load', function (){
						var zoomVal = _GLOBALS['zoomVal'];
			
						if (zoomVal == 1){
							var actualW = $(this).width();
							var actualH = $(this).height();
						}else if (zoomVal > 1){
							var actualW = ($(this).width() / zoomVal);
							var actualH = ($(this).height() / zoomVal);
						}else{
							var actualW = ($(this).width() * (1 / zoomVal));
							var actualH = ($(this).height() * (1 / zoomVal));
						}
						var itemData = $newClipart.data('itemData');
						itemData.imageWidth = actualW / _GLOBALS['ppi'];
						itemData.imageHeight = actualH / _GLOBALS['ppi'];
						
						makeClipartDraggable($newClipart);
					});

					$(this).dialog('destroy').remove();
				}
			}
		});
	});
	/*Clipart*/
	
	var imagesLoading = 0;
	$('#imgZoom').change(function (){
		_GLOBALS['zoomVal'] = parseFloat($(this).val());
		$.ajax({
			url: js_app_link('appExt=productDesigner&app=design&appPage=default&products_id=' + _GLOBALS['products_id'] + '&action=productDesignerZoomImage'),
			data: 'img=' + $('#designerImage').attr('src') + '&zoom=' + _GLOBALS['zoomVal'],
			type: 'post',
			dataType: 'json',
			success: function (data){
				$('#productImageHolder').css('width', data.imgWidth + 'px');
				
				$('#designerImage').css({
					width: data.imgWidth + 'px',
					height: data.imgHeight + 'px'
				});
				
				$('#customizeArea').css({
					width: data.editableWidth + 'px',
					height: data.editableHeight + 'px',
					top: data.editableY + 'px',
					left: data.editableX + 'px'
				});
				_GLOBALS['editableWidth'] = parseInt(data.editableWidth);
				_GLOBALS['editableHeight'] = parseInt(data.editableHeight);
				_GLOBALS['scale'] = parseFloat(data.scale);

				$('.textEntry').each(function (){
					var itemData = $(this).data('itemData');
					var $self = $(this);
					
					var newImage = new Image();
					$(newImage).attr('src', buildTextLink(itemData)).one('load', function (){
						$('img:first', $self).replaceWith($(this));
						fixItemPlacement($self);
					});
				});
				
				$('.clipartEntry').each(function (){
					var itemData = $(this).data('itemData');
					var $self = $(this);

					var newImage = new Image();
					$(newImage).attr('src', buildClipartImageLink(itemData)).one('load', function (){
						$('img:first', $self).replaceWith($(this));
						fixItemPlacement($self);
					});
				});
				
				$('.imageEntry').each(function (){
					var itemData = $(this).data('itemData');
					var $self = $(this);

					var newImage = new Image();
					$(newImage).attr('src', buildUploadImageLink(itemData)).one('load', function (){
						$('img:first', $self).replaceWith($(this));
						fixItemPlacement($self);
					});
				});
			}
		});
	}).bind('initialZoom', function (){
		if (imagesLoading <= 0){
			$(this).trigger('change');
		}
	});
	
	$('.productDesignerInfoBox').mousedown(function (e){
		e.stopPropagation();
		$('select[name=fontFamily]').combobox('close');
	});

	//$('#designerImage').one('ready', function (){
	$('.textEntry, .variableTextEntry').each(function (){
		var self = $(this);
		
		if (self.zIndex() > _GLOBALS['highestZindex']){
			_GLOBALS['highestZindex'] = self.zIndex();
		}
		
		imagesLoading++;
		$(':first-child', self).one('load', function (){
			var dataObj = self.attr('data-obj');
			if (dataObj && dataObj != ''){
				eval('dataObj = {' + dataObj + '};');
				self.data('itemData', dataObj);
			
				if (self.hasClass('variableTextEntry')){
					makeVariableTextDraggable(self);
				}else{
					makeTextDraggable(self);
				}
				imagesLoading--;
				$('#imgZoom').trigger('initialZoom');
			}
		});
	});
	
	$('.clipartEntry, .variableClipartEntry').each(function (){
		var self = $(this);
		
		if (self.zIndex() > _GLOBALS['highestZindex']){
			_GLOBALS['highestZindex'] = self.zIndex();
		}
		
		imagesLoading++;
		$(':first-child', self).one('load', function (){
			var dataObj = self.attr('data-obj');
			if (dataObj && dataObj != ''){
				eval('dataObj = {' + dataObj + '};');
				self.data('itemData', dataObj);
			
				if (self.hasClass('variableClipartEntry')){
					makeVariableClipartDraggable(self);
				}else{
					makeClipartDraggable(self);
				}
				imagesLoading--;
				$('#imgZoom').trigger('initialZoom');
			}
		});
	});
	
	if ($('.textEntry, .variableTextEntry, .clipartEntry, .variableClipartEntry').size() <= 0){
		$('#imgZoom').trigger('initialZoom');
	}
	//});
	
	$('button[name=buy_new_product]').click(function (){
		var self = this;
		$('.textEntry').each(function (i, el){
			var elData = $(this).data('itemData');
			
			$.each(elData, function (key, value){
				$('<input type="hidden" />')
				.attr('name', 'item[text][' + i + '][' + key + ']')
				.val(value)
				.insertBefore(self);
			});
		});
		
		$('.clipartEntry').each(function (i, el){
			var elData = $(this).data('itemData');
			
			$.each(elData, function (key, value){
				$('<input type="hidden" />')
				.attr('name', 'item[clipart][' + i + '][' + key + ']')
				.val(value)
				.insertBefore(self);
			});
		});
		
		$('.imageEntry').each(function (i, el){
			var elData = $(this).data('itemData');
			
			$.each(elData, function (key, value){
				$('<input type="hidden" />')
				.attr('name', 'item[image][' + i + '][' + key + ']')
				.val(value)
				.insertBefore(self);
			});
		});
	});
	
	$('select[name=fontFamily]').combobox({
		listItemBeforeShow: function (listItem, origListItem){
			$(listItem).css({
				'font-family': $(this).html(),
				'font-size': '2em',
				'font-weight': 'normal'
			})
			.html('<img src="' + js_catalog_app_link('appExt=productDesigner&app=font_thumb&appPage=default&font=' + $(origListItem).val()) + '" />');
		}
	});
}).mousedown(function (e){
	$('.activeItem').removeClass('activeItem');
	$('.editWindow').hide();
	$('.ui-combobox.ui-state-active').trigger('closeMenu');
});

(function ($){
	$.widget('ui.combobox', {
		options: {
			selectBoxClass: 'ui-combobox',
			selectedDisplayClass: 'ui-combobox-selected',
			dropArrowClass: 'ui-combobox-drop-icon',
			listContainerClass: 'ui-combobox-list-container',
			listOptionClass: 'ui-combobox-list-option',
			hoverClass: 'ui-state-hover',
			defaultClass: 'ui-state-default',
			activeClass: 'ui-state-active',
			boxBeforeShow: null,
			boxAfterShow: null,
			listItemBeforeShow: null,
			listItemAfterShow: null,
			listItemOnSelect: null,
			listItemOnHover: null,
			position: {
				my: 'center center',
				at: 'center center',
				offset: '0 0'
			}
		},
		_create: function (){
			var self = this,
			o = this.options;
			
			this.newElements = {};
			this.newElements.selectBox = $('<div></div>')
				.addClass('ui-widget ui-widget-content ui-corner-all ' + o.selectBoxClass + ' ' + o.defaultClass)
				.mouseover(function (){
					if ($(this).hasClass(o.activeClass)) return;
					$(this).removeClass(o.defaultClass).addClass(o.hoverClass);
				})
				.mouseout(function (){
					if ($(this).hasClass(o.activeClass)) return;
					$(this).removeClass(o.hoverClass).addClass(o.defaultClass);
				})
				.mousedown(function (e){
					e.stopPropagation();
					$(this).removeClass(o.hoverClass).addClass(o.activeClass);
				})
				.mouseup(function (){
					$(this).trigger('openMenu');
				})
				.bind('closeMenu', function (){
					$(this).removeClass(o.activeClass).addClass(o.defaultClass);
					$(this).find('.' + o.activeClass).filter(function (){
						return (!$(this).hasClass(o.listOptionClass));
					}).removeClass(o.activeClass).addClass(o.defaultClass);
			
					$(this).find('.' + o.listContainerClass).hide();
				})
				.bind('openMenu', function (){
					var $container = $(this).find('.' + o.listContainerClass);
					$container.show();

					$container.css({
						top: ($(this).height() + 1) + 'px',
						left: '0px'
					});
					if ($container.width() < $(this).width()){
						$container.width($(this).width());
					}
				});
				
			this.newElements.selectedDisplay = $('<span></span>')
				.addClass(o.selectedDisplayClass);
			
			this.newElements.dropButton = $('<div></div>')
				.addClass('ui-widget ui-widget-content ui-corner-tr ui-corner-br ' + o.dropArrowClass + ' ' + o.defaultClass)
				.html('<span class="ui-icon ui-icon-triangle-1-s"></span>')
				.mouseover(function (){
					if ($(this).hasClass(o.activeClass)) return;
					$(this).removeClass(o.defaultClass).addClass(o.hoverClass);
				})
				.mouseout(function (){
					if ($(this).hasClass(o.activeClass)) return;
					$(this).removeClass(o.hoverClass).addClass(o.defaultClass);
				})
				.mousedown(function (e){
					$(this).removeClass(o.hoverClass).addClass(o.activeClass);
				})
				.mouseup(function (){
				});
				
			this.newElements.listOptionContainer = $('<ul></ul>');
			this.newElements.listOptions = [];

			var selectedOption = this.element[0].options.selectedIndex;
			$(this.element[0].options).each(function (i, option){
				if (i == selectedOption){
					self.newElements.selectedDisplay.html($(this).html());
				}
				
				var $newOption = $('<li></li>')
					.addClass(o.listOptionClass + ' ' + o.defaultClass)
					.mouseover(function (){
						$(this).addClass(o.hoverClass);
						
						/* @TODO: check if this is ok according to jquery ui specs -- BEGIN -- */
						if (o.listItemOnHover){
							o.listItemOnHover.apply(self, [event, this]);
						}
						/* @TODO: check if this is ok according to jquery ui specs -- END -- */
					})
					.mouseout(function (){
						$(this).removeClass(o.hoverClass);
						
						/* @TODO: check if this is ok according to jquery ui specs -- BEGIN -- */
						if (o.listItemOnHover){
							o.listItemOnHover.apply(self, [event, this]);
						}
						/* @TODO: check if this is ok according to jquery ui specs -- END -- */
					})
					.click(function (event){
						event.stopPropagation();
						
						$(this).parent().find('.' + o.activeClass).removeClass(o.activeClass);
						$(this).removeClass(o.hoverClass).addClass(o.activeClass);
						$(this).parent().parent().parent().find('.' + o.selectedDisplayClass).html($(this).html());
						
						$(self.element[0]).val($(this).attr('optionValue')).trigger('change');
						
						self.newElements.selectBox.trigger('closeMenu');
						
						/* @TODO: check if this is ok according to jquery ui specs -- BEGIN -- */
						if (o.listItemOnSelect){
							o.listItemOnSelect.apply(self, [event, this]);
						}
						/* @TODO: check if this is ok according to jquery ui specs -- END -- */
					})
					.attr('optionValue', $(this).val())
					.html($(this).html());
					
				self.newElements.listOptions[i] = $newOption;
			});
			
			this.newElements.listContainer = $('<div></div>')
				.addClass('ui-widget ui-widget-content ui-corner-all ' + o.listContainerClass)
				.css('height', '14em');
				
			$.each(this.newElements.listOptions, function (i, el){
				/* @TODO: check if this is ok according to jquery ui specs -- BEGIN -- */
				if (o.listItemBeforeShow){
					o.listItemBeforeShow.apply(self, [this, self.element[0].options[i]]);
				}
				/* @TODO: check if this is ok according to jquery ui specs -- END -- */

				self.newElements.listOptionContainer.append(this);

				/* @TODO: check if this is ok according to jquery ui specs -- BEGIN -- */
				if (o.listItemAfterShow){
					o.listItemAfterShow.apply(self, [this]);
				}
				/* @TODO: check if this is ok according to jquery ui specs -- END -- */
			});
			
			this.newElements.listContainer
				.append(this.newElements.listOptionContainer);
			
			/* @TODO: check if this is ok according to jquery ui specs -- BEGIN -- */
			if (o.boxBeforeShow){
				o.boxBeforeShow.apply(this, [this.newElements.selectBox]);
			}
			/* @TODO: check if this is ok according to jquery ui specs -- END -- */
			
			this.newElements.selectBox
				.append(this.newElements.dropButton)
				.append(this.newElements.selectedDisplay)
				.append(this.newElements.listContainer)
				.insertAfter(this.element[0]);
				
			/* @TODO: check if this is ok according to jquery ui specs -- BEGIN -- */
			if (o.boxAfterShow){
				o.boxAfterShow.apply(this, [this.newElements.selectBox]);
			}
			/* @TODO: check if this is ok according to jquery ui specs -- END -- */

			$(this.element[0]).hide();
		},
		close: function (){
			this.newElements.selectBox.trigger('closeMenu');
		},
		widget: function() {
			return this.newElements.selectBox;
		},
		destroy: function() {
			this.newElements.selectBox.remove();
			$(this.element).show();
			
			$.Widget.prototype.destroy.call( this );
		},
		_setOption: function ( key, value ){
			$.Widget.prototype._setOption.apply( this, arguments );
		}
	});
}(jQuery));
