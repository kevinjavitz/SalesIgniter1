$(document).ready(function (){
	$('#tabs').tabs();

	$('#write_review').submit(function(){
		var returnVal = true;
		$('.reviewText').each(function(){
			if ($(this).val() == ''){
				if (returnVal === true){
					alert('All the fields are necesary');
				}
				returnVal = false;
			}
		});
		return returnVal;
	});

	var mainProductImageSrc = $('#productsImage img').attr('src');
	var myind = 0;
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
			index:myind,
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
});