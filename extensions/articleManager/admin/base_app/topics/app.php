<?php
/*
	Articles Extension Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

	$appContent = $App->getAppContentFile();

	// check if the catalog image directory exists
	if (is_dir(DIR_FS_CATALOG_IMAGES)){
		if (!is_writeable(DIR_FS_CATALOG_IMAGES)) $messageStack->add(sysLanguage::get('ERROR_CATALOG_IMAGE_DIRECTORY_NOT_WRITEABLE'), 'error');
	}else{
		$messageStack->add(sysLanguage::get('ERROR_CATALOG_IMAGE_DIRECTORY_DOES_NOT_EXIST'), 'error');
	}
	
	$App->addJavascriptFile('ext/jQuery/ui/jquery.ui.datepicker.js');
	$App->addJavascriptFile('admin/rental_wysiwyg/ckeditor.js');
?>