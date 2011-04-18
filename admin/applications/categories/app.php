<?php
	$App->setInfoBoxId((isset($_GET['cID']) ? $_GET['cID'] : null));
	$appContent = $App->getAppContentFile();
	
	if ($App->getAppPage() == 'new_category'){
		$App->addJavascriptFile('admin/rental_wysiwyg/ckeditor.js');
		$App->addJavascriptFile('ext/jQuery/ui/jquery.ui.tabs.js');
	}else{
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