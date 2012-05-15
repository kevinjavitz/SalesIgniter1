<?php
/*
	SalesIgniter E-Commerce System v1

	I.T. Web Experts
	http://www.itwebexperts.com

	Copyright (c) 2010 I.T. Web Experts

	This script and it's source is not redistributable
*/
	$fileType = $_GET['fileType'];
	
	require(SysConfig::getDirFsCatalog() . 'includes/classes/uploadManager.php');
	if($fileType == 'image'){
		$mgr = new UploadManager($fileTypeUploadDirs[$fileType]['abs'], '777', array('gif','png','jpg','jpeg'));
	}else{
		$mgr = new UploadManager($fileTypeUploadDirs[$fileType]['abs'], '777', array('zip','pdf','avi','flv','swf','mov'));
	}

	$file = new UploadFile('Filedata');
	if ($mgr->processFile($file)){
		//$file->moveTo($fileTypeUploadDirs[$fileType]['abs']);
		$json = array(
			'success' => true,
			'image_name' => $file->getName(),
			'thumb_path' => 'imagick_thumb.php?width=80&height=80&imgSrc=' . $fileTypeUploadDirs[$fileType]['abs'] . $file->getName(),
			'image_path' => $fileTypeUploadDirs[$fileType]['rel'] . $file->getName()
		);
	}else{
		$exception = $mgr->getException();
		$json = array(
			'success' => false,
			'fileType' => $fileType,
			'uploadedTo' => $fileTypeUploadDirs[$fileType]['abs'],
			'errorMsg' => $exception->getMessage()
		);
	}

	EventManager::attachActionResponse($json, 'json');
?>