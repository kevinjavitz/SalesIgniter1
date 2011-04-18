function updateSortOrderInputs(){
	var result = $('#currentOptions').sortable('toArray');
	for(var i=0; i<result.length; i++){
		$('input[name="' + result[i] + '"]').val(i+1);
	}
}

function setUpAddImage($el){
	var $element = $('.addImage', $('#tab_attributes'));
	if ($el){
		$element = $el;
	}

	$element.click(function (){
		var $deleteIcon = $('<span></span>').addClass('ui-icon ui-icon-closethick').css({
			float: 'right',
			position: 'relative'
		});

		setUpAddImageDelete($deleteIcon);

		var $newDiv = $('<div style="display:none;padding:.2em;text-align:right;"></div>')
		.html('View Name: <input type="text" name="' + $(this).attr('view_name') + '" />&nbsp;&nbsp;&nbsp;&nbsp;View Image: <input type="file" name="' + $(this).attr('upload_name') + '" />')
		.append($deleteIcon);

		$newDiv.insertAfter($(this)).show('fast');
	});
}

function setUpAddImageDelete($el){
	var $element = $('td .ui-icon-closethick', $('#tab_attributes'));
	if ($el){
		$element = $el;
	}

	$element.click(function (){
		$(this).parent().remove();
	});
}

function setUpDragArrow($el){
	var $element = $('.ui-icon-arrow-4', $('#tab_attributes'));
	if ($el){
		$element = $el;
	}

	$element.hover(function (){
		this.style.cursor = 'move';
	}, function (){
		this.style.cursor = 'default';
	});
}

function setUpOptionDelete($el){
	var $element = $('.ui-icon-closethick', $('#tab_attributes'));
	if ($el){
		$element = $el;
	}

	$element.hover(function (){
		this.style.cursor = 'pointer';
	}, function (){
		this.style.cursor = 'default';
	}).click(function (){
		$(this).parent().parent().remove();
		updateSortOrderInputs();
	});
}

$(document).ready(function (){
	$('#currentOptions', $('#tab_attributes')).sortable({
		axis: 'y',
		containment: 'parent',
		cursor: 'move',
		handle: '.ui-icon-arrow-4',
		tolerance: 'pointer',
		update: updateSortOrderInputs
	});

	$('#loadSet').click(function (){
		$('<div></div>').dialog({
			autoOpen: true,
			title: 'Select Attribute Group',
			position: 'top',
			open: function (e, ui){
				$('.ui-dialog-content', ui.element).html('<div class="ui-ajax-loader ui-ajax-loader-xlarge" style="margin-left:auto;margin-right:auto;"></div>');
				$.ajax({
					cache: false,
					url: js_app_link('app=products&appPage=new_product&action=getGroupWindow'),
					dataType: 'html',
					success: function (data){
						$('.ui-dialog-content', ui.element).html(data);
					}
				});
			},
			buttons: {
				'Select': function (){
					var self = $(this);
					$.ajax({
						cache: false,
						url: js_app_link('app=products&appPage=new_product&action=loadGroup'),
						data: $('.ui-dialog-content *', self.element).serialize(),
						type: 'post',
						dataType: 'html',
						success: function (data){
							$('#currentGroup').html(data);
							setUpAddImage($('.addImage', $('#currentGroup')));
							self.dialog('close');
						}
					});
				},
				'Cancel': function (){
					$(this).dialog('close');
				}
			}
		});
	});

	$('#newOption').click(function (){
		$('<div></div>').dialog({
			autoOpen: true,
			title: 'Select Attribute',
			position: 'top',
			open: function (e, ui){
				$('.ui-dialog-content', ui.element).html('<div class="ui-ajax-loader ui-ajax-loader-xlarge" style="margin-left:auto;margin-right:auto;"></div>');
				$.ajax({
					cache: false,
					url: js_app_link('app=products&appPage=new_product&action=getOptionWindow'),
					dataType: 'html',
					success: function (data){
						$('.ui-dialog-content', ui.element).html(data);
					}
				});
			},
			buttons: {
				'Select': function (){
					var self = $(this);
					if ($('#option_' + $('select[name="option"]', self).val()).size() > 0){
						alert('The selected option already exists.');
						return false;
					}

					$.ajax({
						cache: false,
						url: js_app_link('app=products&appPage=new_product&action=loadOption'),
						data: $('.ui-dialog-content *', self.element).serialize(),
						type: 'post',
						dataType: 'html',
						success: function (data){
							var $data = $(data);
							setUpDragArrow($('.ui-icon-arrow-4', $data));
							setUpOptionDelete($('.ui-icon-closethick', $data));
							setUpAddImage($('.addImage', $data));

							$('input[name="' + $data.attr('id') + '_sort' + '"]', $data)
							.val($('li', $('#currentOptions')).size() + 1);

							$('<li></li>')
								.attr('id', $data.attr('id') + '_sort')
								.css('padding', '.5em')
								.append($data)
								.appendTo($('#currentOptions'));

							self.dialog('close');
						}
					});
				},
				'Cancel': function (){
					$(this).dialog('close');
				}
			}
		});
	});
	
	$('.attributeStockAddButton').click(function (){
		var self = this;
		var purchaseType = $(this).attr('data-purchase_type');
		var trackMethod = $(this).parent().parent().find('.trackMethodButton:checked').val();
		
		var addPostParams = [];
		addPostParams.push('purchaseType=' + purchaseType);
		addPostParams.push('trackMethod=' + trackMethod);
		addPostParams.push('products_id=' + productID);
		
		$.ajax({
			cache: false,
			url: js_app_link('app=products&appPage=new_product&action=getInventoryTable'),
			data: addPostParams.join('&') + '&' + $(this).parent().find('select').serialize(),
			type: 'post',
			dataType: 'html',
			success: function (data){
				var $data = $(data)
				$data.find('.addBarcode').button();
				$(self).parent().parent().find('.attributesInventoryTables').prepend($data);
			}
		});
	});
	
	$('.attributesInventoryTables .ui-icon-closethick').live('click', function (){
		var trackMethod = $(this).attr('data-track_method');
		var purchaseType = $(this).attr('data-purchase_type');
		var aID_string = $(this).attr('data-attribute_string');
		var productId = $(this).attr('data-product_id');
		var removeText = (trackMethod == 'barcode' ? 'Barcode' : 'Quantity');
		
		var $thisBlock = $(this).parent().parent();
		$thisBlock.fadeTo('fast', .3, function (){
			applyRowOverlay($thisBlock, 'Removing ' + removeText + ', Please Wait', function (){
				$.ajax({
					cache: false,
					url: js_app_link('app=products&appPage=new_product&action=deleteAttributeInventory&trackMethod=' + trackMethod + '&purchaseType=' + purchaseType + '&aID_string=' + aID_string + '&product_id=' + productId),
					dataType: 'json',
					success: function (data){
						var removeBlock = false;
						if (typeof data.errorMsg == 'undefined'){
							removeBlock = true;
						}else{
							alert(data.errorMsg);
						}
						removeRowOverlay($thisBlock, removeBlock);
					}
				});
			});
		});
	});

	setUpAddImage();
	setUpAddImageDelete();
	setUpDragArrow();
	setUpOptionDelete();
	updateSortOrderInputs();
});