/**
 * Coin Slider - Unique jQuery Image Slider
 * @version: 1.0 - (2010/04/04)
 * @requires jQuery v1.2.2 or later 
 * @author Ivan Lazarevic
 * Examples and documentation at: http://workshop.rs/projects/coin-slider/
 
 * Licensed under MIT licence:
 *   http://www.opensource.org/licenses/mit-license.php
**/

/**
 * jqFancyTransitions - jQuery plugin
 * @version: 1.7 (2010/03/26)
 * @requires jQuery v1.2.2 or later 
 * @author Ivan Lazarevic
 * Examples and documentation at: http://www.workshop.rs/projects/jqfancytransitions
 
 * Dual licensed under the MIT and GPL licenses:
 *   http://www.opensource.org/licenses/mit-license.php
 *   http://www.gnu.org/licenses/gpl.html
**/


(function($) {
	var opts = new Array;
	var level = new Array;
	var img = new Array;
	var links = new Array;
	var linksTarget = new Array;
	var titles = new Array;
	var order = new Array;
	var imgInc = new Array;
	var inc = new Array;
	var stripInt = new Array;
	var imgInt = new Array;	
	var k22 = 0;
	var k33 = 0;

	$.getUrlVars = function(link){
		//alert(link);
		var vars = [], hash;
		if(link){

    var hashes = link.slice(link.indexOf('?') + 1).split('&');
    for(var i = 0; i < hashes.length; i++)
    {
        hash = hashes[i].split('=');
        vars.push(hash[0]);
        vars[hash[0]] = hash[1];
    }
		}
    return vars;
}

	
	$.fn.jqFancyTransitions = $.fn.jqfancytransitions = function(options){
	
	init = function(el){

		opts[el.id] = $.extend({}, $.fn.jqFancyTransitions.defaults, options);
		img[el.id] = new Array(); // images array
		links[el.id] = new Array(); // links array
		titles[el.id] = new Array(); // titles array
		order[el.id] = new Array(); // strips order 
		linksTarget[el.id] = new Array();
		imgInc[el.id] = 0;
		inc[el.id] = 0;

		params = opts[el.id];
		params[el.id] = opts[el.id];

		if(params.effect == 'zipper'){ params.direction = 'alternate'; params.position = 'alternate'; }
		if(params.effect == 'wave'){ params.direction = 'alternate'; params.position = 'top'; }
		if(params.effect == 'curtain'){ params.direction = 'alternate'; params.position = 'curtain'; }	

		// create images, links and titles arrays
		//	$('#'+el.id).html(params.addB(1)+ params.addB(2));
		//	k33 = 2;//to be removed
		
		// width of strips
		
		stripWidth = parseInt(params.width / params.strips); 
		gap = params.width - stripWidth*params.strips; // number of pixels
		stripLeft = 0;

		
		// create images and titles arrays
		$.each($('#'+el.id+' img'), function(i,item){
				img[el.id][i] = $(item).attr('src');						
				links[el.id][i] 		= $(item).parent().is('a') ? $(item).parent().attr('href') : '';									
				linksTarget[el.id][i] 	= $(item).parent().is('a') ? $(item).parent().attr('target') : '';
				titles[el.id][i] 		= $(item).next().is('span') ? $(item).next().html() : '';
				$(item).hide();
				$(item).next().hide();
		});
		

		// set panel
		$('#'+el.id).css({
			'background-image':'url('+img[el.id][0]+')',
			'width': params.width,
			'height': params.height,
			'position': 'relative',
			'background-position': 'top left'
			}).wrap("<div class='coin-slider' id='coin-slider-"+el.id+"' />");	
			
				
			// create title bar
			$('#'+el.id).append("<div class='cs-title' id='cs-title-"+el.id+"' style='position: absolute; bottom:0; left: 0; z-index: 1000;'></div>");

			if(params.showCustom)
				$.navigation(el, 'custom');			
			
			if(params.showNumbers)
				$.navigation(el, 'numbers');
			
			if(params.showArrows)			
				$.navigation(el, 'arrows');
			
			
			if(params.showThumbnails)
				$.navigation(el, 'thumbs');
			
			

		odd = 1;
		// creating bars
		// and set their position
		for(j=1; j < params.strips+1; j++){
			
			if( gap > 0){
				tstripWidth = stripWidth + 1;
				gap--;
			} else {
				tstripWidth = stripWidth;
			}
			
			if(params.links)	
				$('#'+el.id).append("<a href='"+links[el.id][0]+"' class='cs-"+el.id+"' id='cs-"+el.id+j+"' style='width:"+tstripWidth+"px; height:"+params.height+"px; float: left; position: absolute;outline:none;'></a>");
			else
				$('#'+el.id).append("<div class='cs-"+el.id+"' id='cs-"+el.id+j+"' style='width:"+tstripWidth+"px; height:"+params.height+"px; float: left; position: absolute;'></div>");
							
			// positioning bars
			$("#cs-"+el.id+j).css({ 
				'background-position': -stripLeft +'px top',
				'left' : stripLeft 
			});
			
			stripLeft += tstripWidth;

			if(params.position == 'bottom')
				$("#cs-"+el.id+j).css( 'bottom', 0 );
				
			if (j%2 == 0 && params.position == 'alternate')
				$("#cs-"+el.id+j).css( 'bottom', 0 );
	
			// bars order
				// fountain
				if(params.direction == 'fountain' || params.direction == 'fountainAlternate'){ 
					order[el.id][j-1] = parseInt(params.strips/2) - (parseInt(j/2)*odd);
					order[el.id][params.strips-1] = params.strips; // fix for odd number of bars
					odd *= -1;
				} else {
				// linear
					order[el.id][j-1] = j;
				}
	
		}

		if(params[el.id].autoHideNumbers && params[el.id].showNumbers){
			$('#cs-navigation-numbers'+el.id).css({'display':'none'});
			
			$('.cs-'+el.id).mouseover(function(){
				$('#cs-navigation-numbers'+el.id).show();
			});
		
			$('.cs-'+el.id).mouseout(function(){
				$('#cs-navigation-numbers'+el.id).hide();
			});	
		}else{
			$('#cs-navigation-numbers'+el.id).show();
		}
		
		if(params[el.id].autoHideArrows && params[el.id].showArrows){						
			$('.cs-'+el.id).mouseover(function(){			
				$('#cs-navigation-arrows'+el.id).show();
			});
		
			$('.cs-'+el.id).mouseout(function(){
				$('#cs-navigation-arrows'+el.id).hide();
			});	
		}else{
		if(params[el.id].showArrows)
			$('#cs-navigation-arrows'+el.id).show();
		else
			$('#cs-navigation-arrows'+el.id).hide();
		}
		
		if(params[el.id].autoHideThumbs && params[el.id].showThumbnails){
			$('.cs-'+el.id).mouseover(function(){
				$('#cs-navigation-thumbs'+el.id).show();
			});
		
			$('.cs-'+el.id).mouseout(function(){
				$('#cs-navigation-thumbs'+el.id).hide();
			});	
		}else{
			$('#cs-navigation-thumbs'+el.id).show();
		}
		
		if(params[el.id].autoHideThumbsDesc && params[el.id].showThumbsDesc){
			$('.cs-'+el.id).mouseover(function(){
				$('#cs-navigation-thumbs-desc'+el.id).show();
			});
		
			$('.cs-'+el.id).mouseout(function(){
				$('#cs-navigation-thumbs-desc'+el.id).hide();
			});	
		}else{
			$('#cs-navigation-thumbs-desc'+el.id).show();
		}
		
		if(params[el.id].autoHideCustom && params[el.id].showCustom){
			$('.cs-'+el.id).mouseover(function(){
				$('#cs-navigation-custom'+el.id).show();
			});
		
			$('.cs-'+el.id).mouseout(function(){
				$('#cs-navigation-custom'+el.id).hide();
			});	
		}else{
			$('#cs-navigation-custom'+el.id).show();
		}
		
		$('#cs-title-'+el.id).css({'width':(params[el.id].width-20)+"px"});
		
		if(params[el.id].autoHideTitle){	
			$('.cs-'+el.id).mouseover(function(){
				$('#cs-title-'+el.id).show();
			});
		
			$('.cs-'+el.id).mouseout(function(){
				$('#cs-title-'+el.id).hide();
			});	
		}else{
			$('#cs-title-'+el.id).show();
		}
			
			if(params[el.id].hoverPause){	
				$('.cs-'+el.id).mouseover(function(){
					params[el.id].pause = true;
				});
			
				$('.cs-'+el.id).mouseout(function(){
					params[el.id].pause = false;
				});	
				
				$('#cs-title-'+el.id).mouseover(function(){
					params[el.id].pause = true;
				});
			
				$('#cs-title-'+el.id).mouseout(function(){
					params[el.id].pause = false;
				});	
			}
		$.transition(el) ;
		clearInterval(imgInt[el.id]);	
		imgInt[el.id] = setInterval(function() { $.transition(el)  }, params.delay+params.stripDelay*params.strips);

	};

	// transition
	$.transition = function(el,direction){

		if(opts[el.id].pause == true) return;

		stripInt[el.id] = setInterval(function() { $.strips(order[el.id][inc[el.id]], el)  },opts[el.id].stripDelay);
		
		$('#'+el.id).css({ 'background-image': 'url('+img[el.id][imgInc[el.id]]+')' });
		
		if(typeof(direction) == "undefined")
			imgInc[el.id]++;
		else
			if(direction == 'prev')
				imgInc[el.id]--;
			else
				imgInc[el.id] = direction;

		if  (imgInc[el.id] == params.nImages) {
			imgInc[el.id] = 0;
		}
		
		if (imgInc[el.id] == -1){
			imgInc[el.id] = params.nImages-1;
		}
		
		inc[el.id] = 0;

		if(opts[el.id].direction == 'random')
			$.fisherYates (order[el.id]);
			
		if((opts[el.id].direction == 'right' && order[el.id][0] == 1) 
			|| opts[el.id].direction == 'alternate'
			|| opts[el.id].direction == 'fountainAlternate')			
				order[el.id].reverse();		
				
				
		
		if (params[el.id].showCustom){
				$('.cs-button-custom'+el.id).removeClass('cs-activecustom');
				$('#cs-button-custom'+el.id+"-"+(imgInc[el.id]+1)).addClass('cs-activecustom');
			}
			
		if (params[el.id].showNumbers){
				$('.cs-button-numbers'+el.id).removeClass('cs-activenumbers');
				$('#cs-button-numbers'+el.id+"-"+(imgInc[el.id]+1)).addClass('cs-activenumbers');
			}
		
		if (params[el.id].showThumbnails){
				$('.cs-button-thumbs'+el.id).removeClass('cs-activethumbs');
				$('#cs-button-thumbs'+el.id+"-"+(imgInc[el.id]+1)).addClass('cs-activethumbs');
			}
			
			if(titles[el.id][imgInc[el.id]]){
				$('#cs-title-'+el.id).css({ 'opacity' : 0 }).animate({ 'opacity' : params[el.id].opacity }, params[el.id].titleSpeed);
				$('#cs-title-'+el.id).html(titles[el.id][imgInc[el.id]]);
			} else {
				$('#cs-title-'+el.id).css('opacity',0);
			}				
			if(params[el.id].onView)
						params[el.id].onView($.getUrlVars(links[el.id][imgInc[el.id]])["bid"]);
			
			changed = false;
		/*	if(params[el.id].addB){
				//$('#'+el.id).children('.chtml').remove();
				//k33++;
				
				if(params[el.id].addB(k33) && k33<4){
					changed = true;
					$('#'+el.id).append(params[el.id].addB(k33));
			
					$.each($('#'+el.id+' img'), function(i,item){
					
					img[el.id][i] = $(item).attr('src');						
					links[el.id][i] 		= $(item).parent().is('a') ? $(item).parent().attr('href') : '';									
					linksTarget[el.id][i] 	= $(item).parent().is('a') ? $(item).parent().attr('target') : '';
					titles[el.id][i] 		= $(item).next().is('span') ? $(item).next().html() : '';			
					$(item).hide();
					$(item).next().hide();
				});		
				}				
			}
			
			k22++;//to be removed
			
			if(params[el.id].removeB && k22>100){
				changed = true;
				k22 = 0;//to be removed
				s = params[el.id].removeB();
				$('#'+el.id).children('#chtml'+s).remove();
				params[el.id].nImages = 2;
				imgInc[el.id] = 0;
				$.each($('#'+el.id+' img'), function(i,item){
					img[el.id][i] = $(item).attr('src');						
					links[el.id][i] 		= $(item).parent().is('a') ? $(item).parent().attr('href') : '';									
					linksTarget[el.id][i] 	= $(item).parent().is('a') ? $(item).parent().attr('target') : '';
					titles[el.id][i] 		= $(item).next().is('span') ? $(item).next().html() : '';			
					$(item).hide();
					$(item).next().hide();
				});		
			}	
	*/
			if(changed){
				if(params[el.id].showCustom){
					$('#cs-buttons-custom'+el.id).remove();
					$.navigation(el, 'custom');			
				}
			
				if(params[el.id].showNumbers){
					$('#cs-buttons-numbers'+el.id).remove();
					$.navigation(el, 'numbers');
				}
			
				if(params[el.id].showThumbnails){			
					$('#cs-buttons-thumbs'+el.id).remove();
					$.navigation(el, 'thumbs');
				}
			}
			
	};


	// strips animations
	$.strips = function(itemId, el){

		temp = opts[el.id].strips;
		if (inc[el.id] == temp) {
			clearInterval(stripInt[el.id]);
			return;
		}
		$('.cs-'+el.id).attr('href',links[el.id][imgInc[el.id]]);
		if(opts[el.id].position == 'curtain'){
			currWidth = $('#cs-'+el.id+itemId).width();
			$('#cs-'+el.id+itemId).css({ width: 0, opacity: 0, 'background-image': 'url('+img[el.id][imgInc[el.id]]+')' });
			$('#cs-'+el.id+itemId).animate({ width: currWidth, opacity: 1 }, 1000);
		} else {
			$('#cs-'+el.id+itemId).css({ height: 0, opacity: 0, 'background-image': 'url('+img[el.id][imgInc[el.id]]+')' });
			$('#cs-'+el.id+itemId).animate({ height: opts[el.id].height, opacity: 1 }, 1000);
		}
		
		inc[el.id]++;
		
	};

	// navigation
	$.navigation = function(el, t){
		
		if(t == 'arrows'){
	
			$(el).append("<div id='cs-navigation-arrows"+el.id+"'></div>");
			if(params[el.id].autoHideArrows)
				$('#cs-navigation-arrows'+el.id).hide();
			
			$('#cs-navigation-arrows'+el.id).append("<a href='#' id='cs-prev-arrows"+el.id+"' class='cs-prevarrows'>prev</a>");
			$('#cs-navigation-arrows'+el.id).append("<a href='#' id='cs-next-arrows"+el.id+"' class='cs-nextarrows'>next</a>");
			$navarrp =$('#cs-prev-arrows'+el.id).css({
				'position' 	: 'absolute',
				'top'		: params[el.id].height/2 - 45,
				'left'		: 0,
				'z-index' 	: 1001,
				'line-height': '30px',
				'opacity'	: params[el.id].opacity
			}).click( function(e){
					e.preventDefault();
					$.transition(el,'prev');
					clearInterval(imgInt[el.id]);
					imgInt[el.id] = setInterval(function() { $.transition(el)  }, params.delay+params.stripDelay*params.strips);		
			});

			if(params[el.id].autoHideArrows)
								$navarrp.mouseover( function(){ $('#cs-navigation-arrows'+el.id).show() }).mouseout( function(){ $('#cs-navigation-arrows'+el.id).hide() });

	
			$navarrn = $('#cs-next-arrows'+el.id).css({
				'position' 	: 'absolute',
				'top'		: params[el.id].height/2 - 45,
				'right'		: 0,
				'z-index' 	: 1001,
				'line-height': '30px',
				'opacity'	: params[el.id].opacity
			}).click( function(e){
				e.preventDefault();
				$.transition(el);
				clearInterval(imgInt[el.id]);
				imgInt[el.id] = setInterval(function() { $.transition(el)  }, params.delay+params.stripDelay*params.strips);
			});
			if(params[el.id].autoHideArrows)
											$navarrn.mouseover( function(){ $('#cs-navigation-arrows'+el.id).show() }).mouseout( function(){ $('#cs-navigation-arrows'+el.id).hide() });

		}
		if(t == 'custom'){
			// image buttons
			$("<div id='cs-buttons-custom"+el.id+"' class='cs-buttonscustom'></div>").appendTo($('#coin-slider-'+el.id));

			
			for(k=1;k<params[el.id].nImages+1;k++){
				$('#cs-buttons-custom'+el.id).append("<a href='#' class='cs-button-custom"+el.id+"' id='cs-button-custom"+el.id+"-"+k+"'>"+k+"</a>");
			}
			
			$.each($('.cs-button-custom'+el.id), function(i,item){
				$(item).click( function(e){
					$('.cs-button-custom'+el.id).removeClass('cs-activecustom');
					$(this).addClass('cs-activecustom');
					e.preventDefault();
				$.transition(el,i);
				clearInterval(imgInt[el.id]);
				imgInt[el.id] = setInterval(function() { $.transition(el)  }, params.delay+params.stripDelay*params.strips);							
				})
			});	
			
			/*$('#cs-navigation-'+el.id+' a').mouseout(function(){
				$('#cs-navigation-'+el.id).hide();
				params[el.id].pause = false;
			});*/						

			$("#cs-buttons-custom"+el.id).css({
				'left'			: (params[el.id].width-25*params[el.id].nImages)+"px",				
				'position'		: 'relative'
				
			});
			
		}
		if(t == 'numbers'){
			// image buttons
			$("<div id='cs-buttons-numbers"+el.id+"' class='cs-buttonsnumbers'></div>").appendTo($('#coin-slider-'+el.id));

			
			for(k=1;k<params[el.id].nImages+1;k++){
				$('#cs-buttons-numbers'+el.id).append("<a href='#' class='cs-button-numbers"+el.id+"' id='cs-button-numbers"+el.id+"-"+k+"'>"+k+"</a>");
			}
			
			$.each($('.cs-button-numbers'+el.id), function(i,item){
				$(item).click( function(e){
					$('.cs-button-numbers'+el.id).removeClass('cs-activenumbers');
					$(this).addClass('cs-activenumbers');
				e.preventDefault();
				$.transition(el,i);
				clearInterval(imgInt[el.id]);
				imgInt[el.id] = setInterval(function() { $.transition(el)  }, params.delay+params.stripDelay*params.strips);								
				})
			});	
			
			/*$('#cs-navigation-'+el.id+' a').mouseout(function(){
				$('#cs-navigation-'+el.id).hide();
				params[el.id].pause = false;
			});*/						

			$("#cs-buttons-numbers"+el.id).css({
				'left'			: "25px",
				'position'		: 'relative'
				
			});
			
		}		
		
		if(t == 'thumbs'){
			// image buttons
			$("<div id='cs-buttons-thumbs"+el.id+"' class='cs-buttonsthumbs'></div>").appendTo($('#coin-slider-'+el.id));
			
			$('#cs-buttons-thumbs'+el.id).css({'width':params[el.id].thumbsWidth * params[el.id].nImages +"px", 'height':params[el.id].thumbsHeight+"px"});
			
			for(k=1;k<params[el.id].nImages+1;k++){
			
			if(params[el.id].useAutoResize){
				$('#cs-buttons-thumbs'+el.id).append("<a href='#' class='cs-button-thumbs"+el.id+"' id='cs-button-thumbs"+el.id+"-"+k+"'>"+"<img border='0' src='imagick_thumb.php?width="+params[el.id].thumbsWidth+"&height="+params[el.id].thumbsHeight+"&imgSrc="+img[el.id][k-1]+"'/></a>");
				}else if (params[el.id].useUpThumbs){
					ext = img[el.id][k-1].substr(img[el.id][k-1].length-4,4);
					$('#cs-buttons-thumbs'+el.id).append("<a href='#' class='cs-button-thumbs"+el.id+"' id='cs-button-thumbs"+el.id+"-"+k+"'>"+"<img border='0' src='"+img[el.id][k-1].substr(0,img[el.id][k-1].length-4)+"_thumb"+ext+"'/></a>");
				}else{
					$('#cs-buttons-thumbs'+el.id).append("<a href='#' class='cs-button-thumbs"+el.id+"' id='cs-button-thumbs"+el.id+"-"+k+"'>"+"<img border='0' width='"+params[el.id].thumbsWidth+"' height='"+params[el.id].thumbsHeight+"' src='"+img[el.id][k-1]+"'/></a>");
				}
				
			}
			
			$.each($('.cs-button-thumbs'+el.id), function(i,item){
				$(item).click( function(e){
					$('.cs-button-thumbs'+el.id).removeClass('cs-activethumbs');
					$(this).addClass('cs-activethumbs');
						e.preventDefault();
						$.transition(el,i);
						clearInterval(imgInt[el.id]);
						imgInt[el.id] = setInterval(function() { $.transition(el)  }, params.delay+params.stripDelay*params.strips);				
				})
			});	
			
			/*$('#cs-navigation-'+el.id+' a').mouseout(function(){
				$('#cs-navigation-'+el.id).hide();
				params[el.id].pause = false;
			});*/						

			$("#cs-buttons-thumbs"+el.id).css({
				'left'			: '25px',
				'top'			: (params[el.id].thumbsHeight)+"px",	
				'width'			: (params[el.id].thumbsWidth*3 + 50)+"px",
				'height'		: (params[el.id].thumbsHeight*3)+"px",
				'position'		: 'relative'
				
			});
			
		}

			
	}
	


	// shuffle array function
	$.fisherYates = function(arr) {
	  var i = arr.length;
	  if ( i == 0 ) return false;
	  while ( --i ) {
	     var j = Math.floor( Math.random() * ( i + 1 ) );
	     var tempi = arr[i];
	     var tempj = arr[j];
	     arr[i] = tempj;
	     arr[j] = tempi;
	   }
	}	
		
	this.each (
		function(){ init(this); }
	);
		
};

	// default values
	$.fn.jqFancyTransitions.defaults = {	
		strips: 10, // number of strips
		delay: 5000, // delay between images in ms
		stripDelay: 50 // delay beetwen strips in ms		
	};
	
})(jQuery);


