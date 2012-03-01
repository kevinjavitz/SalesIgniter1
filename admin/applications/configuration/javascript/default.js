function uploadManagerField($el){
	if ($el.attr('type') == 'file'){
		alert($el.attr('name') + ' cannot be an upload manager field because it is not a text input field.');
		return;
	}

	var isMulti = false;
	var autoUpload = true;
	var hasPreviewContainer = false;
	var $debugger = $('#' + $el.attr('id') + '_uploadDebugOutput').addClass('uploadDebugger');

	if ($el.attr('data-is_multi')){
		isMulti = ($el.attr('data-is_multi') == 'true');
	}

	if ($el.attr('data-has_preview')){
		hasPreviewContainer = true;
		var $previewContainer = $('#' + $el.attr('id') + '_previewContainer');
	}

	if ($el.attr('data-auto_upload')){
		autoUpload = ($el.attr('data-auto_upload') == 'true');
	}

	var fileType = $el.attr('data-file_type');
	$el.uploadify({
		uploader: DIR_WS_CATALOG + 'ext/jQuery/external/uploadify/uploadify.swf',
		script: 'application.php',
		method: 'GET',
		multi: isMulti,
		scriptData: {
			'app': thisApp,
			'appPage': thisAppPage,
			'action': 'uploadFile',
			'rType': 'ajax',
			'osCAdminID': sessionId,
			'fileType': fileType
		},
		cancelImg: DIR_WS_CATALOG + 'ext/jQuery/external/uploadify/images/cancel.png',
		auto: autoUpload,
		onError: function (event, queueID, fileObj, errorObj){
			if ($('#turnOnDebugger').data('turnedOn') === true){
				var curVal = $debugger.val();
				$debugger.val(curVal + "\nError Uploading: " + errorObj.type + " :: " + errorObj.info);
			}else{
				alert("Error Uploading: " + errorObj.type + " :: " + errorObj.info);
			}
		},
		onAllComplete: function (){
			if ($('#turnOnDebugger').data('turnedOn') === true){
				var curVal = $debugger.val();
				$debugger.val(curVal + "\nAll Uploads Completed!");
			}
		},
		onOpen: function (event, queueID, fileObj){
			if ($('#turnOnDebugger').data('turnedOn') === true){
				var curVal = $debugger.val();
				$debugger.val(curVal + "\nBeginning Upload: " + fileObj.name);
			}
		},
		onProgress: function (event, queueID, fileObj, data){
			if ($('#turnOnDebugger').data('turnedOn') === true){
				var curVal = $debugger.val();
				$debugger.val(curVal + "\nUpload Speed: " + data.speed + ' KB/ps');
			}
		},
		onComplete: function (event, queueID, fileObj, resp, data){
			if ($('#turnOnDebugger').data('turnedOn') === true){
				var curVal = $debugger.val();
				$debugger.val(curVal + "\nUpload Completed\nJson Response: " + resp);
			}

			var theResp = eval('(' + resp + ')');

			if (theResp.success == true){
				if (isMulti){
					if ($el.val() != ''){
						$el.val($el.val() + ';' + theResp.image_name);
					}else{
						$el.val(theResp.image_name);
					}
				}else{
					$el.val(theResp.image_name);
				}

				if (hasPreviewContainer === true){
					var $deleteIcon = $('<a></a>')
						.addClass('ui-icon ui-icon-closethick');

					var $zoomIcon = $('<a></a>')
						.addClass('ui-icon ui-icon-zoomin');

					var $fancyBox = $('<a></a>')
						.addClass('fancyBox')
						.attr('href', theResp.image_path);

					var $img = $('<img></img>')
						.attr('src', theResp.thumb_path)
						.appendTo($fancyBox);

					var $thumbHolder = $('<div></div>')
						.css('text-align', 'center')
						.append($fancyBox)
						.append($zoomIcon)
						.append($deleteIcon);

					var $theBox = $('<div>').css({
						'float'  : 'left',
						'width'  : '80px',
						'height' : '100px',
						'border' : '1px solid #cccccc',
						'margin' : '.5em'
					}).append($thumbHolder);

					if (isMulti){
						$previewContainer.append($theBox);
					}else{
						$previewContainer.html($theBox);
					}
					$('.fancyBox', $theBox).trigger('loadBox');
				}
			}else{
				alert("Error Uploading: " + theResp.errorMsg);
			}
		}
	});
}

$(document).ready(function (){
	$('.uploadManagerInput').each(function(){
		uploadManagerField($(this));
	});

});
