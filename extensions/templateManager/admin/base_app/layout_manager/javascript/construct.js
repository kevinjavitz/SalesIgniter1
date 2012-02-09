/* construct.js */

/* Standard elements that are created on actions */
var wrapperEl = '<div class="container wrapper"></div>';
var containerEl = '<div class="container"></div>';
var columnEl = '<div class="column"></div>';
var listEl = '<ul class="sortableList"></ul>';
var widgetEl = '<li class="widget">' +
	'<div class="iconHolder">' +
	'<span class="ui-icon ui-icon-comment showWidgetData" tooltip="Show Element Data"></span>' +
	'<span class="ui-icon ui-icon-link" tooltip="Create Linked Widget"></span>' +
	'<span class="ui-icon ui-icon-pencil" tooltip="Edit Widget"></span>' +
	'<span class="ui-icon ui-icon-trash" tooltip="Delete Widget"></span>' +
	'</div>' +
	'<span class="widgetName"></span>' +
	'</li>';
var selectedClass = 'ui-state-active';
var hoverClass = 'ui-state-hover';
var selectedOpacity = 1;
var unselectedOpacity = .8;
var Designer;

function getTemplateName() { return templateName; }
function getLayoutId() { return layoutId; }

function getParams(addParams) {
	var getVars = [];
	getVars.push('appExt=templateManager');
	getVars.push('app=layout_manager');
	getVars.push('appPage=editLayout');
	getVars.push('rType=ajax');

	if (addParams){
		$.each(addParams, function () {
			getVars.push(this);
		});
	}

	return getVars.join('&');
}

function showWidgetEditWindow(o) {
	$('#widgetsForm').show();
	$('.boxListing').fadeOut(500, function () {
		$('.editWindow').html(o.htmlTable).fadeIn(500, function () {
			$(this).find('button').button();

			if (o.onShow){
				o.onShow.apply(this);
			}
		});
	});
}

function hideWidgetEditWindow() {
	var self = this;
	$(self).fadeOut(250, function () {
		$(self).empty();
		$('.boxListing').fadeIn(250, function () {
			$('#widgetsForm').hide();
		});
	});
}

function addWidget(draggable, ul) {
	var widgetCode = $(draggable).attr('id');

	var $listItem = $(widgetEl);
	$listItem.attr('tmid', new Date().getTime());
	$listItem.data('widget_code', widgetCode);
	$listItem.data('widget_settings', {});
	$listItem.find('.widgetName').html(widgetCode);
	$listItem.appendTo(ul);

	showSaveLayout();
}

function editWidget(li) {
	var widgetEl = $(li);
	var ajaxLoaderEl = widgetEl.parent().parent();

	$.ajax({
		cache: false,
		url: js_app_link(getParams([
			'action=editInfobox',
			'widgetCode=' + widgetEl.data('widget_code')
		])),
		dataType: 'html',
		type: 'post',
		data: 'widgetSettings=' + encodeURIComponent(JSON.stringify(widgetEl.data('widget_settings'))),
		beforeSend: function () {
			showAjaxLoader(ajaxLoaderEl, 'large');
		},
		complete: function () {
			removeAjaxLoader(ajaxLoaderEl);
		},
		success: function (data) {
			showWidgetEditWindow({
				htmlTable: data,
				onShow: function () {
					var self = this;
					$(self).find('.cancelButton').click(function () {
						hideWidgetEditWindow.apply(self);
					});

					$(self).find('.saveButton').click(function () {
						var buttonEl = $(this);
						$.ajax({
							cache: false,
							url: js_app_link(getParams([
								'action=saveInfobox',
								'widgetCode=' + widgetEl.data('widget_code')
							])),
							dataType: 'json',
							data: $('.editWindow').find('input, textarea, select').serialize(),
							type: 'post',
							beforeSend: function () {
								showAjaxLoader(buttonEl, 'small');
							},
							complete: function () {
								removeAjaxLoader(buttonEl, 'small');
							},
							success: function (data) {
								widgetEl.data('widget_settings', data.widgetSettings);
								if (data.widgetPreview && data.widgetPreview !== false){
									widgetEl.find('.widget_name').html(data.widgetPreview);
								}
								hideWidgetEditWindow.apply(self);
								showSaveLayout();
							}
						});
					});
				}
			});
		}
	});
}