(function($) {

	var params 		= new Array;
	var order		= new Array;
	var images		= new Array;
	var links		= new Array;
	var linksTarget = new Array;
	var titles		= new Array;
	var interval	= new Array;
	var imagePos	= new Array;
	var appInterval = new Array;	
	var squarePos	= new Array;	
	var reverse		= new Array;
	var k22 = 0;
	var k33 = 0;
	$.fn.coinslider= $.fn.CoinSlider = function(options){
		
		init = function(el){				
			order[el.id] 		= new Array();	// order of square appereance
			images[el.id]		= new Array();
			links[el.id]		= new Array();
			linksTarget[el.id]	= new Array();
			titles[el.id]		= new Array();
			imagePos[el.id]		= 0;
			squarePos[el.id]	= 0;
			reverse[el.id]		= 1;						
				
			params[el.id] = $.extend({}, $.fn.coinslider.defaults, options);
						
			// create images, links and titles arrays
			//$('#'+el.id).html(params[el.id].addB(1)+ params[el.id].addB(2));
			//k33 = 2;//to be removed
						
			
			$.each($('#'+el.id+' img:first-child,'+'#'+el.id+' object:first-child,'+'#'+el.id+' p:first-child'), function(i,item){
			
				images[el.id][i] 		= $(item).attr('src');
				links[el.id][i] 		= $(item).parent().is('a') ? $(item).parent().attr('href') : '';				
				linksTarget[el.id][i] 	= $(item).parent().is('a') ? $(item).parent().attr('target') : '';
				titles[el.id][i] 		= $(item).next().is('span') ? $(item).next().html() : '';
				$(item).next().hide();			
				if (params[el.id].effect == 'fade' || params[el.id].effect == 'slideLeft' ||  params[el.id].effect == 'slideTop' ||  params[el.id].effect == 'none' ){											
					$(item).css({'border':'0'});
					$(item).parent().attr('id',el.id+"chtml"+i);
					//$(item).parent().css('z-index','200'+i);
				}else{
					$(item).hide();
				}
				
			});		
			
			//$('.chtml').mouseover(params[el.id].onClick());
				if (params[el.id].effect == 'fade' || params[el.id].effect == 'slideLeft' ||  params[el.id].effect == 'slideTop' ||  params[el.id].effect == 'none' ){																
				}else{
					$(el).css({	'background-image':'url('+images[el.id][0]+')'});
				}
			// set panel
			$(el).css({			
							
				'width': params[el.id].width,
				'height': params[el.id].height,
				'overflow': 'hidden',
				'position': 'relative',
				'background-position': 'top left'
			}).wrap("<div class='coin-slider' id='coin-slider-"+el.id+"' />");	
			
			if (params[el.id].effect == 'fade' || params[el.id].effect == 'slideLeft' ||  params[el.id].effect == 'slideTop' ||  params[el.id].effect == 'none' ){
				$("#"+el.id).addClass('cs-'+el.id);
			}
				
			// create title bar
			$('#'+el.id).append("<div class='cs-title' id='cs-title-"+el.id+"' style='position: absolute; bottom:0; left: 0; z-index: 1000;'></div>");
						
			$.setFields(el);
			
			if(params[el.id].showCustom)
				$.setNavigation(el, 'custom');			
			
			if(params[el.id].showNumbers)
				$.setNavigation(el, 'numbers');
			
			if(params[el.id].showArrows)			
				$.setNavigation(el, 'arrows');
			
			
			if(params[el.id].showThumbnails)
				$.setNavigation(el, 'thumbs');
			
			
			$.transition(el,0);
			$.transitionCall(el);
				
		}
		
		
		$.setB = function (el, p){
		
		for(k = 0; k < params[el.id].nImages; k++){
			if(k !=p ){
				switch (params[el.id].effect){ 
			
                case "slideLeft":
                    $sl = $("#"+el.id+"chtml" + k).css({ position:"absolute" }).css({"left": -params[el.id].width, "z-index":"101"});
                    break;
                case "slideTop":
                   $sl = $("#"+el.id+"chtml" + k).css({ position:"absolute" }).css({"top": -params[el.id].height, "z-index":"101"});
                    break;
                case "fade":
                    $sl = $("#"+el.id+"chtml" + k).css({ position:"absolute" }).css({ "opacity":0, "z-index":"101" });
                    break;
				case "none":
                    $sl = $("#"+el.id+"chtml" + k).css({ position:"absolute" }).css({ "display":"none", "z-index":"101" });
                    break;	
				}			
			}else{
				
				switch (params[el.id].effect){ 
			
                case "slideLeft":
					$sl = $("#"+el.id+"chtml" + k).css({ position:"absolute" }).css({"left": 0,'z-index':"100"});
                    break;
                case "slideTop":
                   $sl = $("#"+el.id+"chtml" + k).css({ position:"absolute" }).css({"top": 0,'z-index':"100"});
                    break;
                case "fade":
                    $sl = $("#"+el.id+"chtml" + k).css({ position:"absolute" }).css({"opacity": 1,'z-index':"100"});
                    break;
				case "none":
                    $sl = $("#"+el.id+"chtml" + k).css({ position:"absolute" }).css({"display": "",'z-index':"100"});
                    break;		
				}			
				
			}		
		}
	}
	
		
		$.setFields = function(el){
			
	if (params[el.id].effect == 'fade' || params[el.id].effect == 'slideLeft' ||  params[el.id].effect == 'slideTop' ||  params[el.id].effect == 'none' ){				
			//$.setB(el, 0);
		}
		else{
			
			tWidth = sWidth = parseInt(params[el.id].width/params[el.id].spw);
			tHeight = sHeight = parseInt(params[el.id].height/params[el.id].sph);
			
			counter = sLeft = sTop = 0;
			tgapx = gapx = params[el.id].width - params[el.id].spw*sWidth;
			tgapy = gapy = params[el.id].height - params[el.id].sph*sHeight;
			
			for(i=1;i <= params[el.id].sph;i++){
				gapx = tgapx;
				
					if(gapy > 0){
						gapy--;
						sHeight = tHeight+1;
					} else {
						sHeight = tHeight;
					}
				
				for(j=1; j <= params[el.id].spw; j++){	

					if(gapx > 0){
						gapx--;
						sWidth = tWidth+1;
					} else {
						sWidth = tWidth;
					}

					order[el.id][counter] = i+''+j;
					counter++;
					
					if(params[el.id].links)
						$('#'+el.id).append("<a href='"+links[el.id][0]+"' class='cs-"+el.id+"' id='cs-"+el.id+i+j+"' style='width:"+sWidth+"px; height:"+sHeight+"px; float: left; position: absolute;'></a>");
					else
						$('#'+el.id).append("<div class='cs-"+el.id+"' id='cs-"+el.id+i+j+"' style='width:"+sWidth+"px; height:"+sHeight+"px; float: left; position: absolute;'></div>");
								
					
					
					// positioning squares
					$("#cs-"+el.id+i+j).css({ 
						'background-position': -sLeft +'px '+(-sTop+'px'),
						'left' : sLeft ,
						'top': sTop
					});
				
					sLeft += sWidth;
				}

				sTop += sHeight;
				sLeft = 0;					
					
			}
		}
			
		if(params[el.id].autoHideNumbers && params[el.id].showNumbers){
			$('#cs-navigation-numbers'+el.id).css({'display':'none'});
			
			$('.cs-'+el.id).mouseover(function(){
				$('#cs-navigation-numbers'+el.id).show();
			});
		
			$('.cs-'+el.id).mouseout(function(){
				$('#cs-navigation-numbers'+el.id).hide();
			});	
		}else{
			$('#cs-navigation-numbers'+el.id).show();
		}
		
		if(params[el.id].autoHideArrows && params[el.id].showArrows){						
			$('.cs-'+el.id).mouseover(function(){			
				$('#cs-navigation-arrows'+el.id).show();
			});
		
			$('.cs-'+el.id).mouseout(function(){
				$('#cs-navigation-arrows'+el.id).hide();
			});	
		}else{
			
		if(params[el.id].showArrows)
			$('#cs-navigation-arrows'+el.id).show();
		else
			$('#cs-navigation-arrows'+el.id).hide();
		}
		
		if(params[el.id].autoHideThumbs && params[el.id].showThumbnails){
			$('.cs-'+el.id).mouseover(function(){
				$('#cs-navigation-thumbs'+el.id).show();
			});
		
			$('.cs-'+el.id).mouseout(function(){
				$('#cs-navigation-thumbs'+el.id).hide();
			});	
		}else{
			$('#cs-navigation-thumbs'+el.id).show();
		}
		
		if(params[el.id].autoHideThumbsDesc && params[el.id].showThumbsDesc){
			$('.cs-'+el.id).mouseover(function(){
				$('#cs-navigation-thumbs-desc'+el.id).show();
			});
		
			$('.cs-'+el.id).mouseout(function(){
				$('#cs-navigation-thumbs-desc'+el.id).hide();
			});	
		}else{
			$('#cs-navigation-thumbs-desc'+el.id).show();
		}
		
		if(params[el.id].autoHideCustom && params[el.id].showCustom){
			$('.cs-'+el.id).mouseover(function(){
				$('#cs-navigation-custom'+el.id).show();
			});
		
			$('.cs-'+el.id).mouseout(function(){
				$('#cs-navigation-custom'+el.id).hide();
			});	
		}else{
			$('#cs-navigation-custom'+el.id).show();
		}
		
		$('#cs-title-'+el.id).css({'width':(params[el.id].width-20)+"px"});
		
		if(params[el.id].autoHideTitle){	
			$('.cs-'+el.id).mouseover(function(){
				$('#cs-title-'+el.id).show();
			});
		
			$('.cs-'+el.id).mouseout(function(){
				$('#cs-title-'+el.id).hide();
			});	
		}else{
			$('#cs-title-'+el.id).show();
		}
			
			if(params[el.id].hoverPause){	
				$('.cs-'+el.id).mouseover(function(){
					params[el.id].pause = true;
				});
			
				$('.cs-'+el.id).mouseout(function(){
					params[el.id].pause = false;
				});	
				
				$('#cs-title-'+el.id).mouseover(function(){
					params[el.id].pause = true;
				});
			
				$('#cs-title-'+el.id).mouseout(function(){
					params[el.id].pause = false;
				});	
			}
			
		};
				
	
    $.showImage = function(i, el) {
     
        switch (params[el.id].effect)
        { 
            case "slideLeft": $("#"+el.id+"chtml"+i).animate({ left: 0 }, params[el.id].sDelay*10, params[el.id].easefunction, function(){$.setB(el, i);});
                break;
            case "slideTop":  $("#"+el.id+"chtml"+i).stop().animate({ top: 0 }, params[el.id].sDelay*10, params[el.id].easefunction, function(){$.setB(el, i);});
                break;
            case "fade":
                    $("#"+el.id+"chtml"+i).stop().animate({ opacity: 1 }, params[el.id].sDelay*20, params[el.id].easefunction, function(){$.setB(el, i);});
					break;
			case "none":
                    $("#"+el.id+"chtml"+i).css({ "display": "" });
					$.setB(el, i);
                break;
        }
	}
				
		
		$.transitionCall = function(el){
			clearInterval(interval[el.id]);	
			if (params[el.id].effect == 'fade' || params[el.id].effect == 'slideLeft' ||  params[el.id].effect == 'slideTop' || params[el.id].effect == 'none'){
				interval[el.id] = setInterval(function() { $.transition(el)  }, params[el.id].delay+  params[el.id].sDelay*10);
			}else{						
				delay = params[el.id].delay + params[el.id].spw*params[el.id].sph*params[el.id].sDelay;
				interval[el.id] = setInterval(function() { $.transition(el)  }, delay);
			}
			
		}
		
		// transitions
		$.transition = function(el,direction){
			
			if(params[el.id].pause == true) return;
			
			if (params[el.id].effect == 'fade' || params[el.id].effect == 'slideLeft' ||  params[el.id].effect == 'slideTop' ||  params[el.id].effect == 'none' ){
				//$.showImage(imagePos[el.id],el);
			}else{
				$.effect(el);			
				squarePos[el.id] = 0;
				appInterval[el.id] = setInterval(function() { $.appereance(el,order[el.id][squarePos[el.id]])  },params[el.id].sDelay);					
				$(el).css({ 'background-image': 'url('+images[el.id][imagePos[el.id]]+')' });
			}
			
			if(typeof(direction) == "undefined")
				imagePos[el.id]++;
			else
				if(direction == 'prev')
					imagePos[el.id]--;
				else
					imagePos[el.id] = direction;
		
			if  (imagePos[el.id] == params[el.id].nImages) {//if views or clicks or date expires the buttons, thumbs navigation has to be changed. a new function onChangeTotalBanners
				imagePos[el.id] = 0;
			}
			
			if (imagePos[el.id] == -1){
				imagePos[el.id] = params[el.id].nImages-1;
			}
			
			if (params[el.id].effect == 'fade' || params[el.id].effect == 'slideLeft' ||  params[el.id].effect == 'slideTop' ||  params[el.id].effect == 'none' ){
				$.showImage(imagePos[el.id],el);
				//$.setB(el, imagePos[el.id]);
			}
	
			if (params[el.id].showCustom){
				$('.cs-button-custom'+el.id).removeClass('cs-activecustom');
				$('#cs-button-custom'+el.id+"-"+(imagePos[el.id]+1)).addClass('cs-activecustom');
			}
			
			if (params[el.id].showNumbers){
				$('.cs-button-numbers'+el.id).removeClass('cs-activenumbers');
				$('#cs-button-numbers'+el.id+"-"+(imagePos[el.id]+1)).addClass('cs-activenumbers');
			}
		
		if (params[el.id].showThumbnails){
				$('.cs-button-thumbs'+el.id).removeClass('cs-activethumbs');
				$('#cs-button-thumbs'+el.id+"-"+(imagePos[el.id]+1)).addClass('cs-activethumbs');
			}
		
			
			if(titles[el.id][imagePos[el.id]]){
				$('#cs-title-'+el.id).css({ 'opacity' : 0 }).animate({ 'opacity' : params[el.id].opacity }, params[el.id].titleSpeed);
				$('#cs-title-'+el.id).html(titles[el.id][imagePos[el.id]]);
			} else {
				$('#cs-title-'+el.id).css('opacity',0);
			}


			if(params[el.id].onView)
						params[el.id].onView( $.getUrlVars(links[el.id][imagePos[el.id]])["bid"]);				
			
			changed = false;
			if(params[el.id].addB){
				//$('#'+el.id).children('.chtml').remove();

				/*
				if(params[el.id].addB(k33) && k33<4){
					changed = true;
					$('#'+el.id).append(params[el.id].addB(k33));
			
					$.each($('#'+el.id+' img:first-child,'+'#'+el.id+' object:first-child,'+'#'+el.id+' p:first-child'), function(i,item){
					
					images[el.id][i] 		= $(item).attr('src');
					links[el.id][i] 		= $(item).parent().is('a') ? $(item).parent().attr('href') : '';									
					linksTarget[el.id][i] 	= $(item).parent().is('a') ? $(item).parent().attr('target') : '';
					titles[el.id][i] 		= $(item).next().is('span') ? $(item).next().html() : '';
					//alert($(item).html());
					$(item).next().hide();
					if (params[el.id].effect == 'fade' || params[el.id].effect == 'slideLeft' ||  params[el.id].effect == 'slideTop' ||  params[el.id].effect == 'none'){											
						$(item).css({'border':'0'});
						$(item).parent().attr('id',"chtml"+i);
						//$(item).parent().css('z-index','200'+i);
					}else{
						$(item).hide();
					}
				});	
				
				if (params[el.id].effect == 'fade' || params[el.id].effect == 'slideLeft' ||  params[el.id].effect == 'slideTop' ||  params[el.id].effect == 'none'){		
					$.setB(el, 0 );					
				}
				
				}	*/			
			}
			
			//k22++;//to be removed
			
			/*if(params[el.id].removeB && k22>100){
				changed = true;
				k22 = 0;//to be removed
				s = params[el.id].removeB();//array with removed ids.
				$('#'+el.id).children('#chtml'+s).remove();
				params[el.id].nImages --;
				imagePos[el.id] = 0;
				$.each($('#'+el.id+' img:first-child,'+'#'+el.id+' object:first-child,'+'#'+el.id+' p:first-child'), function(i,item){
					images[el.id][i] 		= $(item).attr('src');
					links[el.id][i] 		= $(item).parent().is('a') ? $(item).parent().attr('href') : '';									
					linksTarget[el.id][i] 	= $(item).parent().is('a') ? $(item).parent().attr('target') : '';
					titles[el.id][i] 		= $(item).next().is('span') ? $(item).next().html() : '';
					$(item).next().hide();
					if (params[el.id].effect == 'fade' || params[el.id].effect == 'slideLeft' ||  params[el.id].effect == 'slideTop' ||  params[el.id].effect == 'none' ){											
					$(item).css({'border':'0'});
					$(item).parent().attr('id',"chtml"+i);
					//$(item).parent().css('z-index','200'+i);
				}else{
					$(item).hide();
				}
				});		
				if (params[el.id].effect == 'fade' || params[el.id].effect == 'slideLeft' ||  params[el.id].effect == 'slideTop' ||  params[el.id].effect == 'none' ){		
					$.setB(el, 0 );					
				}
			}	*/
	
			if(changed){
				if(params[el.id].showCustom){
					$('#cs-buttons-custom'+el.id).remove();
					$.setNavigation(el, 'custom');			
				}
			
				if(params[el.id].showNumbers){
					$('#cs-buttons-numbers'+el.id).remove();
					$.setNavigation(el, 'numbers');
				}
			
				if(params[el.id].showThumbnails){			
					$('#cs-buttons-thumbs'+el.id).remove();
					$.setNavigation(el, 'thumbs');
				}
			}
			

		};
		
		$.appereance = function(el,sid){

			$('.cs-'+el.id).attr('href',links[el.id][imagePos[el.id]]).attr('target',linksTarget[el.id][imagePos[el.id]]);
			$('#cs-'+el.id+sid).die();
			$('#cs-'+el.id+sid).live('click',function(){params[el.id].onMyClick(titles[el.id][imagePos[el.id]]);});
			
			if (squarePos[el.id] == params[el.id].spw*params[el.id].sph) {
				clearInterval(appInterval[el.id]);
				return;
			}

			$('#cs-'+el.id+sid).css({ opacity: 0, 'background-image': 'url('+images[el.id][imagePos[el.id]]+')' });
			$('#cs-'+el.id+sid).animate({ opacity: 1 }, 300);
			squarePos[el.id]++;
			
		};
		
		// navigation
		$.setNavigation = function(el, t){
			// create prev and next 
		if(t == 'arrows'){
	
			$("<div id='cs-navigation-arrows"+el.id+"'></div>").appendTo($('#coin-slider-'+el.id));
			if(params[el.id].autoHideArrows)
				$('#cs-navigation-arrows'+el.id).hide();
			
			
			
			$('#cs-navigation-arrows'+el.id).append("<a href='#' id='cs-prev-arrows"+el.id+"' class='cs-prevarrows'>prev</a>");
			$('#cs-navigation-arrows'+el.id).append("<a href='#' id='cs-next-arrows"+el.id+"' class='cs-nextarrows'>next</a>");
			$navarrp = $('#cs-prev-arrows'+el.id).css({
				'position' 	: 'absolute',
				'top'		: params[el.id].height/2 - 45,
				'left'		: 0,
				'z-index' 	: 1001,
				'line-height': '30px',
				'opacity'	: params[el.id].opacity
			}).click( function(e){
				e.preventDefault();
				$.transition(el,'prev');
				$.transitionCall(el);		
			});
					if(params[el.id].autoHideArrows)
						$navarrp.mouseover( function(){ $('#cs-navigation-arrows'+el.id).show() }).mouseout( function(){ $('#cs-navigation-arrows'+el.id).hide() });
	
			$navarrn = $('#cs-next-arrows'+el.id).css({
				'position' 	: 'absolute',
				'top'		: params[el.id].height/2 - 45,
				'left'		: params[el.id].width-45,
				'right'		: 0,
				'z-index' 	: 1001,
				'line-height': '30px',
				'opacity'	: params[el.id].opacity
			}).click( function(e){
				e.preventDefault();
				$.transition(el);
				$.transitionCall(el);
			});
				if(params[el.id].autoHideArrows)
					$navarrn.mouseover( function(){ $('#cs-navigation-arrows'+el.id).show() }).mouseout( function(){ $('#cs-navigation-arrows'+el.id).hide() });
		}
		if(t == 'custom'){
			// image buttons
			$("<div id='cs-buttons-custom"+el.id+"' class='cs-buttonscustom'></div>").appendTo($('#coin-slider-'+el.id));

			
			for(k=1;k<params[el.id].nImages+1;k++){
				$('#cs-buttons-custom'+el.id).append("<a href='#' class='cs-button-custom"+el.id+"' id='cs-button-custom"+el.id+"-"+k+"'>"+k+"</a>");
			}
			
			$.each($('.cs-button-custom'+el.id), function(i,item){
				$(item).click( function(e){
					$('.cs-button-custom'+el.id).removeClass('cs-activecustom');
					$(this).addClass('cs-activecustom');
					e.preventDefault();
					$.transition(el,i);
					$.transitionCall(el);				
				})
			});	
			
			/*$('#cs-navigation-'+el.id+' a').mouseout(function(){
				$('#cs-navigation-'+el.id).hide();
				params[el.id].pause = false;
			});*/						

			$("#cs-buttons-custom"+el.id).css({
				'left'			: (params[el.id].width-25*params[el.id].nImages)+"px",				
				'position'		: 'relative'
				
			});
			
		}
		if(t == 'numbers'){
			// image buttons
			$("<div id='cs-buttons-numbers"+el.id+"' class='cs-buttonsnumbers'></div>").appendTo($('#coin-slider-'+el.id));

			
			for(k=1;k<params[el.id].nImages+1;k++){
				$('#cs-buttons-numbers'+el.id).append("<a href='#' class='cs-button-numbers"+el.id+"' id='cs-button-numbers"+el.id+"-"+k+"'>"+k+"</a>");
			}
			
			$.each($('.cs-button-numbers'+el.id), function(i,item){
				$(item).click( function(e){
					$('.cs-button-numbers'+el.id).removeClass('cs-activenumbers');
					$(this).addClass('cs-activenumbers');
					e.preventDefault();
					$.transition(el,i);
					$.transitionCall(el);				
				})
			});	
			
			/*$('#cs-navigation-'+el.id+' a').mouseout(function(){
				$('#cs-navigation-'+el.id).hide();
				params[el.id].pause = false;
			});*/						

			$("#cs-buttons-numbers"+el.id).css({
				'left'			: "25px",
				'position'		: 'relative'
				
			});
			
		}		
		
		if(t == 'thumbs'){
			// image buttons
			$("<div id='cs-buttons-thumbs"+el.id+"' class='cs-buttonsthumbs'></div>").appendTo($('#coin-slider-'+el.id));
			
			$('#cs-buttons-thumbs'+el.id).css({'width':params[el.id].thumbsWidth * params[el.id].nImages +"px", 'height':params[el.id].thumbsHeight+"px"});
			
			for(k=1;k<params[el.id].nImages+1;k++){
			
			if(params[el.id].useAutoResize){
				$('#cs-buttons-thumbs'+el.id).append("<a href='#' class='cs-button-thumbs"+el.id+"' id='cs-button-thumbs"+el.id+"-"+k+"'>"+"<img border='0' src='imagick_thumb.php?width="+params[el.id].thumbsWidth+"&height="+params[el.id].thumbsHeight+"&imgSrc="+images[el.id][k-1]+"'/></a>");
				}else if (params[el.id].useUpThumbs){
					ext = images[el.id][k-1].substr(images[el.id][k-1].length-4,4);
					$('#cs-buttons-thumbs'+el.id).append("<a href='#' class='cs-button-thumbs"+el.id+"' id='cs-button-thumbs"+el.id+"-"+k+"'>"+"<img border='0' src='"+images[el.id][k-1].substr(0,images[el.id][k-1].length-4)+"_thumb"+ext+"'/></a>");
				}else{
					$('#cs-buttons-thumbs'+el.id).append("<a href='#' class='cs-button-thumbs"+el.id+"' id='cs-button-thumbs"+el.id+"-"+k+"'>"+"<img border='0' width='"+params[el.id].thumbsWidth+"' height='"+params[el.id].thumbsHeight+"' src='"+images[el.id][k-1]+"'/></a>");
				}
				
			}
			
			$.each($('.cs-button-thumbs'+el.id), function(i,item){
				$(item).click( function(e){
					$('.cs-button-thumbs'+el.id).removeClass('cs-activethumbs');
					$(this).addClass('cs-activethumbs');
					e.preventDefault();
					$.transition(el,i);
					$.transitionCall(el);				
				})
			});	
			
			/*$('#cs-navigation-'+el.id+' a').mouseout(function(){
				$('#cs-navigation-'+el.id).hide();
				params[el.id].pause = false;
			});*/						

			$("#cs-buttons-thumbs"+el.id).css({
				'left'			: '25px',
				'top'			: (params[el.id].thumbsHeight)+"px",	
				'width'			: (params[el.id].thumbsWidth*3 + 50)+"px",
				'height'		: (params[el.id].thumbsHeight*3)+"px",
				'position'		: 'relative'
				
			});
			
		}
		
		//in the next versions thumbs with description like s3slider(the titlebar needs modified) or http://demo.webdeveloperplus.com/featured-content-slider/
		if(t == 'thumbsdesc'){
			// image buttons
			$("<div id='cs-buttons-"+el.id+"' class='cs-buttons'></div>").appendTo($('#coin-slider-'+el.id));

			
			for(k=1;k<params[el.id].nImages+1;k++){
				$('#cs-buttons-'+el.id).append("<a href='#' class='cs-button-"+el.id+"' id='cs-button-"+el.id+"-"+k+"'>"+k+"</a>");
			}
			
			$.each($('.cs-button-'+el.id), function(i,item){
				$(item).click( function(e){
					$('.cs-button-'+el.id).removeClass('cs-active');
					$(this).addClass('cs-active');
					e.preventDefault();
					$.transition(el,i);
					$.transitionCall(el);				
				})
			});	
			
			$('#cs-navigation-'+el.id+' a').mouseout(function(){
				$('#cs-navigation-'+el.id).hide();
				params[el.id].pause = false;
			});						

			$("#cs-buttons-"+el.id).css({
				'left'			: '50%',
				'margin-left' 	: -images[el.id].length*15/2-5,
				'position'		: 'relative'
				
			});
			
		}
		
		
		}




		// effects
		$.effect = function(el){
			
			effA = ['random','swirl','rain','straight'];
			if(params[el.id].effect == '')
				eff = effA[Math.floor(Math.random()*(effA.length))];
			else
				eff = params[el.id].effect;

			order[el.id] = new Array();

			if(eff == 'random'){
				counter = 0;
				  for(i=1;i <= params[el.id].sph;i++){
				  	for(j=1; j <= params[el.id].spw; j++){	
				  		order[el.id][counter] = i+''+j;
						counter++;
				  	}
				  }	
				$.random(order[el.id]);
			}
			
			if(eff == 'rain')	{
				$.rain(el);
			}
			
			if(eff == 'swirl')
				$.swirl(el);
				
			if(eff == 'straight')
				$.straight(el);
				
			reverse[el.id] *= -1;
			if(reverse[el.id] > 0){
				order[el.id].reverse();
			}

		}

			
		// shuffle array function
		$.random = function(arr) {
						
		  var i = arr.length;
		  if ( i == 0 ) return false;
		  while ( --i ) {
		     var j = Math.floor( Math.random() * ( i + 1 ) );
		     var tempi = arr[i];
		     var tempj = arr[j];
		     arr[i] = tempj;
		     arr[j] = tempi;
		   }
		}	
		
		//swirl effect by milos popovic
		$.swirl = function(el){

			var n = params[el.id].sph;
			var m = params[el.id].spw;

			var x = 1;
			var y = 1;
			var going = 0;
			var num = 0;
			var c = 0;
			
			var dowhile = true;
						
			while(dowhile) {
				
				num = (going==0 || going==2) ? m : n;
				
				for (i=1;i<=num;i++){
					
					order[el.id][c] = x+''+y;
					c++;

					if(i!=num){
						switch(going){
							case 0 : y++; break;
							case 1 : x++; break;
							case 2 : y--; break;
							case 3 : x--; break;
						
						}
					}
				}
				
				going = (going+1)%4;

				switch(going){
					case 0 : m--; y++; break;
					case 1 : n--; x++; break;
					case 2 : m--; y--; break;
					case 3 : n--; x--; break;		
				}
				
				check = $.max(n,m) - $.min(n,m);			
				if(m<=check && n<=check)
					dowhile = false;
									
			}
		}

		// rain effect
		$.rain = function(el){
			var n = params[el.id].sph;
			var m = params[el.id].spw;

			var c = 0;
			var to = to2 = from = 1;
			var dowhile = true;


			while(dowhile){
				
				for(i=from;i<=to;i++){
					order[el.id][c] = i+''+parseInt(to2-i+1);
					c++;
				}
				
				to2++;
				
				if(to < n && to2 < m && n<m){
					to++;	
				}
				
				if(to < n && n>=m){
					to++;	
				}
				
				if(to2 > m){
					from++;
				}
				
				if(from > to) dowhile= false;
				
			}			

		}

		// straight effect
		$.straight = function(el){
			counter = 0;
			for(i=1;i <= params[el.id].sph;i++){
				for(j=1; j <= params[el.id].spw; j++){	
					order[el.id][counter] = i+''+j;
					counter++;
				}
				
			}
		}

		$.min = function(n,m){
			if (n>m) return m;
			else return n;
		}
		
		$.max = function(n,m){
			if (n<m) return m;
			else return n;
		}		
	
	this.each (
		function(){ init(this); }
	);
	

	};
	
	
	// default values
	$.fn.coinslider.defaults = {	
		width: 565, // width of slider panel
		height: 290, // height of slider panel
		spw: 7, // squares per width
		sph: 5, // squares per height
		delay: 3000, // delay between images in ms
		sDelay: 30, // delay beetwen squares in ms
		opacity: 0.7, // opacity of title and navigation
		titleSpeed: 500, // speed of title appereance in ms
		effect: '', // random, swirl, rain, straight		
		links : true, // show images as links 
		hoverPause: true // pause on hover		
	};	
	
})(jQuery);

