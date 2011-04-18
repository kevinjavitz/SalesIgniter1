<?php
/*
	Product Designer Extension Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

	if (!class_exists('upload')){
		require(sysConfig::getDirFsAdmin() . 'includes/classes/upload.php');
	}
	
	$relDir = 'extensions/productDesigner/images/uploaded/';

	$newImage = new upload();
	$newImage->set_destination(sysConfig::getDirFsCatalog() . $relDir);
	$newImage->set_permissions('777');
	$newImage->set_file('Filedata');
	if ($newImage->parse()){
		$fileExt = strtolower(substr($newImage->filename, strrpos($newImage->filename, '.')+1));
		$randName = date('Ymdhis') . '.' . $fileExt;
		$try = 1;
		while(file_exists(sysConfig::getDirFsCatalog() . $relDir . $randName)){
			$randName = date('Ymdhis') . '_' . $try++ . '.' . $fileExt;
		}
		$newImage->set_filename($randName);
		if ($newImage->save()){
			$json = array(
				'success' => true,
				'image_name' => $newImage->filename,
				'thumb_path' => 'imagick_thumb.php?path=rel&width=80&height=80&imgSrc=' . sysConfig::getDirWsCatalog() . $relDir . $newImage->filename,
				'image_path' => sysConfig::getDirWsCatalog() . $relDir . $newImage->filename
			);
		}else{
			$json = array(
				'success' => false
			);
		}
	}else{
		$json = array(
			'success' => false
		);
	}

	EventManager::attachActionResponse($json, 'json');
?>