function runLayoutAction(action) {
	var $Column = $('#construct');
	var rType = 'post';

	var getVars = [];
	getVars.push('appExt=templateManager');
	getVars.push('app=layout_manager');
	getVars.push('appPage=editLayout');
	getVars.push('rType=ajax');

	if (action == 'save'){
		getVars.push('layout_id=' + getLayoutId());
		getVars.push('action=saveLayout');
	}

	if (rType == 'post'){
		var postVars = [];
		postVars.push($('#templatePages *').serialize());
		postVars.push('templateData=' + encodeURIComponent(getMarkup()));
	}

	showAjaxLoader($Column, 'xlarge');
	$.ajax({
		cache: false,
		url: js_app_link(getVars.join('&')),
		dataType: 'json',//add pages to post
		type: rType,
		data: (rType == 'post' ? postVars.join('&') : null),
		success: function (data) {
			removeAjaxLoader($Column);
			if (action == 'save'){
				$.each(data.newElementInfo, function (type, tInfo){
					$.each(tInfo, function (tmId, elId){
						if (type == 'containers'){
							$('.container[tmid=' + tmId + ']').removeAttr('tmid').data('container_id', elId);
						}else if (type == 'columns'){
							$('.column[tmid=' + tmId + ']').removeAttr('tmid').data('column_id', elId);
						}else if (type == 'widgets'){
							$('.widget[tmid=' + tmId + ']').removeAttr('tmid').data('widget_id', elId);
						}
					});
				});
				hideSaveLayout();
			}
		}
	});
}

function containerAdd() {
	// append a new container to the #construct space
	var $newContainer = $(containerEl);
	$newContainer.attr('tmid', new Date().getTime());
	var sortOrder = 1;
	$('#construct > div').each(function (){
		$(this).attr('data-sort_order', sortOrder);
		$(this).attr('data-anchor_id', '0');
		$(this).attr('data-is_anchor', '0');
		sortOrder++;
	});

	$('#construct').append($newContainer);

	setupContainer($newContainer, false);
	$newContainer.trigger('click');
	showSaveLayout();
	$('#construct-addColumn').trigger('click');

	return false;
}

function columnAdd() {
	// if no selected container, just cancel
	if ($('.container.' + selectedClass).size() < 1){
		return false;
	}

	var $parentContainer = $('.container.' + selectedClass).not('.wrapper');

	var newColumnList = $(listEl);
	var $newColumn = $(columnEl).append(newColumnList);
	$newColumn.attr('tmid', new Date().getTime());
	var sortOrder = 1;
	$parentContainer.find('.column').each(function (){
		$(this).attr('data-sort_order', sortOrder);
		$(this).attr('data-anchor_id', '0');
		$(this).attr('data-is_anchor', '0');
		sortOrder++;
	});

	$parentContainer.append($newColumn);

	setupColumn($newColumn);
	$newColumn.trigger('click');

	showSaveLayout();

	return false;
}

function deleteElement(el, withChildren) {
	withChildren = withChildren || false;
	var $el = $(el);

	if ($el.hasClass('wrapper')){
		if (withChildren === true){
			$el.children().each(function () {
				deleteElement($(this));
			});
		}
		else {
			if ($el.parent().attr('id') == 'construct'){
				$($el.html()).insertAfter($el);
			}
			else {
				$el.parent().append($el.html());
			}
		}
		$el.remove();
		showSaveLayout();
	}
	else {
		if ($el.hasClass('container')){
			if ($el.next().is('hr')){
				$el.next().remove();
			}

			$el.next('.container').trigger('click');
			$el.remove();

			if ($('.container.' + selectedClass).size() < 1){
				// we deleted the last container, so now select the "new" last container
				$('.container:last').trigger('click');
			}
			showSaveLayout();
		}
		else {
			if ($el.hasClass('column')){
				// remove the selected column from the DOM and select the next one
				$el.next('.column').trigger('click');
				$el.remove();

				if ($('.container.' + selectedClass + ' .column.' + selectedClass).size() < 1){
					// we deleted the last column, so now select the "new" last column and make it the last one
					$('.container.' + selectedClass + ' .column:last').trigger('click');
				}
				showSaveLayout();
			}
		}
	}
}

function setupContainer($container, isWrapper) {
	isWrapper = isWrapper || false;

	if (isWrapper === false){
		$container.droppable({
			accept: function (el) {
				if ($(el).hasClass('column')){
					return true;
				}
				return false;
			},
			hoverClass: 'ui-state-highlight',
			drop: function (e, ui) {
				$(this).append(ui.draggable);
				showSaveLayout();
			}
		});
	}

	if (!$container.data('styles')){
		$container.data('styles', { });
	}

	if ($container.attr('data-styles')){
		$container.attr('data-styles', '');
	}

	if (!$container.data('inputs')){
		$container.data('inputs', { });
	}

	if ($container.attr('data-inputs')){
		$container.attr('data-inputs', '');
	}

	if (!$container.hasClass(selectedClass)){
		$container.fadeTo(0, unselectedOpacity);
	}

	$container.each(function (){
		//$(this).removeAttr('data-container_id');
		//$(this).removeAttr('data-styles');
		//$(this).removeAttr('data-inputs');
		//$(this).removeAttr('data-sort_order');
	});

	//$container.disableSelection();
}

