<?php
	require(sysConfig::getDirFsAdmin() . 'includes/classes/upload.php');
	$App->setInfoBoxId((isset($_GET['cID']) ? $_GET['cID'] : null));
	$appContent = $App->getAppContentFile();

// calculate category path
if (isset($_GET['cPath'])) {
	$cPath = $_GET['cPath'];
} else {
	$cPath = '';
}

if (tep_not_null($cPath)) {
	$cPath_array = tep_parse_category_path($cPath);
	$cPath = implode('_', $cPath_array);
	$current_category_id = $cPath_array[(sizeof($cPath_array)-1)];
} else {
	$current_category_id = 0;
}

	if ($App->getAppPage() == 'new_category'){
		$App->addJavascriptFile('admin/rental_wysiwyg/ckeditor.js');
		$App->addJavascriptFile('ext/jQuery/ui/jquery.ui.tabs.js');
		$App->addJavascriptFile('ext/jQuery/external/uploadify/swfobject.js');
		$App->addJavascriptFile('ext/jQuery/external/uploadify/jquery.uploadify.js');
		$App->addStylesheetFile('ext/jQuery/external/uploadify/jquery.uploadify.css');
	}else{
	}

	// check if the catalog image directory exists
	if (is_dir(sysConfig::get('DIR_FS_CATALOG_IMAGES'))){
		if (!is_writeable(sysConfig::get('DIR_FS_CATALOG_IMAGES'))){
			$messageStack->add('footerStack', sysLanguage::get('ERROR_CATALOG_IMAGE_DIRECTORY_NOT_WRITEABLE'), 'error');
		}
	}else{
		$messageStack->add('footerStack', sysLanguage::get('ERROR_CATALOG_IMAGE_DIRECTORY_DOES_NOT_EXIST'), 'error');
	}
?>