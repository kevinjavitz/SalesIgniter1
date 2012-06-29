$(document).ready(function (){
	$('#tabs').tabs();
	var mainProductImageSrc = $('#productsImage img').attr('src');
	var myind = 0;

    $('.pprTryButton').click(function(){
        var $Row = $(this).parentsUntil('tbody').last();
        $Row.find('input[name="freeTrialButton"]').val(1);
    });

	$('.additionalImage').live('click',function (e){
		$('.fancyBox.ui-state-active').removeClass('ui-state-active');
		$(this).parent().addClass('ui-state-active');
		
		$('#productsImage img')
		.attr('src', $(this).attr('imgSrc'))
		.attr('alt', $(this).parent().attr('href'));		

		$('#productsImage')
		.attr('href', $(this).attr('imgSrc').replace('&width=250&height=250',''));

		myind = $(this).parent().attr('index');

		return false;
	});


		$('#productsImage').live('click', function(){
		var arr = new Array();		
		$('a[rel=gallery]').each(function(){
			arr.push($(this).attr('href'));
		});
			
		$.fancybox(arr,{
		speedIn: 500,
		speedOut: 500,
		overlayShow: false,
		index: parseInt(myind),
		type: 'image',
		titleShow:false
	});
		return false;

	});
	
	$('#productsImage img').jqueryzoom({
		xzoom: 250, //zooming div default width(default width value is 200)
		yzoom: 250, //zooming div default width(default height value is 200)
		offset: 10 //zooming div default offset(default offset value is 10)
		//position: "right" //zooming div position(default position value is "right")
	});
	$('.inCart').live('click', function() {
			$(this).parent().parent().append('<input type="hidden" name="add_reservation_product">');
			if($('.attributesTable')){
				$('.attributesTable').hide();
				$(this).parent().parent().append($('.attributesTable'));
			}
			$('.selected_period').removeAttr('disabled');
			$(this).closest('form').submit();
			return false;
	});


});