function setupColumn($column) {
	$column.droppable({
		accept: function (el) {
			if ($(el).hasClass('draggableField') && !$(el).hasClass('notInstalled')){
				return true;
			}
			return false;
		},
		hoverClass: 'ui-state-highlight',
		drop: function (e, ui) {
			addWidget(ui.draggable, $column.find('.ui-sortable'));
			showSaveLayout();
		}
	});

	$column.draggable({
		helper: 'clone',
		revert: 'invalid',
		containment: $('#construct')
	});

	$('ul', $column).sortable({
		helper: 'clone',
		containment: $('#construct'),
		connectWith: '.ui-sortable',
		forceHelperSize: true,
		forcePlaceholderSize: true,
		tolerance: 'pointer',
		placeholder: 'ui-state-highlight',
		cursor: 'move',
		items: 'li',
		opacity: .5,
		revert: true,
		update: function () {
			showSaveLayout();
		},
		recieve: function (e, ui) {
			alert('CHECKING');
		}
	});

	if (!$column.data('styles')){
		$column.data('styles', { });
	}

	if (!$column.data('inputs')){
		$column.data('inputs', { });
	}

	if (!$column.hasClass(selectedClass)){
		$column.fadeTo(0, unselectedOpacity);
	}

	$column.each(function (){
		//$(this).removeAttr('data-column_id');
		//$(this).removeAttr('data-styles');
		//$(this).removeAttr('data-inputs');
		//$(this).removeAttr('data-sort_order');
	});

	$column.find('.widget').each(function (){
		//$(this).removeAttr('data-widget_id');
		//$(this).removeAttr('data-widget_code');
		//$(this).removeAttr('data-widget_settings');
		//$(this).removeAttr('data-sort_order');
	});

	//$column.disableSelection();
}

function setupWidgetInstaller() {
	$('.dropToInstall').droppable({
		accept: function (el) {
			if ($(el).hasClass('draggableField') && $(el).hasClass('notInstalled')){
				return true;
			}
			return false;
		},
		hoverClass: 'ui-state-highlight',
		drop: function (e, ui) {
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
				beforeSend: function () {
					showAjaxLoader($this, 'xlarge');
				},
				complete: function () {
					removeAjaxLoader($this);
				},
				success: function (data) {
					$(ui.draggable).removeClass('notInstalled').addClass('installed');
					var lastDraggable = $this.find('.draggableField').last();
					if (lastDraggable.size() <= 0){
						$(ui.draggable).insertBefore($this.find('.ui-helper-clearfix').last());
					}
					else {
						$(ui.draggable).insertAfter(lastDraggable);
					}
				}
			});
		}
	});

	$('.dropToUninstall').droppable({
		accept: function (el) {
			if ($(el).hasClass('draggableField') && $(el).hasClass('installed')){
				return true;
			}
			return false;
		},
		hoverClass: 'ui-state-highlight',
		drop: function (e, ui) {
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
				beforeSend: function () {
					showAjaxLoader($this, 'xlarge');
				},
				complete: function () {
					hideAjaxLoader($this);
				},
				success: function (data) {
					$(ui.draggable).removeClass('installed').addClass('notInstalled');
					var lastDraggable = $this.find('.draggableField').last();
					if (lastDraggable.size() <= 0){
						$(ui.draggable).insertBefore($this.find('.ui-helper-clearfix').last());
					}
					else {
						$(ui.draggable).insertAfter(lastDraggable);
					}

					$('#templateSwitcher').trigger('change');
				}
			});
		}
	});
}

function setupDraggableFields() {
	$('.draggableField').each(function () {
		$(this).draggable({
			revert: 'invalid',
			scroll: false,
			containment: 'document',
			helper: 'clone',
			opacity: .5
		});
	});
}
/* Bind events for Construct interface */


function showAdjustWindow(elBeingAdjusted, insertAfterEl) {
	if ($('.adjustPopup').size() > 0){
		if ($('.adjustPopup').data('targetElement') == elBeingAdjusted){
			$('.adjustPopup .closeWindow').trigger('click');
			return;
		}
		else {
			$('.adjustPopup .closeWindow').trigger('click');
		}
	}

	var $adjustPopup = $('<div class="adjustPopup ui-widget ui-widget-content ui-corner-all"></div>')
		.css({ display: 'block', position: 'relative' })
		.data('targetElement', elBeingAdjusted)
		.append('<span class="ui-icon ui-icon-closethick closeWindow"></span>')
		.append($('#elementProperties').html())
		.insertAfter(insertAfterEl);


	$adjustPopup.LayoutDesigner({
		curElement: elBeingAdjusted
	});
}



