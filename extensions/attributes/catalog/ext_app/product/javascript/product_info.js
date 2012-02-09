$(document).ready(function (){
	var $AttributesTable = $('#attributesTable');
	var $newTable = $('<div></div>').addClass('attributesTable');
	
	var $icon = $('<div></div>').css({
		float: 'left',
		position: 'relative',
		marginRight: '.3em'
	}).addClass('ui-widget ui-widget-content ui-corner-all ui-icon ui-icon-triangle-1-e');
	
	$('tr', $AttributesTable).each(function (i, tr){
		if (i == 0) return;
		
		var curHeading = $('td:eq(0)', this).html();
		var $currentContent = $('td:eq(1)', this);
		var currentContent = $currentContent.html();
		
		var $newHeading = $('<div></div>')
		.css({
			padding: '.2em',
			lineHeight: '1.4em',
			fontSize: '1.1em'
		})
		.append($icon.clone())
		.append('<b>' + curHeading.substr(0, (curHeading.length - 1)) + '</b>');
		
		var $newContent = $('<div></div>').css({
			marginBottom: '1em'
		});
		
		if ($(currentContent).hasClass('useImage')){
			var $newContainer = $('<div></div>')
			.addClass('ui-widget ui-widget-content ui-corner-all').css({
				textAlign: 'center',
				padding: '.3em'
			});
			
			if ($(currentContent).hasClass('useMultiImage')){
				var useMultiple = true;
			}else{
				var useMultiple = false;
			}
			
			var items = [];
			if ($(currentContent).attr('tagName') == 'UL'){
				$('li > :radio', $(currentContent)).each(function (){
					var item = {
						original: '#' + $(this).attr('id'),
						current: $('<img></img>').attr('src', 'product_thumb.php?img=' + $(this).attr('imageSrc') + '&h=80')
					};
					
					if (useMultiple == true){
						if ($('ul', $(this).parent()).size() > 0){
							item.views = $('ul', $(this).parent());
						}
					}
					items.push(item);
				});
			}else if ($(currentContent).attr('tagName') == 'select-one'){
				var optionId = $(currentContent).attr('option_id');
				$('option', $(currentContent)).each(function (){
					var item = {
						original: this,
						current: $('<img></img>').attr('src', 'product_thumb.php?img=' + $(this).attr('imageSrc') + '&h=80')
					};
					
					if (useMultiple == true){
						if ($('#images_' + optionId + '_' + $(this).val(), $(this).parent()).size() > 0){
							item.views = $('#images_' + optionId + '_' + $(this).val(), $(this).parent());
						}
					}
					items.push(item);
				});
			}
			
			var $imageList = $('<ul></ul>')
			.css({
				listStyle: 'none',
				padding: 0,
				margin: 0
			});
			for(var j=0; j<items.length; j++){
				var $img = items[j].current;
				$img.css({
					border: '1px solid transparent'
				}).click(function (){
					if ($(this).hasClass('selected')) return;
					
					$('.selected', $(this).parent().parent()).trigger('unclick');
					$(this).addClass('selected').css('border-color', 'black');
					
					if ($(this).data('viewsElement')){
						$('.attributeImageViewButton').remove();
						$('li', $(this).data('viewsElement')).each(function (i, li){
							var self = $(this);
							
							var $newButton = $('<button type="button"></button>')
							.addClass('attributeImageViewButton')
							.html('<span>' + self.html() + '</span>')
							.click(function (){
								$('.attributeImageViewButton.ui-state-active').removeClass('ui-state-active');
								$(this).addClass('ui-state-active');
								
								$('#productsImage').attr('src', self.attr('imgSrc'));
								$('#productsImage').parent().attr('href', self.attr('bigImgSrc'));
								return false;
							})
							.appendTo($('.productInfoImageBoxFields'))
							.button();
							
							if (i == 0) $newButton.click();
						});
					}
					$($(this).data('originalElement')).click();
				}).bind('unclick', function (){
					$(this).css('border-color', 'transparent');
					$(this).removeClass('selected');
				}).data('originalElement', items[j].original);
				
				if (items[j].views){
					$img.data('viewsElement', items[j].views);
				}
				
				var $liItem = $('<li></li>').css({
					padding: '.3em',
					display: 'inline-block'
				}).append(items[j].current);
				
				if (items[j+1]){
					$liItem.css('border-right', '1px solid #8e8e8e');
				}
				$imageList.append($liItem);
			}
			$newContainer.append($imageList);
			
			$newContent.append($newContainer);
		}else{
			if ($(currentContent).attr('tagName') == 'UL'){
				$currentContent.radioButton();
			}
			$newContent.append($currentContent);
			
			//$currentContent.html('');
		}
		$newTable.append($newHeading).append($newContent);
	});
	
	$AttributesTable.hide();
	$newTable.insertAfter($AttributesTable);

	//$AttributesTable.replaceWith($newTable);
});