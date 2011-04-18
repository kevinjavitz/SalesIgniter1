// **************************************************************
// jQZoom allows you to realize a small magnifier window,close
// to the image or images on your web page easily.
//
// jqZoom version 1.2
// Author Doc. Ing. Renzi Marco(www.mind-projects.it)
// Released on Dec 05 2007
// i'm searching for a job,pick me up!!!
// mail: renzi.mrc@gmail.com
// **************************************************************

(function($){
	$.fn.jqueryzoom = function (options){
		var settings = {
			xzoom: 200,		//zoomed width default width
			yzoom: 200,		//zoomed div default width
			offset: 10,		//zoomed div default offset
			position: "right",  //zoomed div default position,offset position is to the right of the image,
			debug: false
		};

		if (options) {
			$.extend(settings, options);
		}

		var noalt ='';

		$(this).mouseover(function (){
			var $self = $(this);
			var imageTop =  $self.position(true).top;
			var imageLeft = $self.position(true).left;
			var imageWidth = $self.width();
			var imageHeight = $self.height();

			var bigimage = $self.attr('alt');
			noalt = $self.attr('alt');
			$self.attr('alt','');

			if ($('div.zoomdiv').size() == 0){
				$self.after('<div class="zoomdiv"><img class="bigimg" src="' + bigimage + '" /></div>');
				if (settings.debug === true){
					$self.after('<div class="jqzoomDebug"></div>');
				}
			}

			if (settings.position == 'right'){
				leftpos = imageLeft + imageWidth + settings.offset;
			}else{
				leftpos = imageLeft - settings.xzoom - settings.offset;
			}

			$("div.zoomdiv").css({ top: imageTop,left: leftpos });
			$("div.zoomdiv").width(settings.xzoom);
			$("div.zoomdiv").height(settings.yzoom);
			$("div.zoomdiv").show();
			
			if (settings.debug === true){
				$("div.jqzoomDebug").css({ top: imageTop + settings.yzoom,left: leftpos });
				$("div.jqzoomDebug").width(settings.xzoom);
				$("div.jqzoomDebug").height(settings.yzoom);
				$("div.jqzoomDebug").show();
			}
			
			$(document.body).bind('mousemove', function(e){
				if (settings.debug === true){
					$('.jqzoomDebug').empty();
					$('.jqzoomDebug').append('<div>pageX::' + e.pageX + '</div>');
					$('.jqzoomDebug').append('<div>pageY::' + e.pageY + '</div>');
				}
				
				var bigwidth = $('.bigimg').width();
				
				if (settings.debug === true){
					$('.jqzoomDebug').append('<div>bigwidth::' + bigwidth + '</div>');
				}
				
				var bigheight = $('.bigimg').height();
				
				if (settings.debug === true){
					$('.jqzoomDebug').append('<div>bigheight::' + bigheight + '</div>');
				}

				var scaley = 'x';
				var scalex = 'y';

				if (isNaN(scalex)|isNaN(scaley)){
					var scalex = Math.round(bigwidth/imageWidth) ;
					var scaley = Math.round(bigheight/imageHeight);
				}
				
				if (settings.debug === true){
					$('.jqzoomDebug').append('<div>scalex::' + scalex + '</div>');
					$('.jqzoomDebug').append('<div>scaley::' + scaley + '</div>');
				}

				scrolly = e.pageY - $self.offset().top - ($("div.zoomdiv").height()*1/scaley)/2 ;
				scrollx = e.pageX - $self.offset().left - ($("div.zoomdiv").width()*1/scalex)/2 ;
				
				if (settings.debug === true){
					$('.jqzoomDebug').append('<div>scrolly::' + scrolly + '</div>');
					$('.jqzoomDebug').append('<div>scrollx::' + scrollx + '</div>');
				}

				if (scaley > 0){
					if (settings.debug === true){
						$('.jqzoomDebug').append('<div>scrollTop::' + (scrolly * scaley) + '</div>');
					}
					
					$("div.zoomdiv").scrollTop(scrolly * scaley);
				}else{
					if (settings.debug === true){
						$('.jqzoomDebug').append('<div>scrollTop::' + (scrolly) + '</div>');
					}
					
					$("div.zoomdiv").scrollTop(scrolly);
				}
				
				if (scalex > 0){
					if (settings.debug === true){
						$('.jqzoomDebug').append('<div>scrollLeft::' + (scrollx * scalex) + '</div>');
					}
					
					$("div.zoomdiv").scrollLeft(scrollx * scalex);
				}else{
					if (settings.debug === true){
						$('.jqzoomDebug').append('<div>scrollLeft::' + (scrollx) + '</div>');
					}
					
					$("div.zoomdiv").scrollLeft(scrollx);
				}
			});
		}).mouseout(function(){
			$(this).attr("alt",noalt);
			$("div.zoomdiv").hide();
			$(document.body).unbind("mousemove");
			$(".lenszoom").remove();
			$("div.zoomdiv").remove();
			$("div.jqzoomDebug").remove();
		});
	}
})(jQuery);

function MouseEvent(e){
	this.x = e.pageX
	this.y = e.pageY
}