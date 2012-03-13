$(document).ready(function (){
	if($('.slider').size() > 0){
		$('.slider').bxSlider({
			displaySlideQty:1,
			moveSlideQty: 1,
			speed:500,
			easing:'easeInOutQuad',
			prevSelector:'#prevmainGallery',
			nextSelector:'#nextmainGallery',
			pause:3000
		});
	}
	if($('.sliderNivo').size() > 0){
		$('.sliderNivo').nivoSlider({
			controlNavThumbs:true,
			controlNavThumbsFromRel:true
		});
	}
	if($('.fancyList').size() > 0){
		$(".fancybox-button").fancybox({

		});
	}
});