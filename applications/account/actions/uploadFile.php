<?php
/*
	SalesIgniter E-Commerce System v1

	I.T. Web Experts
	http://www.itwebexperts.com

	Copyright (c) 2010 I.T. Web Experts

	This script and it's source is not redistributable
*/

	$fileType = $_GET['fileType'];

	$cID = $_GET['cID'];
	require(sysConfig::getDirFsCatalog() . 'includes/classes/ftp/base.php');
	require(SysConfig::getDirFsCatalog() . 'includes/classes/uploadManager.php');
	$mgr = new UploadManager($fileTypeUploadDirs[$fileType]['abs'].'user_photos/', '777');

	$file = new UploadFile('Filedata');
	if ($mgr->processFile($file)){
		$json = array(
			'success' => true,
			'image_name' => $file->getName(),
			'thumb_path' => 'imagick_thumb.php?width=80&height=80&imgSrc=' . $fileTypeUploadDirs[$fileType]['abs'].'user_photos/' . $file->getName(),
			'image_path' => $fileTypeUploadDirs[$fileType]['rel'] . 'user_photos/'. $file->getName()
		);
	}else{
		$exception = $mgr->getException();
		$json = array(
			'success' => false,
			'fileType' => $fileType,
			'uploadedTo' => $fileTypeUploadDirs[$fileType]['abs'].'user_photos/',
			'errorMsg' => $exception->getMessage()
		);
	}

	Doctrine_Query::create()
	->update('Customers')
	->set('customers_photo', '?','user_photos/'.$file->getName())//$_POST['Filename']
	->where('customers_id = ?', $cID)
	->execute();
	EventManager::attachActionResponse($json, 'json');
?>