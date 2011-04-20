<?php
	require(sysConfig::getDirFsCatalog() . 'includes/classes/fileSystemBrowser.php');
	require(sysConfig::getDirFsAdmin() . 'includes/classes/upload.php');
	$appContent = $App->getAppContentFile();

	if ($App->getAppPage() == 'email'){
		$App->addJavascriptFile('ext/jQuery/ui/jquery.ui.tabs.js');
		$App->addJavascriptFile('admin/rental_wysiwyg/ckeditor.js');
	}
?>