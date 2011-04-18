var pixelsPerInch = 72;

function loadVariableTextEditor(){
	_GLOBALS['currentItem'] = this;
	$('.editWindow').hide();
	
	$('.productDesignerInfoBoxHeader > span').html($('.textEditor').attr('title') + 'TEXT');
	if (!$('.variableTextEditor').hasClass('loaded')){
		$('.variableTextEditor').addClass('loaded');
		setupMoveItemArrows($('.variableTextEditor'));
		setupMoveZindexArrows($('.variableTextEditor'));
		setupCenteringCheckboxes($('.variableTextEditor'));
		setupRemoveButton($('.variableTextEditor'));
		setupColorReplaceCheckboxes($('.variableTextEditor'));
		
		$('.variableTextEditor #colorBlocks').simpleColor({
			columns: 8,
			cellWidth: 18,
			cellHeight: 13,
			cellMargin: 3
		});
		
		$('.variableTextEditor #fontStrokeColorBlocks').simpleColor({
			columns: 8,
			cellWidth: 18,
			cellHeight: 13,
			cellMargin: 3
		});
		
		$('.variableTextEditor select[name=fontFamily]').change(function (){
			var $currentItem = $(_GLOBALS['currentItem']);
			var itemData = $currentItem.data('itemData');
			itemData.fontFamily = $(this).val();

			updateItemImage($currentItem, itemData);
		});
	
		$('.variableTextEditor select[name=fontSize]').change(function (){
			var $currentItem = $(_GLOBALS['currentItem']);
			var itemData = $currentItem.data('itemData');
			itemData.fontSize = $(this).val();

			updateItemImage($currentItem, itemData);
		});
		
		$('.variableTextEditor select[name=textTransform]').change(function (){
			var $currentItem = $(_GLOBALS['currentItem']);
			var itemData = $currentItem.data('itemData');
			itemData.textTransform = $(this).val();

			updateItemImage($currentItem, itemData);
		});
	
		$('.variableTextEditor select[name=textVariable]').change(function (){
			var $currentItem = $(_GLOBALS['currentItem']);
			var itemData = $currentItem.data('itemData');
			itemData.imageText = $(this).val();
			itemData.textVariable = $(this).val();
			
			updateItemImage($currentItem, itemData);
		});
		
		$('.variableTextEditor input[name=fontColor]').click(function (){
			var $currentItem = $(_GLOBALS['currentItem']);
			var itemData = $currentItem.data('itemData');
			itemData.fontColor = $(this).val();
			
			updateItemImage($currentItem, itemData);
		});
		
		$('.variableTextEditor input[name=fontStrokeColor]').click(function (){
			var $currentItem = $(_GLOBALS['currentItem']);
			var itemData = $currentItem.data('itemData');
			itemData.fontStrokeColor = $(this).val();
			
			updateItemImage($currentItem, itemData);
		});
		
		$('.variableTextEditor select[name=fontStroke]').change(function (){
			var $currentItem = $(_GLOBALS['currentItem']);
			var itemData = $currentItem.data('itemData');
			itemData.fontStroke = $(this).val();

			updateItemImage($currentItem, itemData);
		});
	
		$('.variableTextEditor #colorBlocks .simpleColorCell').click(function (){
			var $currentItem = $(_GLOBALS['currentItem']);
			var itemData = $currentItem.data('itemData');
			itemData.fontColor = $(this).attr('id');
			$('img', $currentItem).attr('src', buildTextLink(itemData));
		});
		
		$('.variableTextEditor #fontStrokeColorBlocks .simpleColorCell').click(function (){
			var $currentItem = $(_GLOBALS['currentItem']);
			var itemData = $currentItem.data('itemData');
			itemData.fontStrokeColor = $(this).attr('id');
			$('img', $currentItem).attr('src', buildTextLink(itemData));
		});
	}
	$('.variableTextEditor').show();

	var $currentItem = $(_GLOBALS['currentItem']);
	var itemData = $currentItem.data('itemData');
	
	$('.variableTextEditor select[name=textVariable]').val(itemData.textVariable);
	$('.variableTextEditor select[name=fontSize]').val(itemData.fontSize);
	$('.variableTextEditor select[name=fontFamily]').val(itemData.fontFamily);
	$('.variableTextEditor select[name=textTransform]').val(itemData.textTransform);
	$('.variableTextEditor select[name=fontStroke]').val(itemData.fontStroke);
	
	$('.variableTextEditor input[name=fontColor]').each(function (){
		this.checked = ($(this).val() == itemData.fontColor);
	});
	$('.variableTextEditor input[name=fontStrokeColor]').each(function (){
		this.checked = ($(this).val() == itemData.fontStrokeColor);
	});
	$('.variableTextEditor input[name=centerVertical]').each(function (){
		this.checked = itemData.centerVertical;
	});
	
	$('.variableTextEditor input[name=centerHorizontal]').each(function (){
		this.checked = itemData.centerHorizontal;
	});
	
	$('.variableTextEditor input[name=use_color_replace]').each(function (){
		this.checked = itemData.useColorReplace;
		$(this).trigger('toggleReplaceEl');
	});
	
	var top = parseFloat($currentItem.position().top);
	var left = parseFloat($currentItem.position().left);
	if (top == 0){
		$('.variableTextEditor ' + _GLOBALS['placementArrowNorth']).addClass('ui-state-disabled');
	}
	if (left == 0){
		$('.variableTextEditor ' + _GLOBALS['placementArrowWest']).addClass('ui-state-disabled');
	}
	
	if (itemData.centerVertical){
		$('.variableTextEditor ' + _GLOBALS['placementArrowWest'] + ', .variableTextEditor ' + _GLOBALS['placementArrowEast']).addClass('ui-state-disabled');
	}
	
	if (itemData.centerHorizontal){
		$('.variableTextEditor ' + _GLOBALS['placementArrowNorth'] + ', .variableTextEditor ' + _GLOBALS['placementArrowSouth']).addClass('ui-state-disabled');
	}
}

