<?php
	$appContent = $App->getAppContentFile();
	if ($App->getAppPage() == 'new'){
		$App->addJavascriptFile('ext/jQuery/ui/jquery.ui.tabs.js');
		$App->addJavascriptFile('ext/jQuery/ui/jquery.ui.datepicker.js');
		$App->addJavascriptFile('admin/rental_wysiwyg/ckeditor.js');
	}

	
	if (isset($_GET['pID'])){
		$App->setInfoBoxId($_GET['pID']);
	} 
?>