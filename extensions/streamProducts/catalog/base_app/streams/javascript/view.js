var flowPlayerObj;
$(document).ready(function (){
	$f.addPlugin('StreamViewUpdate', function() {
		this.onStart(function (){
			var $parentEl = $(flowPlayerObj.getParent());
			var postParams = [];
			postParams.push('action=updateViews');
			postParams.push('sID=' + $parentEl.data('sID'));
			
			if ($parentEl.data('oID')){
				postParams.push('oID=' + $parentEl.data('oID'));
			}
			
			if ($parentEl.data('opID')){
				postParams.push('opID=' + $parentEl.data('opID'));
			}
			
			$.ajax({
				cache: false,
				dataType: 'json',
				type: 'post',
				data: postParams.join('&'),
				url: js_app_link('appExt=streamProducts&app=main&appPage=default'),
				success: function (data){
				}
			});
		});

		// remeber to return the player instance
		return this;
	});
	
	var postVars = [];
	postVars.push('pID=' + $('#streamPlayer').data('pID'));
	postVars.push('sID=' + $('#streamPlayer').data('sID'));
	if ($('#streamPlayer').data('oID')){
		postVars.push('oID=' + $('#streamPlayer').data('oID'));
	}
	if ($('#streamPlayer').data('opID')){
		postVars.push('opID=' + $('#streamPlayer').data('opID'));
	}
	
	showAjaxLoader($('#streamPlayer'), 'large');
	$.ajax({
		cache: false,
		dataType: 'json',
		url: js_app_link('appExt=streamProducts&app=main&appPage=default&action=getPlayerConfig'),
		type: 'post',
		data: postVars.join('&'),
		success: function (data){
			removeAjaxLoader($('#streamPlayer'));
			if (data.success){
				data.config.clip.onStart = function(clip){
					var wrap = jQuery(this.getParent());
					var width = data.config.width;
					var height = false;
				
					var RealHeight = parseInt(clip.metaData.height);
					var RealWidth = parseInt(clip.metaData.width);
					var ratio = RealHeight / RealWidth;
				
					ratio = width / RealWidth;
					height = parseInt(RealHeight * ratio);
				
  					wrap.css({width: width, height: height});
				};
				//flowPlayerObj = $f('streamPlayer', 'streamer/flowplayer/flowplayer.commercial-3.2.7.swf', data.config).StreamViewUpdate();
				
				flowPlayerObj = $f('streamPlayer', 'streamer/flowplayer/flowplayer-3.2.5.swf', data.config).StreamViewUpdate();
			}else{
				alert('There was a problem loading the stream.');
			}
		}
	})
});