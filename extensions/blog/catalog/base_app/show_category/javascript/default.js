$(document).ready(function (){
	$('stream').each(function(){
		var player = $(this);
		showAjaxLoader(player, 'large');
		$.ajax({
			cache: false,
			dataType: 'json',
			url: js_app_link('appExt=streamProducts&app=main&appPage=default&action=getPlayerConfig&'+
				'provider='+player.attr('provider')+'&'+
				'type='+player.attr('type')+'&'+
				'file='+player.attr('file')
			),
			success: function (data){
				removeAjaxLoader(player);
				if(data.success){

					data.config.clip.onStart = function(clip){
						var wrap = jQuery(this.getParent());
						var width = 480;
						var height = false;
						/*
						 if(player.attr('height')){
						 height = player.attr('height');
						 }
						 */

						var RealHeight = parseInt(clip.metaData.height);
						var RealWidth = parseInt(clip.metaData.width);
						var ratio = RealHeight / RealWidth;

						//ratio = width / RealWidth;
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

					$f(player.attr('id'), 'streamer/flowplayer/flowplayer-3.2.5.swf', data.config);
				}
			}
		});
	});
});