<?php
	$settingsFile = $_POST['filePath'] . '/settings.xml';
	$langData = simplexml_load_file(
		$settingsFile,
		'SimpleXMLExtended'
	);
	
	$langData->date_format->setCData($_POST['date_format']);
	$langData->date_format_short->setCData($_POST['date_format_short']);
	$langData->date_format_long->setCData($_POST['date_format_long']);
	$langData->date_time_format->setCData($_POST['date_time_format']);
	$langData->default_currency->setCData($_POST['default_currency']);
	$langData->html_params->setCData($_POST['html_params']);
	$langData->html_charset->setCData($_POST['html_charset']);
	
	$fileObj = fopen($settingsFile, 'w+');
	if ($fileObj){
		ftruncate($fileObj, -1);
		fwrite($fileObj, $langData->asXML());
		fclose($fileObj);
	}
	
	EventManager::attachActionResponse(array(
		'success' => true
	), 'json');
?>