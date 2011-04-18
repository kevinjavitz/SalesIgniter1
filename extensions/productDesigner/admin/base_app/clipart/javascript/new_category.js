$(document).ready(function (){
	$('#page-1').tabs();
	$('#tab_container').tabs();

	$('.theBox .ui-icon-closethick').live('click', function (){
		var $parent = $(this).parent();
		$.ajax({
			url: $(this).attr('href'),
			cache: false,
			dataType: 'json',
			beforeSend: function (){
				showAjaxLoader($parent, 'large', 'append');
			},
			success: function (data){
				if (data.success == 'deleted') {
					removeAjaxLoader($parent);
					$parent.remove();
				}
			}
		});
		return false;
	});
	
	$('.theBox .ui-icon-zoomin').live('click', function (){
		$(this).parent().find('.fancyBox').click();
		return false;
	});
	
	$('.theBox .fancyBox').live('loadBox', function (){
		$(this).fancybox({
			speedIn: 500,
			speedOut: 500,
			overlayShow: false,
			type: 'image',
			autoScale: false
		});
	}).trigger('loadBox');

	$('#file_upload').uploadify({
		uploader: DIR_WS_CATALOG + 'ext/jQuery/external/uploadify/uploadify.swf',
		script: 'application.php',
		method: 'GET',
		multi: true,
		queueID: 'uploadQueue',
		scriptData: {
			'appExt': 'productDesigner',
			'app': 'clipart',
			'appPage': 'default',
			'action': 'saveImages',
			'cid': $('#cid').val(),
			'rType':'ajax',
			'osCAdminID':sessionId
		},
		
		cancelImg: DIR_WS_CATALOG + 'ext/jQuery/external/uploadify/images/cancel.png',
		auto: true,
		folder: DIR_FS_CATALOG + 'extensions/productDesigner/images/clipart',
		onError: function (event, queueID, fileObj, errorObj){
			alert('error');
			alert(errorObj.type + ' :: ' + errorObj.info);
		},
		onComplete: function (event, queueID, fileObj, resp, data){
			//$('#debuging').val(resp).show();
			//theBox can be added here and won't be needed a reload.
			var theResp = eval('(' + resp + ')');			

			if (theResp.success == true){
				var $imgThumb = $('<a class="fancyBox" href="' + theResp.image_path + '"><img src="' + theResp.thumb_path + '" /></a><br /><a class="ui-icon ui-icon-zoomin"></a> <a class="ui-icon ui-icon-closethick" href="' + js_app_link('appExt=productDesigner&app=clipart&appPage=new_category&action=deleteImage&iID=' + theResp.iID) + '"></a>');

				var $theBox = $('<div>').addClass('theBox').append($imgThumb);
				
				$('#allBox').append($theBox);
				
				$('.fancyBox', $theBox).trigger('loadBox');
			}
		}
	});
	
});
