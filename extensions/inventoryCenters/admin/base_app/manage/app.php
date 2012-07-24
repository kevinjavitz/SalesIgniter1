<?php
require(sysConfig::getDirFsAdmin() . 'includes/classes/upload.php');
$appContent = $App->getAppContentFile();
	if ($App->getAppPage() == 'new'){
		$App->addJavascriptFile('ext/jQuery/ui/jquery.ui.tabs.js');
		$App->addJavascriptFile('admin/rental_wysiwyg/ckeditor.js');
		$App->addJavascriptFile('ext/jQuery/external/uploadify/swfobject.js');
		$App->addJavascriptFile('ext/jQuery/external/uploadify/jquery.uploadify.js');
		$App->addJavascriptFile('ext/jQuery/external/fancybox/jquery.fancybox.js');
		$App->addJavascriptFile('ext/jQuery/external/colorPicker/jquery.colorpicker.js');
		$App->addStylesheetFile('ext/jQuery/external/colorPicker/jquery.colorpicker.css');

		$App->addStylesheetFile('ext/jQuery/external/datepick/css/jquery.datepick.css');
		$App->addStylesheetFile('ext/jQuery/external/uploadify/jquery.uploadify.css');
		$App->addStylesheetFile('ext/jQuery/external/fancybox/jquery.fancybox.css');

	}
	if (isset($_GET['cID'])){
		$App->setInfoBoxId($_GET['cID']);
	} 
?>