(function($) {

	var params 		= new Array;
	
	$.fn.rentalslide= $.fn.RentalSlide = function(options){
		
		init = function(el){
							
				
			params[el.id] = $.extend({}, $.fn.rentalslide.defaults, options);
			
			params[el.id].opacity = params[el.id].topacity;						
			
			if(params[el.id].effect == 'swirl' || params[el.id].effect == 'rain' || params[el.id].effect == 'straight' || params[el.id].effect == 'fade' || params[el.id].effect == 'slideLeft' || params[el.id].effect == 'slideTop' ||  params[el.id].effect == 'none')	{		
				$('#'+el.id).coinslider(params[el.id])
						
			}else{
				switch (params[el.id].effect){
				
					case "fountainTop": params[el.id].effect = "";params[el.id].direction = "fountain";params[el.id].position = "top"; 
									break;

					case "fountainBottom": params[el.id].effect = "";params[el.id].direction = "fountain";params[el.id].position = "bottom"; 
									break;

					case "fountainAlternate": params[el.id].effect = "";params[el.id].direction = "fountainAlternate";params[el.id].position = "alternate";
									break;
					case "curtainAlternate": params[el.id].effect = "";params[el.id].direction = "alternate";params[el.id].position = "curtain";
									break;
					case "topLeft": params[el.id].effect = "";params[el.id].direction = "left";params[el.id].position = "top";
									break;
					case "topRight": params[el.id].effect = "";params[el.id].direction = "right";params[el.id].position = "top";
									break;
					case "topRandom": params[el.id].effect = "";params[el.id].direction = "random";params[el.id].position = "top";
									break;
					case "bottomLeft": params[el.id].effect = "";params[el.id].direction = "left";params[el.id].position = "bottom";
									break;
					case "bottomRight": params[el.id].effect = "";params[el.id].direction = "right";params[el.id].position = "bottom";
									break;
					case "bottomRandom": params[el.id].effect = "";params[el.id].direction = "random";params[el.id].position = "bottom";
									break;
				}

				params[el.id].titleOpacity = params[el.id].topacity;
				params[el.id].stripDelay = params[el.id].sDelay
				$('#'+el.id).jqFancyTransitions(params[el.id]);
			}
				
		}
		this.each (
		function(){ 
		
		init(this); 
		}
	);
		};
		$.fn.rentalslide.defaults = {	
		width: 450, // width of slider panel
		height: 266, // height of slider panel
		spw: 7, // squares per width
		sph: 5, // squares per height
		strips: 10, // number of strips
		effect: '', // curtain, zipper, wave, fountainTop, fountainBottom,fountainAlternate,curtainAlternate,topLeft,topRight,bottomLeft,bottomRight,topRandom, bottomRandom, swirl, rain, straight, horizontal-slider, vertical-slider, fade
		delay: 3000, // delay between images in ms
		sDelay: 30, // delay beetwen squares in ms
		topacity: 0.7, // opacity of title and navigation
		titleSpeed: 500, // speed of title appereance in ms		
		hoverPause: true, // pause on hover				
		autoHideNumber: false,
		autoHideCustom: false,
		autoHideArrows: true,
		autoHideThumbs: false,
		autoHideThumbsDesc: false,
		autoHideTitle: false,
		showCustom: true,
		showArrows:true,
		showNumbers: true,
		showThumbnails: true,
		showThumbsDesc: false,
		navigationDirection:"horizontal", //hotizontal, vertical
		useAutoResize: false, //if false use "_thumbs" added to the image name, if true use phpthumb
		useThumbs: false,
		thumbsWidth:50,
		thumbsHeight:50,
		nImages:3	
	};	
	
	
})(jQuery);