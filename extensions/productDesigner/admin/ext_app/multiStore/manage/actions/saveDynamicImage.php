<?php
/*
	Product Designer Extension Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

	if (!class_exists('upload')){
		require(DIR_WS_CLASSES . 'upload.php');
	}

	if ($new_image =  new upload('Filedata', $_GET['folder'])){
		$json = array(
			'success' => true,
			'file_name' => $new_image->filename,
			'image_path' => sysConfig::getDirWsCatalog() . 'extensions/productDesigner/images/dynamic/' . $new_image->filename,
			'thumb_path' => 'imagick_thumb.php?width=50&height=50&imgSrc=' . sysConfig::getDirFsCatalog() . 'extensions/productDesigner/images/dynamic/' . $new_image->filename
		);
	}else{
		$json = array(
			'success' => false
		);
	}

	EventManager::attachActionResponse($json, 'json');
?>