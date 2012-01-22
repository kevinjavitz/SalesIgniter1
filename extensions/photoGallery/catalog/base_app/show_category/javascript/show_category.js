$(document).ready(function (){
	if($('.slider').size() > 0){
		$('.slider').nivoSlider({
			controlNavThumbs:true,
			controlNavThumbsFromRel:true
		});
	}
	if($('.fancyList').size() > 0){
		$(".fancybox-button").fancybox({

		});
	}
});