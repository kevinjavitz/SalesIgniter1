<?php
	$appContent = $App->getAppContentFile();
	
	// check if the catalog image directory exists
	if (is_dir(DIR_FS_CATALOG_IMAGES)){
		if (!is_writeable(DIR_FS_CATALOG_IMAGES)){
			$messageStack->add('footerStack', sysLanguage::get('ERROR_CATALOG_IMAGE_DIRECTORY_NOT_WRITEABLE'), 'error');
		}
	}else{
		$messageStack->add('footerStack', sysLanguage::get('ERROR_CATALOG_IMAGE_DIRECTORY_DOES_NOT_EXIST'), 'error');
	}
?>