function updateBackgroundImage() {
	if ($('.adjustPopup').find('select[name=background_type]').val() != 'image'){
		return;
	}

	var Color = $('.adjustPopup').find('input[name=background_color]').val();
	var Image = $('.adjustPopup').find('input[name=background_image]').val();
	var Repeat = $('.adjustPopup').find('select[name=background_repeat]').val();
	var Position_y = $('.adjustPopup').find('input[name=background_position_y]').val();
	var Position_x = $('.adjustPopup').find('input[name=background_position_x]').val();

	var adjustTargetElement = $('.adjustPopup').data('targetElement');
	adjustTargetElement.css({
		backgroundColor: Color,
		backgroundImage: 'url(' + Image + ')',
		backgroundRepeat: Repeat,
		backgroundPosition: Position_y + '% ' + Position_x + '%'
	});
	updateStylesInfo(adjustTargetElement, 'styles', 'background', Color + ' ' + 'url(' + Image + ') ' + Repeat + ' ' + Position_y + '% ' + Position_x + '%');
}

function updateBreadcrumb() {
	var $bodyIcons = $('<span></span>').addClass('iconBlock')
		.append('<span class="ui-icon ui-icon-pencil editElement" tooltip="Edit Element"></span>');
	$('.containerBreadcrumb').empty().append('<b>Body</b>').append($bodyIcons);
	var El = $('#construct');
	$bodyIcons.find('.ui-icon').each(function () {
		$(this).data('element', El);
	});

	$('#construct').find('.' + selectedClass).each(function () {
		var $icons = $('<span></span>').addClass('iconBlock');
		if ($(this).hasClass('column')){
			$icons.append('<span class="ui-icon ui-icon-comment showColumnData" tooltip="Show Element Data"></span>');
		}else{
			$icons.append('<span class="ui-icon ui-icon-comment showContainerData" tooltip="Show Element Data"></span>');
		}

		if ($(this).hasClass('wrapper')){
			$icons
				.append('<span class="ui-icon ui-icon-arrowreturnthick-1-w removeWrapper" tooltip="Remove Wrapper Element"></span>');
		}
		else {
			if (!$(this).hasClass('column')){
				$icons.append('<span class="ui-icon ui-icon-newwin wrapElement" tooltip="Wrap Element"></span>');
				$icons
					.append('<span class="ui-icon ui-icon-arrowthick-1-n moveContainerUp" tooltip="Move Container And Wrappers Up"></span>');
				$icons
					.append('<span class="ui-icon ui-icon-arrowthick-1-s moveContainerDown" tooltip="Move Container And Wrappers Down"></span>');
			}
		}
		$icons.append('<span class="ui-icon ui-icon-pencil editElement" tooltip="Edit Element"></span>');
		$icons
			.append('<span class="ui-icon ui-icon-closethick deleteElement" tooltip="Delete Element And Children"></span>');

		$('.containerBreadcrumb').append(' &raquo; ');
		if ($(this).hasClass('wrapper')){
			$('.containerBreadcrumb').append('Wrapper');
		}else
		if ($(this).hasClass('container')){
			$('.containerBreadcrumb').append('Container');
		}
		else {
			$('.containerBreadcrumb').append('Column');
		}
		if ($(this).attr('id')){
			$('.containerBreadcrumb').append(' ( <small>' + $(this).attr('id') + '</small> ) ');
		}
		$('.containerBreadcrumb').append($icons);

		var El = $(this);
		$icons.find('.ui-icon').each(function () {
			$(this).data('element', El);
		});
	});
}

function rgb2hex(rgb) {
	if (rgb.length > 0){
		rgb = rgb.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
		function hex(x) {
			return ("0" + parseInt(x).toString(16)).slice(-2);
		}

		return "#" + hex(rgb[1]) + hex(rgb[2]) + hex(rgb[3]);
	}
}

function getLastParent($el) {
	if ($el.parent().attr('id') == 'construct'){
		var Container = $el;
	}
	else {
		var Container = $el.parentsUntil('#construct').last();
	}
	return Container;
}

function showSaveLayout() {
	$('#topButtonBar').hide();
	$('#saveLayoutText').show();
	setConfirmUnload(true);
}

function hideSaveLayout() {
	$('#saveLayoutText').hide();
	$('#topButtonBar').show();
	setConfirmUnload(false);
}

function setConfirmUnload(on) {
	window.onbeforeunload = (on) ? unloadMessage : null;
}

function unloadMessage() {
	return 'The Layout Has Been Changed. If you navigate away from this page without first saving your data, the changes will be lost.';
}

