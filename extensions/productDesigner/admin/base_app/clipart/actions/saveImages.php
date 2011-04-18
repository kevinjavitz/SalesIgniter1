<?php
/*
	Product Designer Extension Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

	if (!class_exists('upload')){
		require (DIR_WS_CLASSES . 'upload.php');
	}

	$newImage = new upload();
	$newImage->set_destination($_GET['folder']);
	$newImage->set_permissions('777');
	$newImage->set_file('Filedata');
	if ($newImage->parse()){
		$Image = Doctrine_Core::getTable('ProductDesignerClipartImages');
		$Image = $Image->create();
		$Image->ProductDesignerClipartImagesToCategories[0]['categories_id'] = $_GET['cid'];
		$Image->image = '';
		$Image->save();
		$newImage->set_filename($Image->images_id . $newImage->filename);
		if ($newImage->save()){
			$Image->image = $newImage->filename;
			$Image->save();
		
			$json = array(
				'success' => true,
				'thumb_path' => 'imagick_thumb.php?width=100&height=100&imgSrc=' . sysConfig::getDirFsCatalog() . 'extensions/productDesigner/images/clipart/' . $Image->image,
				'image_path' => sysConfig::getDirWsCatalog() . 'extensions/productDesigner/images/clipart/' . $Image->image,
				'iID' => $Image->images_id
			);
		}else{
			$Image->delete();
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