<?php
/*
	Product Designer Extension Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

	$Image = Doctrine_Core::getTable('ProductDesignerClipartImages');
	$theImage = $Image->findOneBy('images_id', $_GET['iID']);
	if ($theImage) {
		$theImage->delete();
		unlink(DIR_FS_CATALOG . 'extensions/productDesigner/images/clipart/' . $theImage->image);
	}

	EventManager::attachActionResponse(array('success' => 'deleted'), 'json');
?>
