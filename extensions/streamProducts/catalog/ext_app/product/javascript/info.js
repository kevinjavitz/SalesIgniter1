var flowPlayerObj;
$(document).ready(function (){
	showAjaxLoader($('#streamPlayer'), 'large');
	$.ajax({
		cache: false,
		dataType: 'json',
		url: js_app_link('appExt=streamProducts&app=main&appPage=default&action=getPlayerConfig&pID=' + $('#streamPlayer').data('pID') + '&sID=' + $('#streamPlayer').data('sID')),
		success: function (data){
			removeAjaxLoader($('#streamPlayer'));
			data.config.clip.onStart = function(clip){
				var wrap = jQuery(this.getParent());
				var width = 700;
				var height;
				
				var RealHeight = parseInt(clip.metaData.height);
				var RealWidth = parseInt(clip.metaData.width);
				var ratio = RealHeight / RealWidth;
				
				ratio = width / RealWidth;
				height = parseInt(RealHeight * ratio);
				
				// Scale the image if not the original size
				if (RealWidth != width || RealHeight != height){
					var rx = RealWidth / width;
					var ry = RealHeight / height;
					
					if (rx < ry){
						width = parseInt(height / ratio);
					}else{
						height = parseInt(width * ratio);
					}
				}
  				wrap.css({width: width, height: height}); 
			};

			flowPlayerObj = $f('streamPlayer', 'streamer/flowplayer/flowplayer.commercial-3.2.5.swf', data.config);
		}
	});
});