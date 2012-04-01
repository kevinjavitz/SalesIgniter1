<?php

$RequestObj = new CurlRequest('https://' . sysConfig::get('SYSTEM_UPGRADE_SERVER') . '/sesUpgrades/getTemplates.php');

$RequestObj->setSendMethod('post');

$RequestObj->setData(array(

	'action' => 'process',

	'version' => 1,

	'username' => sysConfig::get('SYSTEM_UPGRADE_USERNAME'),

	'password' => sysConfig::get('SYSTEM_UPGRADE_PASSWORD'),

	'domain' => sysConfig::get('HTTP_HOST')

));



$ResponseObj = $RequestObj->execute();



$infoBox = htmlBase::newElement('infobox');

$infoBox->setHeader('<b>' . sysLanguage::get('WINDOW_HEADING_IMPORT_TEMPLATES') . '</b>');

$infoBox->setButtonBarLocation('top');



$installButton = htmlBase::newElement('button')->addClass('installButton')->usePreset('save')->setText('Import');

$cancelButton = htmlBase::newElement('button')->addClass('cancelButton')->usePreset('cancel');



$infoBox->addButton($installButton)->addButton($cancelButton);


require(sysConfig::getDirFsAdmin() . 'includes/classes/upload.php');

$templateZip = htmlBase::newElement('uploadManagerInput')
	->setName('templateZip')
	->autoUpload(true)
	->showPreview(true)
	->showMaxUploadSize(true)
	->allowMultipleUploads(false);

$templateName = htmlBase::newElement('input')
->setName('templateName')
->setLabel('Template Name')
->setLabelPosition('before');

$infoBox->addContentRow($templateName);
$infoBox->addContentRow($templateZip->draw());

$json = json_decode($ResponseObj->getResponse());

if ($json->success === true){

	$templatesContainer = htmlBase::newElement('div')

	->addClass('importTemplateContainer');



	$numAvailable = 0;

	foreach($json->templates as $tInfo){

		//if (is_dir(sysConfig::getDirFsCatalog() . 'templates/' . strtolower($tInfo->name))) continue;

		$numAvailable++;



		$images = '<ul style="list-style:none;position:absolute;bottom:0;left:0;margin:1em;padding:0;">';

		foreach($tInfo->images as $iInfo){

			$images .= '<li style="display:inline;margin: 0 10px;"><img src="' . $iInfo . '" width="75px"></li>';

		}

		$images .= '</ul>';



		$TemplateBox = htmlBase::newElement('div')

		->addClass('ui-widget ui-widget-content ui-corner-all importTemplate')

		->css(array(

			'float' => 'left',

			'width' => '350px',

			'height' => '450px',

			'margin' => '.5em',

			'position' => 'relative'

		))

		->html('<center>' .

			'<input type=checkbox name="template[]" value="' . strtolower($tInfo->name) . '">&nbsp;' .

			'<b style="font-size:1.2em;">' . $tInfo->name . '</b><br><br>' .

			'<div class="currentImage" style="height:300px"></div>' .

			$images .

		'</center>');



		$templatesContainer->append($TemplateBox);

	}



	if ($numAvailable == 0){

		$infoBox->addContentRow('No Templates Available');

	}else{

		$infoBox->addContentRow($templatesContainer->draw() . '<div class="ui-helper-clearfix"></div>');

	}

}else{

	$infoBox->addContentRow('No Templates Available');

}



ob_start();

?>

<link type="text/css" href="ext/jQuery/external/uploadify/jquery.uploadify.css"/>
<script type="text/javascript">

(function( jQuery ) {

	var getScript = jQuery.getScript;

	jQuery.getScript = function( resources, callback ) {

		var // reference declaration & localization
			length = resources.length,
			handler = function() { counter++; },
			deferreds = [],
			counter = 0,
			idx = 0;

		for ( ; idx < length; idx++ ) {
			deferreds.push(
				getScript( resources[ idx ], handler )
			);
		}

		jQuery.when.apply( null, deferreds ).then(function() {
			callback && callback();
		});
	};

})( jQuery );



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
			onSelect:function(event,ID,fileObj) {
				$('.installButton').attr('disabled', 'disabled');
			},
			onCancel: function(event,ID,fileObj,data) {
				$('.installButton').removeAttr('disabled');
			},
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
				$('.installButton').attr('disabled', 'disabled');
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
				$('.installButton').removeAttr('disabled');

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
							//.attr('src', theResp.thumb_path)
							.appendTo($fancyBox);

						var $thumbHolder = $('<div></div>')
							.css('text-align', 'center')
							.append($fancyBox);
							//.append($zoomIcon)
							//.append($deleteIcon);

						var $textHolder = $('<div>File uploaded:'+theResp.image_name+'. Click Import to install template</div>');

						var $theBox = $('<div>').css({
							'float'  : 'left',
							'width'  : '180px',
							//'height' : '100px',
							'border' : '1px solid #cccccc',
							'margin' : '.5em'
						}).append($thumbHolder).append($textHolder);

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

function importWindowOnLoad(){
	var deps = [ DIR_WS_CATALOG+"ext/jQuery/external/uploadify/swfobject.js",
		DIR_WS_CATALOG+"ext/jQuery/external/uploadify/jquery.uploadify.js" ];


	jQuery.getScript( deps, function( jqXhr ) {
		$('head').append('<link rel="stylesheet" href="'+DIR_WS_CATALOG+'ext/jQuery/external/uploadify/jquery.uploadify.css" type="text/css" />');
		$('.uploadManagerInput').each(function(){
			uploadManagerField($(this));
		});
	});


	$('.importTemplateContainer').find('.importTemplate').each(function (){

		$(this).find('img').each(function (){

			$(this).click(function (){

				$(this).parent().parent().find('.ui-state-highlight').removeClass('ui-state-highlight');

				$(this).addClass('ui-state-highlight');

				$(this).parent().parent().parent().find('.currentImage').html('<img src="' + $(this).attr('src') + '" width="300px">');

			}).mouseover(function (){

				this.style.cursor = 'pointer';

			}).mouseout(function (){

				this.style.cursor = 'default';

			});

		});



		$(this).find('img').first().click();

	});

}

</script>

<?php

$javascript = ob_get_contents();

ob_end_clean();



EventManager::attachActionResponse($javascript . $infoBox->draw(), 'html');

