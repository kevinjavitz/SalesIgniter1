function loadEditableArea(loc){
	var addId = '';
	if (loc == 'back'){
		addId = 'Back';
	}
	var areaConfig = {
		parent: $('.productImageEditable' + addId).parent(),
		handles: true,
		onSelectEnd: function (img, selection) {
			$('#selectedX1' + addId).val(selection.x1);
			$('#selectedY1' + addId).val(selection.y1);
			$('#selectedX2' + addId).val(selection.x2);
			$('#selectedY2' + addId).val(selection.y2);
			$('#selectedWidth' + addId).val(selection.width);
			$('#selectedHeight' + addId).val(selection.height);
		}
	};
	
	if ($('#selectedX1' + addId).val() > 0){
		$.extend(areaConfig, {
			persistent: false,
			x1: $('#selectedX1' + addId).val(),
			y1: $('#selectedY1' + addId).val(),
			x2: $('#selectedX2' + addId).val(),
			y2: $('#selectedY2' + addId).val()
		});
	}
	
	$('.productImageEditable' + addId).imgAreaSelect(areaConfig); 
}

$(document).ready(function (){
	$('.designControl').click(function (){
		var $inputs = $('#' + $(this).attr('id') + '_inputs');
		
		if (this.checked){
			$inputs.show();
		}else{
			$inputs.hide();
		}
	});
	
	$('.defaultSetSelector').live('click', function (){
		var clickedBox = this;
		$('.defaultSetSelector').each(function (){
			if ($(this).val() == $(clickedBox).val() && this != clickedBox){
				this.checked = false;
			}
		});
	});
	
	$('#tab_productDesigner_editableAreas_tabs').tabs();
	$('#product_designer_settings_tabs').tabs();
	
	$('#tab_productDesigner_editableAreas_tabs').bind('tabsshow', function (event, ui){
		if ($(ui.panel).attr('id') == 'tab_productDesigner_editableAreas_tab_back'){
			if (!$('#tab_productDesigner_editableAreas_tab_back').hasClass('areaLoaded')){
				loadEditableArea('back');
				$('#tab_productDesigner_editableAreas_tab_back').addClass('areaLoaded');
			}
		}else{
			if (!$('#tab_productDesigner_editableAreas_tab_front').hasClass('areaLoaded')){
				loadEditableArea('front');
				$('#tab_productDesigner_editableAreas_tab_front').addClass('areaLoaded');
			}
		}
	});
	
	$('#product_designer_settings_tabs').bind('tabsshow', function (event, ui){
		if ($(ui.panel).attr('id') != 'tab_productDesigner_editableAreas') return;
		
		if (!$('#tab_productDesigner_editableAreas_tab_front').hasClass('areaLoaded')){
			loadEditableArea('front');
			$('#tab_productDesigner_editableAreas_tab_front').addClass('areaLoaded');
		}
	});
	
	$('.addLightImageSet').click(function (){
		var idx = 0;
		if ($('.lightImageContainer').size() > 0){
			idx = $('.lightImageContainer').size();
		}
		var $frontInput = $('<input type=text />')
		.addClass('uploadManagerInput')
		.attr('name', 'designer_image_light_front[' + idx + ']')
		.attr('id', 'designer_image_light_front_' + idx)
		.attr('data-auto_upload', 'true')
		.attr('data-file_type', 'image');
		
		var $frontDebugger = $('<textarea></textarea>')
		.css({
			width: '300px',
			height: '150px'
		})
		.attr('id', 'designer_image_light_front_' + idx + '_uploadDebugOutput')
		.hide();
		
		var $backInput = $('<input type=text />')
		.addClass('uploadManagerInput')
		.attr('name', 'designer_image_light_back[' + idx + ']')
		.attr('id', 'designer_image_light_back_' + idx)
		.attr('data-auto_upload', 'true')
		.attr('data-file_type', 'image');
		
		var $backDebugger = $('<textarea></textarea>')
		.css({
			width: '300px',
			height: '150px'
		})
		.attr('id', 'designer_image_light_back_' + idx + '_uploadDebugOutput')
		.hide();
		
		var $colorInput = $('<input type=text />').attr('id', 'designer_image_light_color' + idx).attr('name', 'designer_image_light_color[' + idx + ']').addClass('iColorPicker');
		var $defaultInput = $('.defaultSetSelector:eq(0)').parent().parent().parent().parent().clone();
		$('.defaultSetSelector', $defaultInput).each(function (){
			$(this).attr('name', 'designer_image_default[light_' + idx + '][]');
			this.checked = false;
		});
		
		var $fieldSet = $('<fieldset></fieldset>').addClass('lightImageContainer');
		var $legend = $('<legend>Light Image Set</legend>');
		
		$fieldSet.append($legend);
		
		var $divContainer = $('<div />').css('position', 'relative');
		$('<div />').append('<span style="width:150px;display:inline-block;">Front Image:</span>').append($frontInput).append($frontDebugger).appendTo($divContainer);
		$('<div />').append('<span style="width:150px;display:inline-block;">Back Image:</span>').append($backInput).append($backDebugger).appendTo($divContainer);
		$('<div />').append('<span style="width:150px;display:inline-block;">Color:</span>').append($colorInput).appendTo($divContainer);
		$('<div />').append('<span style="width:150px;display:inline-block;">Default Set:</span>').append($defaultInput).appendTo($divContainer);
		
		$('<a href="#" />').addClass('ui-icon ui-icon-circle-close imageSetRemove').css({
			position: 'absolute',
			top: '0px',
			right: '0px'
		}).appendTo($divContainer);
		
		$divContainer.appendTo($fieldSet);
		$fieldSet.appendTo($(this).parent());
		iColorPicker();
		$fieldSet.find('.uploadManagerInput').each(function (){
			uploadManagerField($(this));
		});
	});
	
	$('.addDarkImageSet').click(function (){
		var idx = 0;
		if ($('.darkImageContainer').size() > 0){
			idx = $('.darkImageContainer').size();
		}
		var $frontInput = $('<input type=text />')
		.addClass('uploadManagerInput')
		.attr('name', 'designer_image_dark_front[' + idx + ']')
		.attr('id', 'designer_image_dark_front_' + idx)
		.attr('data-auto_upload', 'true')
		.attr('data-file_type', 'image');
		
		var $frontDebugger = $('<textarea></textarea>')
		.css({
			width: '300px',
			height: '150px'
		})
		.attr('id', 'designer_image_dark_front_' + idx + '_uploadDebugOutput')
		.hide();
		
		var $backInput = $('<input type=text />')
		.addClass('uploadManagerInput')
		.attr('name', 'designer_image_dark_back[' + idx + ']')
		.attr('id', 'designer_image_dark_back_' + idx)
		.attr('data-auto_upload', 'true')
		.attr('data-file_type', 'image');
		
		var $backDebugger = $('<textarea></textarea>')
		.css({
			width: '300px',
			height: '150px'
		})
		.attr('id', 'designer_image_dark_back_' + idx + '_uploadDebugOutput')
		.hide();

		var $colorInput = $('<input type=text />').attr('id', 'designer_image_dark_color' + idx).attr('name', 'designer_image_dark_color[' + idx + ']').addClass('iColorPicker');
		var $defaultInput = $('.defaultSetSelector:eq(0)').parent().parent().parent().parent().clone();
		$('.defaultSetSelector', $defaultInput).each(function (){
			$(this).attr('name', 'designer_image_default[dark_' + idx + '][]');
			this.checked = false;
		});
		
		var $fieldSet = $('<fieldset></fieldset>').addClass('darkImageContainer');
		var $legend = $('<legend>Dark Image Set</legend>');
		
		$fieldSet.append($legend);
		
		var $divContainer = $('<div />').css('position', 'relative');
		$('<div />').append('<span style="width:150px;display:inline-block;">Front Image:</span>').append($frontInput).append($frontDebugger).appendTo($divContainer);
		$('<div />').append('<span style="width:150px;display:inline-block;">Back Image:</span>').append($backInput).append($backDebugger).appendTo($divContainer);
		$('<div />').append('<span style="width:150px;display:inline-block;">Color:</span>').append($colorInput).appendTo($divContainer);
		$('<div />').append('<span style="width:150px;display:inline-block;">Default Set:</span>').append($defaultInput).appendTo($divContainer);
		
		$('<a href="#" />').addClass('ui-icon ui-icon-circle-close imageSetRemove').css({
			position: 'absolute',
			top: '0px',
			right: '0px'
		}).appendTo($divContainer);

		$divContainer.appendTo($fieldSet);
		$fieldSet.appendTo($(this).parent());
		iColorPicker();
		$fieldSet.find('.uploadManagerInput').each(function (){
			uploadManagerField($(this));
		});
	});
	
	$('.imageSetRemove').live('click', function (){
		$(this).parent().parent().remove();
		return false;
	});
	iColorPicker();
});