$(document).ready(function (){
	$('.fancyBox').live('loadBox', function (){
		$(this).fancybox({
			speedIn: 500,
			speedOut: 500,
			overlayShow: false,
			type: 'image'
		});
	}).trigger('loadBox');
	
	$('.ui-icon-zoomin').live('click mouseover mouseout', function (event){
		switch(event.type){
			case 'click':
				$(this).parent().find('.fancyBox').click();
				break;
			case 'mouseover':
				this.style.cursor = 'pointer';
				//$(this).addClass('ui-state-hover');
				break;
			case 'mouseout':
				this.style.cursor = 'default';
				//$(this).removeClass('ui-state-hover');
				break;
		}
	});
	
	$('.ui-icon-closethick').live('click mouseover mouseout', function (event){
		switch(event.type){
			case 'click':
				$(this).parent().empty();
				break;
			case 'mouseover':
				this.style.cursor = 'pointer';
				//$(this).addClass('ui-state-hover');
				break;
			case 'mouseout':
				this.style.cursor = 'default';
				//$(this).removeClass('ui-state-hover');
				break;
		}
	});
	
	$('#idTabs').tabs();
	
	$('.ajaxUpload').uploadify({
		uploader: DIR_WS_CATALOG + 'ext/jQuery/external/uploadify/uploadify.swf',
		script: 'application.php',
		method: 'GET',
		multi: false,
		scriptData: {
			'appExt': 'multiStore',
			'app': 'manage',
			'appPage': 'new_store',
			'action': 'saveDynamicImage',
			'rType': 'ajax',
			'osCAdminID': sessionId
		},
		
		cancelImg: DIR_WS_CATALOG + 'ext/jQuery/external/uploadify/images/cancel.png',
		auto: true,
		folder: DIR_FS_CATALOG + 'extensions/productDesigner/images/dynamic',
		onError: function (event, queueID, fileObj, errorObj){
			alert('error');
			alert(errorObj.type + ' :: ' + errorObj.info);
		},
		onComplete: function (event, queueID, fileObj, resp, data){
			var theResp = eval('(' + resp + ')');
			
			var $theTr = $(event.target).parent().parent();
			
			$('.imagePreview', $theTr).html('<a class="fancyBox" href="' + theResp.image_path + '"><img src="' + theResp.thumb_path + '" /></a><br /><a class="ui-icon ui-icon-zoomin"></a> <a class="ui-icon ui-icon-closethick"></a><input type="hidden" name="clipart[' + $theTr.attr('clipart_key') + '][' + $theTr.attr('color_tone') + ']" value="' + theResp.file_name + '" />');
			$('.fancyBox', $theTr).trigger('loadBox');
		}
	});
});