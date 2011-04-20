<?php
/*
	Product Additional Images Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

	$newImage = new upload();
	$newImage->set_destination($_GET['folder']);
	$newImage->set_permissions('777');
	$newImage->set_file('Filedata');
	if ($newImage->parse() && $newImage->save()){
		$json = array(
			'success' => true,
			'image_name' => $newImage->filename,
			'thumb_path' => 'imagick_thumb.php?width=80&height=80&imgSrc=' . sysConfig::getDirFsCatalog() . 'images/' . $newImage->filename,
			'image_path' => sysConfig::getDirWsCatalog() . 'images/' . $newImage->filename
		);
	}else{
		$json = array(
			'success' => false
		);
	}

	EventManager::attachActionResponse($json, 'json');
?>