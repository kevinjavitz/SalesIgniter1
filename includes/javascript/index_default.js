function makeScroller(selector, items){
	var $container = $('.' + selector + 'Container');
	var $carousel = $('.' + selector + 'Middle', $container);
	var $nextButton = $('.' + selector + 'NextButton', $container);
	var $prevButton = $('.' + selector + 'PrevButton', $container);
	var $extButtonContainer = $('.' + selector + 'ExtButtons', $container);
	var totalItems = $(items, $carousel).size();

	totalItems = Math.ceil(totalItems/3);
	var btnGoArr = [];
	for(var i=0; i<totalItems; i++){
		if (i == 0){
			var className = selector + 'ExtButton1';
		}else{
			var className = selector + 'ExtButton' + (i*3);
		}
		
		var $extButton = $('<a href="#" class="' + className + ' extButton">&nbsp;&nbsp;&nbsp;</a>');

		if (i == 0){
			$extButton.addClass('extButton_active');
		}

		$extButton.hover(function (){
			if (!$(this).hasClass('extButton_active')){
				$(this).addClass('extButton_on');
			}
		}, function (){
			if (!$(this).hasClass('extButton_active')){
				$(this).removeClass('extButton_on');
			}
		}).click(function (){
			$('.extButton_active', $extButtonContainer).removeClass('extButton_active');
			$(this).removeClass('extButton_on').addClass('extButton_active');
		});
		
		btnGoArr.push('.' + className);
		$extButtonContainer.append($extButton);
	}

	if (totalItems <= 1){
		$nextButton.addClass('ui-state-disabled');
	}

	$carousel.jCarouselLite({
		visible: 3,
		start: 0,
		scroll: 3,
		btnNext: '.' + selector + 'NextButton',
		btnPrev: '.' + selector + 'PrevButton',
		btnGo:btnGoArr
	});
}

$(document).ready(function (){
	if ($('.newProductsCarouselContainer').size() > 0){
		makeScroller('newProductsCarousel', '.carouselProduct');
	}
	if ($('.featuredProductsCarouselContainer').size() > 0){
		makeScroller('featuredProductsCarousel', '.carouselProduct');
	}
	if ($('.bestSellersCarouselContainer').size() > 0){
		makeScroller('bestSellersCarousel', '.carouselProduct');
	}

	$('#tab_newProducts, #tab_bestSellers').hide();

	var selectedTab = $('#tab_featuredProducts');
	var selectedTabButton = $('#button_featured');
	$('#button_featured, #button_newproducts, #button_bestsellers').each(function (){
		$(this).unbind('mouseover').unbind('mouseout').bind('unclick', function (){
			$(this).removeClass('ui-state-active').removeClass('ui-state-hover');
			//var curSrc = $(this).attr('src');
			//$(this).removeClass('scrollTabSelected').attr('src', curSrc.replace('_on.', '_off.'));
			selectedTab.hide();
		}).mouseover(function (e){
			//var curSrc = $(this).attr('src');
			if (!$(this).hasClass('ui-state-active')){
				$(this).addClass('ui-state-hover');
			//	$(this).css('cursor', 'pointer').attr('src', curSrc.replace('_off.', '_on.'));
			}
		}).mouseout(function (){
			if (!$(this).hasClass('ui-state-active')){
				$(this).removeClass('ui-state-hover');
			//	var curSrc = $(this).attr('src');
			//	$(this).attr('src', curSrc.replace('_on.', '_off.'));
			}
		}).click(function (){
			if (selectedTabButton){
				selectedTabButton.trigger('unclick');
			}
			$(this).addClass('ui-state-active');
			//$(this).addClass('scrollTabSelected');
			
			if ($(this).attr('id') == 'button_featured'){
				selectedTab = $('#tab_featuredProducts').show();
			}else if ($(this).attr('id') == 'button_newproducts'){
				selectedTab = $('#tab_newProducts').show();
			}else if ($(this).attr('id') == 'button_bestsellers'){
				selectedTab = $('#tab_bestSellers').show();
			}
			
			selectedTabButton = $(this);
		});
	});
	
	$('#button_featured').click();
});