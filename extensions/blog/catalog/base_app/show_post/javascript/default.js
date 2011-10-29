function checkEmail(email) {
var filter = /^([a-zA-Z0-9_.-])+@(([a-zA-Z0-9-])+.)+([a-zA-Z0-9]{2,4})+$/;

if (!filter.test(email)) {
return false;
}
	return true;
}

$(document).ready(function (){
		$('#commentf').submit(function(){

			retf = true;
			$('.comf').each(function(){

				if($(this).attr('name') == 'comment_email'){
					if(!checkEmail($(this).val())){
						if(retf){
							alert('Wrong Email Address');
						}
						retf = false;
					}
				}

				if($(this).val() == ''){
					//if($(this).attr('name') != 'comment_text'){
						if(retf){
							alert('All the fields are necesary');
						}
						retf = false;
					/*}else{
						if(CKEDITOR.instances.comment_text.getData() =='' || CKEDITOR.instances.comment_text.getData() == null){
							if(retf){
								alert('All the fields are necesary');
							}
						retf = false;
						}
					}*/
				}
			});
			return retf;
		});

		/*$('.makeFCK').each(function (){
			CKEDITOR.replace(this, {
				toolbar : 'Basic'
			});
		});*/
		
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
					 data.config.clip.onStart = function(clip){
						 var wrap = jQuery(this.getParent());
						 var width = player.attr('width');
						 var height = player.attr('height');

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

					$f(player.attr('id'), 'streamer/flowplayer/flowplayer-3.2.5.swf', data.config);				
				 }
			 });			
		})
			

});