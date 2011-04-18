$(document).ready(function (){
	$('.ui-icon-circle-close').click(function (){
		$(this).parent().remove();
	});
	
	$('.newColumnButton').click(function (){
		var $newBox = $('#newColumnBox:first').clone().removeAttr('id');
		
		var randNum = Math.floor(Math.random()*10000);
		while($('input[name="products_listing_heading_align[new][' + randNum + ']"]').size() > 0){
			randNum++;
		}

		$(':input, label', $newBox).each(function (){
			if ($(this).attr('name')){
				var name = new String($(this).attr('name'));
				name = name.replace(/RandomNumber/i, randNum);
				//alert($(this).attr('name') + '::' + name);
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
	});
});