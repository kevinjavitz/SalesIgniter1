$(document).ready(function (){
	$('select[name=option_type]').change(function (){
		var Option = $(this).val();
		if (Option == ''){
			$('.noSelection').show();
			$('.optionBox').hide();
		}else{
			$('.optionBox, .noSelection').hide();
			$('select[name=option_id[' + $(this).val() + ']]').val('').show();
		}
	});
	
	$('.addOptionButton').click(function (){
		var optionType = $('select[name=option_type]').val();
		var addToList = false;
		if (optionType == 'attribute' || optionType == 'custom_field'){
			var heading = $('select[name=option_id[' + optionType + ']]').find('option:selected').html();
			var optionId = $('select[name=option_id[' + optionType + ']]').find('option:selected').val();
			if (optionId != '' && $('input[name="option[' + optionType + '][]"][value=' + optionId + ']').size() <= 0){
				addToList = true;
			}
		}else{
			if (optionType == 'purchase_type'){
				var heading = 'Purchase Type';
			}else if (optionType == 'price'){
				var heading = 'Price';
			}
			optionId = optionType;
			addToList = true;
		}
		
		if (addToList === true){
			var idx = $('.searchOptions li').size();
			var liHtml = '<li id="options_' + optionType + '_' + optionId + '" data-option_type="' + optionType + '" data-option_id="' + optionId + '">' + 
				'<div class="ui-widget ui-widget-content ui-corner-all">' + 
					'<table cellpadding="2" cellspacing="0" border="0">' +
						'<tr>' +
							'<td valign="top">' +
								'<b>Heading</b><br/><textarea name="option_heading[' + optionType + '][' + optionId + ']" rows="3" cols="50">' + 
									heading +
								'</textarea>' + 
								'<input type="hidden" name="option[' + optionType + '][]" value="' + optionId + '">' + 
								'<input type="hidden" class="sortBox" name="option_sort[' + optionType + '][' + optionId + ']" value="' + idx + '">' + 
							'</td>' +
						'</tr>' +
					'</table>' + 
				'</div>' +
			'</li>';

			$('.searchOptions').append(liHtml);
			$('.searchOptions').sortable('refresh');
		}
	}).button();
	
	$('.searchOptions').sortable({
		revert: true,
		tolerance: 'intersect',
		forcePlaceholderSize: true,
		placeholder: 'ui-state-highlight',
		forceHelperSize: true,
		opacity: 0.5,
		update: function (e, ui){
			var self = ui.item;
			$('.searchOptions li').each(function (i, el){
				$('.sortBox', el).val(i + 1);
			});
		}
	});
	
	$('.searchTrashBin').droppable({
		accept: 'li',
		tolerance: 'touch',
		hoverClass: 'ui-state-highlight',
		drop: function (e, ui){
			$(ui.draggable).remove();
			$('.searchOptions').sortable('refresh');
		}
	});
});