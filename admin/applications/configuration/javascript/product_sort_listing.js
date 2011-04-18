$(document).ready(function (){
	$('.ui-icon-circle-close').click(function (){
		$(this).parent().remove();
	});

	$('.products_listing_module_select').change(function(){
		listingId = $(this).attr('listId');
		selectedVal = $(this).val();
		sortSelectedValue = $('#products_listing_sort_key'+listingId).val();
		if (sortSelectedValue){
			sortSelectedValue = sortSelectedValue.replace('=','~');
		}else{
			sortSelectedValue = '';
		}
		$('#products_listing_sort_key'+listingId).html('<option value="">Loading ...</option>');
		//showAjaxLoader($('#products_listing_sort_key'+listingId));
		$.ajax({
				cache: false,
				url: js_app_link('app=configuration&appPage=product_sort_listing&action=getSortKeys&listing_id='+selectedVal+'&selected='+sortSelectedValue+'&listId='+listingId),
				dataType: 'json',
				success: function (data){
					//hideAjaxLoader($('#products_listing_sort_key'+data.listingId));
					if ($('#products_listing_sort_key'+data.listId)){
						$('#products_listing_sort_key'+data.listId).html(data.html);
					}else{
						$('#products_listing_sort_key_new'+data.listId).html(data.html);
					}
				}
			});
	});

	$('.products_listing_module_select').change();

	$('.newColumnButton').live('click', function (){
		var $newBox = $('#newColumnBox:first').clone().removeAttr('id');
		
		var randNum = Math.floor(Math.random()*10000);
		while($('input[name="products_listing_heading_align[new][' + randNum + ']"]').size() > 0){
			randNum++;
		}

		$(':input, label', $newBox).each(function (){
			if ($(this).attr('name')){
				var name = new String($(this).attr('name'));
				name = name.replace(/RandomNumber/i, randNum);				
				$(this).attr('name', name);
			}
			
			if ($(this).attr('id')){
				var id = new String($(this).attr('id'));
				id = id.replace(/RandomNumber/i, randNum);
				$(this).attr('id', id);
			}
			
			if ($(this).attr('for')){
				var forAttrib = new String($(this).attr('for'));
				forAttrib = forAttrib.replace(/RandomNumber/i, randNum);
				$(this).attr('for', forAttrib);
			}
		});
		
		$('.ui-icon-circle-close', $newBox).click(function (){
			$(this).parent().remove();
		});
		
		$newBox.appendTo($('#columnsHolder')).show();

		$('.products_listing_module_select_new').change(function(){
			selectedVal = $(this).val();
			//showAjaxLoader($('#products_listing_sort_key_new_'+randNum));
			$('#products_listing_sort_key_new_'+randNum).html('<option value="">Loading ...</option>');
			$.ajax({
					cache: false,
					url: js_app_link('app=configuration&appPage=product_sort_listing&action=getSortKeys&listing_id='+selectedVal+'&listId='+randNum),
					dataType: 'json',
					success: function (data){
						$('#products_listing_sort_key_new_'+data.listId).html(data.html);
					}
				});
		});
		$('.products_listing_module_select_new').change();

	});
});