$(document).ready(function() {
	$('#construct-addContainer').click(containerAdd);
	$('#construct-addColumn').click(columnAdd);

	$('#construct-widgets').click(function() {
		if ($('#widgetsForm').css('display') != 'none'){
			$('#widgetsForm').hide();
		}
		else {
			$('#widgetsForm').show();
		}
		return false;
	});

	$('#hideWidgets').click(function() {
		if ($('.editWindow').find('.ui-dialog').size() > 0){
			hideWidgetEditWindow.apply($('.editWindow'));
		}
		else {
			$('#widgetsForm').hide();
		}
	});

	$('#saveLayoutT').click(function() { runLayoutAction('save'); });
	$('#noSaveLayout').click(function () { hideSaveLayout(); });

	/* column and container properties */
	/* end column and container*/

	/*end here*/

	// add "onclick" event to select by click
	$('.column').live('click mouseover mouseout', function(e) {
		if ($(this).hasClass(selectedClass)) return false;

		if (e.type == 'mouseover'){
			$(this).addClass(hoverClass);
		}else if (e.type == 'mouseout'){
			$(this).removeClass(hoverClass);
		}else{
			$('.column.' + selectedClass).fadeTo(0, unselectedOpacity).removeClass(selectedClass);

			if ($('.adjustPopup').size() > 0){
				$('.adjustPopup').find('.closeAdjustPopup').trigger('click');
			}
			$(this).removeClass(selectedClass).addClass(selectedClass).fadeTo(0, selectedOpacity);

			$(this).parent().trigger('click');
		}
		return false;
	});

	$('.container').live('click mouseover mouseout', function(e) {
		if ($(this).hasClass(selectedClass) && e.type != 'click') return false;

		if (e.type == 'mouseover'){
			$(this).addClass(hoverClass);
		}else if (e.type == 'mouseout'){
			$(this).removeClass(hoverClass);
		}else{
			if (!$(this).hasClass(selectedClass)){
				$('.container.' + selectedClass).fadeTo(0, unselectedOpacity).removeClass(selectedClass);

				if ($('.adjustPopup').size() > 0){
					$('.adjustPopup').find('.closeAdjustPopup').trigger('click');
				}
				$(this).removeClass(hoverClass).addClass(selectedClass).fadeTo(0, selectedOpacity);
				$(this).parentsUntil('#construct').addClass(selectedClass).fadeTo(0, selectedOpacity);
				if ($(this).hasClass('wrapper')){
					$(this).find('.container').addClass(selectedClass).fadeTo(0, selectedOpacity);
				}

				if ($(this).find('.column').size() <= 0){
					$('.column.' + selectedClass).removeClass(selectedClass);
				}
				else {
					if ($(this).find('.column.' + selectedClass).size() <= 0){
						$(this).find('.column').first().trigger('click');
					}
				}
			}
			updateBreadcrumb();
		}
		return false;
	});

	$('.ui-icon-pencil').live('click', function (e) {
		e.preventDefault();
		if (!$(this).hasClass('editElement')){
			editWidget($(this).parent().parent());
		}
	});

	$('.ui-icon-link').live('click', function (e) {
		e.preventDefault();
		var newWidget = $(this).parent().parent().clone(true);
		newWidget.insertAfter($(this).parent().parent());
		newWidget.data('widget_settings', { "linked_to": newWidget.data('widget_id') });
		newWidget.attr('data-widget_settings', JSON.stringify({ "linked_to": newWidget.data('widget_id') }));
		newWidget.removeAttr('data-widget_id');
		newWidget.data('widget_id', '');
		newWidget.find('.ui-icon-link').remove();
		showSaveLayout();
	});

	$('.ui-icon-trash').live('click', function (e) {
		e.preventDefault();
		$(this).parent().parent().remove();
		showSaveLayout();
	});

	$('.showWidgetData').live('click', function (){
		var El = $(this).parent().parent();
		$('<div title="Widget Data"></div>').html('<pre>' +
			'<b><u>Widget Id</u></b> :: ' + El.data('widget_id') + '<br>' +
			'<b><u>Widget Code</u></b> :: ' + El.data('widget_code') + '<br>' +
			'<b><u>Sort Order</u></b> :: ' + El.data('sort_order') + '<br>' +
			'<b><u>Widget Settings</u></b> :: ' + JSON.stringify(El.data('widget_settings'), null, '\t') + '<br>' +
			'</pre>')
			.dialog({
			            height: 500,
			            width: 600
			        });
	});

	$('.showContainerData').live('click', function (){
		var El = $(this).data('element');
		$('<div title="Container Data"></div>').html('<pre>' +
			'<b><u>Container Id</u></b> :: ' + El.data('container_id') + '<br>' +
			'<b><u>Sort Order</u></b> :: ' + El.data('sort_order') + '<br>' +
			'<b><u>Styles</u></b> :: ' + JSON.stringify(El.data('styles'), null, '\t') + '<br>' +
			'<b><u>Inputs</u></b> :: ' + JSON.stringify(El.data('inputs'), null, '\t') + '<br>' +
			'</pre>')
			.dialog({
			            height: 500,
			            width: 600
			        });
	});

	$('.showColumnData').live('click', function (){
		var El = $(this).data('element');
		$('<div title="Column Data"></div>').html('<pre>' +
			'<b><u>Column Id</u></b> :: ' + El.data('column_id') + '<br>' +
			'<b><u>Sort Order</u></b> :: ' + El.data('sort_order') + '<br>' +
			'<b><u>Styles</u></b> :: ' + JSON.stringify(El.data('styles'), null, '\t') + '<br>' +
			'<b><u>Inputs</u></b> :: ' + JSON.stringify(El.data('inputs'), null, '\t') + '<br>' +
			'</pre>')
			.dialog({
			            height: 500,
			            width: 600
			        });
	});

	$('li.widget').live('mouseover mouseout', function (e) {
		switch(e.type){
			case 'mouseover':
				this.style.cursor = 'move';
				$(this).addClass('ui-state-highlight');
				break;
			case 'mouseout':
				this.style.cursor = 'default';
				$(this).removeClass('ui-state-highlight');
				break;
		}
	});

	$('.draggableField').live('mouseover mouseout', function (e) {
		switch(e.type){
			case 'mouseover':
				this.style.cursor = 'move';
				$(this).addClass('ui-state-highlight');
				break;
			case 'mouseout':
				this.style.cursor = 'default';
				$(this).removeClass('ui-state-highlight');
				break;
		}
	});

	//$('#construct-header').stickyBar();

	$('input[name=equal_heights]').live('click', function () {
		var adjustTargetElement = $('.adjustPopup').data('targetElement');
		if (this.checked){
			adjustTargetElement.addClass('equalHeights');
		}
		else {
			adjustTargetElement.removeClass('equalHeights');
		}
	});

	$('.adjustPopup .closeAdjustPopup').live('click', function () {
		$('.adjustPopup').remove();
		showSaveLayout();
	});

	$('.deleteElement').live('click', function () {
		if ($(this).data('element').hasClass('wrapper')){
			deleteElement($(this).data('element'), false);
		}
		else {
			deleteElement($(this).data('element'), true);
		}
		updateBreadcrumb();
	});

	$('.removeWrapper').live('click', function () {
		deleteElement($(this).data('element'), false);
		updateBreadcrumb();
	});

	$('.moveContainerUp').live('click', function (e) {
		e.preventDefault();
		var Container = getLastParent($(this).data('element'));
		var prevDiv = Container.prev('.container');
		if (prevDiv){
			var newContainerSort = prevDiv.attr('data-sort_order');
			var newPrevDivSort = Container.attr('data-sort_order');
			Container.insertBefore(prevDiv);
			prevDiv.attr('data-sort_order', newPrevDivSort);
			Container.attr('data-sort_order', newContainerSort);
			showSaveLayout();
		}
	});

	$('.isAnchor').live('change', function(){
			var Container = $('.adjustPopup').data('targetElement');//$(this);
			Container.attr('data-is_anchor', $('.isAnchor option:selected').val());

	});

	$('.anchorID').live('change', function(){
		var Container = $('.adjustPopup').data('targetElement');//$(this);
		Container.attr('data-anchor_id', $('.anchorID option:selected').val());

	});

	$('.moveContainerDown').live('click', function (e) {
		e.preventDefault();
		var Container = getLastParent($(this).data('element'));
		var nextDiv = Container.next('.container');
		if (nextDiv){
			var newContainerSort = nextDiv.attr('data-sort_order');
			var newNextDivSort = Container.attr('data-sort_order');
			Container.insertAfter(nextDiv);
			nextDiv.attr('data-sort_order', newNextDivSort);
			Container.attr('data-sort_order', newContainerSort);
			showSaveLayout();
		}
	});

	$('.wrapElement').live('click', function () {
		var $wrapper = $(wrapperEl);
		$wrapper.data('styles', {});
		$wrapper.data('inputs', {});
		$wrapper.attr('tmid', new Date().getTime());

		var Container = getLastParent($(this).data('element'));
		Container.wrap($wrapper);

		//$wrapper.disableSelection();

		Container.removeClass(selectedClass).trigger('click');
		showSaveLayout();
	});

	$('.editElement').live('click', function () {
		showAdjustWindow($(this).data('element'), $(this).parent().parent());
	});

	$('.container').each(function () {
		setupContainer($(this), $(this).hasClass('wrapper'));
	});
	$('.column').each(function() {
		setupColumn($(this));
	});

	setupDraggableFields();
	setupWidgetInstaller();
	$('.container.' + selectedClass).first().trigger('click');

	$('#construct-borders').click(function (e){
		e.preventDefault();
		if ($(this).html() == 'Show Outline'){
			$('#construct').addClass('showOutline');
			$(this).html('Hide Outline');
		}else{
			$('#construct').removeClass('showOutline');
			$(this).html('Show Outline');
		}
	});
	$('#construct-borders').trigger('click');

	$('.BrowseServerField').live('click focusout', function (e) {
		if (e.type == 'click'){
			browserField = $(this);
			currentFolder = 'templates/' + getTemplateName() + '/images';
			window.open(
				DIR_WS_ADMIN + 'rental_wysiwyg/filemanager/index.php',
				"myWindow",
				"status = 1, height = 600, width = 800, resizable = 1"
				);
		}
	});

	$('.translateText').live('click', function (){
		var inputField = $(this).parent().find('input').first();
		showAjaxLoader(inputField, 'small');
		$.ajax({
			cache: false,
			url: js_app_link('appExt=templateManager&app=layout_manager&appPage=default&action=translateText&from=' + $(this).attr('data-from') + '&to=' + $(this).attr('data-to') + '&string=' + inputField.val()),
			dataType: 'html',
			success: function (html){
				inputField.val(html);
				removeAjaxLoader(inputField);
			}
		});
	});
});