function loadVariableClipartEditor(){
	_GLOBALS['currentItem'] = this;
	$('.editWindow').hide();
	
	$('.productDesignerInfoBoxHeader > span').html($('.textEditor').attr('title') + 'CLIPART');
	if (!$('.variableClipartEditor').hasClass('loaded')){
		$('.variableClipartEditor').addClass('loaded');
		setupMoveItemArrows($('.variableClipartEditor'));
		setupMoveZindexArrows($('.variableClipartEditor'));
		setupCenteringCheckboxes($('.variableClipartEditor'));
		setupRemoveButton($('.variableClipartEditor'));
		setupColorReplaceCheckboxes($('.variableClipartEditor'));
		
		$('.variableClipartEditor select[name=clipartVariable]').change(function (){
			var $currentItem = $(_GLOBALS['currentItem']);
			var itemData = $currentItem.data('itemData');
			itemData.clipartVariable = $(this).val();
			
			updateItemImage($currentItem, itemData);
		});
	}
	$('.variableClipartEditor').show();

	var $currentItem = $(_GLOBALS['currentItem']);
	var itemData = $currentItem.data('itemData');
	
	$('.variableClipartEditor select[name=clipartVariable]').val(itemData.clipartVariable);
	$('.variableClipartEditor input[name=centerVertical]').each(function (){
		this.checked = itemData.centerVertical;
	});
	
	$('.variableClipartEditor input[name=centerHorizontal]').each(function (){
		this.checked = itemData.centerHorizontal;
	});
	
	var top = parseFloat($currentItem.position().top);
	var left = parseFloat($currentItem.position().left);
	if (top == 0){
		$('.variableClipartEditor ' + _GLOBALS['placementArrowNorth']).addClass('ui-state-disabled');
	}
	if (left == 0){
		$('.variableClipartEditor ' + _GLOBALS['placementArrowWest']).addClass('ui-state-disabled');
	}
	
	if (itemData.centerVertical){
		$('.variableClipartEditor ' + _GLOBALS['placementArrowWest'] + ', .variableClipartEditor ' + _GLOBALS['placementArrowEast']).addClass('ui-state-disabled');
	}
	
	if (itemData.centerHorizontal){
		$('.variableClipartEditor ' + _GLOBALS['placementArrowNorth'] + ', .variableClipartEditor ' + _GLOBALS['placementArrowSouth']).addClass('ui-state-disabled');
	}
}

