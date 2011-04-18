<?php
/*
	Product Designer Extension Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

	$appContent = $App->getAppContentFile();

	if ($App->getAppPage() == 'new_category'){
		$App->addJavascriptFile('ext/jQuery/ui/jquery.ui.tabs.js');
		$App->addJavascriptFile('ext/jQuery/external/uploadify/swfobject.js');
		$App->addJavascriptFile('ext/jQuery/external/uploadify/jquery.uploadify.js');
		$App->addJavascriptFile('ext/jQuery/external/fancybox/jquery.fancybox.js');
		
		$App->addStylesheetFile('ext/jQuery/external/uploadify/jquery.uploadify.css');
		$App->addStylesheetFile('ext/jQuery/external/fancybox/jquery.fancybox.css');
	}

	// check if the catalog image directory exists
	if (is_dir(DIR_FS_CATALOG_IMAGES)){
		if (!is_writeable(DIR_FS_CATALOG_IMAGES)){
			$messageStack->add('footerStack', sysLanguage::get('ERROR_CATALOG_IMAGE_DIRECTORY_NOT_WRITEABLE'), 'error');
		}
	}else{
		$messageStack->add('footerStack', sysLanguage::get('ERROR_CATALOG_IMAGE_DIRECTORY_DOES_NOT_EXIST'), 'error');
	}
?>