var LayoutDesigner = {
	tabs: {},
	options: {
		curElement: false
	},
	_init: function (o){
		this.options = $.extend(this.options, o);
		var $TabPanel = $('#mainTabPanel');

		this.buildBackgroundColorPicker_RGBA($TabPanel.find('.makeColorPicker_RGBA'));
		this.buildColorPicker_RGB($TabPanel.find('.makeColorPicker'));

		var self = this;
		$.each(this.tabs, function (){
			this.init.apply(self);
		});

		if (this.options.curElement.hasClass('container')){
			$TabPanel.find('.containerOnly').show();
			$TabPanel.find('.columnOnly').hide();
			$TabPanel.find('.widgetOnly').hide();
		}
		else {
			if (this.options.curElement.hasClass('widget')){
				$TabPanel.find('.containerOnly').hide();
				$TabPanel.find('.columnOnly').hide();
				$TabPanel.find('.widgetOnly').show();
			}
			else {
				$TabPanel.find('.containerOnly').hide();
				$TabPanel.find('.columnOnly').show();
				$TabPanel.find('.widgetOnly').hide();
			}
		}

		$TabPanel.tabs();
		$TabPanel.tabs('disable', 6);
		$TabPanel.tabs('disable', 7);
	},
	getCurrentElement: function () {
		return this.options.curElement;
	},
	getElStylesData: function (){
		return this.options.curElement.data('styles');
	},
	getElInputData: function (){
		return this.options.curElement.data('inputs');
	},
	setElStylesData: function (data) {
		this.options.curElement.data('styles', data);
	},
	setElInputData: function (data) {
		this.options.curElement.data('inputs', data);
	},
	updateInputVal: function (key, val) {
		var inputVals = this.getElInputData();
		inputVals[key] = val;
		this.setElInputData(inputVals);
	},
	updateStylesVal: function (key, val, skipCssUpdate) {
		if (!skipCssUpdate){
			this.options.curElement.css(key, val);
		}

		var Styles = this.getElStylesData();
		Styles[key] = val;
		this.setElStylesData(Styles);
	},
	getBrowserInfo: function (){
		$.browser.chrome = /chrome/.test(navigator.userAgent.toLowerCase());
		$.browser.safari = ( $.browser.safari && !$.browser.chrome ) ? false : true;

		$.browser.gecko = /gecko/.test(navigator.userAgent.toLowerCase());
		$.browser.webkit = /applewebkit/.test(navigator.userAgent.toLowerCase());
		$.browser.trident = /trident/.test(navigator.userAgent.toLowerCase());
		$.browser.presto = /presto/.test(navigator.userAgent.toLowerCase());

		var userAgent = navigator.userAgent.toLowerCase();
		var engine = 'unknown';
		var version = 0;
		//alert(userAgent);
		if ($.browser.webkit){
			engine = 'webkit';
		}
		else {
			if ($.browser.gecko){
				engine = 'gecko';
			}
			else {
				if ($.browser.trident){
					engine = 'trident';
				}
				else {
					if ($.browser.presto){
						engine = 'presto';
					}
				}
			}
		}

		// Is this a version of IE?
		if ($.browser.msie){
			userAgent = $.browser.version;
			version = userAgent.substring(0, userAgent.indexOf('.'));
		}
		else {
			if ($.browser.chrome){
				userAgent = userAgent.substring(userAgent.indexOf('chrome/') + 7);
				version = userAgent.substring(0, userAgent.indexOf('.'));
				// If it is chrome then jQuery thinks it's safari so we have to tell it it isn't
				$.browser.safari = false;
			}
			else {
				if ($.browser.safari){
					userAgent = userAgent.substring(userAgent.indexOf('safari/') + 7);
					version = userAgent.substring(0, userAgent.indexOf('.'));
				}
				else {
					if ($.browser.mozilla){
						//Is it Firefox?
						if (navigator.userAgent.toLowerCase().indexOf('firefox') != -1){
							userAgent = userAgent.substring(userAgent.indexOf('firefox/') + 8);
							version = userAgent.substring(0, userAgent.indexOf('.'));
						}
						// If not then it must be another Mozilla
						else {
						}
					}
					else {
						if ($.browser.opera){
							userAgent = userAgent.substring(userAgent.indexOf('version/') + 8);
							version = userAgent.substring(0, userAgent.indexOf('.'));
						}
					}
				}
			}
		}

		return {
			engine: engine,
			version: version
		};
	},
	createPercentSlider: function ($el, options) {
		var defaults = {
			value: 0,
			slide: function () { },
			create: function () { },
			change: function () { },
			stop: function () { },
			start: function () { }
		};
		var o = $.extend(defaults, options);

		$el.slider({
				max: 100,
				min: 0,
				step: 1,
				value: o.value,
				create: o.create,
				change: o.change,
				start: o.start,
				stop: o.stop,
				slide: o.slide
			});
	},
	createAngleSlider: function ($el, options) {
		var defaults = {
			value: 0,
			slide: function () { },
			create: function () { },
			change: function () { },
			stop: function () { },
			start: function () { }
		};
		var o = $.extend(defaults, options);

		$el.slider({
				max: 360,
				min: 0,
				step: 45,
				value: o.value,
				create: o.create,
				change: o.change,
				start: o.start,
				stop: o.stop,
				slide: o.slide
			});
	},
	buildBackgroundColorPicker_RGBA: function($el, o) {
		$el.each(function () {
			var self = this;

			var inputR = o.inputR || $('.colorPickerRGBA_Red');
			var inputG = o.inputG || $('.colorPickerRGBA_Green');
			var inputB = o.inputB || $('.colorPickerRGBA_Blue');
			var inputA = o.inputA || $('.colorPickerRGBA_Alpha');

			$(self).ColorPicker({
				onSubmit: function(hsb, hex, rgb, el) {
					inputR.val(rgb.r);
					inputG.val(rgb.g);
					inputB.val(rgb.b);

					$(self).ColorPickerHide();

					updatePickerTriggerBackground();

					$(self).trigger('onSubmit');
				},
				onBeforeShow: function () {
					$(self).ColorPickerSetColor({
						r: inputR.val(),
						g: inputG.val(),
						b: inputB.val()
					});

					$(self).trigger('onBeforeShow');
				},
				onChange: function (hsb, hex, rgb, el) {
					inputR.val(rgb.r);
					inputG.val(rgb.g);
					inputB.val(rgb.b);

					updatePickerTriggerBackground();

					$(self).trigger('onChange');
				}
			});

			function updatePickerTriggerBackground() {
				var rgbaStr = 'rgba(' +
					inputR.val() + ', ' +
					inputG.val() + ', ' +
					inputB.val() + ', ' +
					(parseInt(inputA.val()) / 100) +
					')';
				$(self).css('background-color', rgbaStr);
				$(self).trigger('onChange');
			}

			inputR.keyup(updatePickerTriggerBackground);
			inputG.keyup(updatePickerTriggerBackground);
			inputB.keyup(updatePickerTriggerBackground);
			inputA.keyup(updatePickerTriggerBackground);

			updatePickerTriggerBackground();
		});
	},
	buildColorPicker_RGB: function($el, o) {
		var thisCls = this;
		$el.each(function () {
			var self = this;
			$(this).ColorPicker({
				onSubmit: function(hsb, hex, rgb, el) {
					$(self).val('#' + hex);
					$(self).ColorPickerHide();

					$(self).trigger('onSubmit');
				},
				onBeforeShow: function () {
					$(this).ColorPickerSetColor(this.value);

					$(self).trigger('onBeforeShow');
				},
				onChange: function (hsb, hex, rgb, el) {
					$(self).val('#' + hex);

					$(self).trigger('onChange');
				}
			});
		});
	}
};
$.widget('ui.LayoutDesigner', LayoutDesigner);

function getBrowserInfo(){
	return Designer.getBrowserInfo();
}