function makeVariableTextDraggable($item){
	makeItemDraggable({
		item: $item,
		allowResize: false,
		onClick: function (e){
			loadVariableTextEditor.apply(this);
		},
		onDragStop: function (){
			$(this).trigger('variableTextDragStop');
		}
	});
}

function makeVariableClipartDraggable($item){
	makeItemDraggable({
		item: $item,
		allowResize: false,
		onClick: function (e){
			loadVariableClipartEditor.apply(this);
		},
		onDragStop: function (){
			$(this).trigger('variableClipartDragStop');
		}
	});
}

$(document).ready(function (){
	_GLOBALS['editableWidth'] = 12 * pixelsPerInch;
	_GLOBALS['editableHeight'] = 12 * pixelsPerInch;
	_GLOBALS['zoomVal'] = 1;
	
	$('#addVariableTextButton').click(function (){
		var $selectClone = $('select[name=textVariable]');
		popupDialog({
			width: '400px',
			headerText: '<span class="headerText">ADD VARIABLE TEXT</span>',
			headerInfo: '<span class="headerSubText">Please select a variable to use.</span>',
			body: 'Variable: <select name="new_image_variable_text">' + $selectClone.html() + '</select>',
			buttons: {
				'SUBMIT': function (){
					var $newText = $('<span class="variableTextEntry"></span>');
					$newText.zIndex(_GLOBALS['highestZindex']+1);
					_GLOBALS['highestZindex']++;
					
					$newText.data('itemData', {
						fontSize: 1,
						fontColor: 'primary',
						fontFamily: 'arial.ttf',
						fontStroke: 0,
						fontStrokeColor: 'primary',
						textVariable: $('select[name=new_image_variable_text]', this).val(),
						imageText: $('select[name=new_image_variable_text]', this).val(),
						textTransform: 'straight',
						centerVertical: false,
						centerHorizontal: false,
						xPos: 1,
						yPos: 1,
						zIndex: $newText.zIndex(),
						scale: _GLOBALS['scale']
					});
					$newText.html('<img src="' + buildTextLink($newText.data('itemData')) + '" />');
					$('#customizeArea').append($newText);
					makeVariableTextDraggable($newText);
					
					$(this).dialog('destroy');
				}
			}
		});
	});
	
	$('#addVariableClipartButton').click(function (){
		var $selectClone = $('select[name=clipartVariable]');
		popupDialog({
			width: '400px',
			headerText: '<span class="headerText">ADD VARIABLE CLIPART</span>',
			headerInfo: '<span class="headerSubText">Please select a variable to use.</span>',
			body: 'Variable: <select name="new_image_variable_clipart">' + $selectClone.html() + '</select>',
			buttons: {
				'SUBMIT': function (){
					var $newClipart = $('<span class="variableClipartEntry"></span>');
					$newClipart.zIndex(_GLOBALS['highestZindex']+1);
					_GLOBALS['highestZindex']++;
					
					$newClipart.data('itemData', {
						clipartVariable: $('select[name=new_image_variable_clipart]', this).val(),
						centerVertical: false,
						centerHorizontal: false,
						xPos: 1,
						yPos: 1,
						zIndex: $newClipart.zIndex(),
						scale: _GLOBALS['scale']
					});
					$newClipart.html('<img src="' + buildClipartImageLink($newClipart.data('itemData')) + '" />');
					$('#customizeArea').append($newClipart);
					makeVariableClipartDraggable($newClipart);
					
					$(this).dialog('destroy');
				}
			}
		});
	});
	
	$('#saveButton').click(function (){
		var self = this;
		$('.textEntry, .variableTextEntry').each(function (i, el){
			var elData = $(this).data('itemData');
			
			$.each(elData, function (key, value){
				$('<input type="hidden" />')
				.attr('name', 'item[text][' + i + '][' + key + ']')
				.val(value)
				.insertBefore(self);
			});
		});
		
		$('.clipartEntry, .variableClipartEntry').each(function (i, el){
			var elData = $(this).data('itemData');
			
			$.each(elData, function (key, value){
				$('<input type="hidden" />')
				.attr('name', 'item[clipart][' + i + '][' + key + ']')
				.val(value)
				.insertBefore(self);
			});
		});
	});
});